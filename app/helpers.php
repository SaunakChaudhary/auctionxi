<?php

if (!function_exists('playerAvatarSrc')) {
    function playerAvatarSrc($player): ?string
    {
        if (!empty($player->photo)) {
            // Cloudinary URL — use directly
            if (str_contains($player->photo, 'cloudinary.com')) {
                return $player->photo;
            }
            // Legacy local path
            return \Illuminate\Support\Facades\Storage::url($player->photo);
        }
        if (!empty($player->image_url)) {
            return $player->image_url;
        }
        return null;
    }
}

if (!function_exists('playerAvatarColor')) {
    function playerAvatarColor(string $name): string
    {
        $colors = [
            'A' => '#e74c3c', 'B' => '#e67e22', 'C' => '#f39c12',
            'D' => '#27ae60', 'E' => '#1abc9c', 'F' => '#2980b9',
            'G' => '#8e44ad', 'H' => '#c0392b', 'I' => '#d35400',
            'J' => '#16a085', 'K' => '#2c3e50', 'L' => '#7f8c8d',
            'M' => '#e67e22', 'N' => '#27ae60', 'O' => '#2980b9',
            'P' => '#8e44ad', 'Q' => '#c0392b', 'R' => '#1abc9c',
            'S' => '#2c3e50', 'T' => '#27ae60', 'U' => '#f39c12',
            'V' => '#e74c3c', 'W' => '#8e44ad', 'X' => '#2980b9',
            'Y' => '#16a085', 'Z' => '#d35400',
        ];
        $initial = strtoupper(substr($name, 0, 1));
        return $colors[$initial] ?? '#6c3fc5';
    }
}

if (!function_exists('teamLogoSrc')) {
    function teamLogoSrc($team): ?string
    {
        if (!empty($team->logo)) {
            if (str_contains($team->logo, 'cloudinary.com')) {
                return $team->logo;
            }
            return \Illuminate\Support\Facades\Storage::url($team->logo);
        }
        return null;
    }
}

if (!function_exists('profilePhotoSrc')) {
    function profilePhotoSrc($user): ?string
    {
        if (!empty($user->profile_photo)) {
            if (str_contains($user->profile_photo, 'cloudinary.com')) {
                return $user->profile_photo;
            }
            return \Illuminate\Support\Facades\Storage::url(
                $user->profile_photo
            );
        }
        return null;
    }
}