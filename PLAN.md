# Point of Sales (POS) Application - Development Plan

## Overview

Aplikasi Point of Sales (POS) berbasis web yang dioptimalkan untuk tablet/iPad, dibangun menggunakan PHP Native tanpa framework, SQLite3 database, dan Bootstrap UI.

---

## Tech Stack

| Component       | Technology                          |
|-----------------|-------------------------------------|
| Backend         | PHP 8.2 (Native, no framework)      |
| Database        | SQLite3                             |
| Frontend UI     | Bootstrap 5                         |
| Icons           | FontAwesome 6                       |
| Architecture    | MVC Pattern                         |
| Authentication  | Session-based with RBAC             |
| Target Device   | Tablet / iPad (responsive design)   |

---

## Project Structure

```
pos-app/
├── public/
│   ├── index.php              # Entry point (front controller)
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css      # Custom styles
│   │   └── js/
│   │       └── app.js         # Custom JavaScript
│   └── uploads/               # Product images, receipts, etc.
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ProductController.php
│   │   ├── CategoryController.php
│   │   ├── TransactionController.php
│   │   ├── ReportController.php
│   │   ├── UserController.php
│   │   └── RoleController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Transaction.php
│   │   ├── TransactionItem.php
│   │   └── Setting.php
│   ├── Views/
│   │   ├── layouts/
│   │   │   ├── main.php       # Main layout (header, sidebar, footer)
│   │   │   └── auth.php       # Auth layout (login page)
│   │   ├── auth/
│   │   │   └── login.php
│   │   ├── dashboard/
│   │   │   └── index.php
│   │   ├── products/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   ├── categories/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   ├── transactions/
│   │   │   ├── index.php      # Transaction list
│   │   │   ├── pos.php        # POS interface (main cashier)
│   │   │   └── receipt.php    # Receipt view/print
│   │   ├── reports/
│   │   │   ├── daily.php
│   │   │   ├── monthly.php
│   │   │   └── products.php
│   │   ├── users/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   └── roles/
│   │       ├── index.php
│   │       └── edit.php
│   ├── Core/
│   │   ├── App.php            # Router / Front Controller
│   │   ├── Controller.php     # Base Controller
│   │   ├── Model.php          # Base Model
│   │   ├── View.php           # View renderer
│   │   ├── Database.php       # SQLite3 connection & query builder
│   │   ├── Auth.php           # Authentication helper
│   │   ├── Middleware.php     # Middleware handler
│   │   └── Helpers.php        # Global helper functions
│   └── Middleware/
│       ├── AuthMiddleware.php     # Require login
│       └── RBACMiddleware.php     # Role-based access check
├── config/
│   ├── app.php              # App configuration
│   ├── database.php         # SQLite3 config
│   └── routes.php           # Route definitions
├── database/
│   ├── pos.db               # SQLite3 database file
│   └── schema.sql           # Database schema & seed data
├── storage/
│   └── logs/                # Application logs
└── composer.json            # Optional: for autoload (psr-4)
```

---

## Database Schema

### Tables

#### 1. `roles`
| Column       | Type         | Constraints          |
|-------------|--------------|----------------------|
| id          | INTEGER      | PK, AUTO_INCREMENT   |
| name        | TEXT         | UNIQUE, NOT NULL     |
| description | TEXT         |                      |
| created_at  | DATETIME     | DEFAULT CURRENT_TIMESTAMP |

#### 2. `permissions`
| Column       | Type         | Constraints          |
|-------------|--------------|----------------------|
| id          | INTEGER      | PK, AUTO_INCREMENT   |
| name        | TEXT         | UNIQUE, NOT NULL     |
| description | TEXT         |                      |

#### 3. `role_permissions`
| Column        | Type    | Constraints        |
|--------------|---------|--------------------|
| role_id      | INTEGER | FK -> roles.id     |
| permission_id| INTEGER | FK -> permissions.id|

