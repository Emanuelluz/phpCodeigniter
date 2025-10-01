<?php

namespace Modules\Auth\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    public function login()
    {
        // Se já autenticado, redireciona
        if (auth()->loggedIn()) {
            return redirect()->to('/');
        }

        return view('Modules\\Auth\\Views\\login');
    }

    public function doLogin(): RedirectResponse
    {
        $credentials = [
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ];

        $result = auth()->attempt($credentials);
        if (! $result->isOK()) {
            return redirect()->back()->with('error', $result->reason());
        }

        return redirect()->to('/');
    }

    public function logout(): RedirectResponse
    {
        auth()->logout();
        return redirect()->to('/login');
    }
}
