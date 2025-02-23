<?php

namespace App\Models;

use Michalsn\Uuid\UuidModel;

class FileModel extends UuidModel
{

    protected $uuidFields = ['file_id'];

    protected $uuidUseBytes    = false;

    protected $table = 'files';

    protected $primaryKey = 'id';

    protected $allowedFields = ['ip', 'file_id', 'file_name', 'og_file_name', 'file_path', 'format_from', 'format_to', 'converted_file_path', 'error_message', 'status'];

    protected $useTimestamps = true;

    public function findUuid(string $uuid)
    {
        return $this->where('file_id', $uuid)->first();
    }
}