#### 4. `users`
| Column       | Type         | Constraints          |
|-------------|--------------|----------------------|
| id          | INTEGER      | PK, AUTO_INCREMENT   |
| username    | TEXT         | UNIQUE, NOT NULL     |
| password    | TEXT         | NOT NULL             |
| full_name   | TEXT         | NOT NULL             |
| email       | TEXT         | UNIQUE               |
| role_id     | INTEGER      | FK -> roles.id       |
| is_active   | INTEGER      | DEFAULT 1            |
| created_at  | DATETIME     | DEFAULT CURRENT_TIMESTAMP |
| updated_at  | DATETIME     |                      |

#### 5. `categories`
| Column       | Type         | Constraints          |
|-------------|--------------|----------------------|
| id          | INTEGER      | PK, AUTO_INCREMENT   |
| name        | TEXT         | UNIQUE, NOT NULL     |
| description | TEXT         |                      |
| created_at  | DATETIME     | DEFAULT CURRENT_TIMESTAMP |

#### 6. `products`
| Column       | Type         | Constraints          |
|-------------|--------------|----------------------|
| id          | INTEGER      | PK, AUTO_INCREMENT   |
| sku         | TEXT         | UNIQUE, NOT NULL     |
| name        | TEXT         | NOT NULL             |
| category_id | INTEGER      | FK -> categories.id  |
| price       | REAL         | NOT NULL             |
| cost        | REAL         |                      |
| stock       | INTEGER      | DEFAULT 0            |
| image       | TEXT         |                      |
| is_active   | INTEGER      | DEFAULT 1            |
| created_at  | DATETIME     | DEFAULT CURRENT_TIMESTAMP |
| updated_at  | DATETIME     |                      |

#### 7. `transactions`
| Column       | Type         | Constraints          |
|-------------|--------------|----------------------|
| id          | INTEGER      | PK, AUTO_INCREMENT   |
| invoice_no  | TEXT         | UNIQUE, NOT NULL     |
| user_id     | INTEGER      | FK -> users.id       |
| subtotal    | REAL         | NOT NULL             |
| tax         | REAL         | DEFAULT 0            |
| discount    | REAL         | DEFAULT 0            |
| total       | REAL         | NOT NULL             |
| payment_method | TEXT      | (cash, card, qris)   |
| amount_paid | REAL         | NOT NULL             |
| change      | REAL         |                      |
| created_at  | DATETIME     | DEFAULT CURRENT_TIMESTAMP |

#### 8. `transaction_items`
| Column       | Type         | Constraints          |
|-------------|--------------|----------------------|
| id          | INTEGER      | PK, AUTO_INCREMENT   |
| transaction_id | INTEGER   | FK -> transactions.id|
| product_id  | INTEGER      | FK -> products.id    |
| quantity    | INTEGER      | NOT NULL             |
| price       | REAL         | NOT NULL             |
| subtotal    | REAL         | NOT NULL             |

#### 9. `settings`
| Column       | Type         | Constraints          |
|-------------|--------------|----------------------|
| id          | INTEGER      | PK, AUTO_INCREMENT   |
| key         | TEXT         | UNIQUE, NOT NULL     |
| value       | TEXT         |                      |

---

## RBAC Implementation

### Roles (Default)
| Role         | Description                    |
|-------------|--------------------------------|
| Super Admin | Full access to everything      |
| Admin       | Manage products, users, reports|
| Cashier     | POS interface, view own transactions |
| Viewer      | Read-only access to reports    |

### Permissions (Default)
| Permission Key              | Description                |
|----------------------------|----------------------------|
| dashboard.view             | View dashboard             |
| products.view              | View products              |
| products.create            | Create products            |
| products.edit              | Edit products              |
| products.delete            | Delete products            |
| categories.manage          | Manage categories          |
| transactions.view          | View transactions          |
| transactions.pos           | Access POS interface       |
| transactions.delete        | Void transactions          |
| reports.view               | View reports               |
| users.manage               | Manage users               |
| roles.manage               | Manage roles & permissions |
| settings.manage            | Manage application settings|

