<?php

namespace App\Models;

use App\Core\Model;

class JournalEntry extends Model
{
    protected string $table = 'journal_entries';

    /**
     * Create journal entry with lines (double-entry validation)
     */
    public function createEntry(array $entryData, array $lines, ?int $createdBy = null): int
    {
        // Validate balance
        $totalDebit = array_sum(array_column($lines, 'debit'));
        $totalCredit = array_sum(array_column($lines, 'credit'));

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new \Exception('Debit dan Credit harus balance. Selisih: ' . formatRupiah(abs($totalDebit - $totalCredit)));
        }

        if (empty($lines)) {
            throw new \Exception('Journal entry harus memiliki minimal 1 baris');
        }

        $db = $this->db;
        $db->beginTransaction();

        try {
            // Generate entry number
            $entryData['entry_no'] = $this->generateEntryNo($entryData['date'] ?? date('Y-m-d'));
            $entryData['status'] = 'posted';
            if ($createdBy) {
                $entryData['created_by'] = $createdBy;
            }

            // Insert entry
            $entryId = $db->insert($this->table, $entryData);

            // Insert lines
            foreach ($lines as $line) {
                $db->insert('journal_lines', [
                    'entry_id' => $entryId,
                    'coa_id' => $line['coa_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? '',
                ]);
            }

            $db->commit();
            return $entryId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Get entry with lines and COA info
     */
    public function getEntry(int $id): ?array
    {
        $entry = $this->find($id);
        if (!$entry) return null;

        $entry['lines'] = $this->query(
            "SELECT jl.*, c.code as coa_code, c.name as coa_name, c.type as coa_type
             FROM journal_lines jl
             INNER JOIN coa c ON jl.coa_id = c.id
             WHERE jl.entry_id = :entry_id
             ORDER BY jl.id ASC",
            ['entry_id' => $id]
        );

        return $entry;
    }

    /**
     * Get entries with filters
     */
    public function getEntries(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $search = null,
        ?int $limit = null,
        int $offset = 0
    ): array {
        $sql = "SELECT je.*, u.full_name as created_by_name,
                    COALESCE(SUM(jl.debit), 0) as total_debit,
                    COALESCE(SUM(jl.credit), 0) as total_credit
                FROM journal_entries je
                LEFT JOIN users u ON je.created_by = u.id
                LEFT JOIN journal_lines jl ON je.id = jl.entry_id
                WHERE 1=1";
        $params = [];

        if ($startDate) {
            $sql .= " AND je.date >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND je.date <= :end_date";
            $params['end_date'] = $endDate;
        }

        if ($search) {
            $sql .= " AND (je.entry_no LIKE :search OR je.description LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $sql .= " GROUP BY je.id ORDER BY je.date DESC, je.id DESC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->query($sql, $params);
    }

    /**
     * Void journal entry (creates reversing entry)
     */
    public function voidEntry(int $id, string $reason): bool
    {
        $entry = $this->getEntry($id);
        if (!$entry) return false;
        if ($entry['status'] === 'voided') return false;

        $db = $this->db;
        $db->beginTransaction();

        try {
            // Mark as voided
            $db->update($this->table, [
                'status' => 'voided',
                'void_reason' => $reason,
            ], 'id = :id', ['id' => $id]);

            // Create reversing entry (swap debit/credit)
            $reversingLines = [];
            foreach ($entry['lines'] as $line) {
                $reversingLines[] = [
                    'coa_id' => $line['coa_id'],
                    'debit' => $line['credit'],
                    'credit' => $line['debit'],
                    'description' => 'Reversal: ' . ($line['description'] ?? ''),
                ];
            }

            $reversalData = [
                'date' => date('Y-m-d'),
                'description' => 'Reversal: ' . $entry['description'],
                'reference_type' => 'reversal',
                'reference_id' => $id,
                'created_by' => currentUser()['id'] ?? null,
            ];

            $this->createEntry($reversalData, $reversingLines, currentUser()['id'] ?? null);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Generate entry number
     */
    public function generateEntryNo(?string $date = null): string
    {
        $prefix = 'JNL/' . date('Ymd', strtotime($date ?? date('Y-m-d'))) . '/';

        $lastEntry = $this->db->fetchColumn(
            "SELECT entry_no FROM journal_entries WHERE entry_no LIKE :prefix ORDER BY id DESC LIMIT 1",
            ['prefix' => $prefix . '%']
        );

        if ($lastEntry) {
            $number = (int) substr($lastEntry, -4);
            $newNumber = $number + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Find entries by reference
     */
    public function getByReference(string $refType, int $refId): array
    {
        return $this->query(
            "SELECT * FROM journal_entries WHERE reference_type = :ref_type AND reference_id = :ref_id AND status = 'posted'",
            ['ref_type' => $refType, 'ref_id' => $refId]
        );
    }

    /**
     * Get entry totals
     */
    public function getEntryTotals(int $id): array
    {
        return $this->queryOne(
            "SELECT COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit
             FROM journal_lines WHERE entry_id = :entry_id",
            ['entry_id' => $id]
        );
    }

    /**
     * Count entries
     */
    public function countEntries(?string $startDate = null, ?string $endDate = null): int
    {
        $sql = "SELECT COUNT(*) FROM journal_entries WHERE 1=1";
        $params = [];

        if ($startDate) {
            $sql .= " AND date >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND date <= :end_date";
            $params['end_date'] = $endDate;
        }

        return (int) $this->db->fetchColumn($sql, $params);
    }
}
