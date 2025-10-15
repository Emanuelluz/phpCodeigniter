<?php

namespace Modules\Sso\Models;

use CodeIgniter\Model;

/**
 * SSO Provider Model
 * 
 * Gerencia providers de autenticação SSO
 */
class ProviderModel extends Model
{
    protected $table            = 'sso_providers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'type',
        'title',
        'description',
        'config',
        'is_enabled',
        'is_default',
        'priority',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name'  => 'required|min_length[2]|max_length[100]|is_unique[sso_providers.name,id,{id}]',
        'type'  => 'required|in_list[local,ldap,oauth,saml]',
        'title' => 'required|min_length[2]|max_length[255]',
    ];
    protected $validationMessages   = [
        'name' => [
            'required'   => 'O nome do provider é obrigatório',
            'is_unique'  => 'Já existe um provider com este nome',
        ],
        'type' => [
            'required'   => 'O tipo do provider é obrigatório',
            'in_list'    => 'Tipo de provider inválido',
        ],
        'title' => [
            'required'   => 'O título do provider é obrigatório',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['encodeConfig'];
    protected $beforeUpdate   = ['encodeConfig'];
    protected $afterFind      = ['decodeConfig'];

    /**
     * Codifica configuração para JSON antes de inserir/atualizar
     */
    protected function encodeConfig(array $data)
    {
        if (isset($data['data']['config']) && is_array($data['data']['config'])) {
            $data['data']['config'] = json_encode($data['data']['config']);
        }

        return $data;
    }

    /**
     * Decodifica configuração JSON após buscar
     */
    protected function decodeConfig(array $data)
    {
        if (isset($data['data'])) {
            // Múltiplos resultados
            if (is_array($data['data']) && isset($data['data'][0])) {
                foreach ($data['data'] as &$row) {
                    if (isset($row['config']) && is_string($row['config'])) {
                        $row['config'] = json_decode($row['config'], true) ?? [];
                    }
                }
            }
            // Resultado único
            elseif (isset($data['data']['config']) && is_string($data['data']['config'])) {
                $data['data']['config'] = json_decode($data['data']['config'], true) ?? [];
            }
        }

        return $data;
    }

    /**
     * Busca providers ativos
     */
    public function getActiveProviders(): array
    {
        return $this->where('is_enabled', true)
                    ->orderBy('priority', 'ASC')
                    ->orderBy('title', 'ASC')
                    ->findAll();
    }

    /**
     * Busca provider padrão
     */
    public function getDefaultProvider(): ?array
    {
        return $this->where('is_default', true)
                    ->where('is_enabled', true)
                    ->first();
    }

    /**
     * Busca provider por nome
     */
    public function getByName(string $name): ?array
    {
        return $this->where('name', $name)->first();
    }

    /**
     * Busca provider por tipo
     */
    public function getByType(string $type): array
    {
        return $this->where('type', $type)
                    ->where('is_enabled', true)
                    ->findAll();
    }

    /**
     * Define provider como padrão
     */
    public function setAsDefault(int $id): bool
    {
        // Remove padrão de todos
        $this->set('is_default', false)->update();

        // Define o novo padrão
        return $this->update($id, ['is_default' => true]);
    }

    /**
     * Alterna status do provider
     */
    public function toggleStatus(int $id): bool
    {
        $provider = $this->find($id);
        if (!$provider) {
            return false;
        }

        return $this->update($id, [
            'is_enabled' => !$provider['is_enabled']
        ]);
    }

    /**
     * Valida configuração do provider
     */
    public function validateProviderConfig(string $type, array $config): bool
    {
        switch ($type) {
            case 'ldap':
                return isset($config['host'], $config['base_dn']);
            
            case 'oauth':
                return isset($config['providers']) && is_array($config['providers']);
            
            case 'saml':
                return isset($config['idp_entity_id'], $config['idp_sso_url']);
            
            case 'local':
            default:
                return true;
        }
    }
}
