<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

/**
 * Configure available authorization groups
 */
class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     */
    public $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Controle completo sobre o sistema teste',
        ],
        'admin' => [
            'title'       => 'Admin Atualizado',
            'description' => 'Administradores do sistema - versao atualizada',
        ],
        'developer' => [
            'title'       => 'Developer',
            'description' => 'Site programmers.',
        ],
        'manager' => [
            'title'       => 'Manager',
            'description' => 'Site managers.',
        ],
        'user' => [
            'title'       => 'Usuario Final',
            'description' => 'Usuarios finais do sistema - acesso limitado',
        ],
        'beta' => [
            'title'       => 'Beta Tester',
            'description' => 'Usuarios com acesso a funcionalidades beta do sistema',
        ],
        'testadores' => [
            'title'       => 'Testadores',
            'description' => 'Grupo para testadores do sistema',
        ],
        'PEFB' => [
            'title'       => 'Beltrao',
            'description' => 'sd',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public $permissions = [
        'admin.access'        => 'Pode acessar areas administrativas',
        'admin.settings'      => 'Pode alterar configuracoes do sistema',
        'users.manage'        => 'Pode gerenciar usuarios do sistema',
        'users.create'        => 'Pode criar novos usuarios',
        'users.edit'          => 'Pode editar usuarios existentes',
        'users.delete'        => 'Pode excluir usuarios',
        'users.view'          => 'Pode visualizar usuarios',
        'groups.manage'       => 'Pode gerenciar grupos de usuarios',
        'groups.create'       => 'Pode criar novos grupos',
        'groups.edit'         => 'Pode editar grupos existentes',
        'groups.delete'       => 'Pode excluir grupos',
        'groups.view'         => 'Pode visualizar grupos',
        'permissions.manage'  => 'Pode gerenciar permissoes do sistema',
        'permissions.create'  => 'Pode criar novas permissoes',
        'permissions.edit'    => 'Pode editar permissoes existentes',
        'permissions.delete'  => 'Pode excluir permissoes',
        'permissions.view'    => 'Pode visualizar permissoes',
        'beta.access'         => 'Acesso a funcionalidades beta',
        'developer.access'    => 'Acesso a ferramentas de desenvolvimento',
        'api.access'          => 'Pode acessar APIs do sistema',
        'reports.view'        => 'Pode visualizar relatorios',
        'reports.create'      => 'Pode criar relatorios',
        'files.upload'        => 'Pode fazer upload de arquivos',
        'files.download'      => 'Pode fazer download de arquivos',
        'logs.view'           => 'Pode visualizar logs do sistema',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines which permissions each group contains. The group's
     * permission is always included in the user's permissions.
     */
    public $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'groups.*',
            'permissions.*',
            'beta.*',
            'developer.*',
            'api.*',
            'reports.*',
            'files.*',
            'logs.*',
        ],
        'admin' => [
            'admin.access',
            'admin.settings',
            'users.manage',
            'users.create',
            'users.edit',
            'users.view',
            'groups.view',
            'permissions.view',
            'reports.view',
            'reports.create',
            'files.upload',
            'files.download',
        ],
        'developer' => [
            'admin.access',
            'developer.access',
            'api.access',
            'logs.view',
            'users.view',
            'groups.view',
            'permissions.view',
            'files.upload',
            'files.download',
        ],
        'manager' => [
            'admin.access',
            'users.view',
            'groups.view',
            'reports.view',
            'reports.create',
            'files.upload',
            'files.download',
        ],
        'user' => [
            'files.download',
        ],
        'beta' => [
            'beta.access',
            'files.upload',
            'files.download',
        ],
        'testadores' => [
            'admin.access',
            'users.view',
            'groups.view',
            'permissions.view',
            'beta.access',
            'files.upload',
            'files.download',
        ],
        'PEFB' => [
            'files.upload',
            'files.download',
        ],
    ];
}   


