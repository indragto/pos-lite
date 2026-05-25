<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    /**
     * Find user by username
     */
    public function findByUsername(string $username): mixed
    {
        return $this->queryOne(
            "SELECT u.*, r.name as role_name 
             FROM users u 
             LEFT JOIN roles r ON u.role_id = r.id 
             WHERE u.username = :username",
            ['username' => $username]
        );
    }

    /**
     * Get all users with role info
     */
    public function getUsersWithRole(array $conditions = [], string $orderBy = 'u.id DESC', ?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id";
        $params = [];

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                // Handle prefixed columns
                $fullColumn = str_contains($column, '.') ? $column : "u.{$column}";
                $whereClauses[] = "{$fullColumn} = :{$column}";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $sql .= " ORDER BY {$orderBy}";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->query($sql, $params);
    }

    /**
     * Create user with hashed password
     */
    public function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->create($data);
    }

    /**
     * Update user
     */
    public function updateUser(int $id, array $data): int
    {
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->update($id, $data);
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(int $id): int
    {
        $user = $this->find($id);
        $newStatus = $user['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get user count by role
     */
    public function countByRole(int $roleId): int
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM users WHERE role_id = :role_id",
            ['role_id' => $roleId]
        );
    }

    /**
     * Search users
     */
    public function search(string $keyword, int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id 
                WHERE u.username LIKE :keyword 
                OR u.full_name LIKE :keyword 
                OR u.email LIKE :keyword
                ORDER BY u.id DESC
                LIMIT :limit OFFSET :offset";
        
        return $this->query($sql, [
            'keyword' => "%{$keyword}%",
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
}
