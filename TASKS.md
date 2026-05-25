# POS Application - Task List

## Phase 1: Foundation (Setup & Core)

### 1.1 Project Setup
- [ ] Create project directory structure
- [ ] Create `composer.json` with PSR-4 autoload
- [ ] Create `.htaccess` for URL rewriting
- [ ] Create `config/app.php` - application configuration
- [ ] Create `config/database.php` - SQLite3 configuration
- [ ] Create `config/routes.php` - route definitions
- [ ] Create `public/index.php` - front controller / entry point
- [ ] Create `public/assets/css/style.css` - custom stylesheet
- [ ] Create `public/assets/js/app.js` - main JavaScript file
- [ ] Create `public/uploads/` directory with proper permissions
- [ ] Create `storage/logs/` directory with proper permissions

### 1.2 Database Setup
- [ ] Create `database/schema.sql` with all table definitions
- [ ] Create `database/pos.db` SQLite3 database file
- [ ] Write SQL for `roles` table
- [ ] Write SQL for `permissions` table
- [ ] Write SQL for `role_permissions` table
- [ ] Write SQL for `users` table
- [ ] Write SQL for `categories` table
- [ ] Write SQL for `products` table
- [ ] Write SQL for `transactions` table
- [ ] Write SQL for `transaction_items` table
- [ ] Write SQL for `settings` table
- [ ] Write seed data for default roles (Super Admin, Admin, Cashier, Viewer)
- [ ] Write seed data for default permissions
- [ ] Write seed data for default role-permission assignments
- [ ] Write seed data for default users (admin, cashier01)
- [ ] Write seed data for default settings
- [ ] Create database initialization script

### 1.3 Core Classes
- [ ] Create `app/Core/Database.php`
  - [ ] SQLite3 PDO connection with error handling
  - [ ] `query($sql, $params)` method
  - [ ] `insert($table, $data)` method
  - [ ] `update($table, $data, $where)` method
  - [ ] `delete($table, $where)` method
  - [ ] `find($table, $id)` method
  - [ ] `findAll($table, $conditions, $orderBy, $limit)` method
  - [ ] `count($table, $conditions)` method
  - [ ] `beginTransaction()`, `commit()`, `rollBack()` methods
  - [ ] Prepared statement support
- [ ] Create `app/Core/Model.php` (base model)
  - [ ] Auto table name detection from class name
  - [ ] Inherit CRUD from Database class
  - [ ] Validation helper methods
- [ ] Create `app/Core/Controller.php` (base controller)
  - [ ] `view($viewPath, $data)` method
  - [ ] `redirect($url)` method
  - [ ] `json($data, $statusCode)` method
  - [ ] `model($modelName)` method to load models
  - [ ] `input($key, $default)` method
  - [ ] `session($key, $default)` method
- [ ] Create `app/Core/View.php`
  - [ ] Render view files with layout
  - [ ] Extract data to variables
  - [ ] Support for partials/includes
- [ ] Create `app/Core/Helpers.php`
  - [ ] `view()` global helper
  - [ ] `redirect()` global helper
  - [ ] `url()` global helper
  - [ ] `asset()` global helper
  - [ ] `e()` - escape HTML helper
  - [ ] `formatRupiah()` helper
  - [ ] `formatDate()` helper
  - [ ] `csrf_token()` helper
  - [ ] `csrf_field()` helper
- [ ] Create `app/Core/App.php` (router)
  - [ ] Parse URL from query string
  - [ ] Match route from `config/routes.php`
  - [ ] Support route parameters (`:id`)
  - [ ] Load and instantiate controller
  - [ ] Execute middleware chain
  - [ ] Handle 404 not found

### 1.4 Authentication
- [ ] Create `app/Core/Auth.php`
  - [ ] `login($username, $password)` - authenticate user
  - [ ] `logout()` - destroy session
  - [ ] `check()` - check if user is logged in
  - [ ] `user()` - get current user data
  - [ ] `id()` - get current user ID
  - [ ] `hasPermission($permission)` - check permission
  - [ ] `hasRole($role)` - check role
  - [ ] Session regeneration on login
