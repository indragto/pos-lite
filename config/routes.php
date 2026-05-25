<?php

/**
 * Route Definitions
 * 
 * Format: 'url_pattern' => ['controller' => 'Name', 'action' => 'method', 'middleware' => [...]]
 * 
 * Route parameters: :id, :slug, etc.
 * Middleware: 'auth' for authentication, 'rbac:permission.key' for RBAC
 */

return [
    // Authentication
    ''                              => ['controller' => 'Auth', 'action' => 'showLogin'],
    'login'                         => ['controller' => 'Auth', 'action' => 'showLogin'],
    'authenticate'                  => ['controller' => 'Auth', 'action' => 'login', 'middleware' => []],
    'logout'                        => ['controller' => 'Auth', 'action' => 'logout'],

    // Dashboard
    'dashboard'                     => ['controller' => 'Dashboard', 'action' => 'index', 'middleware' => ['auth', 'rbac:dashboard.view']],

    // Products
    'products'                      => ['controller' => 'Product', 'action' => 'index', 'middleware' => ['auth', 'rbac:products.view']],
    'products/create'               => ['controller' => 'Product', 'action' => 'create', 'middleware' => ['auth', 'rbac:products.create']],
    'products/store'                => ['controller' => 'Product', 'action' => 'store', 'middleware' => ['auth', 'rbac:products.create']],
    'products/edit/:id'             => ['controller' => 'Product', 'action' => 'edit', 'middleware' => ['auth', 'rbac:products.edit']],
    'products/update/:id'           => ['controller' => 'Product', 'action' => 'update', 'middleware' => ['auth', 'rbac:products.edit']],
    'products/delete/:id'           => ['controller' => 'Product', 'action' => 'delete', 'middleware' => ['auth', 'rbac:products.delete']],
    'api/products/search'           => ['controller' => 'Product', 'action' => 'search', 'middleware' => ['auth']],

    // Categories
    'categories'                    => ['controller' => 'Category', 'action' => 'index', 'middleware' => ['auth', 'rbac:categories.manage']],
    'categories/create'             => ['controller' => 'Category', 'action' => 'create', 'middleware' => ['auth', 'rbac:categories.manage']],
    'categories/store'              => ['controller' => 'Category', 'action' => 'store', 'middleware' => ['auth', 'rbac:categories.manage']],
    'categories/edit/:id'           => ['controller' => 'Category', 'action' => 'edit', 'middleware' => ['auth', 'rbac:categories.manage']],
    'categories/update/:id'         => ['controller' => 'Category', 'action' => 'update', 'middleware' => ['auth', 'rbac:categories.manage']],
    'categories/delete/:id'         => ['controller' => 'Category', 'action' => 'delete', 'middleware' => ['auth', 'rbac:categories.manage']],

    // POS & Transactions
    'pos'                           => ['controller' => 'Transaction', 'action' => 'pos', 'middleware' => ['auth', 'rbac:transactions.pos']],
    'transactions'                  => ['controller' => 'Transaction', 'action' => 'index', 'middleware' => ['auth', 'rbac:transactions.view']],
    'transactions/store'            => ['controller' => 'Transaction', 'action' => 'store', 'middleware' => ['auth', 'rbac:transactions.pos']],
    'transactions/:id'              => ['controller' => 'Transaction', 'action' => 'show', 'middleware' => ['auth', 'rbac:transactions.view']],
    'transactions/void/:id'         => ['controller' => 'Transaction', 'action' => 'void', 'middleware' => ['auth', 'rbac:transactions.delete']],
    'transactions/receipt/:id'      => ['controller' => 'Transaction', 'action' => 'receipt', 'middleware' => ['auth', 'rbac:transactions.view']],
    'api/cart/calculate'            => ['controller' => 'Transaction', 'action' => 'calculateCart', 'middleware' => ['auth']],

    // Reports
    'reports/daily'                 => ['controller' => 'Report', 'action' => 'daily', 'middleware' => ['auth', 'rbac:reports.view']],
    'reports/monthly'               => ['controller' => 'Report', 'action' => 'monthly', 'middleware' => ['auth', 'rbac:reports.view']],
    'reports/products'              => ['controller' => 'Report', 'action' => 'products', 'middleware' => ['auth', 'rbac:reports.view']],
    'reports/export/daily'          => ['controller' => 'Report', 'action' => 'exportDaily', 'middleware' => ['auth', 'rbac:reports.view']],
    'reports/export/monthly'        => ['controller' => 'Report', 'action' => 'exportMonthly', 'middleware' => ['auth', 'rbac:reports.view']],
    'reports/export/products'       => ['controller' => 'Report', 'action' => 'exportProducts', 'middleware' => ['auth', 'rbac:reports.view']],
    'api/reports/sales/summary'     => ['controller' => 'Report', 'action' => 'summary', 'middleware' => ['auth', 'rbac:reports.view']],

    // Users
    'users'                         => ['controller' => 'User', 'action' => 'index', 'middleware' => ['auth', 'rbac:users.manage']],
    'users/create'                  => ['controller' => 'User', 'action' => 'create', 'middleware' => ['auth', 'rbac:users.manage']],
    'users/store'                   => ['controller' => 'User', 'action' => 'store', 'middleware' => ['auth', 'rbac:users.manage']],
    'users/edit/:id'                => ['controller' => 'User', 'action' => 'edit', 'middleware' => ['auth', 'rbac:users.manage']],
    'users/update/:id'              => ['controller' => 'User', 'action' => 'update', 'middleware' => ['auth', 'rbac:users.manage']],
    'users/toggle/:id'              => ['controller' => 'User', 'action' => 'toggleActive', 'middleware' => ['auth', 'rbac:users.manage']],
    'users/delete/:id'              => ['controller' => 'User', 'action' => 'delete', 'middleware' => ['auth', 'rbac:users.manage']],

    // Roles
    'roles'                         => ['controller' => 'Role', 'action' => 'index', 'middleware' => ['auth', 'rbac:roles.manage']],
    'roles/edit/:id'                => ['controller' => 'Role', 'action' => 'edit', 'middleware' => ['auth', 'rbac:roles.manage']],
    'roles/update/:id'              => ['controller' => 'Role', 'action' => 'update', 'middleware' => ['auth', 'rbac:roles.manage']],
    'roles/permissions/:id'         => ['controller' => 'Role', 'action' => 'permissions', 'middleware' => ['auth', 'rbac:roles.manage']],
    'roles/save-permissions/:id'    => ['controller' => 'Role', 'action' => 'savePermissions', 'middleware' => ['auth', 'rbac:roles.manage']],

    // Settings
    'settings'                      => ['controller' => 'Setting', 'action' => 'index', 'middleware' => ['auth', 'rbac:settings.manage']],
    'settings/update'               => ['controller' => 'Setting', 'action' => 'update', 'middleware' => ['auth', 'rbac:settings.manage']],
];
