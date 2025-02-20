<?php

namespace App\Models;

use Michalsn\Uuid\UuidModel;

class FileModel extends UuidModel
{

    protected $uuidFields = ['file_id'];

    protected $table = 'files';

    protected $primaryKey = 'id';

    protected $allowedFields = ['file_id', 'file_name', 'file_path', 'status'];

    protected $useTimestamps = true;
}
