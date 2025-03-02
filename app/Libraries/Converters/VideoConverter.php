<?php

namespace App\Libraries\Converters;

use Exception;

class VideoConverter implements ConverterInterface
{
    public static function getSupportedConversions(): array
    {
        return [
            'video/mp4'  => ['avi', 'mkv', 'mov', 'flv', 'webm'],
            'video/avi'  => ['mp4', 'mkv', 'mov', 'flv', 'webm'],
            'video/mkv'  => ['mp4', 'avi', 'mov', 'flv', 'webm'],
            'video/mov'  => ['mp4', 'avi', 'mkv', 'flv', 'webm'],
            'video/flv'  => ['mp4', 'avi', 'mkv', 'mov', 'webm'],
            'video/webm' => ['mp4', 'avi', 'mkv', 'mov', 'flv']
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
