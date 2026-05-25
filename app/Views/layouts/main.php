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
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-cash-register"></i>
                <span class="sidebar-title"><?= e(setting('store_name', 'POS')) ?></span>
                <button class="sidebar-toggle d-lg-none" onclick="toggleSidebar()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <?php
                $currentPage = $_GET['url'] ?? '';
                $user = currentUser();
                ?>

                <a href="<?= url('dashboard') ?>" 
                   class="nav-item <?= str_starts_with($currentPage, 'dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <?php if (hasPermission('transactions.pos')): ?>
                <a href="<?= url('pos') ?>" 
                   class="nav-item <?= str_starts_with($currentPage, 'pos') ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Point of Sale</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('products.view')): ?>
                <a href="<?= url('products') ?>" 
                   class="nav-item <?= str_starts_with($currentPage, 'products') ? 'active' : '' ?>">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('categories.manage')): ?>
                <a href="<?= url('categories') ?>" 
                   class="nav-item <?= str_starts_with($currentPage, 'categories') ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('transactions.view')): ?>
                <a href="<?= url('transactions') ?>" 
                   class="nav-item <?= str_starts_with($currentPage, 'transactions') ? 'active' : '' ?>">
                    <i class="fas fa-receipt"></i>
                    <span>Transactions</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('reports.view')): ?>
                <div class="nav-group">
                    <a href="#" class="nav-group-toggle" onclick="toggleNavGroup(this)">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="nav-group-items">
                        <a href="<?= url('reports/daily') ?>" 
                           class="nav-item <?= str_starts_with($currentPage, 'reports/daily') ? 'active' : '' ?>">
                            <i class="fas fa-calendar-day"></i>
                            <span>Daily Report</span>
                        </a>
                        <a href="<?= url('reports/monthly') ?>" 
                           class="nav-item <?= str_starts_with($currentPage, 'reports/monthly') ? 'active' : '' ?>">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Monthly Report</span>
                        </a>
                        <a href="<?= url('reports/products') ?>" 
                           class="nav-item <?= str_starts_with($currentPage, 'reports/products') ? 'active' : '' ?>">
                            <i class="fas fa-box-open"></i>
                            <span>Product Sales</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <div class="nav-divider"></div>

                <?php if (hasPermission('users.manage')): ?>
                <a href="<?= url('users') ?>" 
                   class="nav-item <?= str_starts_with($currentPage, 'users') ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('roles.manage')): ?>
                <a href="<?= url('roles') ?>" 
                   class="nav-item <?= str_starts_with($currentPage, 'roles') ? 'active' : '' ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Roles</span>
                </a>
                <?php endif; ?>

                <?php if (hasPermission('settings.manage')): ?>
                <a href="<?= url('settings') ?>" 
                   class="nav-item <?= str_starts_with($currentPage, 'settings') ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?= e($user['full_name'] ?? '') ?></div>
                        <div class="user-role"><?= e($user['role_name'] ?? '') ?></div>
                    </div>
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
    }

    function toggleNavGroup(element) {
        element.parentElement.classList.toggle('open');
    }
    </script>
</body>
</html>
