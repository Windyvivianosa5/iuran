# 🚀 Deployment Guide - Sistem Iuran PGRI
## Production Deployment Step-by-Step

---

## 📋 Table of Contents

1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Server Requirements](#server-requirements)
3. [Server Setup](#server-setup)
4. [Application Deployment](#application-deployment)
5. [Database Setup](#database-setup)
6. [Web Server Configuration](#web-server-configuration)
7. [SSL Certificate Setup](#ssl-certificate-setup)
8. [Midtrans Production Setup](#midtrans-production-setup)
9. [Email Configuration](#email-configuration)
10. [Performance Optimization](#performance-optimization)
11. [Monitoring & Logging](#monitoring--logging)
12. [Backup Strategy](#backup-strategy)
13. [Troubleshooting](#troubleshooting)

---

## ✅ Pre-Deployment Checklist

### Code Preparation
- [ ] All tests passing
- [ ] Code reviewed and approved
- [ ] Environment variables documented
- [ ] Database migrations tested
- [ ] Seeders prepared (if needed)
- [ ] Assets compiled (`npm run build`)
- [ ] Dependencies updated
- [ ] Security vulnerabilities checked

### Infrastructure
- [ ] Server provisioned
- [ ] Domain registered
- [ ] DNS configured
- [ ] SSL certificate ready
- [ ] Backup system configured
- [ ] Monitoring tools setup

### Third-Party Services
- [ ] Midtrans production account created
- [ ] Production API keys obtained
- [ ] Email service configured (SMTP/SendGrid)
- [ ] CDN configured (optional)

---

## 💻 Server Requirements

### Minimum Requirements

```yaml
Operating System: Ubuntu 22.04 LTS (Recommended)
CPU: 2 vCPU
RAM: 2 GB
Storage: 20 GB SSD
Network: 100 Mbps
```

### Recommended for Production

```yaml
Operating System: Ubuntu 22.04 LTS
CPU: 4 vCPU
RAM: 4 GB
Storage: 50 GB SSD
Network: 1 Gbps
Backup: Daily automated backups
```

### Software Requirements

```yaml
PHP: 8.3 or higher
Composer: 2.x
Node.js: 20.x LTS
NPM: 10.x
MySQL: 8.0 or MariaDB 10.5+
Nginx: 1.24+ or Apache 2.4+
Redis: 7.x (optional, for caching)
Supervisor: 4.x (for queue workers)
```

---

## 🔧 Server Setup

### 1. Update System

```bash
# Connect to server
ssh root@your-server-ip

# Update system packages
sudo apt update && sudo apt upgrade -y

# Install basic utilities
sudo apt install -y curl wget git unzip software-properties-common
```

### 2. Install PHP 8.3

```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP and extensions
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-zip php8.3-gd php8.3-mbstring \
    php8.3-curl php8.3-xml php8.3-bcmath php8.3-intl \
    php8.3-redis php8.3-opcache

# Verify installation
php -v
```

### 3. Install Composer

```bash
# Download and install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Verify installation
composer --version
```

### 4. Install Node.js & NPM

```bash
# Install Node.js 20.x LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verify installation
node -v
npm -v
```

### 5. Install MySQL

```bash
# Install MySQL Server
sudo apt install -y mysql-server

# Secure MySQL installation
sudo mysql_secure_installation

# Login to MySQL
sudo mysql -u root -p
```

**Create Database:**
```sql
CREATE DATABASE iuran_pgri CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pgri_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON iuran_pgri.* TO 'pgri_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 6. Install Nginx

```bash
# Install Nginx
sudo apt install -y nginx

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Check status
sudo systemctl status nginx
```

### 7. Install Redis (Optional, for Caching)

```bash
# Install Redis
sudo apt install -y redis-server

# Start and enable Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Test Redis
redis-cli ping
# Should return: PONG
```

### 8. Install Supervisor (for Queue Workers)

```bash
# Install Supervisor
sudo apt install -y supervisor

# Start and enable Supervisor
sudo systemctl start supervisor
sudo systemctl enable supervisor
```

---

## 📦 Application Deployment

### 1. Create Application Directory

```bash
# Create directory for application
sudo mkdir -p /var/www/iuran-pgri
sudo chown -R $USER:$USER /var/www/iuran-pgri

# Navigate to directory
cd /var/www/iuran-pgri
```

### 2. Clone Repository

**Option A: Using Git (Recommended)**
```bash
# Clone from GitHub
git clone https://github.com/your-username/iuran-pgri-main.git .

# Or if using private repository
git clone git@github.com:your-username/iuran-pgri-main.git .
```

**Option B: Upload Files**
```bash
# From local machine, upload files using SCP
scp -r /path/to/local/iuran-pgri-main/* user@server-ip:/var/www/iuran-pgri/
```

### 3. Install PHP Dependencies

```bash
cd /var/www/iuran-pgri

# Install Composer dependencies (production only)
composer install --optimize-autoloader --no-dev

# If you need dev dependencies for debugging
# composer install --optimize-autoloader
```

### 4. Install Node Dependencies & Build Assets

```bash
# Install NPM dependencies
npm ci --production

# Build production assets
npm run build

# Clean up node_modules to save space (optional)
rm -rf node_modules
```

### 5. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit environment file
nano .env
```

**Production `.env` Configuration:**
```env
APP_NAME="Sistem Iuran PGRI"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://iuran.pgri.or.id

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iuran_pgri
DB_USERNAME=pgri_user
DB_PASSWORD=strong_password_here

# Midtrans Production
MIDTRANS_SERVER_KEY=your_production_server_key
MIDTRANS_CLIENT_KEY=your_production_client_key
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true

# Mail Configuration (Example: Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@pgri.or.id
MAIL_FROM_NAME="${APP_NAME}"

# Cache & Session (Redis)
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 6. Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/iuran-pgri

# Set directory permissions
sudo find /var/www/iuran-pgri -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/iuran-pgri -type f -exec chmod 644 {} \;

# Set storage and cache permissions
sudo chmod -R 775 /var/www/iuran-pgri/storage
sudo chmod -R 775 /var/www/iuran-pgri/bootstrap/cache
```

---

## 🗄️ Database Setup

### 1. Run Migrations

```bash
cd /var/www/iuran-pgri

# Run migrations
php artisan migrate --force

# Verify migrations
php artisan migrate:status
```

### 2. Seed Database (Optional)

```bash
# Seed initial data (admin user, etc)
php artisan db:seed --force

# Or specific seeder
php artisan db:seed --class=AdminUserSeeder --force
```

### 3. Optimize Database

```bash
# Optimize database tables
php artisan db:optimize

# Or manually in MySQL
mysql -u pgri_user -p iuran_pgri -e "OPTIMIZE TABLE users, transactions, iurans;"
```

---

## 🌐 Web Server Configuration

### Nginx Configuration

**Create Nginx configuration file:**
```bash
sudo nano /etc/nginx/sites-available/iuran-pgri
```

**Configuration:**
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name iuran.pgri.or.id www.iuran.pgri.or.id;
    
    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name iuran.pgri.or.id www.iuran.pgri.or.id;
    
    root /var/www/iuran-pgri/public;
    index index.php index.html;
    
    # SSL Configuration (will be added by Certbot)
    ssl_certificate /etc/letsencrypt/live/iuran.pgri.or.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/iuran.pgri.or.id/privkey.pem;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    
    # Logging
    access_log /var/log/nginx/iuran-pgri-access.log;
    error_log /var/log/nginx/iuran-pgri-error.log;
    
    # Client max body size (for file uploads)
    client_max_body_size 10M;
    
    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
    
    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

**Enable site:**
```bash
# Create symbolic link
sudo ln -s /etc/nginx/sites-available/iuran-pgri /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

---

## 🔒 SSL Certificate Setup

### Using Let's Encrypt (Free)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d iuran.pgri.or.id -d www.iuran.pgri.or.id

# Follow prompts:
# - Enter email address
# - Agree to terms
# - Choose to redirect HTTP to HTTPS (recommended)

# Test auto-renewal
sudo certbot renew --dry-run

# Auto-renewal is configured via cron/systemd timer
```

**Verify SSL:**
```bash
# Check certificate
sudo certbot certificates

# Test SSL configuration
curl -I https://iuran.pgri.or.id
```

---

## 💳 Midtrans Production Setup

### 1. Create Production Account

1. Login to https://dashboard.midtrans.com/
2. Switch to **Production** environment
3. Go to **Settings** → **Access Keys**
4. Copy **Server Key** and **Client Key**

### 2. Configure Webhook URL

1. Go to **Settings** → **Configuration**
2. Set **Payment Notification URL**:
   ```
   https://iuran.pgri.or.id/user/payment/callback
   ```
3. Set **Finish Redirect URL** (optional):
   ```
   https://iuran.pgri.or.id/kabupaten/iuran
   ```
4. Set **Error Redirect URL** (optional):
   ```
   https://iuran.pgri.or.id/kabupaten/iuran
   ```
5. Save configuration

### 3. Update Environment

```bash
# Edit .env
nano /var/www/iuran-pgri/.env
```

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-YOUR_PRODUCTION_KEY
MIDTRANS_CLIENT_KEY=SB-Mid-client-YOUR_PRODUCTION_KEY
MIDTRANS_IS_PRODUCTION=true
```

### 4. Test Production Payment

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Test with small amount (Rp 10,000)
# Use real payment method
```

---

## 📧 Email Configuration

### Option 1: Gmail SMTP

**Setup App Password:**
1. Go to Google Account → Security
2. Enable 2-Step Verification
3. Generate App Password
4. Copy password

**Configure `.env`:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your_16_char_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@pgri.or.id
MAIL_FROM_NAME="Sistem Iuran PGRI"
```

### Option 2: SendGrid (Recommended for Production)

**Setup:**
1. Create account at https://sendgrid.com
2. Create API Key
3. Verify sender email

**Configure `.env`:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@pgri.or.id
MAIL_FROM_NAME="Sistem Iuran PGRI"
```

### Test Email

```bash
# Test email sending
php artisan tinker

# In tinker:
Mail::raw('Test email', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});
```

---

## ⚡ Performance Optimization

### 1. Laravel Optimization

```bash
cd /var/www/iuran-pgri

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize --classmap-authoritative

# Clear all caches (if needed)
php artisan optimize:clear
```

### 2. PHP-FPM Optimization

```bash
# Edit PHP-FPM pool configuration
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```

**Optimize settings:**
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

**Restart PHP-FPM:**
```bash
sudo systemctl restart php8.3-fpm
```

### 3. OPcache Configuration

```bash
# Edit PHP configuration
sudo nano /etc/php/8.3/fpm/php.ini
```

**Enable OPcache:**
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

### 4. MySQL Optimization

```bash
# Edit MySQL configuration
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

**Add optimizations:**
```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
max_connections = 200
query_cache_size = 0
query_cache_type = 0
```

**Restart MySQL:**
```bash
sudo systemctl restart mysql
```

### 5. Setup Queue Worker

**Create Supervisor configuration:**
```bash
sudo nano /etc/supervisor/conf.d/iuran-pgri-worker.conf
```

**Configuration:**
```ini
[program:iuran-pgri-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/iuran-pgri/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/iuran-pgri/storage/logs/worker.log
stopwaitsecs=3600
```

**Start worker:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start iuran-pgri-worker:*
```

---

## 📊 Monitoring & Logging

### 1. Setup Log Rotation

```bash
# Create logrotate configuration
sudo nano /etc/logrotate.d/iuran-pgri
```

**Configuration:**
```
/var/www/iuran-pgri/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### 2. Monitor Application Logs

```bash
# Real-time log monitoring
tail -f /var/www/iuran-pgri/storage/logs/laravel.log

# Check Nginx logs
tail -f /var/log/nginx/iuran-pgri-access.log
tail -f /var/log/nginx/iuran-pgri-error.log
```

### 3. System Monitoring

```bash
# Install monitoring tools
sudo apt install -y htop iotop nethogs

# Monitor system resources
htop

# Monitor disk usage
df -h

# Monitor MySQL
mysqladmin -u root -p processlist
```

### 4. Setup Uptime Monitoring

**Recommended Services:**
- UptimeRobot (free)
- Pingdom
- StatusCake

**Monitor endpoints:**
- https://iuran.pgri.or.id (main page)
- https://iuran.pgri.or.id/login (application)

---

## 💾 Backup Strategy

### 1. Database Backup Script

```bash
# Create backup directory
sudo mkdir -p /var/backups/iuran-pgri

# Create backup script
sudo nano /usr/local/bin/backup-iuran-pgri.sh
```

**Backup script:**
```bash
#!/bin/bash

# Configuration
DB_NAME="iuran_pgri"
DB_USER="pgri_user"
DB_PASS="strong_password_here"
BACKUP_DIR="/var/backups/iuran-pgri"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete

# Backup application files
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz \
    /var/www/iuran-pgri/storage/app \
    /var/www/iuran-pgri/.env

echo "Backup completed: $DATE"
```

**Make executable:**
```bash
sudo chmod +x /usr/local/bin/backup-iuran-pgri.sh
```

### 2. Setup Cron Job

```bash
# Edit crontab
sudo crontab -e
```

**Add daily backup at 2 AM:**
```cron
0 2 * * * /usr/local/bin/backup-iuran-pgri.sh >> /var/log/iuran-pgri-backup.log 2>&1
```

### 3. Test Backup

```bash
# Run backup manually
sudo /usr/local/bin/backup-iuran-pgri.sh

# Verify backup
ls -lh /var/backups/iuran-pgri/
```

### 4. Restore from Backup

```bash
# Restore database
gunzip < /var/backups/iuran-pgri/db_backup_YYYYMMDD_HHMMSS.sql.gz | \
    mysql -u pgri_user -p iuran_pgri

# Restore files
tar -xzf /var/backups/iuran-pgri/files_backup_YYYYMMDD_HHMMSS.tar.gz -C /
```

---

## 🔧 Troubleshooting

### Issue: 500 Internal Server Error

**Solution:**
```bash
# Check Laravel logs
tail -f /var/www/iuran-pgri/storage/logs/laravel.log

# Check Nginx error logs
tail -f /var/log/nginx/iuran-pgri-error.log

# Check PHP-FPM logs
tail -f /var/log/php8.3-fpm.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Issue: Webhook Not Working

**Solution:**
```bash
# Check webhook logs
tail -f /var/www/iuran-pgri/storage/logs/laravel.log | grep webhook

# Test webhook endpoint
curl -X POST https://iuran.pgri.or.id/user/payment/callback \
    -H "Content-Type: application/json" \
    -d '{"order_id":"test"}'

# Verify Midtrans configuration
php artisan tinker
config('midtrans.server_key')
```

### Issue: Email Not Sending

**Solution:**
```bash
# Test SMTP connection
php artisan tinker

# Test email
Mail::raw('Test', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});

# Check mail logs
tail -f /var/www/iuran-pgri/storage/logs/laravel.log | grep mail
```

### Issue: Queue Not Processing

**Solution:**
```bash
# Check supervisor status
sudo supervisorctl status

# Restart queue worker
sudo supervisorctl restart iuran-pgri-worker:*

# Check worker logs
tail -f /var/www/iuran-pgri/storage/logs/worker.log
```

### Issue: High Memory Usage

**Solution:**
```bash
# Check memory usage
free -h

# Optimize PHP-FPM
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
# Reduce pm.max_children

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm

# Clear application cache
php artisan cache:clear
```

---

## 🎯 Post-Deployment Checklist

- [ ] Application accessible via HTTPS
- [ ] SSL certificate valid
- [ ] Database migrations completed
- [ ] Admin user created
- [ ] Midtrans webhook working
- [ ] Email notifications working
- [ ] Queue workers running
- [ ] Backups configured and tested
- [ ] Monitoring setup
- [ ] Log rotation configured
- [ ] Performance optimizations applied
- [ ] Security headers configured
- [ ] Firewall rules configured
- [ ] Documentation updated

---

## 📞 Support & Maintenance

### Regular Maintenance Tasks

**Daily:**
- Monitor application logs
- Check system resources
- Verify backups

**Weekly:**
- Review error logs
- Check disk space
- Update dependencies (if needed)

**Monthly:**
- Security updates
- Performance review
- Database optimization
- Backup restoration test

---

**Last Updated:** 29 Desember 2025  
**Version:** 1.0  
**Status:** ✅ Production Ready