### Middleware Flow
```
Request → AuthMiddleware → RBACMiddleware → Controller → View
```

---

## Modules & Features

### 1. Authentication Module
- [x] Login / Logout
- [x] Session management
- [x] Password hashing (password_hash / password_verify)
- [x] Remember me (optional)

### 2. Dashboard Module
- [x] Sales summary (today, this week, this month)
- [x] Low stock alerts
- [x] Recent transactions
- [x] Quick stats cards

### 3. Product Management
- [x] CRUD Products
- [x] SKU auto-generation
- [x] Product image upload
- [x] Stock management
- [x] Search & filter by category
- [x] Low stock indicator

### 4. Category Management
- [x] CRUD Categories

### 5. POS Module (Main Feature)
- [x] Touch-friendly interface optimized for tablet
- [x] Product grid with search
- [x] Cart management (add, remove, update qty)
- [x] Discount application (fixed / percentage)
- [x] Tax calculation
- [x] Payment method selection (Cash, Card, QRIS)
- [x] Change calculation
- [x] Receipt generation & print
- [x] Keyboard shortcuts

### 6. Transaction Module
- [x] Transaction history
- [x] Transaction detail view
- [x] Void transaction (with permission)
- [x] Receipt reprint

### 7. Report Module
- [x] Daily sales report
- [x] Monthly sales report
- [x] Product sales report (best seller)
- [x] Export to CSV
- [x] Date range filter

### 8. User Management (Admin only)
- [x] CRUD Users
- [x] Assign roles
- [x] Activate / Deactivate users

### 9. Role & Permission Management (Super Admin only)
- [x] CRUD Roles
- [x] Assign permissions to roles

### 10. Settings
- [x] Store name & info
- [x] Tax rate
- [x] Receipt footer text
- [x] Currency format

---

## UI/UX Design Guidelines (Tablet/iPad Optimized)

### Layout
- **Sidebar Navigation**: Collapsible, icon + text
- **Top Bar**: User info, notifications, logout
- **Content Area**: Card-based layout, ample spacing
- **Grid System**: Bootstrap grid (col-md-*) optimized for 768px+ screens

### POS Interface
```
+------------------------------------------+
|  Search Bar                  [Cart Icon] |
+------------------+-----------------------+
|                  |                       |
|  Product Grid    |     Cart / Basket     |
|  (touch cards)   |                       |
|  3-4 columns     |  - Item list          |
|                  |  - Qty +/- buttons    |
|                  |  - Subtotal           |
|                  |  - Tax & Discount     |
|                  |  - Total (large)      |
|                  |                       |
|                  |  [Payment Button]     |
+------------------+-----------------------+
```

### Touch-Friendly Elements
- Minimum button size: 44x44px
- Large tap targets for cart operations
- Swipe-friendly product cards
- Modal-based dialogs for payment
- Font size: minimum 14px for body, 16px+ for interactive elements

### Color Scheme
- Primary: #4361ee (Bootstrap primary)
- Success: #2ec4b6 (for positive/sales)
- Danger: #e63946 (for void/delete)
- Warning: #fca311 (for low stock)
- Neutral backgrounds with card shadows

---

## Routing System

### URL Pattern
```
/index.php?controller=product&action=index&id=123
```

### Clean URLs (via .htaccess)
```
/products
/products/create
/products/edit/123
/pos
/transactions
/reports/daily
```

