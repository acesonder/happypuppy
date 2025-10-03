# HappyPuppy Installation Guide

## System Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher  
- **Web Server**: Apache 2.4+ or Nginx 1.18+ or PHP built-in server
- **PHPMyAdmin**: Optional but recommended for database management

## Step-by-Step Installation

### Step 1: Install Prerequisites

#### On Ubuntu/Debian:

```bash
sudo apt update
sudo apt install php php-mysql mysql-server apache2 phpmyadmin
```

#### On macOS (using Homebrew):

```bash
brew install php mysql
brew services start mysql
brew services start httpd
```

#### On Windows:

Download and install:
- [XAMPP](https://www.apachefriends.org/) - Includes PHP, MySQL, and Apache
- Or [WampServer](https://www.wampserver.com/)

### Step 2: Clone the Repository

```bash
git clone https://github.com/acesonder/happypuppy.git
cd happypuppy
```

### Step 3: Configure MySQL

#### Start MySQL Service:

```bash
# Ubuntu/Debian
sudo systemctl start mysql
sudo systemctl enable mysql

# macOS
brew services start mysql

# Windows (XAMPP)
# Start MySQL from XAMPP Control Panel
```

#### Secure MySQL Installation (Production):

```bash
sudo mysql_secure_installation
```

For development, you can use:
- **Username**: root
- **Password**: (blank)

### Step 4: Create Database

#### Option A: Using PHPMyAdmin

1. Open PHPMyAdmin: `http://localhost/phpmyadmin`
2. Login with root user
3. Click **"New"** to create database
4. Database name: `happypuppy`
5. Click **"Import"** tab
6. Choose file: `database/schema.sql`
7. Click **"Go"** to execute
8. Import seed data: `database/seed.sql`

#### Option B: Using MySQL Command Line

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

### Step 5: Configure Web Server

#### Option A: Apache with Virtual Host

Create file `/etc/apache2/sites-available/happypuppy.conf`:

```apache
<VirtualHost *:80>
    ServerName happypuppy.local
    DocumentRoot /path/to/happypuppy
    
    <Directory /path/to/happypuppy>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.php
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/happypuppy_error.log
    CustomLog ${APACHE_LOG_DIR}/happypuppy_access.log combined
</VirtualHost>
```

Enable the site:

```bash
sudo a2ensite happypuppy.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Add to `/etc/hosts`:

```
127.0.0.1  happypuppy.local
```

#### Option B: PHP Built-in Server (Development Only)

```bash
cd /path/to/happypuppy
php -S localhost:8000
```

Then open: `http://localhost:8000`

#### Option C: XAMPP/WAMP (Windows)

1. Copy `happypuppy` folder to `C:\xampp\htdocs\`
2. Start Apache from XAMPP Control Panel
3. Open: `http://localhost/happypuppy`

### Step 6: Configure Application

Edit `/includes/config.php` and verify database settings:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password
define('DB_NAME', 'happypuppy');
```

### Step 7: Set File Permissions (Linux/Mac)

```bash
# Make sure PHP can read all files
chmod -R 755 /path/to/happypuppy
chmod -R 644 /path/to/happypuppy/*.php
```

### Step 8: Test Installation

1. Open your browser
2. Navigate to: `http://localhost:8000` or `http://happypuppy.local`
3. You should see the HappyPuppy home page
4. Click **"Login"**
5. Use test credentials:
   - Email: `admin@happypuppy.com`
   - Password: `admin123`

## Verification Checklist

- [ ] Database `happypuppy` created
- [ ] Tables created from `schema.sql`
- [ ] Sample data loaded from `seed.sql`
- [ ] Web server running
- [ ] PHP version 7.4+
- [ ] MySQL connection working
- [ ] Can login with admin account
- [ ] Can view products
- [ ] Can create orders

## Troubleshooting

### "Connection failed" Error

**Problem**: Cannot connect to MySQL database

**Solutions**:
1. Check MySQL is running: `sudo systemctl status mysql`
2. Verify credentials in `/includes/config.php`
3. Check MySQL user has permissions:
   ```sql
   GRANT ALL PRIVILEGES ON happypuppy.* TO 'root'@'localhost';
   FLUSH PRIVILEGES;
   ```

### "404 Not Found" Error

**Problem**: Web server cannot find pages

**Solutions**:
1. Check DocumentRoot points to correct directory
2. Verify Apache is running
3. Check file permissions
4. For Apache, enable mod_rewrite: `sudo a2enmod rewrite`

### "Fatal error: Uncaught Error: Class 'mysqli' not found"

**Problem**: PHP mysqli extension not installed

**Solutions**:
```bash
# Ubuntu/Debian
sudo apt install php-mysql
sudo systemctl restart apache2

# macOS
# Uncomment in php.ini:
# extension=mysqli
```

### Blank Page or White Screen

**Problem**: PHP errors not displaying

**Solutions**:
1. Enable error reporting in PHP:
   ```bash
   sudo nano /etc/php/7.4/apache2/php.ini
   ```
2. Set:
   ```ini
   display_errors = On
   error_reporting = E_ALL
   ```
3. Restart Apache: `sudo systemctl restart apache2`

### Tables Don't Exist

**Problem**: Database tables not created

**Solutions**:
1. Re-import schema:
   ```bash
   mysql -u root -p happypuppy < database/schema.sql
   ```
2. Check for SQL errors during import

## Production Deployment

### Security Hardening

1. **Change default passwords**:
   ```sql
   UPDATE users SET password = PASSWORD_HASH('new_password', PASSWORD_DEFAULT) WHERE email = 'admin@happypuppy.com';
   ```

2. **Use strong MySQL password**:
   ```sql
   ALTER USER 'root'@'localhost' IDENTIFIED BY 'strong_password_here';
   ```

3. **Disable error display**:
   In `/includes/config.php`, add:
   ```php
   ini_set('display_errors', 0);
   error_reporting(0);
   ```

4. **Enable HTTPS**:
   - Install SSL certificate (Let's Encrypt recommended)
   - Force HTTPS redirects

5. **Set secure file permissions**:
   ```bash
   chmod 750 /path/to/happypuppy
   chmod 640 /path/to/happypuppy/includes/config.php
   ```

### Performance Optimization

1. **Enable PHP OPcache**:
   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.max_accelerated_files=10000
   ```

2. **MySQL optimization**:
   - Index frequently queried columns
   - Use connection pooling
   - Regular database optimization

3. **Enable compression**:
   ```apache
   # In Apache config
   <IfModule mod_deflate.c>
       AddOutputFilterByType DEFLATE text/html text/css application/javascript
   </IfModule>
   ```

## Getting Help

If you encounter issues:

1. Check PHP error logs: `/var/log/apache2/error.log`
2. Check MySQL error logs: `/var/log/mysql/error.log`
3. Enable debug mode temporarily
4. Consult the README.md for feature documentation

## Next Steps

After successful installation:

1. Change default admin password
2. Add your own products
3. Customize branding and colors
4. Set up regular database backups
5. Configure email notifications (future feature)

Enjoy using HappyPuppy!