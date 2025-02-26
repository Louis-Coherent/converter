<?php

namespace App\Libraries\Converters;

use Spatie\PdfToImage\Pdf;
use Exception;

class PdfToImageConverter implements ConverterInterface
{
    public static function getSupportedConversions(): array
    {
        return ['application/pdf' => ['jpeg', 'jpg', 'png', 'webp']];
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


        $pdf = new Pdf($filePath);
        $pdf->saveImage($outputPath);
    }
}
