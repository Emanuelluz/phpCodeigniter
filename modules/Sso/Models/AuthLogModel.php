<?php

namespace Modules\Sso\Models;

use CodeIgniter\Model;

/**
 * SSO Auth Log Model
 * 
 * Gerencia logs de tentativas de autenticação
 */
class AuthLogModel extends Model
{
    protected $table            = 'sso_auth_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'provider_id',
        'provider_type',
        'username',
        'ip_address',
        'user_agent',
        'status',
        'failure_reason',
        'extra_data',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['encodeExtraData', 'setCreatedAt'];
    protected $afterFind      = ['decodeExtraData'];

    /**
     * Define created_at antes de inserir
     */
    protected function setCreatedAt(array $data)
    {
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * Codifica extra_data para JSON
     */
    protected function encodeExtraData(array $data)
    {
        if (isset($data['data']['extra_data']) && is_array($data['data']['extra_data'])) {
            $data['data']['extra_data'] = json_encode($data['data']['extra_data']);
        }

        return $data;
    }

    /**
     * Decodifica extra_data JSON
     */
    protected function decodeExtraData(array $data)
    {
        if (isset($data['data'])) {
            if (is_array($data['data']) && isset($data['data'][0])) {
                foreach ($data['data'] as &$row) {
                    if (isset($row['extra_data']) && is_string($row['extra_data'])) {
                        $row['extra_data'] = json_decode($row['extra_data'], true) ?? [];
                    }
                }
            } elseif (isset($data['data']['extra_data']) && is_string($data['data']['extra_data'])) {
                $data['data']['extra_data'] = json_decode($data['data']['extra_data'], true) ?? [];
            }
        }

        return $data;
    }

    /**
     * Log de tentativa de autenticação
     */
    public function logAttempt(array $data): bool
    {
        $logData = [
            'user_id'         => $data['user_id'] ?? null,
            'provider_id'     => $data['provider_id'] ?? null,
            'provider_type'   => $data['provider_type'] ?? 'local',
            'username'        => $data['username'],
            'ip_address'      => $data['ip_address'] ?? service('request')->getIPAddress(),
            'user_agent'      => $data['user_agent'] ?? service('request')->getUserAgent()->getAgentString(),
            'status'          => $data['status'],
            'failure_reason'  => $data['failure_reason'] ?? null,
            'extra_data'      => $data['extra_data'] ?? [],
        ];

        return $this->insert($logData) !== false;
    }

    /**
     * Busca logs por usuário
     */
    public function getByUser(int $userId, int $limit = 50): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Busca logs por provider
     */
    public function getByProvider(int $providerId, int $limit = 100): array
    {
        return $this->where('provider_id', $providerId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Busca tentativas falhadas recentes
     */
    public function getRecentFailures(string $username, int $minutes = 15): array
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        
        return $this->where('username', $username)
                    ->where('status', 'failed')
                    ->where('created_at >=', $since)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Conta tentativas falhadas recentes
     */
    public function countRecentFailures(string $username, int $minutes = 15): int
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        
        return $this->where('username', $username)
                    ->where('status', 'failed')
                    ->where('created_at >=', $since)
                    ->countAllResults();
    }

    /**
     * Limpa logs antigos
     */
    public function cleanOldLogs(int $days = 90): int
    {
        $before = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('created_at <', $before)->delete();
    }

    /**
     * Estatísticas de autenticação
     */
    public function getStats(string $period = 'today'): array
    {
        $where = $this->getPeriodWhere($period);
        
        return [
            'total'      => $this->where($where)->countAllResults(false),
            'successful' => $this->where($where)->where('status', 'success')->countAllResults(false),
            'failed'     => $this->where($where)->where('status', 'failed')->countAllResults(false),
            'blocked'    => $this->where($where)->where('status', 'blocked')->countAllResults(false),
        ];
    }

    /**
     * Cria condição WHERE baseada no período
     */
    private function getPeriodWhere(string $period): string
    {
        switch ($period) {
            case 'today':
                return "DATE(created_at) = CURDATE()";
            case 'week':
                return "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case 'month':
                return "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            default:
                return "1=1";
        }
    }
}
