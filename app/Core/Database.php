<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private array $config;

    public function __construct()
    {
        $this->config = require CONFIG_PATH . '/database.php';
    }

    /**
     * Get PDO instance (singleton)
     */
    public function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'sqlite:' . $this->config['database'];
            $options = $this->config['options'] ?? [];

            try {
                self::$instance = new PDO($dsn, null, null, $options);
                // Enable WAL mode for better concurrency
                self::$instance->exec('PRAGMA journal_mode=WAL');
                self::$instance->exec('PRAGMA foreign_keys=ON');
            } catch (PDOException $e) {
                if (config('app.debug', false)) {
                    die("Database connection failed: " . $e->getMessage());
                }
                error_log("Database connection failed: " . $e->getMessage());
                die("Database connection failed. Please check configuration.");
            }
        }

        return self::$instance;
    }

    /**
     * Execute a query with optional parameters
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch all rows
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Fetch single row
     */
    public function fetch(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Fetch single value
     */
    public function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * Insert data into table
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);

        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * Update data in table
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);
        $stmt = $this->query($sql, $params);

        return $stmt->rowCount();
    }

    /**
     * Delete data from table
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Find record by ID
     */
    public function find(string $table, int $id): mixed
    {
        $sql = "SELECT * FROM {$table} WHERE id = :id LIMIT 1";
        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Find all records with optional conditions
     */
    public function findAll(
        string $table,
        array $conditions = [],
        string $orderBy = '',
        ?int $limit = null,
        int $offset = 0
    ): array {
        $sql = "SELECT * FROM {$table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                if (is_array($value)) {
                    $placeholders = implode(', ', array_map(fn($i) => ":{$column}_{$i}", array_keys($value)));
                    $whereClauses[] = "{$column} IN ({$placeholders})";
                    foreach ($value as $i => $v) {
                        $params["{$column}_{$i}"] = $v;
                    }
                } else {
                    $whereClauses[] = "{$column} = :{$column}";
                    $params[$column] = $value;
                }
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->fetchAll($sql, $params);
    }

    /**
     * Count records
     */
    public function count(string $table, array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        return (int) $this->fetchColumn($sql, $params);
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollBack(): bool
    {
        return $this->getConnection()->rollBack();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId(): int
    {
        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * Execute raw SQL
     */
    public function exec(string $sql): int
    {
        return $this->getConnection()->exec($sql);
    }

    /**
     * Check if table exists
     */
    public function tableExists(string $table): bool
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name=:name";
        return $this->fetch($sql, ['name' => $table]) !== false;
    }
}
