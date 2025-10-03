# HappyPuppy - Quick Start Guide

Get up and running with HappyPuppy in 5 minutes!

## Prerequisites Checklist

Before starting, ensure you have:

- [ ] PHP 7.4 or higher installed
- [ ] MySQL 5.7 or higher installed
- [ ] Web server (Apache/Nginx) or PHP built-in server
- [ ] PHPMyAdmin (optional but recommended)

## Quick Setup (Development)

### Step 1: Clone Repository

```bash
git clone https://github.com/acesonder/happypuppy.git
cd happypuppy
```

### Step 2: Create Database

**Option A - PHPMyAdmin (Easiest)**:

1. Open http://localhost/phpmyadmin
2. Login with username: `root`, password: (blank)
3. Click "New" → Database name: `happypuppy`
4. Import `database/schema.sql`
5. Import `database/seed.sql`

**Option B - Command Line**:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE happypuppy;
USE happypuppy;
SOURCE database/schema.sql;
SOURCE database/seed.sql;
EXIT;
```

### Step 3: Start Web Server

**Option A - PHP Built-in Server (Quickest)**:

```bash
php -S localhost:8000
```

Open: http://localhost:8000

**Option B - XAMPP/WAMP**:

1. Copy `happypuppy` folder to `C:\xampp\htdocs\`
2. Start Apache in XAMPP Control Panel
3. Open: http://localhost/happypuppy

**Option C - Apache Virtual Host**:

See `INSTALL.md` for detailed instructions.

### Step 4: Login & Test

Open your browser and go to http://localhost:8000 (or your configured URL)

**Admin Login**:
- Email: `admin@happypuppy.com`
- Password: `admin123`

**Regular User Login**:
- Email: `user@test.com`
- Password: `user123`

## Verify Installation

After logging in, test these features:

- [ ] View Products page
- [ ] Add item to cart
- [ ] View cart
- [ ] Place an order (checkout)
- [ ] View order history
- [ ] Send a message to support
- [ ] Check notifications
- [ ] View profile

**Admin Only**:
- [ ] Manage Products (add/edit/delete)
- [ ] View all orders
- [ ] Update order status

## Configuration

Default database config in `/includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Blank for development
define('DB_NAME', 'happypuppy');
```

**Change these for production!**

## Common Issues

### Cannot connect to database

**Check**:
1. MySQL is running: `sudo systemctl status mysql`
2. Database exists: `SHOW DATABASES;` in MySQL
3. User has permissions: `GRANT ALL ON happypuppy.* TO 'root'@'localhost';`

### 404 errors on pages

**Fix**:
- Ensure `.htaccess` is present
- Enable Apache mod_rewrite: `sudo a2enmod rewrite`
- Check DocumentRoot in Apache config

### Blank white page

**Fix**:
- Enable PHP error display
- Check PHP error log: `/var/log/apache2/error.log`
- Verify PHP version: `php -v`

### "Class 'mysqli' not found"

**Fix**:
```bash
# Ubuntu/Debian
sudo apt install php-mysql
sudo systemctl restart apache2

# Mac
brew install php
```

## Next Steps

1. **Change default passwords**
2. **Add your own products** via Admin → Manage Products
3. **Customize styling** in `/public/css/style.css`
4. **Test ordering workflow**
5. **Set up regular backups** (see `PHPMYADMIN.md`)

## File Structure Overview

```
happypuppy/
├── admin/              # Admin-only pages
├── database/           # SQL schema & seed data
├── includes/           # Config, header, footer
├── models/             # PHP model classes
├── public/             # CSS, JS, images
├── index.php           # Home page
├── login.php           # Login
├── products.php        # Product catalog
├── cart.php            # Shopping cart
├── checkout.php        # Order checkout
├── orders.php          # Order history
└── messages.php        # Messaging
```

## Development Tips

### Enable Error Reporting

Add to `/includes/config.php`:

```php
// For development only!
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Database Changes

After modifying database schema:

```bash
# Backup first
mysqldump -u root happypuppy > backup.sql

# Apply changes
mysql -u root happypuppy < database/schema.sql
```

### Adding New Products

**Via PHPMyAdmin**:
1. Open `products` table
2. Click "Insert"
3. Fill in all fields
4. Click "Go"

**Via Admin Panel**:
1. Login as admin
2. Go to Admin → Manage Products
3. Click "Add New Product"
4. Fill form and save

### Customizing Design

Edit these files:
- `/public/css/style.css` - Main styles
- `/includes/header.php` - Navigation & header
- `/includes/footer.php` - Footer content

### Testing Workflow

1. Register new user
2. Browse products
3. Add items to cart
4. Checkout with delivery details
5. View order confirmation
6. Send message to support
7. Check notifications

## Getting Help

1. **Check logs**: `/var/log/apache2/error.log`
2. **Enable debug mode** (development only)
3. **Review documentation**: `README.md`, `INSTALL.md`
4. **Database issues**: See `PHPMYADMIN.md`

## Production Deployment

Before going live:

- [ ] Change all default passwords
- [ ] Use strong MySQL password
- [ ] Disable error display
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Configure regular backups
- [ ] Review security settings

See `INSTALL.md` for complete production checklist.

## Features Overview

### User Features
- Browse harm reduction products
- Shopping cart
- Order scheduling (Wed/Fri, 5-9 PM)
- Order tracking
- Message support team
- Notifications
- Profile management

### Admin Features
- Product management (CRUD)
- Order management
- Status updates
- User list
- Inventory control

## Support

For issues or questions:
- Check documentation files
- Review error logs
- Test with default credentials
- Verify database setup

Enjoy using HappyPuppy! 🐶