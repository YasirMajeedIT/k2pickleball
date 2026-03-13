# K2 Pickleball - Deployment Guide

## Requirements

- **PHP**: 8.3+
- **MySQL**: 8.0+
- **Apache**: 2.4+ with `mod_rewrite` enabled
- **Composer**: 2.x
- **PHP Extensions**: `pdo`, `pdo_mysql`, `json`, `mbstring`, `openssl`, `fileinfo`, `curl`

## Local Development (XAMPP)

### 1. Clone & Install

```bash
cd C:\xampp_new\htdocs
git clone <repo-url> k2pickleball
cd k2pickleball
composer install
```

### 2. Environment Configuration

```bash
copy .env.example .env
```

Edit `.env` with your settings:
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/k2pickleball

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=k2pickleball
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=your-random-64-char-secret

SQUARE_ENVIRONMENT=sandbox
SQUARE_ACCESS_TOKEN=your-sandbox-token
SQUARE_APPLICATION_ID=your-app-id
SQUARE_LOCATION_ID=your-location-id
```

### 3. Database Setup

```bash
php database/migrate.php
php database/seed.php
```

### 4. Apache Virtual Host (Optional)

Add to `C:\xampp_new\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName k2pickleball.local
    ServerAlias *.k2pickleball.local
    DocumentRoot "C:/xampp_new/htdocs/k2pickleball/public"
    
    <Directory "C:/xampp_new/htdocs/k2pickleball/public">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/k2pickleball-error.log"
    CustomLog "logs/k2pickleball-access.log" common
</VirtualHost>
```

Add to `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 k2pickleball.local
127.0.0.1 admin.k2pickleball.local
127.0.0.1 api.k2pickleball.local
127.0.0.1 platform.k2pickleball.local
127.0.0.1 demo-sports-club.k2pickleball.local
```

### 5. Access

- **Admin Panel**: http://localhost/k2pickleball/admin/login
- **Platform Panel**: http://localhost/k2pickleball/platform
- **API**: http://localhost/k2pickleball/api/health
- **Login**: admin@k2pickleball.com / K2Admin!2024

---

## Production Deployment

### Server Setup

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install php8.3 php8.3-mysql php8.3-mbstring php8.3-json php8.3-curl php8.3-fileinfo
sudo apt install apache2 libapache2-mod-php8.3
sudo apt install mysql-server

sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Application

```bash
cd /var/www
git clone <repo-url> k2pickleball
cd k2pickleball
composer install --no-dev --optimize-autoloader

cp .env.example .env
# Edit .env with production values
```

### Production .env

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=k2pickleball
DB_USERNAME=k2_prod_user
DB_PASSWORD=strong-random-password

JWT_SECRET=generate-a-64-char-random-string
JWT_ALGORITHM=HS256

SQUARE_ENVIRONMENT=production
SQUARE_ACCESS_TOKEN=your-production-token
SQUARE_APPLICATION_ID=your-prod-app-id
SQUARE_LOCATION_ID=your-prod-location-id
SQUARE_WEBHOOK_SIGNATURE_KEY=your-webhook-key
```

### Apache Config

```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias *.yourdomain.com
    DocumentRoot /var/www/k2pickleball/public

    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem

    <Directory /var/www/k2pickleball/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Prevent direct access to app files
    <DirectoryMatch "/var/www/k2pickleball/(app|config|database|storage|vendor)">
        Require all denied
    </DirectoryMatch>
</VirtualHost>
```

### Database

```bash
php database/migrate.php
php database/seed.php
```

### File Permissions

```bash
chown -R www-data:www-data /var/www/k2pickleball
chmod -R 755 /var/www/k2pickleball
chmod -R 775 /var/www/k2pickleball/storage
```

### Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `JWT_SECRET` (64+ random characters)
- [ ] MySQL user with minimal permissions
- [ ] SSL/TLS enabled
- [ ] Firewall configured (only 80/443 open)
- [ ] `storage/` directory not web-accessible
- [ ] Regular database backups configured
- [ ] Log rotation configured
- [ ] Square webhook signature verification enabled
