-- POS Application Database Schema
-- SQLite3

-- Enable WAL mode for better concurrency
PRAGMA journal_mode=WAL;
PRAGMA foreign_keys=ON;

-- =====================================================
-- ROLES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- PERMISSIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS permissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL,
    description TEXT
);

-- =====================================================
-- ROLE PERMISSIONS (Many-to-Many)
-- =====================================================
CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INTEGER NOT NULL,
    permission_id INTEGER NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- =====================================================
-- USERS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    full_name TEXT NOT NULL,
    email TEXT UNIQUE,
    role_id INTEGER NOT NULL,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- =====================================================
-- CATEGORIES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- PRODUCTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sku TEXT UNIQUE NOT NULL,
    name TEXT NOT NULL,
    category_id INTEGER,
    price REAL NOT NULL,
    cost REAL,
    stock INTEGER DEFAULT 0,
    image TEXT,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- =====================================================
-- TRANSACTIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    invoice_no TEXT UNIQUE NOT NULL,
    user_id INTEGER NOT NULL,
    subtotal REAL NOT NULL,
    tax REAL DEFAULT 0,
    discount REAL DEFAULT 0,
    total REAL NOT NULL,
    payment_method TEXT NOT NULL CHECK(payment_method IN ('cash', 'card', 'qris')),
    amount_paid REAL NOT NULL,
    change_amount REAL DEFAULT 0,
    status TEXT DEFAULT 'completed' CHECK(status IN ('completed', 'voided')),
    void_reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- =====================================================
-- TRANSACTION ITEMS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS transaction_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    price REAL NOT NULL,
    subtotal REAL NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- =====================================================
-- SETTINGS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key TEXT UNIQUE NOT NULL,
    value TEXT
);

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_sku ON products(sku);
CREATE INDEX IF NOT EXISTS idx_products_name ON products(name);
CREATE INDEX IF NOT EXISTS idx_transactions_invoice ON transactions(invoice_no);
CREATE INDEX IF NOT EXISTS idx_transactions_user ON transactions(user_id);
CREATE INDEX IF NOT EXISTS idx_transactions_date ON transactions(created_at);
CREATE INDEX IF NOT EXISTS idx_transactions_status ON transactions(status);
CREATE INDEX IF NOT EXISTS idx_transaction_items_transaction ON transaction_items(transaction_id);
CREATE INDEX IF NOT EXISTS idx_transaction_items_product ON transaction_items(product_id);
CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role_id);

-- =====================================================
-- SEED DATA: ROLES
-- =====================================================
INSERT OR IGNORE INTO roles (id, name, description) VALUES
(1, 'Super Admin', 'Full access to all features'),
(2, 'Admin', 'Manage products, users, and reports'),
(3, 'Cashier', 'Access POS interface and view own transactions'),
(4, 'Viewer', 'Read-only access to reports');

-- =====================================================
-- SEED DATA: PERMISSIONS
-- =====================================================
INSERT OR IGNORE INTO permissions (id, name, description) VALUES
(1, 'dashboard.view', 'View dashboard'),
(2, 'products.view', 'View products'),
(3, 'products.create', 'Create products'),
(4, 'products.edit', 'Edit products'),
(5, 'products.delete', 'Delete products'),
(6, 'categories.manage', 'Manage categories'),
(7, 'transactions.view', 'View transactions'),
(8, 'transactions.pos', 'Access POS interface'),
(9, 'transactions.delete', 'Void transactions'),
(10, 'reports.view', 'View reports'),
(11, 'users.manage', 'Manage users'),
(12, 'roles.manage', 'Manage roles and permissions'),
(13, 'settings.manage', 'Manage application settings');

-- =====================================================
-- SEED DATA: ROLE PERMISSIONS
-- =====================================================
-- Super Admin: All permissions
INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10), (1, 11), (1, 12), (1, 13);

-- Admin: Most permissions except roles.manage
INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6), (2, 7), (2, 8), (2, 10), (2, 11);

-- Cashier: POS and view own transactions
INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES
(3, 1), (3, 2), (3, 7), (3, 8);

-- Viewer: Reports only
INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES
(4, 1), (4, 10);

-- =====================================================
-- SEED DATA: USERS
-- Password: password_hash() with bcrypt for 'admin123' and 'cashier123'
-- admin123 => $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- cashier123 => $2y$10$rM8CqZ3vN5jK7pL2wX4y.eO6tR8uI0aS1dF3gH5jK7lM9nO1pQ2rS
-- =====================================================
INSERT OR IGNORE INTO users (id, username, password, full_name, email, role_id, is_active) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@pos.local', 1, 1),
(2, 'cashier01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Cashier', 'cashier@pos.local', 3, 1);

-- Note: Default password for both users is 'admin123' for easy setup
-- Change passwords after first login!

-- =====================================================
-- SEED DATA: SETTINGS
-- =====================================================
INSERT OR IGNORE INTO settings (key, value) VALUES
('store_name', 'My POS Store'),
('store_address', 'Jl. Contoh No. 123'),
('store_phone', '081234567890'),
('tax_rate', '11'),
('currency', 'Rp'),
('receipt_footer', 'Thank you for shopping with us!'),
('session_timeout', '30');

-- =====================================================
-- SEED DATA: SAMPLE CATEGORIES
-- =====================================================
INSERT OR IGNORE INTO categories (id, name, description) VALUES
(1, 'Makanan', 'Food items'),
(2, 'Minuman', 'Beverage items'),
(3, 'Snack', 'Snack items'),
(4, 'Kebutuhan Pokok', 'Basic necessities');

-- =====================================================
-- SEED DATA: SAMPLE PRODUCTS
-- =====================================================
INSERT OR IGNORE INTO products (sku, name, category_id, price, cost, stock, is_active) VALUES
('PRD001', 'Nasi Goreng', 1, 15000, 8000, 50, 1),
('PRD002', 'Mie Goreng', 1, 13000, 7000, 45, 1),
('PRD003', 'Ayam Goreng', 1, 18000, 10000, 30, 1),
('PRD004', 'Es Teh Manis', 2, 5000, 2000, 100, 1),
('PRD005', 'Es Jeruk', 2, 7000, 3000, 80, 1),
('PRD006', 'Kopi Susu', 2, 10000, 5000, 60, 1),
('PRD007', 'Keripik Kentang', 3, 8000, 4000, 40, 1),
('PRD008', 'Coklat Batang', 3, 12000, 7000, 35, 1),
('PRD009', 'Beras 1kg', 4, 15000, 12000, 100, 1),
('PRD010', 'Minyak Goreng 1L', 4, 18000, 15000, 50, 1);
