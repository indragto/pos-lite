<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? 'POS System') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon"><i class="fas fa-cash-register"></i></div>
                <span class="brand-text"><?= e(setting('store_name', 'POS System')) ?></span>
                <button class="sidebar-close" onclick="toggleSidebar()"><i class="fas fa-times"></i></button>
            </div>

            <nav class="sidebar-nav">
                <?php
                $currentPage = $_GET['url'] ?? '';
                $user = currentUser();

                function isActive(string $page, string $current): bool {
                    if ($page === '' && $current === '') return true;
                    if ($page === '') return false;
                    return $current === $page || str_starts_with($current, $page . '/') || str_starts_with($current, $page . '?');
                }
                ?>

                <div class="nav-section-label">Main</div>

                <a href="<?= url('dashboard') ?>" class="nav-link <?= isActive('dashboard', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-th-large"></i></span>
                    <span class="nav-text">Dashboard</span>
                </a>

                <?php if (hasPermission('transactions.pos')): ?>
                <a href="<?= url('pos') ?>" class="nav-link <?= isActive('pos', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-shopping-cart"></i></span>
                    <span class="nav-text">Point of Sale</span>
                    <span class="nav-badge">POS</span>
                </a>
                <?php endif; ?>

                <div class="nav-section-label">Catalog</div>

                <?php if (hasPermission('products.view')): ?>
                <a href="<?= url('products') ?>" class="nav-link <?= isActive('products', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-box"></i></span>
                    <span class="nav-text">Products</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('categories.manage')): ?>
                <a href="<?= url('categories') ?>" class="nav-link <?= isActive('categories', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-tags"></i></span>
                    <span class="nav-text">Categories</span>
                </a>
                <?php endif; ?>

                <div class="nav-section-label">Sales</div>

                <?php if (hasPermission('transactions.view')): ?>
                <a href="<?= url('transactions') ?>" class="nav-link <?= isActive('transactions', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-receipt"></i></span>
                    <span class="nav-text">Transactions</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('reports.view')): ?>
                <div class="nav-submenu <?= (isActive('reports/daily', $currentPage) || isActive('reports/monthly', $currentPage) || isActive('reports/products', $currentPage)) ? 'open' : '' ?>">
                    <button class="nav-submenu-toggle" onclick="toggleSubmenu(this)">
                        <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                        <span class="nav-text">Reports</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </button>
                    <div class="nav-submenu-items">
                        <a href="<?= url('reports/daily') ?>" class="nav-sub-item <?= isActive('reports/daily', $currentPage) ? 'active' : '' ?>">
                            <i class="fas fa-circle"></i><span>Daily</span>
                        </a>
                        <a href="<?= url('reports/monthly') ?>" class="nav-sub-item <?= isActive('reports/monthly', $currentPage) ? 'active' : '' ?>">
                            <i class="fas fa-circle"></i><span>Monthly</span>
                        </a>
                        <a href="<?= url('reports/products') ?>" class="nav-sub-item <?= isActive('reports/products', $currentPage) ? 'active' : '' ?>">
                            <i class="fas fa-circle"></i><span>Products</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (hasPermission('accounting.coa.view') || hasPermission('accounting.journal.view') || hasPermission('accounting.reports.view')): ?>
                <div class="nav-section-label">Accounting</div>

                <?php if (hasPermission('accounting.coa.view')): ?>
                <a href="<?= url('accounting/coa') ?>" class="nav-link <?= isActive('accounting/coa', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-sitemap"></i></span>
                    <span class="nav-text">Chart of Accounts</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('accounting.journal.view')): ?>
                <a href="<?= url('accounting/journal') ?>" class="nav-link <?= isActive('accounting/journal', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-book"></i></span>
                    <span class="nav-text">Journal Entries</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('accounting.reports.view')): ?>
                <div class="nav-submenu <?= (isActive('accounting/ledger', $currentPage) || isActive('accounting/trial-balance', $currentPage) || isActive('accounting/income-statement', $currentPage) || isActive('accounting/balance-sheet', $currentPage) || isActive('accounting/cash-flow', $currentPage)) ? 'open' : '' ?>">
                    <button class="nav-submenu-toggle" onclick="toggleSubmenu(this)">
                        <span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <span class="nav-text">Financial Reports</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </button>
                    <div class="nav-submenu-items">
                        <a href="<?= url('accounting/ledger') ?>" class="nav-sub-item <?= isActive('accounting/ledger', $currentPage) ? 'active' : '' ?>">
                            <i class="fas fa-circle"></i><span>General Ledger</span>
                        </a>
                        <a href="<?= url('accounting/trial-balance') ?>" class="nav-sub-item <?= isActive('accounting/trial-balance', $currentPage) ? 'active' : '' ?>">
                            <i class="fas fa-circle"></i><span>Trial Balance</span>
                        </a>
                        <a href="<?= url('accounting/income-statement') ?>" class="nav-sub-item <?= isActive('accounting/income-statement', $currentPage) ? 'active' : '' ?>">
                            <i class="fas fa-circle"></i><span>Income Statement</span>
                        </a>
                        <a href="<?= url('accounting/balance-sheet') ?>" class="nav-sub-item <?= isActive('accounting/balance-sheet', $currentPage) ? 'active' : '' ?>">
                            <i class="fas fa-circle"></i><span>Balance Sheet</span>
                        </a>
                        <a href="<?= url('accounting/cash-flow') ?>" class="nav-sub-item <?= isActive('accounting/cash-flow', $currentPage) ? 'active' : '' ?>">
                            <i class="fas fa-circle"></i><span>Cash Flow</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (hasPermission('accounting.settings.manage')): ?>
                <a href="<?= url('accounting/settings') ?>" class="nav-link <?= isActive('accounting/settings', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-sliders-h"></i></span>
                    <span class="nav-text">Accounting Settings</span>
                </a>
                <?php endif; ?>
                <?php endif; ?>

                <div class="nav-section-label">Admin</div>

                <?php if (hasPermission('users.manage')): ?>
                <a href="<?= url('users') ?>" class="nav-link <?= isActive('users', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                    <span class="nav-text">Users</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('roles.manage')): ?>
                <a href="<?= url('roles') ?>" class="nav-link <?= isActive('roles', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
                    <span class="nav-text">Roles & Permissions</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('settings.manage')): ?>
                <a href="<?= url('settings') ?>" class="nav-link <?= isActive('settings', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-cog"></i></span>
                    <span class="nav-text">Settings</span>
                </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div class="user-card">
                    <div class="user-avatar"><?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?></div>
                    <div class="user-info">
                        <div class="user-name"><?= e($user['full_name'] ?? '') ?></div>
                        <div class="user-role"><?= e($user['role_name'] ?? '') ?></div>
                    </div>
                    <a href="<?= url('logout') ?>" class="user-logout" onclick="return confirm('Logout?')" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <header class="topbar">
                <button class="topbar-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div class="topbar-title">
                    <h5 class="mb-0"><?= e($title ?? '') ?></h5>
                </div>
                <div class="topbar-actions">
                    <a href="<?= url('logout') ?>" class="btn btn-ghost btn-sm text-danger"
                       onclick="return confirm('Are you sure you want to logout?')">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </header>

            <main class="page-content">
                <?php if ($error = getFlash('error')): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i><?= e($error) ?></div>
                <?php endif; ?>

                <?php if ($success = getFlash('success')): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i><?= e($success) ?></div>
                <?php endif; ?>

                <?= $content ?>
            </main>

            <footer class="footer">
                &copy; <?= date('Y') ?> <?= e(setting('store_name', 'POS System')) ?>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }
    function toggleSubmenu(btn) {
        btn.closest('.nav-submenu').classList.toggle('open');
    }
    </script>
</body>
</html>
