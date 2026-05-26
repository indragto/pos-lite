<div class="page-header">
    <div>
        <h1><i class="fas fa-balance-scale text-primary me-2"></i>Opening Balance (Saldo Awal)</h1>
        <p>Set initial balances for Asset, Liability, and Equity accounts</p>
    </div>
</div>

<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i><strong>One-Time Setup:</strong> Opening balance can only be set once. Debit must equal Credit. Only Balance Sheet accounts (Asset, Liability, Equity) are allowed.</div>

<div class="card mb-3"><div class="card-body py-2">
    <div class="row text-center small text-muted">
        <div class="col"><strong class="text-info">Asset</strong> → Normal: <strong>Debit</strong></div>
        <div class="col"><strong class="text-warning">Liability</strong> → Normal: <strong>Credit</strong></div>
        <div class="col"><strong class="text-primary">Equity</strong> → Normal: <strong>Credit</strong></div>
    </div>
</div></div>

<div class="card"><div class="card-body">
    <form method="post" action="<?= url('accounting/opening-balance/save') ?>" id="obForm"><?= csrf_field() ?>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><label class="form-label">Date</label><input type="date" name="date" class="form-control" value="<?= date('Y-m-01') ?>" required></div>
        </div>

        <div class="table-responsive mb-3">
            <table class="table align-middle" id="obTable">
                <thead><tr><th style="width:35%">Account</th><th style="width:12%">Type</th><th class="text-center" style="width:12%">Normal</th><th class="text-end" style="width:18%">Debit</th><th class="text-end" style="width:18%">Credit</th><th style="width:50px"></th></tr></thead>
                <tbody id="obBody"></tbody>
                <tfoot><tr style="background:var(--gray-50)"><td colspan="3" class="fw-bold">Totals</td><td class="text-end fw-bold text-success" id="obTotalDebit">Rp 0</td><td class="text-end fw-bold text-danger" id="obTotalCredit">Rp 0</td><td></td></tr></tfoot>
            </table>
        </div>

        <button type="button" class="btn btn-outline btn-sm mb-3" onclick="addObRow()"><i class="fas fa-plus"></i>Add Account</button>

        <div class="row g-3 p-3 mb-3" style="background:var(--gray-50);border-radius:var(--radius)">
            <div class="col-md-4 text-end"><small class="text-muted">Total Debit</small><h4 class="text-success mb-0" id="obDiffDebit">Rp 0</h4></div>
            <div class="col-md-4 text-end"><small class="text-muted">Total Credit</small><h4 class="text-danger mb-0" id="obDiffCredit">Rp 0</h4></div>
            <div class="col-md-4 text-end"><small class="text-muted">Difference</small><h4 id="obDiff" class="text-danger">Rp 0</h4></div>
        </div>

        <div class="d-flex justify-content-end">
            <a href="<?= url('accounting/settings') ?>" class="btn btn-outline me-2">Cancel</a>
            <button type="submit" class="btn btn-primary" id="obSubmit" disabled><i class="fas fa-save"></i>Save Opening Balance</button>
        </div>
    </form>
</div></div>

<script>
// Account data from PHP
var coaAccounts = [
    <?php foreach ($coaOptions as $id => $name): ?>
        {id: <?= $id ?>, name: '<?= e($name) ?>'},
    <?php endforeach; ?>
];
var coaByType = <?= json_encode($coaByType) ?>;

function getNormalBalance(type) {
    return type === 'asset' ? 'debit' : 'credit';
}

function typeLabel(t) {
    var labels = {asset:'Asset',liability:'Liability',equity:'Equity'};
    var colors = {asset:'info',liability:'warning',equity:'primary'};
    return '<span class="badge badge-' + colors[t] + '">' + labels[t] + '</span>';
}

function addObRow(type) {
    var tbody = document.getElementById('obBody');
    var row = document.createElement('tr');
    row.className = 'ob-row';

    // Account select - grouped by type
    var sel = '<select name="coa_id[]" class="form-select" required onchange="onCoaChange(this)">';
    sel += '<option value="">-- Select Account --</option>';
    ['asset','liability','equity'].forEach(function(t) {
        if (coaByType[t] && coaByType[t].length > 0) {
            sel += '<optgroup label="' + t.charAt(0).toUpperCase() + t.slice(1) + '">';
            coaByType[t].forEach(function(a) {
                sel += '<option value="' + a.id + '" data-type="' + a.type + '">' + a.name + '</option>';
            });
            sel += '</optgroup>';
        }
    });
    sel += '</select>';

    row.innerHTML = '<td>' + sel + '</td>' +
        '<td class="normal-type">-</td>' +
        '<td class="text-center normal-label">-</td>' +
        '<td><input type="number" name="debit[]" class="form-control text-end ob-debit" step="0.01" min="0" value="0" onchange="obCalc()" disabled></td>' +
        '<td><input type="number" name="credit[]" class="form-control text-end ob-credit" step="0.01" min="0" value="0" onchange="obCalc()" disabled></td>' +
        '<td><button type="button" class="btn btn-sm btn-ghost text-danger" onclick="this.closest(\'tr\').remove();obCalc()"><i class="fas fa-times"></i></button></td>';

    tbody.appendChild(row);
}

function onCoaChange(sel) {
    var row = sel.closest('tr');
    var opt = sel.options[sel.selectedIndex];
    var type = opt.getAttribute('data-type') || '';
    var normal = getNormalBalance(type);

    // Update type column
    row.querySelector('.normal-type').innerHTML = type ? typeLabel(type) : '-';

    // Update normal balance indicator
    var normalCell = row.querySelector('.normal-label');
    if (type) {
        normalCell.innerHTML = '<span class="badge badge-' + (normal === 'debit' ? 'success' : 'info') + '">' + normal.toUpperCase() + '</span>';
    } else {
        normalCell.innerHTML = '-';
    }

    // Enable the normal side
    var debitInput = row.querySelector('.ob-debit');
    var creditInput = row.querySelector('.ob-credit');
    debitInput.disabled = !type;
    creditInput.disabled = !type;

    // Pre-fill normal side as active, other as 0
    if (normal === 'debit') {
        creditInput.value = 0;
        debitInput.focus();
    } else {
        debitInput.value = 0;
        creditInput.focus();
    }

    obCalc();
}

function obCalc() {
    var td = 0, tc = 0;
    document.querySelectorAll('.ob-row').forEach(function(row) {
        td += parseFloat(row.querySelector('.ob-debit').value) || 0;
        tc += parseFloat(row.querySelector('.ob-credit').value) || 0;
    });

    document.getElementById('obTotalDebit').textContent = 'Rp ' + td.toLocaleString('id-ID');
    document.getElementById('obTotalCredit').textContent = 'Rp ' + td.toLocaleString('id-ID');
    document.getElementById('obDiffDebit').textContent = 'Rp ' + td.toLocaleString('id-ID');
    document.getElementById('obDiffCredit').textContent = 'Rp ' + tc.toLocaleString('id-ID');

    var diff = td - tc;
    var el = document.getElementById('obDiff');
    el.textContent = 'Rp ' + Math.abs(diff).toLocaleString('id-ID');
    el.className = Math.abs(diff) < 0.01 ? 'text-success' : 'text-danger';
    document.getElementById('obSubmit').disabled = Math.abs(diff) >= 0.01;
}

// Add initial rows
addObRow();
addObRow();
addObRow();
</script>
