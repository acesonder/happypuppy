# HappyPuppy - Harm Reduction Ordering System

A comprehensive harm reduction ordering system with messaging, notifications, and inventory management built with PHP, MySQL, HTML, CSS, and Bootstrap.

## Features

- **User Authentication**: Secure login and registration with PHP sessions
- **Product Catalog**: Browse harm reduction products with detailed safety information
- **Shopping Cart**: Add products to cart and manage quantities
- **Scheduled Ordering**: Pickup/delivery only on Wednesdays and Fridays, 5pm-9pm
- **Order Tracking**: Complete order history and status updates
- **Messaging System**: Direct communication with support team
- **Notifications**: In-app notification system with unread badges
- **Admin Dashboard**: Manage products, orders, and users
- **Responsive Design**: Mobile-friendly with Bootstrap 5

## Technology Stack

### Backend
- **PHP 7.4+**: Server-side logic and business rules
- **MySQL 5.7+**: Relational database
- **PHPMyAdmin**: Database management interface

### Frontend
- **HTML5**: Page structure
- **CSS3**: Custom styling
- **Bootstrap 5**: Responsive UI framework
- **Minimal JavaScript**: Form validation and UI enhancements

### Security
- Password hashing with PHP `password_hash()`
- Session-based authentication
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PHPMyAdmin (optional but recommended)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/acesonder/happypuppy.git
cd happypuppy
```

### 2. Configure Web Server

**For Apache (with mod_php):**

Add this to your Apache virtual host configuration:

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /path/to/happypuppy
    
    <Directory /path/to/happypuppy>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**For PHP Built-in Server (Development Only):**

```bash
php -S localhost:8000
```

### 3. Create MySQL Database

**Option A: Using PHPMyAdmin**
1. Open PHPMyAdmin in your browser
2. Click "New" to create a database named `happypuppy`
3. Import the schema: Go to Import tab and select `database/schema.sql`
4. Import seed data: Import `database/seed.sql`

**Option B: Using MySQL Command Line**

```bash
mysql -u root -p
```

```sql
CREATE DATABASE happypuppy;
USE happypuppy;
SOURCE database/schema.sql;
SOURCE database/seed.sql;
```

### 4. Configure Database Connection

The database configuration is in `/includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Blank password for development
define('DB_NAME', 'happypuppy');
```

**Note**: These are the default development credentials. Change them for production!

### 5. Access the Application

Open your browser and navigate to:
- **Local development**: `http://localhost:8000`
- **Apache**: `http://localhost`

## Default Login Credentials

**Admin Account:**
- Email: `admin@happypuppy.com`
- Password: `admin123`

**Test User:**
- Email: `user@test.com`
- Password: `user123`

## Project Structure

```
happypuppy/
├── admin/                  # Admin pages
│   ├── orders.php         # Manage all orders
│   ├── products.php       # Manage products
│   └── product-edit.php   # Add/edit products
├── database/              # Database files
│   ├── schema.sql         # Database structure
│   └── seed.sql           # Sample data
├── includes/              # Shared PHP files
│   ├── config.php         # Configuration & helpers
│   ├── header.php         # Page header
│   └── footer.php         # Page footer
├── models/                # Data models
│   ├── User.php
│   ├── Product.php
│   ├── Order.php
│   ├── Message.php
│   └── Notification.php
├── public/                # Static assets
│   ├── css/
│   │   └── style.css      # Custom styles
│   └── js/
│       └── main.js        # JavaScript utilities
├── index.php              # Home page
├── login.php              # Login page
├── register.php           # Registration page
├── products.php           # Product catalog
├── cart.php               # Shopping cart
├── checkout.php           # Order checkout
├── orders.php             # User's orders
├── order-details.php      # Order details
├── messages.php           # Messaging system
├── notifications.php      # Notifications
├── profile.php            # User profile
└── logout.php             # Logout handler
```

## Key Features

### 1. Product Management
- Browse harm reduction supplies
- Detailed product information
- Safety guidelines for each product
- Real-time inventory tracking
- Category-based organization

### 2. Order System
- Shopping cart functionality
- Delivery type selection (Pickup/Delivery)
- Date/time scheduling (Wednesday & Friday, 5-9 PM)
- Order status tracking
- Order history

### 3. Messaging
- Direct messaging with support team
- Conversation history
- Message notifications
- Order-related discussions

### 4. Admin Features
- Product CRUD operations
- Order status management
- User management
- Inventory control

### 5. Notifications
- Order confirmations
- Status updates
- New messages
- System alerts

## Database Schema

### Tables
- **users**: User accounts and profiles
- **products**: Product catalog
- **orders**: Order records
- **order_items**: Order line items
- **messages**: User-to-user messages
- **notifications**: System notifications
- **check_ins**: Safety check-in records

See `database/schema.sql` for complete structure.

## Security Best Practices

### For Production Deployment:

1. **Change database credentials** in `includes/config.php`
2. **Use strong passwords** for MySQL root user
3. **Enable HTTPS** with SSL certificate
4. **Set proper file permissions**:
   ```bash
   chmod 755 /path/to/happypuppy
   chmod 644 *.php
   ```
5. **Disable error display** in production:
   ```php
   ini_set('display_errors', 0);
   error_reporting(0);
   ```
6. **Keep PHP and MySQL updated**
7. **Regular database backups**

## Harm Reduction Focus

This system follows harm reduction principles:
- **Non-judgmental**: Respectful, stigma-free service
- **Safety-focused**: Comprehensive safety information
- **Accessible**: Easy-to-use interface
- **Confidential**: Secure and private
- **Educational**: Safety guidelines with every product

## Support

For questions or support, log in and use the messaging system to contact the support team.

## License

ISC