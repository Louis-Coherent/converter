<?php

namespace App\Controllers;

use App\Models\BlogModel;
use CodeIgniter\Controller;

class Blog extends Controller
{
    public function index()
    {
        $model = new BlogModel();
        $data['posts'] = $model->orderBy('created_at', 'DESC')->findAll();
        $data['title'] = 'File Conversion Tips, Fixes & Guides';
        $data['metaTitle'] = 'File Conversion Tips, Fixes & Guides';
        return view('blog/index', $data);
    }

    public function view($slug)
    {
        $model = new BlogModel();
        $data['post'] = $model->where('slug', $slug)->first();

        if (!$data['post']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Post not found');
        }

        $data['metaTitle'] = $data['post']['title'];
        $data['title'] = $data['post']['title'];

        return view('blog/view', $data);
    }
}
