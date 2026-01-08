# 📧 Setup SMTP untuk Email Notification

## 🎯 Pilihan SMTP Provider

Berikut beberapa pilihan SMTP provider yang bisa Anda gunakan:

### 1. **Gmail SMTP** (Recommended untuk Development)
- ✅ Gratis
- ✅ Mudah setup
- ✅ Limit: 500 email/hari
- ⚠️ Perlu App Password

### 2. **Mailtrap** (Recommended untuk Testing)
- ✅ Gratis untuk development
- ✅ Tidak kirim email real (testing only)
- ✅ Unlimited testing emails
- ✅ Email preview & debugging

### 3. **SendGrid** (Recommended untuk Production)
- ✅ Free tier: 100 email/hari
- ✅ Professional & reliable
- ✅ Analytics & tracking
- ✅ High deliverability

### 4. **Mailgun** (Alternative Production)
- ✅ Free tier: 5,000 email/bulan
- ✅ Good for transactional emails
- ✅ Easy integration

### 5. **SMTP Server Sendiri**
- ✅ Full control
- ⚠️ Perlu setup & maintenance
- ⚠️ Perlu SPF/DKIM configuration

---

## 🚀 Setup Gmail SMTP (Paling Mudah)

### Step 1: Enable 2-Step Verification

1. Buka https://myaccount.google.com/security
2. Scroll ke "Signing in to Google"
3. Klik "2-Step Verification"
4. Follow wizard untuk enable

### Step 2: Generate App Password

1. Masih di https://myaccount.google.com/security
2. Scroll ke "Signing in to Google"
3. Klik "App passwords"
4. Pilih app: **Mail**
5. Pilih device: **Other** → ketik "Laravel PGRI"
6. Klik **Generate**
7. **COPY** password yang muncul (16 karakter)

### Step 3: Update `.env`

```env
# Gmail SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pgri-riau.id"
MAIL_FROM_NAME="PGRI Riau"

# Admin Email
ADMIN_EMAIL=admin@pgri-riau.id
```

**Contoh:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=pgri.riau@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pgri-riau.id"
MAIL_FROM_NAME="PGRI Riau"
ADMIN_EMAIL=admin.pgri@gmail.com
```

### Step 4: Test Connection

```bash
php artisan tinker
```

```php
\Mail::raw('Test email dari PGRI', function($msg) {
    $msg->to('your-test-email@gmail.com')
        ->subject('Test SMTP Connection');
});
```

Check inbox Anda!

---

## 🧪 Setup Mailtrap (Untuk Testing/Development)

### Step 1: Daftar Mailtrap

1. Buka https://mailtrap.io
2. Sign up gratis
3. Verify email

### Step 2: Get Credentials

1. Login ke Mailtrap
2. Klik "Email Testing" → "Inboxes"
3. Pilih inbox (atau buat baru)
4. Tab "SMTP Settings"
5. Pilih "Laravel 9+"
6. Copy credentials

### Step 3: Update `.env`

```env
# Mailtrap SMTP Configuration (Development Only)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pgri-riau.id"
MAIL_FROM_NAME="PGRI Riau"
ADMIN_EMAIL=admin@pgri-riau.id
```

**Contoh:**
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=a1b2c3d4e5f6g7
MAIL_PASSWORD=h8i9j0k1l2m3n4
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pgri-riau.id"
MAIL_FROM_NAME="PGRI Riau"
ADMIN_EMAIL=admin@pgri-riau.id
```

### Step 4: Test

Semua email akan masuk ke Mailtrap inbox (tidak ke email real). Perfect untuk testing!

---

## 🏢 Setup SendGrid (Untuk Production)

### Step 1: Daftar SendGrid

1. Buka https://sendgrid.com
2. Sign up (Free tier available)
3. Verify email & phone

### Step 2: Create API Key

