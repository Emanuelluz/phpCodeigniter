<?php

namespace Modules\Sso\Controllers;

use App\Controllers\BaseController;
use Modules\Sso\Models\ProviderModel;
use Modules\Sso\Models\AuthLogModel;

/**
 * Admin Controller
 * 
 * Dashboard administrativo do SSO
 */
class AdminController extends BaseController
{
    public function index()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('login');
        }

        $providerModel = new ProviderModel();
        $logModel = new AuthLogModel();

        $data['providers_count'] = $providerModel->countAll();
        $data['active_providers'] = $providerModel->where('is_enabled', true)->countAllResults();
        $data['stats_today'] = $logModel->getStats('today');
        $data['stats_week'] = $logModel->getStats('week');
        $data['recent_logs'] = $logModel->orderBy('created_at', 'DESC')->limit(10)->findAll();

        return view('Modules\Sso\Views\admin\dashboard', $data);
    }
}
