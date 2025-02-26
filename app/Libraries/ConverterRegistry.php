<?php

namespace App\Libraries;

use App\Libraries\Converters\PdfToImageConverter;
use App\Libraries\Converters\HtmlToPdfConverter;
use App\Libraries\Converters\ImagickConverter;
use App\Libraries\Converters\SpreadsheetConverter;
use App\Libraries\Converters\WordConverter;
use App\Libraries\Converters\DataFormatConverter;
use Exception;

class ConverterRegistry
{
    private static array $converters = [
        PdfToImageConverter::class,
        HtmlToPdfConverter::class,
        ImagickConverter::class,
        SpreadsheetConverter::class,
        WordConverter::class,
        DataFormatConverter::class,
    ];

    public static function getConverter(string $from, string $to)
    {
        // Loop through all converters to check if the conversion type is supported
        foreach (self::$converters as $converterClass) {
            $supportedConversions = $converterClass::getSupportedConversions();

            // Check if this converter supports the requested conversion
            if (isset($supportedConversions[$from]) && in_array($to, $supportedConversions[$from])) {
                return new $converterClass();  // Return an instance of the matching converter
            }
        }

        // If no converter supports the conversion, throw an exception
        throw new Exception("Unsupported conversion: $from to $to");
    }

    public static function getSupportedConversions(): array
    {
        $supportedConversions = [];

        // Loop through each converter to merge their supported conversions
        foreach (self::$converters as $converterClass) {
            $converterSupport = $converterClass::getSupportedConversions();

            // Merge each converter's support into a flat structure
            foreach ($converterSupport as $fromMimeType => $toFormats) {
                if (!isset($supportedConversions[$fromMimeType])) {
                    $supportedConversions[$fromMimeType] = [];
                }

                // Merge the supported conversions
                $supportedConversions[$fromMimeType] = array_merge(
                    $supportedConversions[$fromMimeType],
                    $toFormats
                );
            }
        }

        dd($supportedConversions);

        return $supportedConversions;
    }
}