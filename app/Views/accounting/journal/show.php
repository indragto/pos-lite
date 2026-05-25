<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Journal entry details</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('accounting/journal') ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>Back
        </a>
        <?php if (($entry['status'] ?? '') === 'posted'): ?>
            <button type="button" class="btn btn-danger"
                    onclick="voidEntry(<?= (int)($entry['id'] ?? 0) ?>, '<?= e($entry['entry_no'] ?? '') ?>')">
                <i class="fas fa-ban"></i>Void Entry
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Entry Header -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <small class="text-muted d-block">Entry No</small>
                <strong class="fs-5"><?= e($entry['entry_no'] ?? '') ?></strong>
            </div>
            <div class="col-md-2">
                <small class="text-muted d-block">Date</small>
                <strong><?= formatDate($entry['entry_date'] ?? '', 'd/m/Y') ?></strong>
            </div>
            <div class="col-md-2">
                <small class="text-muted d-block">Status</small>
                <?php
                $statusBadge = match ($entry['status'] ?? 'draft') {
                    'posted' => 'badge-success',
                    'voided' => 'badge-danger',
                    'draft' => 'badge-warning',
                    default => 'badge-info',
                };
                $statusIcon = match ($entry['status'] ?? 'draft') {
                    'posted' => 'fa-check-circle',
                    'voided' => 'fa-times-circle',
                    'draft' => 'fa-pencil-alt',
                    default => 'fa-info-circle',
                };
                ?>
                <span class="badge <?= $statusBadge ?>">
                    <i class="fas <?= $statusIcon ?> me-1"></i><?= e(ucfirst($entry['status'] ?? 'draft')) ?>
                </span>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Reference</small>
                <?php if (!empty($entry['reference_type']) && !empty($entry['reference_id'])): ?>
                    <span class="badge badge-primary"><?= e($entry['reference_type']) ?></span>
                    <span><?= e($entry['reference_id']) ?></span>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </div>
            <div class="col-12">
                <small class="text-muted d-block">Description</small>
                <span><?= e($entry['description'] ?? '') ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Journal Lines -->
<div class="card mb-4">
    <div class="card-header">
        <h6><i class="fas fa-list-ol text-primary me-2"></i>Journal Lines</h6>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($entry['lines'])): ?>
            <div class="table-responsive" style="border: none;">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $lineTotalDebit = 0;
                        $lineTotalCredit = 0;
                        foreach ($entry['lines'] as $line):
                            $lineTotalDebit += (float)($line['debit'] ?? 0);
                            $lineTotalCredit += (float)($line['credit'] ?? 0);
                        ?>
                        <tr>
                            <td>
                                <span class="text-primary fw-bold"><?= e($line['coa_code'] ?? '') ?></span>
                                <?= e($line['coa_name'] ?? '') ?>
                                <?php if (!empty($line['coa_type'])): ?>
                                    <?php
                                    $typeBadge = match ($line['coa_type']) {
                                        'asset' => 'badge-info',
                                        'liability' => 'badge-warning',
                                        'equity' => 'badge-primary',
                                        'revenue' => 'badge-success',
                                        'expense' => 'badge-danger',
                                        default => 'badge-info',
                                    };
                                    ?>
                                    <span class="badge <?= $typeBadge ?> ms-1"><?= e(ucfirst($line['coa_type'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold"><?= formatRupiah($line['debit'] ?? 0) ?></td>
                            <td class="text-end fw-bold"><?= formatRupiah($line['credit'] ?? 0) ?></td>
                            <td><?= e($line['description'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td class="fw-bold">Totals</td>
                            <td class="text-end fw-bold text-success"><?= formatRupiah($lineTotalDebit) ?></td>
                            <td class="text-end fw-bold text-danger"><?= formatRupiah($lineTotalCredit) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h5>No Lines</h5>
                <p>This journal entry has no lines.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Void Confirmation Modal -->
<div class="modal fade" id="voidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= url('accounting/journal/void') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-ban text-danger me-2"></i>Void Journal Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to void entry <strong id="voidEntryNo"></strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Reason for voiding..."></textarea>
                    </div>
                    <input type="hidden" name="entry_id" id="voidEntryId" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i>Void Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function voidEntry(id, entryNo) {
    document.getElementById('voidEntryId').value = id;
    document.getElementById('voidEntryNo').textContent = entryNo;
    var modal = new bootstrap.Modal(document.getElementById('voidModal'));
    modal.show();
}
</script>