1. Login ke SendGrid
2. Settings → API Keys
3. Create API Key
4. Name: "PGRI Laravel"
5. Permissions: **Full Access**
6. Create & View
7. **COPY** API Key (hanya muncul sekali!)

### Step 3: Verify Sender Identity

1. Settings → Sender Authentication
2. Verify Single Sender
3. Isi form dengan email Anda
4. Verify email yang dikirim SendGrid

### Step 4: Update `.env`

```env
# SendGrid SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pgri-riau.id"
MAIL_FROM_NAME="PGRI Riau"
ADMIN_EMAIL=admin@pgri-riau.id
```

**Contoh:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.aBcDeFgHiJkLmNoPqRsTuVwXyZ123456789
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pgri-riau.id"
MAIL_FROM_NAME="PGRI Riau"
ADMIN_EMAIL=admin@pgri-riau.id
```

---

## 🔧 Setup SMTP Server Sendiri

### Jika Anda punya SMTP server sendiri:

```env
# Custom SMTP Server
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pgri-riau.id"
MAIL_FROM_NAME="PGRI Riau"
ADMIN_EMAIL=admin@pgri-riau.id
```

**Port Options:**
- **587** - TLS (Recommended)
- **465** - SSL
- **25** - No encryption (Not recommended)

**Encryption Options:**
- **tls** - Recommended
- **ssl** - Alternative
- **null** - No encryption (Not recommended)

---

## ✅ Verifikasi Setup

### 1. Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### 2. Test Email

```bash
php artisan tinker
```

```php
// Test simple email
\Mail::raw('Test SMTP dari PGRI Riau', function($message) {
    $message->to('your-email@example.com')
            ->subject('Test SMTP Configuration');
});

// Test dengan Mailable class
$transaction = \App\Models\Transaction::where('status', 'settlement')->first();

if ($transaction) {
    \Mail::to('your-email@example.com')->send(
        new \App\Mail\PaymentSuccessNotification($transaction)
    );
    echo "Email sent!\n";
} else {
    echo "No settlement transaction found\n";
}
```

### 3. Check Logs

```bash
tail -f storage/logs/laravel.log
```

Look for:
- ✅ "Email notifications sent for transaction: ..."
- ❌ "Failed to send email notifications: ..."

---

## 🚀 Jalankan Queue Worker

Karena email menggunakan `ShouldQueue`, wajib jalankan queue worker:

```bash
# Development
php artisan queue:work

# Atau dengan auto-reload
php artisan queue:listen

# Dengan verbose output
php artisan queue:work --verbose
```

**Untuk Production, gunakan Supervisor** (lihat section di bawah)

---

## 🔍 Troubleshooting

### ❌ Error: "Connection could not be established"

**Solusi:**
1. Check MAIL_HOST benar
2. Check MAIL_PORT benar (587 untuk TLS)
3. Check firewall tidak block port
4. Check internet connection

### ❌ Error: "Authentication failed"

**Solusi:**
1. Check MAIL_USERNAME benar
2. Check MAIL_PASSWORD benar
3. Untuk Gmail: pastikan pakai App Password (bukan password biasa)
4. Clear config cache: `php artisan config:clear`

### ❌ Email tidak terkirim (no error)

**Solusi:**
1. Check queue worker running: `ps aux | grep queue`
2. Check failed jobs: `php artisan queue:failed`
3. Retry failed jobs: `php artisan queue:retry all`
4. Check logs: `tail -f storage/logs/laravel.log`

### ❌ Email masuk spam

**Solusi:**
1. Setup SPF record di DNS
2. Setup DKIM
3. Gunakan email domain yang sama dengan website
4. Hindari spam trigger words
5. Gunakan professional email provider (SendGrid, Mailgun)

---

## 📊 Monitoring Email

### Check Queue Status

```bash
# List all jobs
php artisan queue:work --once

# Check failed jobs
php artisan queue:failed

# Retry specific job
php artisan queue:retry {job-id}

