<?php

namespace App\Libraries\Converters;

use Exception;

use function PHPUnit\Framework\throwException;

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
            'application/vnd.ms-excel' => ['csv', 'xls', 'xlsx'],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['csv', 'xls'],
            'text/csv' => ['xlsx', 'xls'],
        ];
    }

    public function convert(string $filePath, string $outputPath, string $from, string $to): void
    {

        if (!in_array($to, self::getSupportedConversions()[$from])) {
            throw new Exception("Unsupported output format: $to");
        }

        $cmd = "gs -o /dev/null -sDEVICE=nullpage -dSAFER " . escapeshellarg($filePath);
        $output = [];
        $returnVar = 0;

        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new Exception("Corrupt file.");
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
