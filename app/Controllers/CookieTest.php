<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CookieTest extends BaseController
{
    public function index()
    {
        $session = session();
        
        $data = [
            'session_id' => $session->session_id ?? 'não definido',
            'session_data' => $session->get() ?? [],
            'cookies' => $_COOKIE ?? [],
            'headers_sent' => headers_sent(),
            'session_name' => session_name(),
            'session_config' => [
                'cookie_name' => config('Session')->cookieName,
                'cookie_domain' => config('Cookie')->domain,
                'cookie_secure' => config('Cookie')->secure,
                'cookie_httponly' => config('Cookie')->httponly,
                'session_driver' => config('Session')->driver,
                'session_save_path' => config('Session')->savePath,
            ]
        ];
        
        return $this->response->setJSON($data, JSON_PRETTY_PRINT);
    }
}