<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? 'POS System') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Overlay (mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <!-- Brand -->
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <span class="brand-text"><?= e(setting('store_name', 'POS System')) ?></span>
                <button class="sidebar-close d-lg-none" onclick="toggleSidebar()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <?php
                $currentPage = $_GET['url'] ?? '';
                $user = currentUser();

                // Helper to check active state
                function isActive(string $page, string $current): bool {
                    if ($page === '' && $current === '') return true;
                    if ($page === '') return false;
                    return $current === $page || str_starts_with($current, $page . '/') || str_starts_with($current, $page . '?');
                }
                ?>

                <!-- Main Menu -->
                <div class="nav-section-label">Main</div>

                <a href="<?= url('dashboard') ?>"
                   class="nav-link <?= isActive('dashboard', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                    <span class="nav-text">Dashboard</span>
                </a>

                <?php if (hasPermission('transactions.pos')): ?>
                <a href="<?= url('pos') ?>"
                   class="nav-link <?= isActive('pos', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-shopping-cart"></i></span>
                    <span class="nav-text">Point of Sale</span>
                    <span class="nav-badge" style="background:var(--success)">POS</span>
                </a>
                <?php endif; ?>

                <!-- Catalog -->
                <div class="nav-section-label">Catalog</div>

                <?php if (hasPermission('products.view')): ?>
                <a href="<?= url('products') ?>"
                   class="nav-link <?= isActive('products', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-box"></i></span>
                    <span class="nav-text">Products</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('categories.manage')): ?>
                <a href="<?= url('categories') ?>"
                   class="nav-link <?= isActive('categories', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-tags"></i></span>
                    <span class="nav-text">Categories</span>
                </a>
                <?php endif; ?>

                <!-- Sales -->
                <div class="nav-section-label">Sales</div>

                <?php if (hasPermission('transactions.view')): ?>
                <a href="<?= url('transactions') ?>"
                   class="nav-link <?= isActive('transactions', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-receipt"></i></span>
                    <span class="nav-text">Transactions</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('reports.view')): ?>
                <!-- Reports Submenu -->
                <div class="nav-submenu <?= (isActive('reports/daily', $currentPage) || isActive('reports/monthly', $currentPage) || isActive('reports/products', $currentPage)) ? 'open' : '' ?>">
                    <button class="nav-submenu-toggle" onclick="toggleSubmenu(this)">
                        <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                        <span class="nav-text">Reports</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </button>
                    <div class="nav-submenu-items">
                        <a href="<?= url('reports/daily') ?>"
                           class="nav-sub-item <?= isActive('reports/daily', $currentPage) ? 'active' : '' ?>">
                            <i class="fas fa-circle"></i>
                            <span>Daily</span>
                        </a>
                        <a href="<?= url('reports/monthly') ?>"
                           class="nav-sub-item <?= isActive('reports/monthly', $currentPage) ? 'active' : '' ?>">
                            <i class="fas fa-circle"></i>
                            <span>Monthly</span>
                        </a>
                        <a href="<?= url('reports/products') ?>"
                           class="nav-sub-item <?= isActive('reports/products', $currentPage) ? 'active' : '' ?>">
                            <i class="fas fa-circle"></i>
                            <span>Products</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Administration -->
                <div class="nav-section-label">Admin</div>

                <?php if (hasPermission('users.manage')): ?>
                <a href="<?= url('users') ?>"
                   class="nav-link <?= isActive('users', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                    <span class="nav-text">Users</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('roles.manage')): ?>
                <a href="<?= url('roles') ?>"
                   class="nav-link <?= isActive('roles', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-user-shield"></i></span>
                    <span class="nav-text">Roles & Permissions</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('settings.manage')): ?>
                <a href="<?= url('settings') ?>"
                   class="nav-link <?= isActive('settings', $currentPage) ? 'active' : '' ?>">
                    <span class="nav-icon"><i class="fas fa-cog"></i></span>
                    <span class="nav-text">Settings</span>
                </a>
                <?php endif; ?>
            </nav>

            <!-- User Footer -->
            <div class="sidebar-footer">
                <div class="user-card">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= e($user['full_name'] ?? '') ?></div>
                        <div class="user-role"><?= e($user['role_name'] ?? '') ?></div>
                    </div>
                    <a href="<?= url('logout') ?>" class="user-logout"
                       onclick="return confirm('Are you sure you want to logout?')"
                       title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <button class="sidebar-toggle d-lg-none me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="topbar-title">
                    <h5 class="mb-0"><?= e($title ?? '') ?></h5>
                </div>

                <div class="topbar-actions ms-auto">
                    <a href="<?= url('logout') ?>" class="btn btn-outline-danger btn-sm" 
                       onclick="return confirm('Are you sure you want to logout?')">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <main class="page-content">
                <?php if ($error = getFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= e($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if ($success = getFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= e($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?= $content ?>
            </main>

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <span class="text-muted">
                        &copy; <?= date('Y') ?> <?= e(setting('store_name', 'POS System')) ?>
                    </span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
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