- [ ] Create `app/Controllers/AuthController.php`
  - [ ] `login()` - display login form
  - [ ] `authenticate()` - process login
  - [ ] `logout()` - process logout
- [ ] Create `app/Views/layouts/auth.php`
- [ ] Create `app/Views/auth/login.php`
  - [ ] Login form with username & password
  - [ ] CSRF token
  - [ ] Remember me checkbox (optional)
  - [ ] Error message display

### 1.5 Layout & Navigation
- [ ] Create `app/Views/layouts/main.php`
  - [ ] HTML5 doctype with meta viewport for tablet
  - [ ] Include Bootstrap 5 CDN
  - [ ] Include FontAwesome 6 CDN
  - [ ] Sidebar navigation component
  - [ ] Top bar with user info & logout
  - [ ] Main content area with `<?= $content ?>`
  - [ ] Footer
  - [ ] Responsive breakpoints for iPad/tablet
- [ ] Create sidebar navigation with menu items
- [ ] Implement active menu highlighting
- [ ] Implement sidebar collapse/expand
- [ ] Create responsive top bar

---

## Phase 2: RBAC & User Management

### 2.1 RBAC Middleware
- [ ] Create `app/Middleware/AuthMiddleware.php`
  - [ ] Check if user is authenticated
  - [ ] Redirect to login if not authenticated
  - [ ] Store intended URL for redirect after login
- [ ] Create `app/Middleware/RBACMiddleware.php`
  - [ ] Check if user has required permission
  - [ ] Show 403 Forbidden if not authorized
  - [ ] Support multiple permissions (OR/AND logic)
- [ ] Create `app/Core/Middleware.php`
  - [ ] Register middleware classes
  - [ ] Execute middleware chain before controller
  - [ ] Handle middleware response/halt

### 2.2 Role & Permission Models
- [ ] Create `app/Models/Role.php`
  - [ ] CRUD operations for roles
  - [ ] Get permissions for role
  - [ ] Assign permissions to role
- [ ] Create `app/Models/Permission.php`
  - [ ] CRUD operations for permissions
  - [ ] Get roles with permission
- [ ] Create `app/Models/User.php`
  - [ ] Extend base Model
  - [ ] `findByUsername($username)`
  - [ ] `createUser($data)` with password hashing
  - [ ] `updateUser($id, $data)`
  - [ ] `toggleActive($id)`
  - [ ] Get user with role info (JOIN)

### 2.3 Role Management
- [ ] Create `app/Controllers/RoleController.php`
  - [ ] `index()` - list all roles
  - [ ] `create()` - display create form
  - [ ] `store()` - save new role
  - [ ] `edit($id)` - display edit form
  - [ ] `update($id)` - update role
  - [ ] `delete($id)` - delete role
  - [ ] `permissions($id)` - manage role permissions
  - [ ] `savePermissions($id)` - save permissions
- [ ] Create `app/Views/roles/index.php`
  - [ ] Table with role list
  - [ ] Create button (Super Admin only)
  - [ ] Edit & Delete buttons
  - [ ] Permission count column
- [ ] Create `app/Views/roles/edit.php`
  - [ ] Role name & description fields
  - [ ] Permission checkboxes grouped by module
  - [ ] Save & Cancel buttons

### 2.4 User Management
- [ ] Create `app/Controllers/UserController.php`
  - [ ] `index()` - list all users
  - [ ] `create()` - display create form
  - [ ] `store()` - save new user
  - [ ] `edit($id)` - display edit form
  - [ ] `update($id)` - update user
  - [ ] `toggleActive($id)` - activate/deactivate
  - [ ] `delete($id)` - delete user (prevent self-delete)
