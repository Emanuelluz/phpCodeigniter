<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Test extends Controller
{
    public function index()
    {
        return view('test_simple');
    }
    
    public function db()
    {
        try {
            $db = \Config\Database::connect();
            $result = $db->query('SELECT 1 as test');
            $row = $result->getRow();
            
            return json_encode([
                'status' => 'success',
                'message' => 'Conexão com banco de dados funcionando',
                'test_result' => $row->test
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Erro de conexão com banco de dados',
                'error' => $e->getMessage()
            ]);
        }
    }
}