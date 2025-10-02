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
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the site.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Day to day administrators of the site.',
        ],
        'developer' => [
            'title'       => 'Developer',
            'description' => 'Site programmers.',
        ],
        'manager' => [
            'title'       => 'Manager',
            'description' => 'Site managers.',
        ],
        'editor' => [
            'title'       => 'Editor',
            'description' => 'Content editors.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'General users of the site. Often customers.',
        ],
        'beta' => [
            'title'       => 'Beta User',
            'description' => 'Has access to beta features.',
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
    public array $permissions = [
        'admin.access'        => 'Can access the sites admin area',
        'admin.settings'      => 'Can change the sites settings',
        'users.manage-admins' => 'Can manage other admins',
        'users.create'        => 'Can create new non-admin users',
        'users.edit'          => 'Can edit existing non-admin users',
        'users.delete'        => 'Can delete existing non-admin users',
        'users.view'          => 'Can view users',
        'groups.create'       => 'Can create new groups',
        'groups.edit'         => 'Can edit existing groups',
        'groups.delete'       => 'Can delete existing groups',
        'groups.view'         => 'Can view groups',
        'permissions.create'  => 'Can create new permissions',
        'permissions.edit'    => 'Can edit existing permissions',
        'permissions.delete'  => 'Can delete existing permissions',
        'permissions.view'    => 'Can view permissions',
        'posts.create'        => 'Can create new posts',
        'posts.edit'          => 'Can edit existing posts',
        'posts.delete'        => 'Can delete existing posts',
        'posts.view'          => 'Can view posts',
        'beta.access'         => 'Can access beta features',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'groups.*',
            'permissions.*',
            'posts.*',
            'beta.*',
        ],
        'admin' => [
            'admin.access',
            'admin.settings',
            'users.create',
            'users.edit',
            'users.delete',
            'users.view',
            'groups.view',
            'permissions.view',
            'posts.*',
        ],
        'developer' => [
            'admin.access',
            'admin.settings',
            'users.create',
            'users.edit',
            'users.view',
            'groups.*',
            'permissions.*',
            'posts.*',
            'beta.access',
        ],
        'manager' => [
            'users.create',
            'users.edit',
            'users.view',
            'groups.view',
            'posts.*',
        ],
        'editor' => [
            'posts.create',
            'posts.edit',
            'posts.view',
        ],
        'user' => [],
        'beta' => [
            'beta.access',
        ],
    ];
}