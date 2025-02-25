<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class IPThrottler implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        $throttler = Services::throttler();

        // Restrict an IP address to no more
        // than 1 request per second across the
        // entire site.
        if ($throttler->check('site-wide', 60, MINUTE) === false) {
            if ($request->isAJAX()) {
                return Services::response()->setStatusCode(429)->setJSON(['status' => 'error', 'message' => 'Too many requests.']);
            } else {
                return Services::response()->setStatusCode(429)->setBody('Too many requests');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
