<?php

namespace App\Libraries\Converters;

interface ConverterInterface
{
    public static function getSupportedConversions(): array;
    public function convert(string $filePath, string $outputPath, string $from, string $to): void;
}