- [ ] Create `app/Views/users/index.php`
  - [ ] Table with user list
  - [ ] Search & filter
  - [ ] Create button
  - [ ] Edit, Activate/Deactivate, Delete buttons
  - [ ] Role & Status columns
- [ ] Create `app/Views/users/create.php`
  - [ ] Username, full name, email fields
  - [ ] Password & confirm password fields
  - [ ] Role selection dropdown
  - [ ] Form validation
- [ ] Create `app/Views/users/edit.php`
  - [ ] Same fields as create (password optional)
  - [ ] Pre-fill existing data
  - [ ] Validation

---

## Phase 3: Product & Category Management

### 3.1 Category Management
- [ ] Create `app/Models/Category.php`
  - [ ] CRUD operations
  - [ ] Count products in category
  - [ ] Check if category is in use
- [ ] Create `app/Controllers/CategoryController.php`
  - [ ] `index()` - list categories
  - [ ] `create()` - display create form
  - [ ] `store()` - save category
  - [ ] `edit($id)` - display edit form
  - [ ] `update($id)` - update category
  - [ ] `delete($id)` - delete (check if in use)
- [ ] Create `app/Views/categories/index.php`
  - [ ] Table with category list
  - [ ] Product count column
  - [ ] Create, Edit, Delete buttons
- [ ] Create `app/Views/categories/create.php`
  - [ ] Name & description fields
  - [ ] Validation
- [ ] Create `app/Views/categories/edit.php`

### 3.2 Product Management
- [ ] Create `app/Models/Product.php`
  - [ ] CRUD operations
  - [ ] `search($keyword, $categoryId)` - search products
  - [ ] `getWithCategory($id)` - JOIN with categories
  - [ ] `updateStock($id, $quantity)` - adjust stock
  - [ ] `getLowStock($threshold)` - low stock products
  - [ ] SKU auto-generation
  - [ ] Image upload handling
- [ ] Create `app/Controllers/ProductController.php`
  - [ ] `index()` - list products with pagination
  - [ ] `create()` - display create form
  - [ ] `store()` - save product with image upload
  - [ ] `edit($id)` - display edit form
  - [ ] `update($id)` - update product
  - [ ] `delete($id)` - delete product & image
  - [ ] `search()` - AJAX search for POS
  - [ ] Filter by category
- [ ] Create `app/Views/products/index.php`
  - [ ] Table with product list
  - [ ] Search input
  - [ ] Category filter dropdown
  - [ ] Pagination
  - [ ] SKU, Name, Category, Price, Stock columns
  - [ ] Stock status indicator (low/out of stock)
  - [ ] Create, Edit, Delete buttons
- [ ] Create `app/Views/products/create.php`
  - [ ] SKU (auto-generated, editable)
  - [ ] Name, category dropdown
  - [ ] Price & cost fields
  - [ ] Stock quantity
  - [ ] Image upload with preview
  - [ ] Active toggle
  - [ ] Validation (price > 0, required fields)
- [ ] Create `app/Views/products/edit.php`
  - [ ] Same fields as create
  - [ ] Current image display with replace option

### 3.3 Image Upload
- [ ] Implement file upload validation
  - [ ] Allowed types: jpg, jpeg, png, webp
  - [ ] Max file size: 2MB
  - [ ] Generate unique filename
- [ ] Create upload directory structure
- [ ] Image resize/crop (optional)
- [ ] Delete old image on update

---

## Phase 4: POS Module (Core Feature)

### 4.1 POS Interface
- [ ] Create `app/Controllers/TransactionController.php`
  - [ ] `pos()` - display POS interface
  - [ ] `store()` - process transaction
  - [ ] `index()` - transaction history
  - [ ] `show($id)` - transaction detail
  - [ ] `void($id)` - void transaction
  - [ ] `receipt($id)` - receipt view/print
  - [ ] AJAX endpoints for POS
