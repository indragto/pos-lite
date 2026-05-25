<?php

namespace App\Models;

use App\Core\Model;

class Transaction extends Model
{
    protected string $table = 'transactions';

    /**
     * Create a new transaction with items
     */
    public function createTransaction(array $transactionData, array $items): int
    {
        $db = $this->db;
        $db->beginTransaction();

        try {
            // Generate invoice number
            $transactionData['invoice_no'] = generateInvoiceNo();

            // Insert transaction
            $transactionId = $db->insert($this->table, $transactionData);

            // Insert transaction items
            foreach ($items as $item) {
                $db->insert('transaction_items', [
                    'transaction_id' => $transactionId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);

                // Update product stock
                $db->query(
                    "UPDATE products SET stock = stock - :qty WHERE id = :id",
                    ['qty' => $item['quantity'], 'id' => $item['product_id']]
                );
            }

            $db->commit();
            return $transactionId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Get transaction with items and user info
     */
    public function getTransactionWithDetails(int $id): mixed
    {
        $transaction = $this->queryOne(
            "SELECT t.*, u.full_name as cashier_name
             FROM transactions t
             LEFT JOIN users u ON t.user_id = u.id
             WHERE t.id = :id",
            ['id' => $id]
        );

        if (!$transaction) {
            return null;
        }

        // Get transaction items
        $items = $this->query(
            "SELECT ti.*, p.name as product_name, p.sku
             FROM transaction_items ti
             INNER JOIN products p ON ti.product_id = p.id
             WHERE ti.transaction_id = :transaction_id
             ORDER BY ti.id ASC",
            ['transaction_id' => $id]
        );

        $transaction['items'] = $items;
        return $transaction;
    }

    /**
     * Get transactions with pagination and filters
     */
    public function getTransactionsWithFilters(
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $userId = null,
        string $status = 'completed',
        string $orderBy = 't.id DESC',
        ?int $limit = null,
        int $offset = 0
    ): array {
        $sql = "SELECT t.*, u.full_name as cashier_name
                FROM transactions t
                LEFT JOIN users u ON t.user_id = u.id
                WHERE 1=1";
        $params = [];

        if ($startDate) {
            $sql .= " AND DATE(t.created_at) >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND DATE(t.created_at) <= :end_date";
            $params['end_date'] = $endDate;
        }

        if ($userId !== null) {
            $sql .= " AND t.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        if ($status) {
            $sql .= " AND t.status = :status";
            $params['status'] = $status;
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
     * Get sales summary
     */
    public function getSalesSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_transactions,
                    COALESCE(SUM(subtotal), 0) as total_subtotal,
                    COALESCE(SUM(tax), 0) as total_tax,
                    COALESCE(SUM(discount), 0) as total_discount,
                    COALESCE(SUM(total), 0) as total_sales,
                    COALESCE(AVG(total), 0) as avg_transaction
                FROM transactions
                WHERE status = 'completed'";
        $params = [];

        if ($startDate) {
            $sql .= " AND DATE(created_at) >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND DATE(created_at) <= :end_date";
            $params['end_date'] = $endDate;
        }

        return $this->queryOne($sql, $params) ?? [];
    }

    /**
     * Get sales by payment method
     */
    public function getSalesByPaymentMethod(?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "SELECT 
                    payment_method,
                    COUNT(*) as count,
                    COALESCE(SUM(total), 0) as total
                FROM transactions
                WHERE status = 'completed'";
        $params = [];

        if ($startDate) {
            $sql .= " AND DATE(created_at) >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND DATE(created_at) <= :end_date";
            $params['end_date'] = $endDate;
        }

        $sql .= " GROUP BY payment_method ORDER BY total DESC";

        return $this->query($sql, $params);
    }

    /**
     * Void transaction
     */
    public function voidTransaction(int $id, string $reason): bool
    {
        $db = $this->db;
        $db->beginTransaction();

        try {
            // Update transaction status
            $db->update($this->table, [
                'status' => 'voided',
                'void_reason' => $reason
            ], 'id = :id', ['id' => $id]);

            // Optional: restore stock (configurable)
            // For now, we don't restore stock on void

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * Get daily sales breakdown
     */
    public function getDailySales(string $startDate, string $endDate): array
    {
        return $this->query(
            "SELECT 
                DATE(created_at) as date,
                COUNT(*) as transaction_count,
                COALESCE(SUM(total), 0) as daily_total
             FROM transactions
             WHERE status = 'completed' 
             AND DATE(created_at) BETWEEN :start_date AND :end_date
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            ['start_date' => $startDate, 'end_date' => $endDate]
        );
    }

    /**
     * Get top selling products
     */
    public function getTopProducts(?string $startDate = null, ?string $endDate = null, int $limit = 10): array
    {
        $sql = "SELECT 
                    p.name as product_name,
                    p.sku,
                    SUM(ti.quantity) as total_qty,
                    SUM(ti.subtotal) as total_revenue
                FROM transaction_items ti
                INNER JOIN products p ON ti.product_id = p.id
                INNER JOIN transactions t ON ti.transaction_id = t.id
                WHERE t.status = 'completed'";
        $params = [];

        if ($startDate) {
            $sql .= " AND DATE(t.created_at) >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND DATE(t.created_at) <= :end_date";
            $params['end_date'] = $endDate;
        }

        $sql .= " GROUP BY ti.product_id, p.name, p.sku
                  ORDER BY total_qty DESC
                  LIMIT :limit";
        $params['limit'] = $limit;

        return $this->query($sql, $params);
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(int $limit = 10): array
    {
        return $this->query(
            "SELECT t.*, u.full_name as cashier_name
             FROM transactions t
             LEFT JOIN users u ON t.user_id = u.id
             WHERE t.status = 'completed'
             ORDER BY t.created_at DESC
             LIMIT :limit",
            ['limit' => $limit]
        );
    }
}
