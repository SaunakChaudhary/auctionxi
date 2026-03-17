<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    /**
     * Upload file to Cloudinary using REST API
     * No package required — uses Laravel Http facade
     */
    public static function upload(
        UploadedFile $file,
        string $folder = 'auction-xi'
    ): ?string {
        $cloudName = config('cloudinary.cloud_name');
        $apiKey    = config('cloudinary.api_key');
        $apiSecret = config('cloudinary.api_secret');

        if (!$cloudName || !$apiKey || !$apiSecret) {
            Log::error('Cloudinary credentials missing in config.');
            return null;
        }

        $timestamp = time();
        $params    = [
            'folder'    => $folder,
            'timestamp' => $timestamp,
        ];

        $signature = self::sign($params, $apiSecret);

        $uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

        try {
            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post($uploadUrl, [
                'api_key'   => $apiKey,
                'timestamp' => $timestamp,
                'folder'    => $folder,
                'signature' => $signature,
            ]);

            if ($response->successful()) {
                $secureUrl = $response->json('secure_url');
                Log::info('Cloudinary upload success: ' . $secureUrl);
                return $secureUrl;
            }

            Log::error(
                'Cloudinary upload failed: ' . $response->body()
            );
            return null;
        } catch (\Exception $e) {
            Log::error('Cloudinary upload exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete file from Cloudinary by its URL
     */
    public static function delete(?string $url): void
    {
        if (!$url || !str_contains($url, 'cloudinary.com')) {
            return;
        }

        try {
            $cloudName = config('cloudinary.cloud_name');
            $apiKey    = config('cloudinary.api_key');
            $apiSecret = config('cloudinary.api_secret');

            // Extract public_id from URL
            // e.g. https://res.cloudinary.com/demo/image/upload/v123/auction-xi/abc.jpg
            // public_id = auction-xi/abc
            preg_match(
                '/\/upload\/(?:v\d+\/)?(.+)\.[a-zA-Z]+$/',
                $url,
                $matches
            );

            if (empty($matches[1])) {
                return;
            }

            $publicId  = $matches[1];
            $timestamp = time();
            $params    = [
                'public_id' => $publicId,
                'timestamp' => $timestamp,
            ];

            $signature = self::sign($params, $apiSecret);
            $deleteUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy";

            Http::post($deleteUrl, [
                'public_id' => $publicId,
                'api_key'   => $apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ]);
        } catch (\Exception $e) {
            Log::warning(
                'Cloudinary delete failed: ' . $e->getMessage()
            );
        }
    }

    /**
     * Generate Cloudinary API signature (SHA1)
     */
    private static function sign(
        array $params,
        string $apiSecret
    ): string {
        ksort($params);

        $stringToSign = implode('&', array_map(
            fn($k, $v) => "{$k}={$v}",
            array_keys($params),
            array_values($params)
        ));

        return sha1($stringToSign . $apiSecret);
    }
}
