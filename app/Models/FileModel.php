<?php

namespace App\Models;

use Michalsn\Uuid\UuidModel;

class FileModel extends UuidModel
{
    protected $table = 'files';

    protected $primaryKey = 'id';

    protected $allowedFields = ['file_name', 'file_path', 'status'];

    protected $useTimestamps = true;
}
