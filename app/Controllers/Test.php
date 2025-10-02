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
    
    public function migrate()
    {
        try {
            $migrate = \Config\Services::migrations();
            
            // Executar migrações
            $migrate->latest();
            
            return json_encode([
                'status' => 'success',
                'message' => 'Migrações executadas com sucesso'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Erro ao executar migrações',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function tables()
    {
        try {
            $db = \Config\Database::connect();
            $tables = $db->listTables();
            
            return json_encode([
                'status' => 'success',
                'message' => 'Tabelas do banco de dados',
                'tables' => $tables
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Erro ao listar tabelas',
                'error' => $e->getMessage()
            ]);
        }
    }
}