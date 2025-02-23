<?php

namespace App\Libraries\Converters;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class SpreadsheetConverter implements ConverterInterface
{
    public static function getSupportedConversions(): array
    {
        return [
            'application/vnd.ms-excel' => ['csv', 'xls', 'xlsx', 'ods'],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['csv', 'xls', 'ods'],
            'text/csv' => ['xlsx', 'xls', 'ods'],
            'application/vnd.oasis.opendocument.spreadsheet' => ['xlsx', 'xls', 'csv'],
        ];
    }

    public function convert(string $filePath, string $outputPath, string $from, string $to): void
    {
        // Check if the conversion is supported
        if (!in_array($to, self::getSupportedConversions()[$from])) {
            throw new Exception("Unsupported output format: $to");
        }

        // Load the spreadsheet
        $spreadsheet = IOFactory::load($filePath);

        // Select the appropriate writer based on the desired output format
        switch ($to) {
            case 'xlsx':
                $writer = new Xlsx($spreadsheet);
                break;
            case 'xls':
                $writer = new Xls($spreadsheet);
                break;
            case 'csv':
                $writer = new Csv($spreadsheet);
                break;
            case 'ods':
                $writer = new Ods($spreadsheet);
                break;
            default:
                throw new Exception("Unsupported conversion type: $to");
        }

        // Save the converted file
        $writer->save($outputPath);
    }
}