- [ ] Create `app/Views/transactions/pos.php`
  - [ ] Two-column layout (products | cart)
  - [ ] Product search bar with AJAX
  - [ ] Category filter tabs
  - [ ] Product grid (cards with image, name, price, stock)
  - [ ] Touch-friendly cards (min 44x44px tap targets)
  - [ ] Cart/basket panel (sticky right side)
  - [ ] Cart item list with qty +/- buttons
  - [ ] Remove item button per cart item
  - [ ] Subtotal display
  - [ ] Tax calculation display
  - [ ] Discount input (fixed / percentage toggle)
  - [ ] Grand total (large, prominent)
  - [ ] Payment button (large, green)

### 4.2 Cart Management (JavaScript)
- [ ] Create `public/assets/js/pos-cart.js`
  - [ ] Cart class/object to manage items
  - [ ] `addItem(product)` - add product to cart
  - [ ] `removeItem(productId)` - remove from cart
  - [ ] `updateQuantity(productId, qty)` - update qty
  - [ ] `getItems()` - get cart items
  - [ ] `getSubtotal()` - calculate subtotal
  - [ ] `getTax()` - calculate tax
  - [ ] `getDiscount()` - calculate discount
  - [ ] `getTotal()` - calculate total
  - [ ] `clearCart()` - empty cart
  - [ ] LocalStorage persistence (optional, for refresh recovery)
  - [ ] Update UI on cart changes

### 4.3 Product Search
- [ ] Create `public/assets/js/product-search.js`
  - [ ] AJAX search on input (debounced 300ms)
  - [ ] Display search results in grid
  - [ ] Category filter via tabs
  - [ ] Click product to add to cart
  - [ ] Loading indicator
  - [ ] Empty state message

### 4.4 Checkout Flow
- [ ] Create payment modal
  - [ ] Payment method selection (Cash, Card, QRIS)
  - [ ] Amount paid input
  - [ ] Change calculation (auto)
  - [ ] Quick cash buttons (for cash payment): 50k, 100k, 200k, etc.
  - [ ] Confirm payment button
  - [ ] Cancel button
- [ ] Implement checkout validation
  - [ ] Cart not empty
  - [ ] Amount paid >= total
  - [ ] Payment method selected
- [ ] Create `public/assets/js/modal-handler.js`
  - [ ] Open/close payment modal
  - [ ] Calculate change in real-time
  - [ ] Submit transaction via AJAX

### 4.5 Transaction Processing
- [ ] Implement `store()` transaction logic
  - [ ] Begin database transaction
  - [ ] Generate invoice number (INV/YYYYMMDD/XXXX)
  - [ ] Insert into `transactions` table
  - [ ] Insert items into `transaction_items` table
  - [ ] Update product stock (reduce)
  - [ ] Commit database transaction
  - [ ] Return transaction ID for receipt
  - [ ] Rollback on error
- [ ] Create `app/Models/Transaction.php`
  - [ ] `createTransaction($data, $items)` - full transaction
  - [ ] `getTransaction($id)` - with items & user info
  - [ ] `getTransactionsByDate($start, $end)` - date range
  - [ ] `getTransactionsByUser($userId)` - by cashier
  - [ ] `getSummary($period)` - sales summary
  - [ ] `voidTransaction($id)` - void with reason
  - [ ] Generate unique invoice number
- [ ] Create `app/Models/TransactionItem.php`
  - [ ] CRUD for transaction items

### 4.6 Receipt
- [ ] Create `app/Views/transactions/receipt.php`
  - [ ] Print-friendly layout (80mm thermal)
  - [ ] Store name, address, phone
  - [ ] Invoice number, date, cashier name
  - [ ] Item list (name, qty, price, subtotal)
  - [ ] Subtotal, tax, discount, total
  - [ ] Payment method & amount paid
  - [ ] Change amount
  - [ ] Footer text from settings
  - [ ] Auto-print on load (optional)
