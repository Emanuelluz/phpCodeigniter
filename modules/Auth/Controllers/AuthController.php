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
        log_message('info', 'AuthController::doLogin - Início do método');
        log_message('info', 'AuthController::doLogin - Headers: ' . json_encode($this->request->headers()));
        log_message('info', 'AuthController::doLogin - POST data: ' . json_encode($this->request->getPost()));
        log_message('info', 'AuthController::doLogin - Cookies: ' . json_encode($this->request->getCookie()));
        
        $email = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        log_message('info', 'AuthController::doLogin - Email: ' . $email . ', Password present: ' . (!empty($password) ? 'yes' : 'no'));

        if ($email === '' || $password === '') {
            log_message('warning', 'AuthController::doLogin - Email ou senha vazios');
            return redirect()->back()->with('error', 'Informe e-mail e senha.');
        }

        $result = auth()->attempt(['email' => $email, 'password' => $password]);
        log_message('info', 'AuthController::doLogin - Resultado do auth()->attempt(): ' . ($result->isOK() ? 'OK' : 'FALHA - ' . $result->reason()));
        
        if (! $result->isOK()) {
            return redirect()->back()->with('error', $result->reason() ?? 'Falha na autenticação.');
        }

        // Debug: verificar se usuário está logado
        if (!auth()->loggedIn()) {
            log_message('error', 'AuthController::doLogin - Usuário não está logado após auth()->attempt() bem-sucedido');
            return redirect()->back()->with('error', 'Erro na autenticação. Tente novamente.');
        }

        log_message('info', 'AuthController::doLogin - Login bem-sucedido. Redirecionando para /admin');

        // Redireciona ao admin por padrão após login
        return redirect()->to('/admin')->with('success', 'Login realizado com sucesso!');
    }

    public function logout(): RedirectResponse
    {
        auth()->logout();
        return redirect()->to(url_to('login'));
    }
}