# Retry all failed jobs
php artisan queue:retry all

# Flush failed jobs
php artisan queue:flush
```

### Check Database

```sql
-- Pending jobs
SELECT * FROM jobs ORDER BY created_at DESC;

-- Failed jobs
SELECT * FROM failed_jobs ORDER BY failed_at DESC;
```

### Check Logs

```bash
# Email logs
tail -f storage/logs/laravel.log | grep "Email"

# Queue logs
tail -f storage/logs/laravel.log | grep "queue"

# All logs
tail -f storage/logs/laravel.log
```

---

## 🏭 Production Setup dengan Supervisor

### Install Supervisor

```bash
sudo apt-get install supervisor
```

### Create Config File

File: `/etc/supervisor/conf.d/pgri-queue-worker.conf`

```ini
[program:pgri-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/pgri/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/pgri/storage/logs/worker.log
stopwaitsecs=3600
```

### Reload Supervisor

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pgri-queue-worker:*
```

### Manage Worker

```bash
# Status
sudo supervisorctl status pgri-queue-worker:*

# Start
sudo supervisorctl start pgri-queue-worker:*

# Stop
sudo supervisorctl stop pgri-queue-worker:*

# Restart
sudo supervisorctl restart pgri-queue-worker:*

# View logs
sudo supervisorctl tail -f pgri-queue-worker:pgri-queue-worker_00 stdout
```

---

## 📋 Checklist Setup SMTP

### Development:
- [ ] Pilih SMTP provider (Gmail/Mailtrap)
- [ ] Get credentials
- [ ] Update `.env`
- [ ] Clear config cache: `php artisan config:clear`
- [ ] Test email: `php artisan tinker`
- [ ] Jalankan queue: `php artisan queue:work`
- [ ] Test pembayaran via Midtrans
- [ ] Check email masuk

### Production:
- [ ] Pilih professional provider (SendGrid/Mailgun)
- [ ] Setup domain verification
- [ ] Setup SPF & DKIM
- [ ] Update `.env` production
- [ ] Clear config cache
- [ ] Test email
- [ ] Setup Supervisor untuk queue worker
- [ ] Monitor email delivery
- [ ] Setup alerts untuk failed jobs

---

## 🎯 Rekomendasi

### Untuk Development/Testing:
✅ **Mailtrap** - Perfect untuk testing tanpa kirim email real

### Untuk Production (Small Scale):
✅ **Gmail SMTP** - Mudah, gratis, reliable (max 500/hari)

### Untuk Production (Medium-Large Scale):
✅ **SendGrid** atau **Mailgun** - Professional, scalable, analytics

---

## 📧 Email Configuration Summary

```env
# === PILIH SALAH SATU ===

# 1. Gmail (Development/Small Production)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# 2. Mailtrap (Testing Only)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls

# 3. SendGrid (Production)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls

# === WAJIB (Untuk Semua) ===
MAIL_FROM_ADDRESS="noreply@pgri-riau.id"
MAIL_FROM_NAME="PGRI Riau"
ADMIN_EMAIL=admin@pgri-riau.id
```

---

## 🆘 Butuh Bantuan?

1. Check dokumentasi: `EMAIL_NOTIFICATION_COMPLETE.md`
2. Check logs: `storage/logs/laravel.log`
3. Check queue: `php artisan queue:failed`
4. Test SMTP: `php artisan tinker` → send test email

---

## ✅ Next Steps

1. **Pilih SMTP provider** yang sesuai kebutuhan
2. **Get credentials** dari provider
3. **Update `.env`** dengan credentials
4. **Clear cache**: `php artisan config:clear`
5. **Test email**: `php artisan tinker`
6. **Jalankan queue**: `php artisan queue:work`
7. **Test pembayaran** via Midtrans
8. **Check inbox** untuk email notification

**Selesai!** Email notification akan otomatis terkirim setiap ada pembayaran berhasil! 🚀
