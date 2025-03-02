<?php

namespace App\Libraries\Converters;

use Exception;

class AudioConverter implements ConverterInterface
{
    public static function getSupportedConversions(): array
    {
        return [
            'audio/wav'  => ['mp3', 'ogg', 'flac', 'aac', 'm4a'],
            'audio/x-wav'  => ['mp3', 'ogg', 'flac', 'aac', 'm4a'],
            'audio/mp3'  => ['wav', 'ogg', 'flac', 'aac', 'm4a'],
            'audio/ogg'  => ['mp3', 'wav', 'flac', 'aac', 'm4a'],
            'audio/flac' => ['mp3', 'wav', 'ogg', 'aac', 'm4a'],
            'audio/aac'  => ['mp3', 'wav', 'ogg', 'flac', 'm4a'],
            'audio/m4a'  => ['mp3', 'wav', 'ogg', 'flac', 'aac']
        ];
    }

    public function convert(string $filePath, string $outputPath, string $from, string $to): void
    {
        $supported = self::getSupportedConversions();
        if (!isset($supported[$from]) || !in_array($to, $supported[$from])) {
            throw new Exception("Unsupported conversion: $from to $to");
        }

        // Execute FFmpeg command
        $command = escapeshellcmd("ffmpeg -i " . escapeshellarg($filePath) . " " . escapeshellarg($outputPath));
        shell_exec($command);

        // Check if conversion was successful
        if (!file_exists($outputPath)) {
            throw new Exception("Conversion failed.");
        }
    }
}
