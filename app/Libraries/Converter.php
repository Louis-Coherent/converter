<?php

namespace App\Libraries;

use CodeIgniter\Files\File;
use Exception;

class Converter
{
    public function convert(string $from, string $to, string $filePath)
    {
        if (ENVIRONMENT == 'production') {
            $scanResult = shell_exec(escapeshellarg('clamdscan') . " --fdpass --no-summary " . escapeshellarg(WRITEPATH . 'uploads/' . $filePath));

            if (strpos($scanResult, 'OK') === false) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                return ['status' => 'error', 'error_message' => 'Malicious file detected'];
            }
        }


        $file = new File($filePath);
        $newFilePath = WRITEPATH . 'converted_files/' . pathinfo($file->getFilename(), PATHINFO_FILENAME) . '.' . $to;

        try {
            $converter = ConverterRegistry::getConverter($from, $to);

            $converter->convert(WRITEPATH . 'uploads/' . $filePath, $newFilePath, $from, $to);
        } catch (Exception $e) {
            return ['status' => 'error', 'error_message' => $e->getMessage()];
        }

        return ['status' => 'success', 'file' => pathinfo($file->getFilename(), PATHINFO_FILENAME) . '.' . $to];
    }
}
