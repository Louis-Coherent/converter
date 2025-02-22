<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Libraries\Converter;

class Dev extends Controller
{

    function test()
    {
        $converter = new Converter();
        $response = $converter->convert('application/pdf', 'png', 'C:\xampp\htdocs\converter\writable\uploads\files\1740173922_448b34ed15cddca2bced.pdf');
    }
}
