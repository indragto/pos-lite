# POS Application - Point of Sale System

A web-based Point of Sale (POS) application optimized for tablets/iPad, built with PHP 8.2 Native, SQLite3, and Bootstrap 5.

## Features

- ✅ **Role-Based Access Control (RBAC)** - 4 default roles with granular permissions
- ✅ **Product Management** - CRUD with image upload, SKU auto-generation, stock tracking
- ✅ **Category Management** - Organize products by category
- ✅ **POS Interface** - Touch-friendly cashier interface optimized for tablets
- ✅ **Transaction Management** - Process sales, view history, void transactions
- ✅ **Reports** - Daily, monthly, and product sales reports with CSV export
- ✅ **User Management** - Manage users and assign roles
- ✅ **Settings** - Configure store info, tax rate, receipt settings

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | PHP 8.2 (Native, no framework) |
| Database | SQLite3 |
| Frontend UI | Bootstrap 5 |
| Icons | FontAwesome 6 |
| Architecture | MVC Pattern |
| Authentication | Session-based with RBAC |

## Requirements

- PHP 8.2 or higher
- SQLite3 extension enabled
- Apache/Nginx with mod_rewrite
- Minimum 2GB RAM

## Installation

### 1. Clone or Download

Download the project to your web server directory.

### 2. Configure

Edit `config/app.php` to set your application URL:

```php
'url' => 'http://localhost/pos-app',
```

### 3. Initialize Database

Run the database initialization script:

```bash
php init_db.php
```

This will create the SQLite database and seed it with default data.

**Important:** Delete `init_db.php` after running it.

### 4. Set Permissions

Ensure these directories are writable:

```bash
chmod 775 database/
chmod 775 storage/
chmod 775 public/uploads/
```

### 5. Access the Application

Navigate to your application URL in a browser.

## Default Credentials

| Username | Password | Role |
|----------|----------|------|
| admin | admin123 | Super Admin |
| cashier01 | admin123 | Cashier |

**⚠️ Change these passwords after first login!**

**To regenerate password hash:**
```bash
php -r "echo password_hash('yourpassword', PASSWORD_BCRYPT);"
```

## Project Structure

```
pos-app/
├── app/
│   ├── Controllers/     # Application controllers
│   ├── Core/           # Core framework classes
│   ├── Middleware/     # Auth and RBAC middleware
│   ├── Models/         # Database models
│   └── Views/          # View templates
├── config/             # Configuration files
├── database/           # SQLite database
├── public/             # Public assets
│   ├── assets/        # CSS, JS files
│   └── uploads/       # Uploaded images
├── storage/           # Application storage
│   └── logs/          # Log files
├── init_db.php        # Database initialization (delete after use)
└── README.md          # This file
```

## Default Roles & Permissions

### Roles
- **Super Admin** - Full access to all features
- **Admin** - Manage products, users, and reports
- **Cashier** - Access POS interface and view transactions
- **Viewer** - Read-only access to reports

### Permissions
- `dashboard.view` - View dashboard
- `products.view/create/edit/delete` - Product management
- `categories.manage` - Category management
- `transactions.view/pos/delete` - Transaction management
- `reports.view` - View reports
- `users.manage` - User management
- `roles.manage` - Role & permission management
- `settings.manage` - Application settings

## Usage

### Point of Sale
1. Navigate to "Point of Sale" from the sidebar
2. Search or browse products
3. Click products to add to cart
4. Adjust quantities using +/- buttons
5. Click "Payment" to checkout
6. Select payment method and enter amount
7. Complete transaction and print receipt

### Adding Products
1. Go to Products > Add Product
2. Fill in product details (SKU auto-generated)
3. Upload product image (optional)
4. Set stock quantity
5. Save

### Managing Users
1. Go to Users > Add User
2. Fill in user details
3. Assign a role
4. Save and share credentials

### Generating Reports
1. Go to Reports > Daily/Monthly/Product Sales
2. Select date range
3. View summary and breakdown
4. Export to CSV if needed

## Security

- Password hashing with bcrypt
- Prepared statements (SQL injection prevention)
- CSRF token protection on all forms
- XSS prevention (output escaping)
- Session regeneration on login
- RBAC enforcement on all routes
- Input validation and sanitization

## SQLite Considerations

- Database uses WAL mode for better concurrency
- Regular backups recommended (copy the .db file)
- Database file must be writable by web server
- Suitable for small to medium traffic POS systems

## Troubleshooting

### Database connection failed
- Ensure `database/pos.db` exists and is writable
- Check SQLite3 extension is enabled in PHP

### 404 errors on routes
- Ensure mod_rewrite is enabled (Apache)
- Check .htaccess file exists in public/

### Permission denied
- Check file permissions on database, storage, uploads
- Verify user has correct role and permissions

### Session timeout
- Adjust `session_timeout` in Settings
- Check PHP session configuration

## License

This project is open-source and available for personal and commercial use.

## Support

For issues or questions, please refer to the documentation or contact the development team.

---

**Version:** 1.0.0  
**PHP Version:** 8.2+  
**Last Updated:** 2026-05-25
