<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class NoIndex implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // No action needed before request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add X-Robots-Tag header to prevent indexing
        $response->setHeader('X-Robots-Tag', 'noindex, nofollow');
    }
}
