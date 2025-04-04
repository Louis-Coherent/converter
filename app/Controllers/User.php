<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class User extends BaseController
{
    public function logout()
    {
        auth()->logout();

        return redirect('/');
    }
}
