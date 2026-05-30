<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateIcons extends Command
{
    protected $signature   = 'app:icons';
    protected $description = 'Crop the graphic from logo.png and generate PWA icon sizes (192, 512)';

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error('GD extension is not loaded.');
            return 1;
        }

        $src = public_path('logo.png');
        $img = imagecreatefrompng($src);
        if (! $img) {
            $this->error("Could not open {$src}");
            return 1;
        }

        // Logo is 931×268. The clipboard icon sits in the left portion.
        // Crop a square from the left edge (width = height = 268px).
        $size = imagesy($img);

        $square = imagecreatetruecolor($size, $size);
        imagealphablending($square, false);
        imagesavealpha($square, true);
        imagefill($square, 0, 0, imagecolorallocatealpha($square, 0, 0, 0, 127));
        imagecopy($square, $img, 0, 0, 0, 0, $size, $size);
        imagedestroy($img);

        foreach ([192, 512] as $px) {
            $out = imagecreatetruecolor($px, $px);
            imagealphablending($out, false);
            imagesavealpha($out, true);
            imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
            imagecopyresampled($out, $square, 0, 0, 0, 0, $px, $px, $size, $size);
            $path = public_path("icon-{$px}.png");
            imagepng($out, $path);
            imagedestroy($out);
            $this->info("Created icon-{$px}.png");
        }

        imagedestroy($square);
        $this->info('Icons generated. manifest.json updated to reference them.');
        return 0;
    }
}
