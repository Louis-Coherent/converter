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
     */
    public const mimeTypes = [
        'application/pdf' => ['jpg', 'jpeg', 'png', 'webp'],
        'text/html' => ['pdf'],
        'text/plain' => ['pdf'],
        'image/jpeg'  => ['jpeg', 'jpg', 'png', 'webp', 'bmp', 'gif', 'tiff', 'pdf'],
        'image/png'   => ['jpeg', 'jpg', 'png', 'webp', 'bmp', 'gif', 'tiff', 'pdf'],
        'image/gif'   => ['jpeg', 'jpg', 'png', 'webp', 'bmp', 'tiff', 'pdf'],
        'image/bmp'   => ['jpeg', 'jpg', 'png', 'webp', 'gif', 'tiff', 'pdf'],
        'image/webp'  => ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'tiff', 'pdf'],
        'image/tiff'  => ['jpeg', 'jpg', 'png', 'webp', 'gif', 'bmp', 'pdf'],
        'image/svg+xml' => ['pdf', 'png', 'jpeg', 'jpg'],
        'image/vnd.microsoft.icon' => ['png', 'jpeg', 'jpg', 'gif', 'bmp'],
        'image/vnd.adobe.photoshop' => ['jpeg', 'jpg', 'png', 'webp', 'tiff', 'pdf'],
        'application/vnd.ms-excel' => ['csv', 'xls', 'xlsx', 'ods'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['csv', 'xls', 'ods'],
        'text/csv' => ['xlsx', 'xls', 'ods'],
        'application/vnd.oasis.opendocument.spreadsheet' => ['xlsx', 'xls', 'csv'],
        'application/msword' => ['docx', 'pdf', 'rtf', 'odt', 'txt', 'html', 'epub'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['pdf', 'rtf', 'odt', 'txt', 'html', 'epub', 'doc'],
        'application/rtf' => ['docx', 'pdf', 'odt', 'txt', 'html', 'epub'],
        'application/vnd.oasis.opendocument.text' => ['docx', 'pdf', 'rtf', 'txt', 'html', 'epub'],
    ];

    public const extGrouped = [
        'Images' => ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'gif', 'tiff', 'svg', 'ico'],
        'Documents' => ['pdf', 'docx', 'rtf', 'odt', 'txt', 'html', 'epub'],
        'Spreadsheets' => ['csv', 'xls', 'xlsx', 'ods'],
    ];

    public const PENDING = 'pending';
    public const UPLOADED = 'uploaded';
    public const QUEUED = 'queued';
    public const PROCESSING = 'processing';
    public const COMPLETE = 'complete';
    public const FAILED = 'failed';

    public static function all(): array
    {
        return [
            self::PENDING,
            self::IN_PROGRESS,
            self::COMPLETE,
            self::FAILED,
        ];
    }
}
