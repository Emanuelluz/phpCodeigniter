<?php

namespace Modules\Admin\Controllers;

use CodeIgniter\Controller;

class Admin extends Controller
{
    public function index()
    {
        return view('Modules\Admin\Views\index');
    }
}