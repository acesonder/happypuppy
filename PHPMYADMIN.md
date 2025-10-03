# PHPMyAdmin Configuration Guide

## Accessing PHPMyAdmin

PHPMyAdmin provides a web-based interface for managing MySQL databases.

### Default URLs

**XAMPP (Windows)**:
```
http://localhost/phpmyadmin
```

**Ubuntu/Debian**:
```
http://localhost/phpmyadmin
```

**macOS (Homebrew)**:
```
http://localhost:8080/phpmyadmin
```

## Login Credentials

For **development environment** as specified:

- **Username**: `root`
- **Password**: (leave blank)

**⚠️ SECURITY WARNING**: This configuration is ONLY for local development. Never use blank passwords in production!

## Initial Setup in PHPMyAdmin

### 1. Create Database

1. Click **"New"** in the left sidebar
2. Enter database name: `happypuppy`
3. Collation: `utf8mb4_unicode_ci`
4. Click **"Create"**

### 2. Import Schema

1. Select the `happypuppy` database from left sidebar
2. Click **"Import"** tab at the top
3. Click **"Choose File"**
4. Select: `/path/to/happypuppy/database/schema.sql`
5. Click **"Go"** at the bottom
6. Wait for success message

### 3. Import Seed Data

1. Still in `happypuppy` database
2. Click **"Import"** tab
3. Choose file: `/path/to/happypuppy/database/seed.sql`
4. Click **"Go"**
5. Verify: Should see message "2 rows affected" for users table

### 4. Verify Tables

Click on `happypuppy` database in left sidebar. You should see 7 tables:

- ✅ `users` (2 rows)
- ✅ `products` (10 rows)
- ✅ `orders` (0 rows initially)
- ✅ `order_items` (0 rows initially)
- ✅ `messages` (0 rows initially)
- ✅ `notifications` (0 rows initially)
- ✅ `check_ins` (0 rows initially)

## Common PHPMyAdmin Tasks

### View Table Structure

1. Click on table name (e.g., `users`)
2. Click **"Structure"** tab
3. See columns, types, indexes

### Browse Table Data

1. Click on table name
2. Click **"Browse"** tab
3. View all rows

### Run SQL Queries

1. Click **"SQL"** tab at top
2. Enter your SQL query
3. Click **"Go"**

Example queries:

```sql
-- View all users
SELECT * FROM users;

-- View all products
SELECT * FROM products;

-- Check admin password hash
SELECT email, password FROM users WHERE role = 'admin';

-- Count orders by status
SELECT status, COUNT(*) as count FROM orders GROUP BY status;
```

### Edit Data

1. Click table name
2. Click **"Browse"** tab
3. Click **"Edit"** icon (pencil) next to row
4. Modify values
5. Click **"Go"** to save

### Add New Product

1. Click on `products` table
2. Click **"Insert"** tab
3. Fill in form:
   - `name`: Product name
   - `description`: Description
   - `category`: Category name
   - `quantity`: Number available
   - `unit`: Unit (e.g., "packs", "doses")
   - `safety_info`: Safety instructions
   - `is_available`: 1 (checked)
4. Click **"Go"**

### Change User Password

1. Click on `users` table
2. Click **"Browse"**
3. Find user and click **"Edit"**
4. In `password` field, enter: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`
   - This is the hash for: `user123`
5. Click **"Go"**

**Or use SQL**:

```sql
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'admin@happypuppy.com';
```

## Backup Database

### Using PHPMyAdmin

1. Select `happypuppy` database
2. Click **"Export"** tab
3. Method: **Quick** (or Custom for more options)
4. Format: **SQL**
5. Click **"Go"**
6. File downloads as `happypuppy.sql`

### Schedule Regular Backups

Create a backup script (Linux/Mac):

```bash
#!/bin/bash
# backup-db.sh
mysqldump -u root happypuppy > backup_$(date +%Y%m%d_%H%M%S).sql
```

Make executable and run:

```bash
chmod +x backup-db.sh
./backup-db.sh
```

Add to crontab for daily backups:

```bash
# Edit crontab
crontab -e

# Add line for daily backup at 2 AM
0 2 * * * /path/to/backup-db.sh
```

## Restore Database

### Using PHPMyAdmin

1. Select `happypuppy` database
2. Click **"Import"** tab
3. Choose your backup `.sql` file
4. Click **"Go"**

### Using Command Line

```bash
mysql -u root happypuppy < backup_file.sql
```

## Troubleshooting

### "Import file too large"

Edit PHP configuration:

**XAMPP**: Edit `C:\xampp\php\php.ini`

**Linux**: Edit `/etc/php/7.4/apache2/php.ini`

Change:
```ini
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
```

Restart Apache.

### Cannot Access PHPMyAdmin

1. Check Apache is running
2. Verify PHPMyAdmin is installed
3. For Ubuntu: `sudo apt install phpmyadmin`
4. Check PHPMyAdmin config: `/etc/phpmyadmin/config.inc.php`

### Access Denied

For production, create dedicated user:

```sql
CREATE USER 'happypuppy_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON happypuppy.* TO 'happypuppy_user'@'localhost';
FLUSH PRIVILEGES;
```

Update `/includes/config.php`:

```php
define('DB_USER', 'happypuppy_user');
define('DB_PASS', 'strong_password');
```

## Security Best Practices

### For Production

1. **Never use root with blank password**
2. **Create dedicated database user** with limited privileges
3. **Use strong passwords**
4. **Restrict PHPMyAdmin access** to specific IPs
5. **Enable HTTPS** for PHPMyAdmin
6. **Keep PHPMyAdmin updated**
7. **Consider alternative** admin tools for production

### Restrict PHPMyAdmin Access

Edit Apache config:

```apache
<Directory /usr/share/phpmyadmin>
    Require ip 192.168.1.0/24  # Your network
    # Or specific IP: Require ip 192.168.1.100
</Directory>
```

## Alternative Database Tools

- **MySQL Workbench**: Desktop GUI application
- **DBeaver**: Universal database tool
- **Adminer**: Lightweight PHPMyAdmin alternative (single PHP file)
- **Command Line**: `mysql` CLI tool

## Quick Reference

| Task | SQL Command |
|------|-------------|
| List databases | `SHOW DATABASES;` |
| Use database | `USE happypuppy;` |
| List tables | `SHOW TABLES;` |
| Describe table | `DESCRIBE users;` |
| Count rows | `SELECT COUNT(*) FROM users;` |
| Show indexes | `SHOW INDEX FROM products;` |
| Optimize table | `OPTIMIZE TABLE orders;` |

## Support

For more PHPMyAdmin help:
- Official docs: https://docs.phpmyadmin.net/
- User guide: https://www.phpmyadmin.net/docs/
