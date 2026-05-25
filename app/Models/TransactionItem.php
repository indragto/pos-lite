<?php

namespace App\Models;

use App\Core\Model;

class TransactionItem extends Model
{
    protected string $table = 'transaction_items';

    /**
     * Get items for a transaction
     */
    public function getItemsByTransaction(int $transactionId): array
    {
        return $this->query(
            "SELECT ti.*, p.name as product_name, p.sku
             FROM transaction_items ti
             INNER JOIN products p ON ti.product_id = p.id
             WHERE ti.transaction_id = :transaction_id
             ORDER BY ti.id ASC",
            ['transaction_id' => $transactionId]
        );
    }

    /**
     * Get items with product details
     */
    public function getItemsWithProduct(int $transactionId): array
    {
        return $this->query(
            "SELECT ti.*, p.name as product_name, p.sku, p.image
             FROM transaction_items ti
             INNER JOIN products p ON ti.product_id = p.id
             WHERE ti.transaction_id = :transaction_id",
            ['transaction_id' => $transactionId]
        );
    }
}
