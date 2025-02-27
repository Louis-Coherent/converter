<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SecurityHeaders implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // No action before request
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains')
                 ->setHeader('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'")
                 ->setHeader('X-Frame-Options', 'SAMEORIGIN')
                 ->setHeader('X-Content-Type-Options', 'nosniff')
                 ->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
                 ->setHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}