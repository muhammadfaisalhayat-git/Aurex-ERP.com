# Aurex ERP Deployment Guide

## Table of Contents
1. [Local Deployment (Docker)](#local-deployment-docker)
2. [Hostinger Deployment](#hostinger-deployment)
3. [GoDaddy Deployment](#godaddy-deployment)
4. [Troubleshooting](#troubleshooting)

---

## Local Deployment (Docker) - RECOMMENDED

This is the easiest and fastest way to deploy Aurex ERP locally.

### Prerequisites
- Docker Desktop (Windows/Mac) or Docker Engine (Linux)
- Docker Compose
- At least 4GB RAM available
- 10GB free disk space

### Quick Start

#### Windows
1. Open PowerShell as Administrator
2. Navigate to the project folder:
   ```powershell
   cd C:\path\to\aurex-erp
   ```
3. Run the deployment script:
   ```powershell
   .\deploy.ps1
   ```

#### macOS/Linux
1. Open Terminal
2. Navigate to the project folder:
   ```bash
   cd /path/to/aurex-erp
   ```
3. Make the script executable and run:
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```

### Manual Docker Deployment

If the scripts don't work, you can deploy manually:

```bash
# Build and start all services
docker-compose up --build -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f

# Stop services
docker-compose down
```

### Access the Application

After deployment:
- **URL**: http://localhost:8080
- **Email**: admin@aurex.com
- **Password**: admin123

### Default Demo Data

The system comes pre-loaded with:
- 5 branches
- 8 warehouses
- 50+ inventory items
- 10+ customers
- 8+ suppliers
- Sample quotations, sales orders, invoices
- Sample purchase orders, supply orders
- Sample local purchases
- Sample supplier and customer registrations

---

## Hostinger Deployment

### Prerequisites
- Hostinger Business or higher plan (requires SSH access)
- Domain name (optional but recommended)
- PHP 8.2+ support
- PostgreSQL database

### Step 1: Prepare Your Hostinger Account

1. Log in to your Hostinger control panel
2. Go to **Advanced** → **PHP Configuration**
3. Set PHP version to **8.2** or higher
4. Enable these PHP extensions:
   - pdo_pgsql
   - pgsql
   - mbstring
   - openssl
   - tokenizer
   - xml
   - ctype
   - json
   - bcmath
   - zip
   - gd

### Step 2: Create PostgreSQL Database

1. In Hostinger panel, go to **Databases** → **PostgreSQL**
2. Create a new database:
   - Database name: `aurex_erp`
   - Username: `aurex_user`
   - Password: (generate a strong password)
3. Note down the database credentials

### Step 3: Upload Files

#### Option A: Using Git (Recommended if available)
```bash
# SSH into your Hostinger account
ssh u123456789@your-domain.com

# Navigate to public_html
cd ~/public_html

# Clone the repository
git clone https://your-repo-url.git .
```

#### Option B: Using File Manager
1. In Hostinger panel, go to **Files** → **File Manager**
2. Navigate to `public_html`
3. Upload the `aurex-erp.zip` file
4. Extract the archive

#### Option C: Using FTP
1. Use an FTP client (FileZilla, WinSCP)
2. Connect to your Hostinger FTP account
3. Upload all files to `public_html`

### Step 4: Configure Environment

1. In File Manager, navigate to `public_html`
2. Find `.env.example` and rename it to `.env`
3. Edit `.env` file with your database credentials:

```env
APP_NAME="Aurex ERP"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=your-postgres-host
DB_PORT=5432
DB_DATABASE=aurex_erp
DB_USERNAME=aurex_user
DB_PASSWORD=your-database-password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync
```

4. Generate APP_KEY:
   - SSH into your account
   - Run: `php artisan key:generate`

### Step 5: Set Up Public Directory

1. In File Manager, move all files from `public` folder to `public_html`
2. Or create a subdomain pointing to the `public` folder

### Step 6: Configure .htaccess

Create or edit `.htaccess` in `public_html`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to public folder
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ /public/$1 [L]
</IfModule>

# PHP Settings
php_value upload_max_filesize 64M
php_value post_max_size 64M
php_value max_execution_time 300
php_value max_input_time 300
```

### Step 7: Set Permissions

Via SSH:
```bash
cd ~/public_html
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 644 bootstrap/app.php
```

### Step 8: Install Dependencies & Run Migrations

Via SSH:
```bash
cd ~/public_html

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Seed database with demo data
php artisan db:seed --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 9: Access Your ERP

- **URL**: https://your-domain.com
- **Email**: admin@aurex.com
- **Password**: admin123

---

## GoDaddy Deployment

### Prerequisites
- GoDaddy Deluxe Linux Hosting or higher
- cPanel access
- SSH access (may need to enable)

### Step 1: Enable SSH Access

1. Log in to your GoDaddy account
2. Go to **Web Hosting** → **Manage**
3. Click **Settings** → **Server**
4. Enable SSH access
5. Note the SSH credentials

### Step 2: Check PHP Version

1. In cPanel, go to **Select PHP Version**
2. Set to PHP 8.2 or higher
3. Enable these extensions:
   - pdo_pgsql
   - pgsql
   - mbstring
   - openssl
   - tokenizer
   - xml
   - ctype
   - json
   - bcmath
   - zip
   - gd

### Step 3: Create PostgreSQL Database

**Note**: GoDaddy shared hosting may not support PostgreSQL. You have options:

#### Option A: Use External PostgreSQL (Recommended)
- Use a service like:
  - ElephantSQL (free tier available)
  - AWS RDS PostgreSQL
  - Google Cloud SQL
  - DigitalOcean Managed Databases

#### Option B: Use MySQL (Requires Code Modification)
If you must use GoDaddy's MySQL, you'll need to:
1. Convert all migrations from PostgreSQL to MySQL syntax
2. Update `config/database.php`
3. Change `DB_CONNECTION=mysql`

### Step 4: Upload Files

#### Using File Manager
1. Log in to cPanel
2. Go to **File Manager**
3. Navigate to `public_html`
4. Upload `aurex-erp.zip`
5. Extract the archive

#### Using FTP
1. Use FileZilla or similar
2. Connect with your FTP credentials
3. Upload to `public_html`

### Step 5: Configure Environment

1. In File Manager, rename `.env.example` to `.env`
2. Edit with your database credentials:

```env
APP_NAME="Aurex ERP"
APP_ENV=production
APP_KEY=base64:your-key
APP_DEBUG=false
APP_URL=https://your-domain.com

# If using external PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=your-external-db-host
DB_PORT=5432
DB_DATABASE=aurex_erp
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

# Or if using MySQL (requires code changes)
# DB_CONNECTION=mysql
# DB_HOST=localhost
# DB_PORT=3306
# DB_DATABASE=aurex_erp
# DB_USERNAME=your-mysql-user
# DB_PASSWORD=your-mysql-password
```

### Step 6: Set Up Public Directory

Create `.htaccess` in `public_html`:

```apache
RewriteEngine On

# Point to Laravel's public folder
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ /public/$1 [L]

# Handle Laravel routing
<IfModule mod_rewrite.c>
    <Directory /public_html/public>
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    </Directory>
</IfModule>
```

### Step 7: Set Permissions

Via SSH or File Manager:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Step 8: Install & Configure

Via SSH:
```bash
cd ~/public_html

# Install dependencies (if composer is available)
composer install --no-dev --optimize-autoloader

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed data
php artisan db:seed --force

# Cache
php artisan config:cache
php artisan route:cache
```

### Step 9: Access ERP

- **URL**: https://your-domain.com
- **Email**: admin@aurex.com
- **Password**: admin123

---

## Troubleshooting

### 404 Not Found Error

**Cause**: Apache mod_rewrite not enabled or .htaccess not configured

**Solution**:
1. Ensure `.htaccess` exists in public folder
2. Check Apache configuration allows .htaccess overrides
3. Verify mod_rewrite is enabled

### 500 Internal Server Error

**Cause**: Usually PHP or permission issues

**Solution**:
1. Check PHP version (must be 8.2+)
2. Check error logs: `storage/logs/laravel.log`
3. Verify file permissions (755 for directories, 644 for files)
4. Check .env file exists and is readable

### Database Connection Error

**Cause**: Incorrect database credentials

**Solution**:
1. Verify DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env
2. Check if database exists
3. Verify database user has proper permissions
4. For external databases, check firewall rules

### Permission Denied Errors

**Solution**:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 644 bootstrap/app.php
chown -R www-data:www-data .  # On Ubuntu/Debian
```

### Composer Memory Limit Error

**Solution**:
```bash
php -d memory_limit=2G composer install --no-dev
```

### Class Not Found Errors

**Solution**:
```bash
composer dump-autoload
php artisan optimize:clear
```

### Demo Data Not Showing

**Solution**:
```bash
php artisan migrate:fresh --seed --force
```

---

## Support

For deployment issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Apache/Nginx error logs
3. Verify all prerequisites are met
4. Ensure database is accessible

---

## Security Recommendations

1. **Change default password** immediately after first login
2. **Use HTTPS** for production deployments
3. **Regular backups** of database and files
4. **Keep dependencies updated**
5. **Use strong database passwords**
6. **Restrict database access** to application IP only
7. **Enable 2FA** for admin accounts

---

## Performance Optimization

For production deployments:

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize

# Enable OPcache in php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
```

---

**Last Updated**: 2024
**Version**: 1.0.0