### Route Configuration (`config/routes.php`)
```php
return [
    ''                          => ['controller' => 'Dashboard', 'action' => 'index'],
    'login'                     => ['controller' => 'Auth', 'action' => 'login'],
    'logout'                    => ['controller' => 'Auth', 'action' => 'logout'],
    'dashboard'                 => ['controller' => 'Dashboard', 'action' => 'index', 'middleware' => ['auth']],
    'products'                  => ['controller' => 'Product', 'action' => 'index', 'middleware' => ['auth', 'rbac:products.view']],
    'products/create'           => ['controller' => 'Product', 'action' => 'create', 'middleware' => ['auth', 'rbac:products.create']],
    'products/edit/:id'         => ['controller' => 'Product', 'action' => 'edit', 'middleware' => ['auth', 'rbac:products.edit']],
    'products/delete/:id'       => ['controller' => 'Product', 'action' => 'delete', 'middleware' => ['auth', 'rbac:products.delete']],
    'categories'                => ['controller' => 'Category', 'action' => 'index', 'middleware' => ['auth', 'rbac:categories.manage']],
    'pos'                       => ['controller' => 'Transaction', 'action' => 'pos', 'middleware' => ['auth', 'rbac:transactions.pos']],
    'transactions'              => ['controller' => 'Transaction', 'action' => 'index', 'middleware' => ['auth', 'rbac:transactions.view']],
    'transactions/:id'          => ['controller' => 'Transaction', 'action' => 'show', 'middleware' => ['auth', 'rbac:transactions.view']],
    'transactions/void/:id'     => ['controller' => 'Transaction', 'action' => 'void', 'middleware' => ['auth', 'rbac:transactions.delete']],
    'reports/daily'             => ['controller' => 'Report', 'action' => 'daily', 'middleware' => ['auth', 'rbac:reports.view']],
    'reports/monthly'           => ['controller' => 'Report', 'action' => 'monthly', 'middleware' => ['auth', 'rbac:reports.view']],
    'reports/products'          => ['controller' => 'Report', 'action' => 'products', 'middleware' => ['auth', 'rbac:reports.view']],
    'users'                     => ['controller' => 'User', 'action' => 'index', 'middleware' => ['auth', 'rbac:users.manage']],
    'roles'                     => ['controller' => 'Role', 'action' => 'index', 'middleware' => ['auth', 'rbac:roles.manage']],
    'settings'                  => ['controller' => 'Setting', 'action' => 'index', 'middleware' => ['auth', 'rbac:settings.manage']],
];
```

---

## Core Classes

### 1. Database (`app/Core/Database.php`)
- SQLite3 PDO connection
- Query builder methods: `query()`, `insert()`, `update()`, `delete()`, `find()`, `findAll()`
- Prepared statements for security
- Transaction support

### 2. Controller (`app/Core/Controller.php`)
- Base controller with `view()`, `redirect()`, `json()` methods
- Load models via `$this->model('ModelName')`

### 3. Model (`app/Core/Model.php`)
- Base model with CRUD operations
- Table name auto-detection
- Soft delete support

### 4. View (`app/Core/View.php`)
- Render views with layout
- Pass data to views
- Helper function `view('path', $data)`

### 5. Auth (`app/Core/Auth.php`)
- `login($username, $password)`
- `logout()`
- `check()`
- `user()`
- `hasPermission($permission)`
- `hasRole($role)`

### 6. Middleware (`app/Core/Middleware.php`)
- Register and execute middleware
- Before/after hooks

---

## Security Considerations

- [x] Password hashing with `password_hash()` (bcrypt/argon2)
- [x] Prepared statements (prevent SQL injection)
- [x] CSRF token for forms
- [x] XSS prevention (htmlspecialchars in views)
- [x] Session regeneration on login
- [x] Input validation & sanitization
- [x] File upload validation (type, size)
- [x] RBAC enforcement on every protected route
- [x] HTTPS recommendation for production

---

## Development Phases

### Phase 1: Foundation (Setup & Core)
- Project structure setup
- Core classes (Database, Controller, Model, View, Auth)
- Routing system
- Database schema & seeding
- Authentication (login/logout)
- Basic layout & navigation

### Phase 2: RBAC & User Management
- Roles & Permissions tables
- RBAC middleware
- User CRUD
- Role CRUD
- Permission assignment UI

### Phase 3: Product & Category Management
- Category CRUD
- Product CRUD with image upload
- Search, filter, pagination
- Stock management

