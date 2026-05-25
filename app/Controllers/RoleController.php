<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Role;

class RoleController extends Controller
{
    private Role $roleModel;

    public function __construct()
    {
        parent::__construct();
        $this->roleModel = new Role();
    }

    public function index(): void
    {
        $roles = $this->roleModel->getRoleWithPermissionCount();

        $this->view('roles/index', [
            'title' => 'Roles',
            'roles' => $roles,
        ]);
    }

    public function edit(string $id): void
    {
        $role = $this->roleModel->find((int)$id);

        if (!$role) {
            $this->setFlash('error', 'Role not found');
            $this->redirect('roles');
        }

        $this->view('roles/edit', [
            'title' => 'Edit Role',
            'role' => $role,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireCsrf();

        $id = (int)$id;
        $name = trim($this->input('name'));
        $description = trim($this->input('description'));

        if (empty($name)) {
            $this->setFlash('error', 'Role name is required');
            $this->redirect("roles/edit/{$id}");
        }

        try {
            $this->roleModel->update($id, [
                'name' => $name,
                'description' => $description,
            ]);

            $this->setFlash('success', 'Role updated successfully');
            $this->redirect('roles');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Role name already exists');
            $this->redirect("roles/edit/{$id}");
        }
    }

    public function permissions(string $id): void
    {
        $role = $this->roleModel->find((int)$id);

        if (!$role) {
            $this->setFlash('error', 'Role not found');
            $this->redirect('roles');
        }

        $allPermissions = $this->roleModel->getAllPermissions();
        $rolePermissions = $this->roleModel->getPermissions((int)$id);
        $rolePermissionIds = array_column($rolePermissions, 'id');

        // Group permissions by module
        $groupedPermissions = [];
        foreach ($allPermissions as $permission) {
            $module = explode('.', $permission['name'])[0];
            $groupedPermissions[$module][] = $permission;
        }

        $this->view('roles/permissions', [
            'title' => 'Role Permissions',
            'role' => $role,
            'groupedPermissions' => $groupedPermissions,
            'rolePermissionIds' => $rolePermissionIds,
        ]);
    }

    public function savePermissions(string $id): void
    {
        $this->requireCsrf();

        $id = (int)$id;
        $permissionIds = $this->input('permissions') ?? [];

        // Ensure all values are integers
        $permissionIds = array_map('intval', $permissionIds);

        $this->roleModel->assignPermissions($id, $permissionIds);

        $this->setFlash('success', 'Permissions updated successfully');
        $this->redirect("roles/permissions/{$id}");
    }
}