- [ ] Create `public/assets/js/receipt-print.js`
  - [ ] `window.print()` trigger
  - [ ] Print-specific CSS
  - [ ] Close window after print (optional)

### 4.7 Transaction History
- [ ] Create `app/Views/transactions/index.php`
  - [ ] Table with transaction list
  - [ ] Date range filter
  - [ ] Cashier filter
  - [ ] Invoice, Date, Cashier, Total, Payment Method columns
  - [ ] View Detail, Print Receipt, Void buttons
  - [ ] Pagination
- [ ] Create transaction detail view
  - [ ] Transaction info header
  - [ ] Items table
  - [ ] Payment summary
  - [ ] Void button (if permitted)

### 4.8 Void Transaction
- [ ] Implement void logic
  - [ ] Check permission (transactions.delete)
  - [ ] Log void reason
  - [ ] Optional: restore stock (configurable)
  - [ ] Mark transaction as voided
  - [ ] Exclude from reports

---

## Phase 5: Reports

### 5.1 Daily Sales Report
- [ ] Create `app/Controllers/ReportController.php`
  - [ ] `daily()` - daily sales report
  - [ ] `monthly()` - monthly sales report
  - [ ] `products()` - product sales report
  - [ ] AJAX endpoint for summary
- [ ] Create `app/Views/reports/daily.php`
  - [ ] Date selector
  - [ ] Summary cards: total transactions, gross sales, tax, net sales
  - [ ] Payment method breakdown
  - [ ] Hourly sales chart (optional)
  - [ ] Transaction list for selected date
  - [ ] Export to CSV button

### 5.2 Monthly Sales Report
- [ ] Create `app/Views/reports/monthly.php`
  - [ ] Month/year selector
  - [ ] Summary: total transactions, gross sales, average daily
  - [ ] Daily breakdown table
  - [ ] Comparison with previous month (optional)
  - [ ] Export to CSV

### 5.3 Product Sales Report
- [ ] Create `app/Views/reports/products.php`
  - [ ] Date range filter
  - [ ] Top selling products (by qty & by revenue)
  - [ ] Low selling products
  - [ ] Product sales table: name, qty sold, revenue, stock remaining
  - [ ] Export to CSV

### 5.4 Dashboard Reports
- [ ] Create `app/Controllers/DashboardController.php`
  - [ ] `index()` - dashboard home
  - [ ] Get today's sales summary
  - [ ] Get recent transactions (last 10)
  - [ ] Get low stock products
  - [ ] Get top products today
- [ ] Create `app/Views/dashboard/index.php`
  - [ ] Summary cards: Today's Sales, Transactions, Products Sold
  - [ ] Low stock alert widget
  - [ ] Recent transactions table
  - [ ] Top products list
  - [ ] Quick links

### 5.5 Export Functionality
- [ ] Create CSV export helper
- [ ] Implement export for each report type
- [ ] Proper headers for download (Content-Type, Content-Disposition)
- [ ] Sanitize data for CSV output

---

## Phase 6: Settings & Polish

### 6.1 Settings Module
- [ ] Create `app/Models/Setting.php`
  - [ ] `get($key)` - get setting value
  - [ ] `set($key, $value)` - update/insert setting
  - [ ] `getAll()` - get all settings
  - [ ] Cache settings (avoid repeated DB queries)
- [ ] Create `app/Controllers/SettingController.php`
  - [ ] `index()` - display settings form
  - [ ] `update()` - save settings
- [ ] Create `app/Views/settings/index.php`
  - [ ] Store info: name, address, phone
  - [ ] Tax rate (%)
  - [ ] Currency symbol
  - [ ] Receipt footer text
  - [ ] Session timeout (minutes)
  - [ ] Save settings button

