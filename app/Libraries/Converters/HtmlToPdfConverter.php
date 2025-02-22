<?php

namespace App\Libraries\Converters;

use Dompdf\Dompdf;
use Exception;

class HtmlToPdfConverter implements ConverterInterface
{
    public static function getSupportedConversions(): array
    {
        return [
            'text/html' => ['pdf'],
            'text/plain' => ['pdf'],
        ];
    }

    public function convert(string $filePath, string $outputPath, string $from, string $to): void
    {
        if (!in_array($to, self::getSupportedConversions()[$from])) {
            throw new Exception("Unsupported output format: $to");
        }

        $html = file_get_contents($filePath);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        file_put_contents($outputPath, $dompdf->output());
    }
}
