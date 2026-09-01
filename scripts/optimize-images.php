<?php

declare(strict_types=1);

/**
 * Produces responsive AVIF and WebP variants for the public Nuttime image set.
 *
 * The script deliberately uses PHP's bundled GD extension so the Vite build has
 * no additional Node or Composer dependency to install or maintain.
 */
$imageDirectory = dirname(__DIR__).'/public/images/nuttime';
$widths = [640, 1200];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($imageDirectory));

foreach ($iterator as $file) {
    if (! $file instanceof SplFileInfo || ! $file->isFile()) {
        continue;
    }

    $extension = strtolower($file->getExtension());

    if (! in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
        continue;
    }

    $source = match ($extension) {
        'jpg', 'jpeg' => imagecreatefromjpeg($file->getPathname()),
        'png' => imagecreatefrompng($file->getPathname()),
    };

    if ($source === false) {
        throw new RuntimeException("Unable to read {$file->getPathname()}");
    }

    $sourceWidth = imagesx($source);
    $sourceHeight = imagesy($source);
    $variantWidths = array_unique([...$widths, $sourceWidth]);

    foreach ($variantWidths as $width) {
        if ($width > $sourceWidth) {
            continue;
        }

        $height = (int) round($sourceHeight * ($width / $sourceWidth));
        $variant = imagecreatetruecolor($width, $height);

        imagealphablending($variant, false);
        imagesavealpha($variant, true);
        imagecopyresampled($variant, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        $basename = $file->getPathname().'.'.$width;
        imagewebp($variant, $basename.'.webp', 76);
        imageavif($variant, $basename.'.avif', 46);
    }

}