### 6.2 UI/UX Refinements
- [ ] Add loading spinners for AJAX requests
- [ ] Toast notifications for success/error messages
- [ ] Confirm dialogs for delete/void actions
- [ ] Keyboard shortcuts for POS (F2=search, F4=pay, Esc=cancel)
- [ ] Responsive design testing on iPad/tablet sizes
- [ ] Touch-friendly adjustments (larger buttons, spacing)
- [ ] Dark mode toggle (optional)
- [ ] Custom scrollbar styling
- [ ] Smooth transitions and animations
- [ ] Consistent button sizes and colors
- [ ] Error pages (404, 403, 500)

### 6.3 Error Handling & Logging
- [ ] Create global error handler
- [ ] Custom exception handler
- [ ] Log errors to `storage/logs/error.log`
- [ ] User-friendly error pages
- [ ] Debug mode toggle in config
- [ ] Log important actions (login, void, delete)

### 6.4 Input Validation
- [ ] Create `app/Core/Validator.php`
  - [ ] Required field validation
  - [ ] Email format validation
  - [ ] Number/range validation
  - [ ] String length validation
  - [ ] Unique value validation
  - [ ] File upload validation
  - [ ] Custom validation rules
- [ ] Implement validation in all forms
- [ ] Display validation errors inline
- [ ] Client-side validation (JavaScript)

### 6.5 Security Hardening
- [ ] CSRF token implementation on all forms
- [ ] XSS prevention (escape output in views)
- [ ] SQL injection prevention (prepared statements)
- [ ] File upload security (type, size, rename)
- [ ] Session security (httponly, secure, samesite)
- [ ] Password policy (min length, complexity)
- [ ] Rate limiting for login attempts
- [ ] HTTPS enforcement (in production)
- [ ] Remove debug info in production
- [ ] Secure file permissions

### 6.6 Performance Optimization
- [ ] Enable SQLite3 WAL mode
- [ ] Add indexes to frequently queried columns
- [ ] Implement query result caching
- [ ] Optimize product images
- [ ] Minimize CSS/JS (optional)
- [ ] Lazy load product images
- [ ] Debounce search inputs
- [ ] Pagination for large datasets

### 6.7 Testing
- [ ] Test all CRUD operations
- [ ] Test RBAC permissions (access control)
- [ ] Test POS flow end-to-end
- [ ] Test transaction processing
- [ ] Test receipt printing
- [ ] Test reports with various date ranges
- [ ] Test on iPad/tablet browsers (Safari, Chrome)
- [ ] Test responsive design at breakpoints
- [ ] Test error scenarios (invalid input, network issues)
- [ ] Test concurrent transactions (if possible)

### 6.8 Documentation
- [ ] Write installation guide
- [ ] Write user manual (per role)
- [ ] Write admin guide (settings, user management)
- [ ] Document database schema
- [ ] Document API endpoints
- [ ] Create README.md
- [ ] Add code comments for complex logic

### 6.9 Final Checks
- [ ] Review all permissions are enforced
- [ ] Review all forms have CSRF protection
- [ ] Review all user input is sanitized
- [ ] Check for hardcoded credentials/secrets
- [ ] Test database backup & restore
- [ ] Verify all links and navigation work
- [ ] Test on target devices (iPad/tablet)
- [ ] Performance check (page load times)
- [ ] Browser compatibility check

---

## Task Priority Legend

| Priority | Description                           |
|----------|---------------------------------------|
| P0       | Critical - must be done first         |
| P1       | High priority                         |
| P2       | Medium priority                       |
| P3       | Nice to have / optional               |

---

## Phase 7: Accounting Module

### 7.1 Database Setup
- [ ] Create `coa` table schema in schema.sql
- [ ] Create `journal_entries` table schema
- [ ] Create `journal_lines` table schema
- [ ] Add accounting settings to seed data
- [ ] Create indexes on coa.code, journal_entries.date, journal_lines.entry_id
- [ ] Seed standard COA data (assets, liabilities, equity, revenue, expense)
- [ ] Add 7 new permissions to seed data
- [ ] Assign accounting permissions to Super Admin role

