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

    function testSendEmail()
    {
        $email = \Config\Services::email();

        $email->setTo('louis@coherent-innovations.co.uk');
        $email->setSubject('Files Converted in the Last 5 Minutes');
        $email->setMessage('Here are the files converted in the last 5 minutes:');

        if ($email->send()) {
            echo 'Email sent successfully!';
        } else {
            echo 'Failed to send email.';
            print_r($email->printDebugger(['headers']));
        }
    }
}
