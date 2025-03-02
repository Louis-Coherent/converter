<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogModel extends Model
{
    protected $table      = 'blog_posts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'slug', 'content', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
