<?php

namespace App\Libraries;

use App\Libraries\Converters\PdfToImageConverter;
use App\Libraries\Converters\HtmlToPdfConverter;
use App\Libraries\Converters\ImagickConverter;
use Exception;

class ConverterRegistry
{
    private static array $converters = [
        PdfToImageConverter::class,
        HtmlToPdfConverter::class,
        ImagickConverter::class,
    ];

    public static function getConverter(string $from, string $to)
    {

        foreach (self::$converters as $converterClass) {
            $supportedConversions = $converterClass::getSupportedConversions();

            if (isset($supportedConversions[$from]) && in_array($to, $supportedConversions[$from])) {
                return new $converterClass();
            }
        }

        throw new Exception("Unsupported conversion: $from to $to");
    }
}
