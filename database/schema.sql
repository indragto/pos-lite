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
-- Default password for both users is 'admin123'
-- Hash generated with: password_hash('admin123', PASSWORD_BCRYPT)
-- =====================================================
INSERT OR IGNORE INTO users (id, username, password, full_name, email, role_id, is_active) VALUES
(1, 'admin', '$2y$10$EoxyfyJO26K/MmBxO6GsjepKjQQW6octWlRWBtjkmhV1deHzfPs7O', 'Administrator', 'admin@pos.local', 1, 1),
(2, 'cashier01', '$2y$10$EoxyfyJO26K/MmBxO6GsjepKjQQW6octWlRWBtjkmhV1deHzfPs7O', 'John Cashier', 'cashier@pos.local', 3, 1);

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

-- =====================================================
-- ACCOUNTING MODULE TABLES
-- =====================================================

-- Chart of Accounts
CREATE TABLE IF NOT EXISTS coa (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT UNIQUE NOT NULL,
    name TEXT NOT NULL,
    type TEXT NOT NULL CHECK(type IN ('asset','liability','equity','revenue','expense')),
    parent_id INTEGER REFERENCES coa(id),
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Journal Entries
CREATE TABLE IF NOT EXISTS journal_entries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entry_no TEXT UNIQUE NOT NULL,
    date DATE NOT NULL,
    description TEXT,
    reference_type TEXT,
    reference_id INTEGER,
    created_by INTEGER REFERENCES users(id),
    status TEXT DEFAULT 'posted' CHECK(status IN ('posted','voided')),
    void_reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Journal Lines
CREATE TABLE IF NOT EXISTS journal_lines (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entry_id INTEGER NOT NULL REFERENCES journal_entries(id) ON DELETE CASCADE,
    coa_id INTEGER NOT NULL REFERENCES coa(id),
    debit REAL DEFAULT 0,
    credit REAL DEFAULT 0,
    description TEXT
);

-- Accounting Indexes
CREATE INDEX IF NOT EXISTS idx_coa_code ON coa(code);
CREATE INDEX IF NOT EXISTS idx_coa_type ON coa(type);
CREATE INDEX IF NOT EXISTS idx_coa_parent ON coa(parent_id);
CREATE INDEX IF NOT EXISTS idx_je_date ON journal_entries(date);
CREATE INDEX IF NOT EXISTS idx_je_entry_no ON journal_entries(entry_no);
CREATE INDEX IF NOT EXISTS idx_je_status ON journal_entries(status);
CREATE INDEX IF NOT EXISTS idx_je_reference ON journal_entries(reference_type, reference_id);
CREATE INDEX IF NOT EXISTS idx_jl_entry ON journal_lines(entry_id);
CREATE INDEX IF NOT EXISTS idx_jl_coa ON journal_lines(coa_id);

-- =====================================================
-- SEED DATA: ACCOUNTING PERMISSIONS
-- =====================================================
INSERT OR IGNORE INTO permissions (name, description) VALUES
('accounting.coa.view', 'View chart of accounts'),
('accounting.coa.manage', 'Manage chart of accounts'),
('accounting.journal.view', 'View journal entries'),
('accounting.journal.create', 'Create journal entries'),
('accounting.journal.void', 'Void journal entries'),
('accounting.reports.view', 'View financial reports'),
('accounting.settings.manage', 'Manage accounting settings');

-- Assign accounting permissions to Super Admin (role_id=1)
-- Permission IDs 14-20 (after the 13 existing)
INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES
(1, 14), (1, 15), (1, 16), (1, 17), (1, 18), (1, 19), (1, 20);

-- =====================================================
-- SEED DATA: ACCOUNTING SETTINGS
-- =====================================================
INSERT OR IGNORE INTO settings (key, value) VALUES
('auto_post_journal', '0'),
('default_sales_account', ''),
('default_cogs_account', ''),
('default_tax_account', ''),
('default_cash_account', ''),
('default_inventory_account', ''),
('fiscal_year_start', '1');

-- =====================================================
-- SEED DATA: STANDARD COA (Indonesian Chart of Accounts)
-- =====================================================

-- 1. ASSETS
INSERT OR IGNORE INTO coa (id, code, name, type, parent_id) VALUES
(1, '1', 'ASET', 'asset', NULL),
(2, '11', 'Aset Lancar', 'asset', 1),
(3, '111', 'Kas', 'asset', 2),
(4, '112', 'Kas Kecil', 'asset', 2),
(5, '113', 'Bank BCA', 'asset', 2),
(6, '114', 'Bank Mandiri', 'asset', 2),
(7, '115', 'Piutang Usaha', 'asset', 2),
(8, '116', 'Persediaan Barang Dagang', 'asset', 2),
(9, '117', 'PPN Masukan', 'asset', 2),
(10, '12', 'Aset Tetap', 'asset', 1),
(11, '121', 'Peralatan Toko', 'asset', 10),
(12, '122', 'Akumulasi Penyusutan Peralatan', 'asset', 10);

-- 2. LIABILITIES
INSERT OR IGNORE INTO coa (id, code, name, type, parent_id) VALUES
(20, '2', 'LIABILITAS', 'liability', NULL),
(21, '21', 'Liabilitas Jangka Pendek', 'liability', 20),
(22, '211', 'Utang Usaha', 'liability', 21),
(23, '212', 'Utang Gaji', 'liability', 21),
(24, '213', 'PPN Keluaran', 'liability', 21),
(25, '214', 'Utang Pajak', 'liability', 21);

-- 3. EQUITY
INSERT OR IGNORE INTO coa (id, code, name, type, parent_id) VALUES
(30, '3', 'EKUITAS', 'equity', NULL),
(31, '31', 'Modal Pemilik', 'equity', 30),
(32, '32', 'Laba Ditahan', 'equity', 30),
(33, '33', 'Laba Berjalan', 'equity', 30);

-- 4. REVENUE
INSERT OR IGNORE INTO coa (id, code, name, type, parent_id) VALUES
(40, '4', 'PENDAPATAN', 'revenue', NULL),
(41, '41', 'Pendapatan Penjualan', 'revenue', 40),
(42, '411', 'Penjualan Makanan', 'revenue', 41),
(43, '412', 'Penjualan Minuman', 'revenue', 41),
(44, '413', 'Penjualan Snack', 'revenue', 41),
(45, '414', 'Penjualan Lainnya', 'revenue', 41),
(46, '42', 'Pendapatan Lainnya', 'revenue', 40),
(47, '421', 'Pendapatan Jasa', 'revenue', 46);

-- 5. EXPENSES
INSERT OR IGNORE INTO coa (id, code, name, type, parent_id) VALUES
(50, '5', 'BEBAN', 'expense', NULL),
(51, '51', 'Harga Pokok Penjualan', 'expense', 50),
(52, '511', 'Harga Pokok Makanan', 'expense', 51),
(53, '512', 'Harga Pokok Minuman', 'expense', 51),
(54, '513', 'Harga Pokok Snack', 'expense', 51),
(55, '52', 'Beban Operasional', 'expense', 50),
(56, '521', 'Beban Gaji Karyawan', 'expense', 55),
(57, '522', 'Beban Sewa', 'expense', 55),
(58, '523', 'Beban Listrik & Air', 'expense', 55),
(59, '524', 'Beban Perlengkapan', 'expense', 55),
(60, '525', 'Beban Transportasi', 'expense', 55),
(61, '526', 'Beban Promosi', 'expense', 55),
(62, '527', 'Beban Penyusutan', 'expense', 55),
(63, '53', 'Beban Lainnya', 'expense', 50),
(64, '531', 'Beban Administrasi Bank', 'expense', 63),
(65, '532', 'Beban Pajak', 'expense', 63),
(66, '533', 'Beban Lain-lain', 'expense', 63);
