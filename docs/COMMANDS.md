# 🎯 Command Laravel - Sistem Iuran PGRI

## 📧 Email Commands

### 1. Test Email Reminder (Untuk Testing)
Kirim test email reminder ke user tertentu tanpa kondisi waktu.

```bash
# Lihat daftar user kabupaten
php artisan test:reminder-email

# Kirim test email ke user tertentu
php artisan test:reminder-email user@example.com
```

**Kapan Digunakan:**
- Saat development/testing
- Ingin melihat tampilan email
- Tidak perlu menunggu kondisi waktu tertentu

---

### 2. Check Unpaid Users (Production)
Cek dan kirim email reminder ke user yang belum bayar bulan ini.

```bash
php artisan check:unpaid-users
```

**Kondisi:**
- Hanya berjalan di 7 hari terakhir bulan
- Hanya kirim ke user yang belum bayar bulan ini
- User harus role 'kabupaten'

**Kapan Digunakan:**
- Production environment
- Dijadwalkan otomatis dengan cron/scheduler
- Mengirim reminder real ke user yang benar-benar belum bayar

---

## 🗄️ Database Commands

### Migrate Database
```bash
# Jalankan semua migration
php artisan migrate

# Rollback migration terakhir
php artisan migrate:rollback

# Reset dan migrate ulang
php artisan migrate:fresh

# Reset, migrate, dan seed
php artisan migrate:fresh --seed
```

### Database Seeding
```bash
# Jalankan semua seeder
php artisan db:seed

# Jalankan seeder tertentu
php artisan db:seed --class=DatabaseSeeder
```

---

## 🔧 Cache Commands

### Clear Cache
```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear semua cache sekaligus
php artisan optimize:clear
```

### Optimize
```bash
# Optimize untuk production
php artisan optimize

# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

---

## 🔑 Key Commands

### Generate Application Key
```bash
php artisan key:generate
```

---

## 👤 User Management

### Create Admin User (via Tinker)
```bash
php artisan tinker
```

Kemudian:
```php
\App\Models\User::create([
    'name' => 'Admin PGRI',
    'email' => 'admin@pgri.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);
```

---

## 🌐 Server Commands

### Development Server
```bash
# Jalankan server Laravel
php artisan serve

# Jalankan di port tertentu
php artisan serve --port=8080

# Jalankan di host tertentu
php artisan serve --host=0.0.0.0
```

---

## 📊 Queue Commands

### Queue Worker
```bash
# Jalankan queue worker
php artisan queue:work

# Jalankan sekali saja
php artisan queue:work --once

# Dengan timeout
php artisan queue:work --timeout=60
```

---

## 🕐 Scheduler Commands

### Run Scheduler
```bash
# Development - jalankan scheduler terus menerus
php artisan schedule:work

# Production - jalankan scheduler sekali (untuk cron)
php artisan schedule:run
```

### List Scheduled Tasks
```bash
php artisan schedule:list
```

---

## 🧪 Testing Commands

### Run Tests
```bash
# Jalankan semua test
php artisan test

# Jalankan test tertentu
php artisan test --filter=ExampleTest
```

---

## 📝 Make Commands (Generate Code)

### Generate Files
```bash
# Controller
php artisan make:controller NamaController

# Model
php artisan make:model NamaModel

# Migration
php artisan make:migration create_nama_table

# Seeder
php artisan make:seeder NamaSeeder

# Mail
php artisan make:mail NamaMail

# Command
php artisan make:command NamaCommand

# Middleware
php artisan make:middleware NamaMiddleware
```

---

## 🔍 Info Commands

### List All Commands
```bash
php artisan list
```

### Get Help
```bash
php artisan help [command-name]

# Contoh:
php artisan help migrate
```

### Show Routes
```bash
php artisan route:list
```

---

## 💡 Tips

### Kombinasi Command yang Sering Digunakan

**Reset Development:**
```bash
php artisan migrate:fresh --seed
php artisan optimize:clear
```

**Deploy Production:**
```bash
php artisan migrate --force
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Troubleshooting:**
```bash
php artisan optimize:clear
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

---

## 📚 Dokumentasi Lengkap

- Testing Email: `docs/TESTING_EMAIL_REMINDER.md`
- Quick Test: `QUICK_TEST_EMAIL.md`
- Laravel Docs: https://laravel.com/docs
