<?php

namespace App\Models;

use App\Core\Model;

class Role extends Model
{
    protected string $table = 'roles';

    /**
     * Get all roles
     */
    public function getRoles(): array
    {
        return $this->findAll([], 'id ASC');
    }

    /**
     * Get role with permission count
     */
    public function getRoleWithPermissionCount(): array
    {
        return $this->query(
            "SELECT r.*, COUNT(rp.permission_id) as permission_count
             FROM roles r
             LEFT JOIN role_permissions rp ON r.id = rp.role_id
             GROUP BY r.id
             ORDER BY r.id ASC"
        );
    }

    /**
     * Get permissions for a role
     */
    public function getPermissions(int $roleId): array
    {
        return $this->query(
            "SELECT p.* 
             FROM permissions p
             INNER JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = :role_id
             ORDER BY p.id ASC",
            ['role_id' => $roleId]
        );
    }

    /**
     * Get all permissions
     */
    public function getAllPermissions(): array
    {
        $db = new \App\Core\Database();
        return $db->fetchAll("SELECT * FROM permissions ORDER BY id ASC");
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(int $roleId, array $permissionIds): void
    {
        $db = new \App\Core\Database();
        
        // Delete existing permissions
        $db->delete('role_permissions', 'role_id = :role_id', ['role_id' => $roleId]);

        // Insert new permissions
        foreach ($permissionIds as $permissionId) {
            $db->insert('role_permissions', [
                'role_id' => $roleId,
                'permission_id' => $permissionId
            ]);
        }
    }

    /**
     * Check if role can be deleted (not assigned to users)
     */
    public function canDelete(int $roleId): bool
    {
        $userCount = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM users WHERE role_id = :role_id",
            ['role_id' => $roleId]
        );
        return $userCount == 0;
    }

    /**
     * Check if permission exists for role
     */
    public function hasPermission(int $roleId, string $permissionName): bool
    {
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) 
             FROM role_permissions rp
             INNER JOIN permissions p ON rp.permission_id = p.id
             WHERE rp.role_id = :role_id AND p.name = :permission",
            ['role_id' => $roleId, 'permission' => $permissionName]
        );
        return $count > 0;
    }
}
