# ⚡ SMTP Quick Reference

## 🎯 Pilih Provider Anda

| Provider | Use Case | Limit | Setup Time |
|----------|----------|-------|------------|
| **Gmail** | Development/Small | 500/hari | 5 menit |
| **Mailtrap** | Testing Only | Unlimited | 2 menit |
| **SendGrid** | Production | 100/hari (free) | 10 menit |
| **Mailgun** | Production | 5000/bulan (free) | 10 menit |

---

## 📧 Configuration Templates

### Gmail SMTP
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pgri-riau.id"
MAIL_FROM_NAME="PGRI Riau"
ADMIN_EMAIL=admin@pgri-riau.id
```

**Get App Password:**
1. https://myaccount.google.com/security
2. Enable 2-Step Verification
3. App Passwords → Mail → Generate
4. Copy 16-char password

---

### Mailtrap (Testing)
```env
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

**Get Credentials:**
1. https://mailtrap.io → Sign up
2. Email Testing → Inboxes
3. SMTP Settings → Copy credentials

---

### SendGrid (Production)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.your-api-key-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pgri-riau.id"
MAIL_FROM_NAME="PGRI Riau"
ADMIN_EMAIL=admin@pgri-riau.id
```

**Get API Key:**
1. https://sendgrid.com → Sign up
2. Settings → API Keys → Create
3. Full Access → Copy key

---

## 🚀 Quick Setup (3 Steps)

### 1. Update `.env`
Copy salah satu template di atas → paste ke `.env`

### 2. Clear Cache
```bash
php artisan config:clear
```

### 3. Start Queue
```bash
php artisan queue:work
```

---

## ✅ Test Email

```bash
php artisan tinker
```

```php
// Simple test
\Mail::raw('Test', function($m) {
    $m->to('test@example.com')->subject('Test SMTP');
});

// Test dengan transaction
$t = \App\Models\Transaction::where('status', 'settlement')->first();
\Mail::to('test@example.com')->send(new \App\Mail\PaymentSuccessNotification($t));
```

---

## 🔧 Troubleshooting

### Email tidak terkirim?

```bash
# 1. Check queue running
ps aux | grep queue

# 2. Check failed jobs
php artisan queue:failed

# 3. Retry failed jobs
php artisan queue:retry all

# 4. Check logs
tail -f storage/logs/laravel.log | grep Email
```

### Authentication failed?

1. ✅ Check username & password benar
2. ✅ Gmail: pakai App Password (bukan password biasa)
3. ✅ Clear cache: `php artisan config:clear`
4. ✅ Check port: 587 (TLS) atau 465 (SSL)

---

## 📊 Port & Encryption

| Port | Encryption | Use |
|------|------------|-----|
| **587** | TLS | ✅ Recommended |
| 465 | SSL | Alternative |
| 25 | None | ❌ Not secure |

---

## 🏭 Production (Supervisor)

### Install
```bash
sudo apt-get install supervisor
```

### Config: `/etc/supervisor/conf.d/pgri-worker.conf`
```ini
[program:pgri-worker]
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
```

### Reload
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pgri-worker:*
```

---

## 📋 Checklist

- [ ] Pilih SMTP provider
- [ ] Get credentials
- [ ] Update `.env`
- [ ] `php artisan config:clear`
- [ ] `php artisan queue:work`
- [ ] Test email
- [ ] ✅ Done!

---

## 🎯 Rekomendasi

**Development:** Mailtrap (testing tanpa kirim real)
**Small Production:** Gmail (500/hari, gratis)
**Large Production:** SendGrid/Mailgun (scalable)

---

## 📚 Dokumentasi Lengkap

- `SMTP_SETUP_GUIDE.md` - Panduan detail semua provider
- `EMAIL_QUICK_START.md` - Quick start 3 langkah
- `EMAIL_NOTIFICATION_STATUS.md` - Status implementasi

---

**Need help?** Check logs: `storage/logs/laravel.log`
