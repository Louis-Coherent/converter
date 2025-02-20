<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * File Conversion Configuration
 */
class FileConversion extends Config
{
    /**
     * An array of file types with allowed conversion paths.
     * Each key represents a "from" type, and the values are an array of allowed "to" types.
     * This allows easy checking of valid conversion paths.
     */
    public array $mimeTypes = [
        'application/pdf' => ['application/msword', 'image/jpeg', 'image/png', 'text/plain'],
        'application/msword' => ['application/pdf', 'text/plain', 'text/html'],
        'text/plain' => ['application/pdf', 'application/msword', 'text/html', 'text/csv'],
        'text/html' => ['application/pdf', 'application/msword', 'text/plain', 'text/csv'],
        'text/csv' => ['text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/pdf'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['text/csv', 'application/pdf', 'text/plain'],
        'image/jpeg' => ['application/pdf', 'image/png'],
        'image/png' => ['application/pdf', 'image/jpeg'],
        // Add more MIME types and conversion paths as needed
    ];
}
