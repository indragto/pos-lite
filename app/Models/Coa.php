<?php

namespace App\Models;

use App\Core\Model;

class Coa extends Model
{
    protected string $table = 'coa';

    /**
     * Get COA tree structure
     */
    public function getTree(?string $type = null): array
    {
        $sql = "SELECT * FROM coa WHERE is_active = 1";
        $params = [];

        if ($type) {
            $sql .= " AND type = :type";
            $params['type'] = $type;
        }

        $sql .= " ORDER BY code ASC";
        $accounts = $this->query($sql, $params);

        return $this->buildTree($accounts);
    }

    /**
     * Build tree from flat array
     */
    private function buildTree(array $accounts, int $parentId = null): array
    {
        $tree = [];

        foreach ($accounts as $account) {
            $accountParentId = $account['parent_id'] === null ? null : (int)$account['parent_id'];
            $currentParentId = $parentId;

            if ($accountParentId === $currentParentId) {
                $children = $this->buildTree($accounts, $account['id']);
                if (!empty($children)) {
                    $account['children'] = $children;
                }
                $tree[] = $account;
            }
        }

        return $tree;
    }

    /**
     * Get flat list by type
     */
    public function getByType(string $type): array
    {
        return $this->query(
            "SELECT * FROM coa WHERE type = :type AND is_active = 1 ORDER BY code ASC",
            ['type' => $type]
        );
    }

    /**
     * Get children of a COA
     */
    public function getChildren(int $parentId): array
    {
        return $this->query(
            "SELECT * FROM coa WHERE parent_id = :parent_id AND is_active = 1 ORDER BY code ASC",
            ['parent_id' => $parentId]
        );
    }

    /**
     * Get account balance from journal entries
     */
    public function getBalance(int $coaId, ?string $startDate = null, ?string $endDate = null): float
    {
        $sql = "SELECT COALESCE(SUM(jl.debit - jl.credit), 0) as balance
                FROM journal_lines jl
                INNER JOIN journal_entries je ON jl.entry_id = je.id
                WHERE jl.coa_id = :coa_id AND je.status = 'posted'";
        $params = ['coa_id' => $coaId];

        if ($startDate) {
            $sql .= " AND je.date >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND je.date <= :end_date";
            $params['end_date'] = $endDate;
        }

        return (float) $this->db->fetchColumn($sql, $params);
    }

    /**
     * Get debit/credit totals for account
     */
    public function getDebitCreditTotals(int $coaId, ?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "SELECT COALESCE(SUM(jl.debit), 0) as total_debit, COALESCE(SUM(jl.credit), 0) as total_credit
                FROM journal_lines jl
                INNER JOIN journal_entries je ON jl.entry_id = je.id
                WHERE jl.coa_id = :coa_id AND je.status = 'posted'";
        $params = ['coa_id' => $coaId];

        if ($startDate) {
            $sql .= " AND je.date >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND je.date <= :end_date";
            $params['end_date'] = $endDate;
        }

        return $this->queryOne($sql, $params);
    }

    /**
     * Check if COA has journal entries
     */
    public function hasJournalEntries(int $coaId): bool
    {
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM journal_lines WHERE coa_id = :coa_id",
            ['coa_id' => $coaId]
        );
        return $count > 0;
    }

    /**
     * Get options for dropdown (only leaf accounts)
     */
    public function getLeafOptions(?string $type = null): array
    {
        $sql = "SELECT c.id, c.code, c.name, c.type
                FROM coa c
                LEFT JOIN coa children ON c.id = children.parent_id
                WHERE children.id IS NULL AND c.is_active = 1";
        $params = [];

        if ($type) {
            $sql .= " AND c.type = :type";
            $params['type'] = $type;
        }

        $sql .= " ORDER BY c.code ASC";
        $accounts = $this->query($sql, $params);

        $options = [];
        foreach ($accounts as $a) {
            $options[$a['id']] = $a['code'] . ' - ' . $a['name'];
        }

        return $options;
    }

    /**
     * Get path (hierarchy) for a COA
     */
    public function getPath(int $coaId): array
    {
        $path = [];
        $current = $this->find($coaId);

        while ($current) {
            array_unshift($path, $current);
            if ($current['parent_id']) {
                $current = $this->find((int)$current['parent_id']);
            } else {
                break;
            }
        }

        return $path;
    }

    /**
     * Get all COA with balances
     */
    public function getAllWithBalances(?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "SELECT c.*,
                    COALESCE(SUM(jl.debit), 0) as total_debit,
                    COALESCE(SUM(jl.credit), 0) as total_credit
                FROM coa c
                LEFT JOIN journal_lines jl ON c.id = jl.coa_id
                LEFT JOIN journal_entries je ON jl.entry_id = je.id AND je.status = 'posted'";
        $params = [];

        if ($startDate) {
            $sql .= " AND je.date >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND je.date <= :end_date";
            $params['end_date'] = $endDate;
        }

        $sql .= " GROUP BY c.id ORDER BY c.code ASC";
        return $this->query($sql, $params);
    }
}