### 7.2 COA Model & Controller
- [ ] Create `app/Models/Coa.php`
  - [ ] CRUD operations
  - [ ] `getTree()` - hierarchical COA structure
  - [ ] `getChildren($parentId)` - child accounts
  - [ ] `getBalance($coaId, $startDate, $endDate)` - account balance from journal
  - [ ] `getPath($coaId)` - account hierarchy path
  - [ ] `hasJournalEntries($coaId)` - check if account is used
  - [ ] `getByType($type)` - filter by account type
- [ ] Create `app/Controllers/CoaController.php`
  - [ ] `index()` - COA list grouped by type
  - [ ] `create()` - display create form
  - [ ] `store()` - save new COA
  - [ ] `edit($id)` - display edit form
  - [ ] `update($id)` - update COA
  - [ ] `delete($id)` - delete (check if used in journals)
  - [ ] `tree()` - AJAX tree view data
- [ ] Create `app/Views/accounting/coa/index.php`
  - [ ] Tree/nested list view
  - [ ] Flat list view with toggle
  - [ ] Type filter tabs
  - [ ] Create button
  - [ ] Edit/Delete per account
  - [ ] Balance column from journals
- [ ] Create `app/Views/accounting/coa/create.php`
  - [ ] Code, Name, Type (select), Parent (select), Active toggle
  - [ ] Validation (code unique, type required)
- [ ] Create `app/Views/accounting/coa/edit.php`

### 7.3 Journal Model & Controller
- [ ] Create `app/Models/JournalEntry.php`
  - [ ] `createEntry($data, $lines)` - with double-entry validation
  - [ ] `getEntry($id)` - with lines and COA info
  - [ ] `getEntries($startDate, $endDate, $search)` - filtered list
  - [ ] `voidEntry($id, $reason)` - void with reversal
  - [ ] `validateBalance($lines)` - ensure debit = credit
  - [ ] `generateEntryNo()` - auto number (JNL/YYYYMMDD/XXXX)
  - [ ] `getByReference($type, $id)` - find by reference
- [ ] Create `app/Models/JournalLine.php`
  - [ ] CRUD for journal lines
  - [ ] `getByEntry($entryId)` with COA details
- [ ] Create `app/Controllers/JournalController.php`
  - [ ] `index()` - journal list with filters
  - [ ] `create()` - journal form
  - [ ] `store()` - save journal with validation
  - [ ] `show($id)` - journal detail
  - [ ] `void($id)` - void journal
  - [ ] `createFromTransaction($transactionId)` - auto-post from POS
- [ ] Create `app/Views/accounting/journal/index.php`
  - [ ] Table: Entry No, Date, Description, Reference, Debit, Credit, Actions
  - [ ] Date range filter
  - [ ] Search by entry no or description
  - [ ] View/Void buttons
- [ ] Create `app/Views/accounting/journal/create.php`
  - [ ] Date, Description, Reference fields
  - [ ] Dynamic lines table (add/remove rows)
  - [ ] Each line: COA (autocomplete), Debit, Credit, Description
  - [ ] Real-time debit/credit balance indicator
  - [ ] Submit disabled if unbalanced
- [ ] Create `app/Views/accounting/journal/show.php`
  - [ ] Entry header info
  - [ ] Lines table
  - [ ] Void button with reason modal

### 7.4 General Ledger
- [ ] Create `app/Controllers/AccountingReportController.php`
  - [ ] `ledger()` - general ledger view
  - [ ] `trialBalance()` - trial balance report
  - [ ] `incomeStatement()` - profit/loss report
  - [ ] `balanceSheet()` - balance sheet report
  - [ ] `cashFlow()` - cash flow statement
