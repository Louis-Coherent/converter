<?php

namespace App\Libraries\Converters;

use Exception;

class ImagickConverter implements ConverterInterface
{
    public static function getSupportedConversions(): array
    {
        return [
            'image/jpeg'  => ['jpeg', 'jpg', 'png', 'webp', 'bmp', 'gif', 'tiff', 'pdf'],
            'image/png'   => ['jpeg', 'jpg', 'png', 'webp', 'bmp', 'gif', 'tiff', 'pdf'],
            'image/gif'   => ['jpeg', 'jpg', 'png', 'webp', 'bmp', 'tiff', 'pdf'],
            'image/bmp'   => ['jpeg', 'jpg', 'png', 'webp', 'gif', 'tiff', 'pdf'],
            'image/webp'  => ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'tiff', 'pdf'],
            'image/tiff'  => ['jpeg', 'jpg', 'png', 'webp', 'gif', 'bmp', 'pdf'],
            'image/svg+xml' => ['pdf', 'png', 'jpeg', 'jpg'],
            'image/vnd.microsoft.icon' => ['png', 'jpeg', 'jpg', 'gif', 'bmp'],
            'image/vnd.adobe.photoshop' => ['jpeg', 'jpg', 'png', 'webp', 'tiff', 'pdf'],
        ];
    }

    public function convert(string $filePath, string $outputPath, string $from, string $to): void
    {

        if (!in_array($to, self::getSupportedConversions()[$from])) {
            throw new Exception("Unsupported output format: $to");
        }

        $imagick = new \Imagick();
        $imagick->readImage($filePath);

        // Set output format
        $imagick->setImageFormat($to);

        // Save the converted file
        $imagick->writeImage($outputPath);

        // Free resources
        $imagick->clear();
        $imagick->destroy();
    }
}
