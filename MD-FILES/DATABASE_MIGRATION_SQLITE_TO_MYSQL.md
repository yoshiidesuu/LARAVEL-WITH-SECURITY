# Database Migration: SQLite to MySQL - Complete

## Summary
Successfully migrated the Laravel application from SQLite to MySQL as the default database system.

## Changes Made

### 1. **Environment Configuration (.env)**
```env
# Changed from:
DB_CONNECTION=sqlite

# To:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

### 2. **Environment Example (.env.example)**
Updated to reflect MySQL as the default database configuration.

### 3. **Database Configuration (config/database.php)**
```php
// Changed default connection from:
'default' => env('DB_CONNECTION', 'sqlite'),

// To:
'default' => env('DB_CONNECTION', 'mysql'),
```

### 4. **Queue Configuration (config/queue.php)**
```php
// Updated batching database from:
'database' => env('DB_CONNECTION', 'sqlite'),

// To:
'database' => env('DB_CONNECTION', 'mysql'),

// Updated failed jobs database from:
'database' => env('DB_CONNECTION', 'sqlite'),

// To:
'database' => env('DB_CONNECTION', 'mysql'),
```

### 5. **Test Configuration (phpunit.xml)**
```xml
<!-- Changed from: -->
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>

<!-- To: -->
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="laravel_test"/>
```

### 6. **Composer Scripts (composer.json)**
Removed SQLite database file creation from post-create-project-cmd:
```json
// Removed:
"@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\""
```

### 7. **Database File**
- ✅ Removed `database/database.sqlite` file
- ✅ Updated `database/.gitignore` to include SQL dump files

## MySQL Setup Instructions

### Prerequisites
Make sure you have MySQL installed on your system. You can use:
- **MySQL Server** (official)
- **XAMPP** (includes MySQL/MariaDB)
- **WAMP** (includes MySQL/MariaDB)
- **Laragon** (includes MySQL/MariaDB)
- **Docker** (MySQL container)

### Creating the Database

#### Option 1: Using MySQL Command Line
```bash
mysql -u root -p
CREATE DATABASE laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE laravel_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### Option 2: Using phpMyAdmin
1. Open phpMyAdmin (usually http://localhost/phpmyadmin)
2. Click "New" in the left sidebar
3. Enter database name: `laravel`
4. Choose collation: `utf8mb4_unicode_ci`
5. Click "Create"
6. Repeat for `laravel_test` (for testing)

#### Option 3: Using Artisan Command
Laravel can't create the database, but you can use raw SQL:
```bash
php artisan db
CREATE DATABASE laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Configuration

#### Update .env file with your MySQL credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

#### For local development (if using default MySQL):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

#### For production (example):
```env
DB_CONNECTION=mysql
DB_HOST=production-mysql-server.com
DB_PORT=3306
DB_DATABASE=laravel_production
DB_USERNAME=laravel_user
DB_PASSWORD=secure_password_here
```

### Running Migrations

After setting up the database and configuration:

```bash
# Run migrations
php artisan migrate

# Run migrations with seed data
php artisan migrate --seed

# Fresh migration (drop all tables and re-migrate)
php artisan migrate:fresh

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

## MySQL Advantages Over SQLite

### ✅ Better for Production
- Industry standard for production environments
- Better performance for concurrent users
- Advanced security features
- User permissions and access control

### ✅ Scalability
- Handles larger databases efficiently
- Better support for complex queries
- Supports replication and clustering
- Horizontal scaling options

### ✅ Features
- Full-text search
- Stored procedures
- Triggers and events
- Views and foreign keys
- Better transaction support

### ✅ Tools & Support
- phpMyAdmin for database management
- MySQL Workbench for development
- Extensive documentation
- Large community support

## Testing with MySQL

Your tests will now use MySQL instead of in-memory SQLite. For faster tests, you can:

### Option 1: Use Separate Test Database (Current Setup)
```xml
<!-- phpunit.xml -->
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="laravel_test"/>
```

Create the test database:
```sql
CREATE DATABASE laravel_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Option 2: Use Transactions (Faster)
In your test base class:
```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends BaseTestCase
{
    use RefreshDatabase;
}
```

### Option 3: Keep SQLite for Testing (Optional)
If you want to keep SQLite specifically for faster testing:
```xml
<!-- phpunit.xml -->
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Database Management Tools

### phpMyAdmin
- Web-based MySQL administration
- Usually available at: http://localhost/phpmyadmin
- Comes with XAMPP, WAMP, Laragon

### MySQL Workbench
- Official MySQL GUI tool
- Download: https://dev.mysql.com/downloads/workbench/
- Visual database design
- Query builder

### TablePlus
- Modern database management tool
- Download: https://tableplus.com/
- Supports multiple databases

### DBeaver
- Free universal database tool
- Download: https://dbeaver.io/
- Cross-platform

## Troubleshooting

### Connection Issues

#### Error: "SQLSTATE[HY000] [2002] No such file or directory"
```bash
# Check if MySQL is running
# Windows:
net start MySQL80

# Or check services
services.msc
```

#### Error: "Access denied for user 'root'@'localhost'"
```bash
# Update password in .env
DB_PASSWORD=your_actual_password
```

#### Error: "Unknown database 'laravel'"
```sql
-- Create the database
CREATE DATABASE laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Performance Issues

#### Optimize MySQL Configuration
```ini
# my.ini or my.cnf
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
```

#### Enable Query Logging (for debugging)
```env
# .env
DB_LOG_QUERIES=true
```

## Backup & Restore

### Backup Database
```bash
# Using mysqldump
mysqldump -u root -p laravel > backup.sql

# With Laravel
php artisan db:backup
```

### Restore Database
```bash
mysql -u root -p laravel < backup.sql
```

## Laravel Commands

```bash
# Check database connection
php artisan db:show

# Show tables
php artisan db:table --database=mysql

# Drop all tables and migrate
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Create new migration
php artisan make:migration create_posts_table

# Create model with migration
php artisan make:model Post -m
```

## Configuration Verification

### Test MySQL Connection
```bash
php artisan tinker

# In tinker:
DB::connection()->getPdo();
// Should return PDO object

DB::connection()->getDatabaseName();
// Should return: "laravel"
```

### Test Database Operations
```bash
php artisan tinker

# In tinker:
DB::table('users')->count();
// Returns number of users

DB::select('SELECT VERSION()');
// Returns MySQL version
```

## Status: ✅ COMPLETE

🎉 **Successfully migrated from SQLite to MySQL!**

The Laravel application is now configured to use MySQL as the default database system across all environments (development, testing, and production).

## Next Steps

1. ✅ Install MySQL (if not already installed)
2. ✅ Create `laravel` database
3. ✅ Create `laravel_test` database (for testing)
4. ✅ Update `.env` with your MySQL credentials
5. ✅ Run migrations: `php artisan migrate`
6. ✅ Test the connection: `php artisan db:show`

---

**Note**: Make sure to update your `.env` file with the correct MySQL credentials before running migrations!