- [ ] Create `app/Views/accounting/reports/ledger.php`
  - [ ] Account selector (dropdown or search)
  - [ ] Date range filter
  - [ ] Opening balance row
  - [ ] Journal lines with running balance
  - [ ] Debit/Credit/Balance columns
  - [ ] Total row
  - [ ] Export CSV button
  - [ ] Click to journal detail

### 7.5 Trial Balance
- [ ] Create `app/Views/accounting/reports/trial-balance.php`
  - [ ] Date/period filter
  - [ ] Table: Account Code, Name, Debit, Credit
  - [ ] Grouped by account type
  - [ ] Total debit/credit footer (must match)
  - [ ] Zero-balance toggle
  - [ ] Export CSV button

### 7.6 Income Statement
- [ ] Create `app/Views/accounting/reports/income-statement.php`
  - [ ] Period selector (month/quarter/year)
  - [ ] Revenue section (grouped accounts)
  - [ ] Expense section (grouped accounts)
  - [ ] Net Income = Revenue - Expense
  - [ ] Compare with previous period (optional)
  - [ ] Export CSV/PDF button

### 7.7 Balance Sheet
- [ ] Create `app/Views/accounting/reports/balance-sheet.php`
  - [ ] As-of date selector
  - [ ] Assets section (current, fixed)
  - [ ] Liabilities section (current, long-term)
  - [ ] Equity section
  - [ ] Validation: Assets = Liabilities + Equity
  - [ ] Export CSV/PDF button

### 7.8 Cash Flow Statement
- [ ] Create `app/Views/accounting/reports/cash-flow.php`
  - [ ] Period selector
  - [ ] Operating activities
  - [ ] Investing activities
  - [ ] Financing activities
  - [ ] Net cash flow summary
  - [ ] Export CSV button

### 7.9 Accounting Settings
- [ ] Create `app/Views/accounting/settings/index.php`
  - [ ] Auto-post toggle (checkbox)
  - [ ] Default account mappings (dropdowns from COA)
    - Sales revenue account
    - COGS account
    - Tax payable account
    - Cash account
  - [ ] Fiscal year start month
  - [ ] Save settings button

### 7.10 Auto-Posting Integration
- [ ] Modify `TransactionController::store()` to optionally create journal entry
  - [ ] Debit: Cash/AR account
  - [ ] Credit: Sales revenue account
  - [ ] Credit: Tax payable account (if applicable)
  - [ ] Debit: COGS account (if tracking inventory)
  - [ ] Credit: Inventory account
- [ ] Link journal entry to transaction via reference_type/reference_id

### 7.11 UI Integration
- [ ] Add "Accounting" nav section in sidebar (main.php)
  - [ ] Chart of Accounts
  - [ ] Journal Entries
  - [ ] General Ledger
  - [ ] Trial Balance
  - [ ] Income Statement
  - [ ] Balance Sheet
  - [ ] Cash Flow
  - [ ] Accounting Settings
- [ ] Add accounting routes to `config/routes.php`
- [ ] Add accounting permissions to RBAC middleware

---

## Dependencies

### Phase Dependencies
- Phase 2 depends on Phase 1 (needs Auth & Core)
- Phase 3 depends on Phase 1 (needs Core & Layout)
- Phase 4 depends on Phase 1 & 3 (needs Products & Auth)
- Phase 5 depends on Phase 4 (needs Transactions)
- Phase 6 depends on all previous phases
- Phase 7 depends on Phase 1-4 (needs Transactions for auto-posting, RBAC for permissions)

### Critical Path
```
Phase 1 → Phase 2 → Phase 4 → Phase 5
    ↓                         ↓
Phase 3 ──────→ Phase 4   Phase 7
                              ↓
                          Phase 6
```

---

## Notes

- Mark tasks as complete by checking the box: `[ ]` → `[x]`
- Add notes/comments to tasks as needed during development
- Break down larger tasks into subtasks if they become complex
- Track blockers or dependencies as you encounter them

---

*Task list created: 2026-05-25*
*Based on: PLAN.md*
