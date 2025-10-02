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
        $email = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            return redirect()->back()->with('error', 'Informe e-mail e senha.');
        }

        $result = auth()->attempt(['email' => $email, 'password' => $password]);
        if (! $result->isOK()) {
            return redirect()->back()->with('error', $result->reason() ?? 'Falha na autenticação.');
        }

        // Regenera o ID de sessão após login para segurança
        session()->regenerate(true);

        // Redireciona ao admin por padrão após login
        return redirect()->to('/admin');
    }

    public function logout(): RedirectResponse
    {
        auth()->logout();
        return redirect()->to(url_to('login'));
    }
}
