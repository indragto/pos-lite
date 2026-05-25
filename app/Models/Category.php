<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected string $table = 'categories';

    /**
     * Get all categories
     */
    public function getCategories(string $orderBy = 'name ASC'): array
    {
        return $this->findAll([], $orderBy);
    }

    /**
     * Get categories with product count
     */
    public function getCategoriesWithProductCount(): array
    {
        return $this->query(
            "SELECT c.*, COUNT(p.id) as product_count
             FROM categories c
             LEFT JOIN products p ON c.id = p.category_id
             GROUP BY c.id
             ORDER BY c.name ASC"
        );
    }

    /**
     * Check if category is in use
     */
    public function isInUse(int $categoryId): bool
    {
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE category_id = :category_id",
            ['category_id' => $categoryId]
        );
        return $count > 0;
    }

    /**
     * Get category options for dropdown
     */
    public function getOptions(): array
    {
        $categories = $this->getCategories();
        $options = [];

        foreach ($categories as $category) {
            $options[$category['id']] = $category['name'];
        }

        return $options;
    }
}
