<?php

namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    protected string $table = 'products';

    /**
     * Get products with category info
     */
    public function getProductsWithCategory(
        array $conditions = [],
        string $search = '',
        ?int $categoryId = null,
        string $orderBy = 'p.id DESC',
        ?int $limit = null,
        int $offset = 0
    ): array {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";
        $params = [];

        $whereClauses = [];

        if (!empty($conditions)) {
            foreach ($conditions as $column => $value) {
                $fullColumn = str_contains($column, '.') ? $column : "p.{$column}";
                $whereClauses[] = "{$fullColumn} = :{$column}";
                $params[$column] = $value;
            }
        }

        if ($search !== '') {
            $whereClauses[] = "(p.name LIKE :search OR p.sku LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if ($categoryId !== null) {
            $whereClauses[] = "p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        if (!empty($whereClauses)) {
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
     * Search products for POS
     */
    public function searchProducts(string $keyword, ?int $categoryId = null, int $limit = 50): array
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 AND p.stock > 0";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (p.name LIKE :keyword OR p.sku LIKE :keyword)";
            $params['keyword'] = "%{$keyword}%";
        }

        if ($categoryId !== null) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $sql .= " ORDER BY p.name ASC LIMIT :limit";
        $params['limit'] = $limit;

        return $this->query($sql, $params);
    }

    /**
     * Get product with category
     */
    public function getProductWithCategory(int $id): mixed
    {
        return $this->queryOne(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.id = :id",
            ['id' => $id]
        );
    }

    /**
     * Update stock
     */
    public function updateStock(int $productId, int $quantity, string $operation = 'reduce'): bool
    {
        if ($operation === 'reduce') {
            $sql = "UPDATE products SET stock = stock - :quantity WHERE id = :id AND stock >= :quantity";
        } else {
            $sql = "UPDATE products SET stock = stock + :quantity WHERE id = :id";
        }

        $stmt = $this->db->query($sql, [
            'quantity' => $quantity,
            'id' => $productId
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Get low stock products
     */
    public function getLowStock(int $threshold = 10): array
    {
        return $this->query(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.is_active = 1 AND p.stock <= :threshold
             ORDER BY p.stock ASC",
            ['threshold' => $threshold]
        );
    }

    /**
     * Check if SKU exists
     */
    public function skuExists(string $sku, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM products WHERE sku = :sku";
        $params = ['sku' => $sku];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        return $this->db->fetchColumn($sql, $params) > 0;
    }

    /**
     * Get product count by category
     */
    public function countByCategory(int $categoryId): int
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE category_id = :category_id",
            ['category_id' => $categoryId]
        );
    }

    /**
     * Handle image upload
     */
    public function handleImageUpload(array $file, ?string $oldImage = null): ?string
    {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        $extension = getFileExtension($file['name']);

        if (!in_array($extension, $allowedTypes)) {
            return null;
        }

        if ($file['size'] > $maxSize) {
            return null;
        }

        // Delete old image if exists
        if ($oldImage && file_exists(PUBLIC_PATH . '/uploads/' . $oldImage)) {
            unlink(PUBLIC_PATH . '/uploads/' . $oldImage);
        }

        // Generate unique filename
        $filename = 'product_' . time() . '_' . random_string(8) . '.' . $extension;
        $destination = PUBLIC_PATH . '/uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }

        return null;
    }
}