### Phase 4: POS Module (Core Feature)
- POS interface (tablet-optimized)
- Cart management (JavaScript)
- Checkout flow
- Payment processing
- Receipt generation & print

### Phase 5: Transactions & Reports
- Transaction history
- Transaction detail view
- Daily/Monthly reports
- Product sales report
- CSV export

### Phase 6: Settings & Polish
- Application settings
- UI/UX refinements
- Error handling & logging
- Testing & bug fixes
- Documentation

---

## API / AJAX Endpoints (JSON)

| Endpoint                      | Method | Description              |
|-------------------------------|--------|--------------------------|
| `/api/products/search`        | GET    | Search products for POS  |
| `/api/products/stock/:id`     | GET    | Get product stock        |
| `/api/cart/calculate`         | POST   | Calculate cart total     |
| `/api/transactions/store`     | POST   | Create new transaction   |
| `/api/transactions/:id`       | GET    | Get transaction detail   |
| `/api/reports/sales/summary`  | GET    | Dashboard sales summary  |

---

## JavaScript Components

| Component           | File                  | Description                    |
|---------------------|-----------------------|--------------------------------|
| POS Cart            | `pos-cart.js`         | Cart management, calculations  |
| Product Search      | `product-search.js`   | AJAX search for POS            |
| Form Validator      | `form-validator.js`   | Client-side validation         |
| DataTables          | (CDN or custom)       | Table with sort/search/pagination |
| Modal Handler       | `modal-handler.js`    | Payment modal, confirm dialogs |
| Receipt Print       | `receipt-print.js`    | Print-friendly receipt         |

---

## Seed Data

### Default Users
| Username  | Password   | Role        | Full Name     |
|-----------|-----------|-------------|---------------|
| admin     | admin123  | Super Admin | Administrator |
| cashier01 | cashier123| Cashier     | John Cashier  |

### Default Settings
| Key            | Value              |
|----------------|--------------------|
| store_name     | My Store           |
| store_address  | Jl. Example No. 1  |
| store_phone    | 081234567890       |
| tax_rate       | 11                 |
| currency       | Rp                 |
| receipt_footer | Thank you!         |

---

## Deployment Notes

### Requirements
- PHP 8.2+
- SQLite3 extension enabled
- Apache/Nginx with mod_rewrite
- Minimum 2GB RAM

### SQLite3 Considerations
- Database file must be writable by web server
- Implement WAL mode for better concurrency
- Regular backup strategy (file copy)
- Consider connection pooling for high traffic

### File Permissions
```bash
chmod 664 database/pos.db
chmod 775 storage/
chmod 775 public/uploads/
```

---

## Future Enhancements (Post-MVP)

- [ ] Multi-outlet support
- [ ] Barcode scanner integration
- [ ] Customer management
- [ ] Loyalty program
- [ ] Inventory stock cards
- [ ] Purchase / supplier module
- [ ] Employee attendance
- [ ] API for mobile app integration
- [ ] Cloud sync capability
- [ ] Offline mode with sync

---

## Timeline Estimate

| Phase              | Estimated Effort |
|--------------------|------------------|
| Phase 1: Foundation    | 3-4 days      |
| Phase 2: RBAC          | 2-3 days      |
| Phase 3: Products      | 3-4 days      |
| Phase 4: POS Module    | 5-6 days      |
| Phase 5: Reports       | 3-4 days      |
| Phase 6: Polish        | 2-3 days      |
| **Total**              | **18-24 days** |

---

## Notes

- All monetary values stored in smallest unit or as REAL with 2 decimal precision
- Invoice number format: `INV/YYYYMMDD/XXXX` (auto-increment)
- Stock is reduced on transaction completion
- Voided transactions do NOT restore stock (configurable)
- Receipt can be printed or saved as PDF
- Session timeout: 30 minutes of inactivity

---

*Document created: 2026-05-25*
*Target PHP Version: 8.2*
*Database: SQLite3*
*UI Framework: Bootstrap 5 + FontAwesome 6*
