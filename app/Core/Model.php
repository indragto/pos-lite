<?php

namespace App\Core;

class Model
{
    protected Database $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = new Database();

        // Auto-detect table name from class name
        if (empty($this->table)) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . 's';
        }
    }

    /**
     * Find record by ID
     */
    public function find(int $id): mixed
    {
        return $this->db->find($this->table, $id);
    }

    /**
     * Find all records
     */
    public function findAll(array $conditions = [], string $orderBy = 'id DESC', ?int $limit = null, int $offset = 0): array
    {
        return $this->db->findAll($this->table, $conditions, $orderBy, $limit, $offset);
    }

    /**
     * Insert new record
     */
    public function create(array $data): int
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): int
    {
        return $this->db->update($this->table, $data, 'id = :id', ['id' => $id]);
    }

    /**
     * Delete record
     */
    public function delete(int $id): int
    {
        return $this->db->delete($this->table, 'id = :id', ['id' => $id]);
    }

    /**
     * Count records
     */
    public function count(array $conditions = []): int
    {
        return $this->db->count($this->table, $conditions);
    }

    /**
     * Execute custom query
     */
    public function query(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Execute query and fetch single
     */
    public function queryOne(string $sql, array $params = []): mixed
    {
        return $this->db->fetch($sql, $params);
    }

    /**
     * Get table name
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
