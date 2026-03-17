<?php

namespace App\Console\Commands;

use App\Models\Player;
use Illuminate\Console\Command;

class FixDriveImageUrls extends Command
{
    protected $signature   = 'fix:drive-urls';
    protected $description = 'Convert all Drive URLs to lh3.googleusercontent.com format';

    public function handle(): void
    {
        $players = Player::whereNotNull('image_url')->get();

        $this->info("Found {$players->count()} players with image URLs...");

        $fixed = 0;

        foreach ($players as $player) {
            $url    = $player->image_url;
            $fileId = null;

            // Already in correct format
            if (str_contains($url, 'lh3.googleusercontent.com')) {
                // Make sure it has =w200
                if (!str_contains($url, '=w')) {
                    $player->update([
                        'image_url' => $url . '=w200'
                    ]);
                    $fixed++;
                    $this->info("✓ Updated: {$player->name}");
                } else {
                    $this->line("— Already correct: {$player->name}");
                }
                continue;
            }

            // Extract file ID from drive.google.com URL
            if (str_contains($url, 'drive.google.com')) {
                if (preg_match('#/file/d/([a-zA-Z0-9_-]+)#', $url, $m)) {
                    $fileId = $m[1];
                } elseif (preg_match('#[?&]id=([a-zA-Z0-9_-]+)#', $url, $m)) {
                    $fileId = $m[1];
                }

                if ($fileId) {
                    $newUrl = 'https://lh3.googleusercontent.com/d/'
                            . $fileId . '=w200';

                    $player->update(['image_url' => $newUrl]);
                    $fixed++;
                    $this->info("✓ Fixed: {$player->name}");
                    $this->line("  New: {$newUrl}");
                } else {
                    $this->warn("✗ Could not extract ID: {$url}");
                }
            }
        }

        $this->newLine();
        $this->info("✅ Done — Fixed {$fixed} records.");
    }
}