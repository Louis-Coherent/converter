<?php

namespace App\Controllers;

use App\Models\BlogModel;
use CodeIgniter\Controller;

class Blog extends Controller
{
    public function index()
    {
        $cache = \Config\Services::cache();
        $model = new BlogModel();

        $cacheKey = 'blog_posts';

        // Try to get cached data
        $data['posts'] = $cache->get($cacheKey);

        if (!$data['posts']) {
            // Fetch from database if cache is empty
            $data['posts'] = $model->orderBy('created_at', 'DESC')->findAll();

            // Store in cache for 10 minutes
            $cache->save($cacheKey, $data['posts'], 600);
        }

        $data['title'] = 'File Conversion Tips, Fixes & Guides';
        $data['metaTitle'] = 'File Conversion Tips, Fixes & Guides';

        return view('blog/index', $data);
    }

    public function view($slug)
    {
        $cache = \Config\Services::cache();
        $model = new BlogModel();

        $cacheKey = 'blog_post_' . $slug;

        // Try to get cached post
        $data['post'] = $cache->get($cacheKey);

        if (!$data['post']) {
            $data['post'] = $model->where('slug', $slug)->first();

            if (!$data['post']) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Post not found');
            }

            // Store in cache for 1 hour
            $cache->save($cacheKey, $data['post'], 3600);
        }

        $data['metaTitle'] = $data['post']['title'];
        $data['title'] = $data['post']['title'];

        return view('blog/view', $data);
    }
}
