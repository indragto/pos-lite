<div class="page-header">
    <div>
        <h1><?= e($title) ?></h1>
        <p>Create a new journal entry</p>
    </div>
    <a href="<?= url('accounting/journal') ?>" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i>Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= url('accounting/journal/store') ?>" id="journalForm">
            <?= csrf_field() ?>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="entry_date" class="form-control"
                           value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control"
                           placeholder="Brief description of this entry" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Reference Type</label>
                    <select name="reference_type" class="form-select">
                        <option value="">-- None --</option>
                        <option value="transaction">Transaction</option>
                        <option value="payment">Payment</option>
                        <option value="adjustment">Adjustment</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Reference ID</label>
                    <input type="text" name="reference_id" class="form-control" placeholder="Optional">
                </div>
            </div>

            <h6 class="fw-bold mb-3"><i class="fas fa-list-ol text-primary me-2"></i>Journal Lines</h6>

            <div class="table-responsive mb-3">
                <table class="table" id="linesTable">
                    <thead>
                        <tr>
                            <th style="min-width:200px">Account</th>
                            <th style="width:150px" class="text-end">Debit</th>
                            <th style="width:150px" class="text-end">Credit</th>
                            <th>Description</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="linesBody">
                        <tr>
                            <td>
                                <select name="lines[0][coa_id]" class="form-select form-select-sm" required>
                                    <option value="">-- Select Account --</option>
                                    <?php foreach ($coaOptions as $option): ?>
                                        <option value="<?= $option['id'] ?>">
                                            <?= e($option['code']) ?> - <?= e($option['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="lines[0][debit]" class="form-control form-control-sm text-end"
                                       step="0.01" min="0" value="0" oninput="updateBalance()">
                            </td>
                            <td>
                                <input type="number" name="lines[0][credit]" class="form-control form-control-sm text-end"
                                       step="0.01" min="0" value="0" oninput="updateBalance()">
                            </td>
                            <td>
                                <input type="text" name="lines[0][description]" class="form-control form-control-sm"
                                       placeholder="Line description">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline text-danger" onclick="removeRow(this)" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="lines[1][coa_id]" class="form-select form-select-sm" required>
                                    <option value="">-- Select Account --</option>
                                    <?php foreach ($coaOptions as $option): ?>
                                        <option value="<?= $option['id'] ?>">
                                            <?= e($option['code']) ?> - <?= e($option['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="lines[1][debit]" class="form-control form-control-sm text-end"
                                       step="0.01" min="0" value="0" oninput="updateBalance()">
                            </td>
                            <td>
                                <input type="number" name="lines[1][credit]" class="form-control form-control-sm text-end"
                                       step="0.01" min="0" value="0" oninput="updateBalance()">
                            </td>
                            <td>
                                <input type="text" name="lines[1][description]" class="form-control form-control-sm"
                                       placeholder="Line description">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline text-danger" onclick="removeRow(this)" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="5">
                                <button type="button" class="btn btn-sm btn-outline" onclick="addRow()">
                                    <i class="fas fa-plus"></i>Add Line
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Balance Indicator -->
            <div class="row g-3 align-items-center mb-4">
                <div class="col-md-4">
                    <div class="d-flex gap-3">
                        <div>
                            <small class="text-muted d-block">Total Debit</small>
                            <strong id="totalDebit" class="text-success"><?= formatRupiah(0) ?></strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Credit</small>
                            <strong id="totalCredit" class="text-danger"><?= formatRupiah(0) ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="balanceStatus" class="badge badge-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>Unbalanced
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex gap-2 justify-content-end">
                <a href="<?= url('accounting/journal') ?>" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <i class="fas fa-save"></i>Post Journal Entry
                </button>
            </div>
        </form>
    </div>
</div>

<script>
var lineIndex = 2;

function addRow() {
    var tbody = document.getElementById('linesBody');
    var row = document.createElement('tr');
    var coaOptions = <?= json_encode(array_map(function($o) { return ['id' => $o['id'], 'code' => $o['code'], 'name' => $o['name']]; }, $coaOptions)) ?>;

    var selectHtml = '<option value="">-- Select Account --</option>';
    for (var i = 0; i < coaOptions.length; i++) {
        selectHtml += '<option value="' + coaOptions[i].id + '">' + coaOptions[i].code + ' - ' + coaOptions[i].name + '</option>';
    }

    row.innerHTML =
        '<td><select name="lines[' + lineIndex + '][coa_id]" class="form-select form-select-sm" required>' + selectHtml + '</select></td>' +
        '<td><input type="number" name="lines[' + lineIndex + '][debit]" class="form-control form-control-sm text-end" step="0.01" min="0" value="0" oninput="updateBalance()"></td>' +
        '<td><input type="number" name="lines[' + lineIndex + '][credit]" class="form-control form-control-sm text-end" step="0.01" min="0" value="0" oninput="updateBalance()"></td>' +
        '<td><input type="text" name="lines[' + lineIndex + '][description]" class="form-control form-control-sm" placeholder="Line description"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline text-danger" onclick="removeRow(this)"><i class="fas fa-times"></i></button></td>';

    tbody.appendChild(row);
    lineIndex++;
    updateBalance();
}

function removeRow(btn) {
    var row = btn.closest('tr');
    row.parentNode.removeChild(row);
    updateBalance();
}

function updateBalance() {
    var debits = document.querySelectorAll('input[name$="[debit]"]');
    var credits = document.querySelectorAll('input[name$="[credit]"]');
    var totalDebit = 0;
    var totalCredit = 0;

    for (var i = 0; i < debits.length; i++) {
        totalDebit += parseFloat(debits[i].value) || 0;
    }
    for (var i = 0; i < credits.length; i++) {
        totalCredit += parseFloat(credits[i].value) || 0;
    }

    document.getElementById('totalDebit').textContent = 'Rp ' + totalDebit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2});
    document.getElementById('totalCredit').textContent = 'Rp ' + totalCredit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2});

    var status = document.getElementById('balanceStatus');
    var submitBtn = document.getElementById('submitBtn');
    var diff = Math.abs(totalDebit - totalCredit);

    if (diff < 0.01 && totalDebit > 0) {
        status.className = 'badge badge-success';
        status.innerHTML = '<i class="fas fa-check-circle me-1"></i>Balanced';
        submitBtn.disabled = false;
    } else {
        status.className = 'badge badge-warning';
        status.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Unbalanced';
        submitBtn.disabled = true;
    }
}
</script>
