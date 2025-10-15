<?php

namespace Modules\Sso\Controllers;

use App\Controllers\BaseController;
use Modules\Sso\Models\AuthLogModel;

/**
 * Log Controller
 * 
 * Visualização e gerenciamento de logs de autenticação
 */
class LogController extends BaseController
{
    protected AuthLogModel $model;

    public function __construct()
    {
        $this->model = new AuthLogModel();
    }

    /**
     * Lista logs
     */
    public function index()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('login');
        }

        $perPage = 50;
        $data['logs'] = $this->model->orderBy('created_at', 'DESC')->paginate($perPage);
        $data['pager'] = $this->model->pager;

        return view('Modules\Sso\Views\logs\index', $data);
    }

    /**
     * Visualizar log específico
     */
    public function view(int $id)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('login');
        }

        $log = $this->model->find($id);
        if (!$log) {
            return redirect()->to('sso/admin/logs')
                ->with('error', 'Log não encontrado');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($log);
        }

        $data['log'] = $log;
        return view('Modules\Sso\Views\logs\view', $data);
    }

    /**
     * Limpar logs antigos
     */
    public function clear()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('login');
        }

        $days = (int) $this->request->getPost('days') ?: 90;
        $deleted = $this->model->cleanOldLogs($days);

        return redirect()->to('sso/admin/logs')
            ->with('success', "{$deleted} log(s) excluído(s) com sucesso!");
    }
}
