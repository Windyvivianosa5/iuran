# 🏫 Sistem Iuran PGRI

> Platform Digital untuk Pengelolaan Iuran PGRI secara Online dengan Integrasi Midtrans Payment Gateway

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![React](https://img.shields.io/badge/React-18.x-blue.svg)](https://reactjs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-1.x-purple.svg)](https://inertiajs.com)
[![Midtrans](https://img.shields.io/badge/Midtrans-Payment-green.svg)](https://midtrans.com)

---

## 📋 Daftar Isi

- [Tentang Sistem](#-tentang-sistem)
- [Fitur Utama](#-fitur-utama)
- [Teknologi](#-teknologi)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Dokumentasi](#-dokumentasi)
- [Deployment](#-deployment)
- [Kontribusi](#-kontribusi)
- [Lisensi](#-lisensi)

---

## 🎯 Tentang Sistem

**Sistem Iuran PGRI** adalah aplikasi web modern yang dirancang khusus untuk memudahkan pengelolaan pembayaran iuran PGRI (Persatuan Guru Republik Indonesia) secara digital. Sistem ini menggantikan proses manual yang memakan waktu dengan solusi otomatis yang efisien.

### Masalah yang Diselesaikan

| Sebelum | Sesudah |
|---------|---------|
| Transfer manual via bank | Pembayaran online 20+ metode |
| Kirim bukti via WhatsApp/Email | Otomatis terverifikasi |
| Verifikasi 2-3 hari | Verifikasi real-time |
| Sulit tracking pembayaran | Dashboard lengkap |
| Laporan manual | Laporan otomatis |

---

## ✨ Fitur Utama

### Untuk Kabupaten

- ✅ **Pembayaran Digital** - Bayar iuran dengan 20+ metode pembayaran (Kartu Kredit, Transfer Bank, E-Wallet, Retail Outlet)
- ✅ **Real-time Verification** - Pembayaran otomatis terverifikasi tanpa perlu approval manual
- ✅ **Dashboard Interaktif** - Pantau statistik pembayaran dengan grafik dan chart
- ✅ **Riwayat Transaksi** - Akses semua transaksi yang pernah dilakukan
- ✅ **Email Konfirmasi** - Terima bukti pembayaran otomatis via email
- ✅ **Lanjutkan Pembayaran** - Melanjutkan pembayaran pending yang belum selesai
- ✅ **Laporan PDF** - Export kwitansi pembayaran dalam format PDF

### Untuk Admin

- ✅ **Kelola Kabupaten** - CRUD kabupaten dengan auto-create user account
- ✅ **Monitoring Real-time** - Pantau semua transaksi masuk secara real-time
- ✅ **Laporan Otomatis** - Generate laporan bulanan dan per kabupaten
- ✅ **Notifikasi** - Terima alert untuk setiap pembayaran baru
- ✅ **Analytics** - Lihat statistik dan trend pembayaran
- ✅ **Export Data** - Export laporan dalam format PDF dan Excel
- ✅ **Email Verification** - Sistem verifikasi email untuk keamanan

### Keamanan

- 🔒 **Email Verification** - User harus verifikasi email sebelum akses sistem
- 🔒 **Auto-Verify Admin** - User yang dibuat admin langsung verified
- 🔒 **Forgot Password** - Reset password via email
- 🔒 **Role-based Access** - Pemisahan akses Admin dan Kabupaten
- 🔒 **CSRF Protection** - Perlindungan dari serangan CSRF
- 🔒 **Password Hashing** - Password di-hash dengan bcrypt
- 🔒 **PCI-DSS Certified** - Midtrans tersertifikasi PCI-DSS

---

## 🛠 Teknologi

### Backend
- **Laravel 11.x** - PHP Framework
- **MySQL** - Database
- **Midtrans** - Payment Gateway
- **Laravel Queue** - Background Jobs

### Frontend
- **React 18.x** - UI Library
- **Inertia.js** - Modern Monolith
- **TypeScript** - Type Safety
- **Tailwind CSS** - Styling
- **Recharts** - Data Visualization
- **Shadcn/ui** - UI Components

### DevOps
- **Vite** - Build Tool
- **Composer** - PHP Dependency Manager
- **NPM** - Node Package Manager
- **Git** - Version Control

---

## 💻 Persyaratan Sistem

### Minimum Requirements

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **NPM** >= 9.x
- **MySQL** >= 8.0
- **Web Server** (Apache/Nginx)

### Recommended

- **PHP** 8.3
- **MySQL** 8.0+
- **Node.js** 20.x LTS
- **SSD Storage**
- **2GB RAM** minimum

---

## 📦 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/your-org/sistem-iuran-pgri.git
cd sistem-iuran-pgri
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE laporankeuanganpgri"

# Run migrations
php artisan migrate

# (Optional) Seed database
php artisan db:seed
```

### 5. Storage Link

```bash
php artisan storage:link
```

### 6. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Run Application

```bash
# Development
php artisan serve

# Queue Worker (in separate terminal)
php artisan queue:work
```

Akses aplikasi di: `http://localhost:8000`

---

## ⚙️ Konfigurasi

### Database Configuration

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laporankeuanganpgri
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Mail Configuration (SMTP)

Untuk Email Verification dan Forgot Password:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-gmail-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="PGRI Iuran"
```

**Untuk Gmail:**
1. Buka: https://myaccount.google.com/apppasswords
2. Buat App Password baru
3. Copy password ke `MAIL_PASSWORD`

### Midtrans Configuration

Untuk Payment Gateway:

```env
MIDTRANS_SERVER_KEY=your_server_key_here
MIDTRANS_CLIENT_KEY=your_client_key_here
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

**Cara Mendapatkan Keys:**
1. Daftar di: https://dashboard.midtrans.com
2. Buka: Settings → Access Keys
3. Copy Server Key dan Client Key
4. Untuk production, gunakan Production Keys

**Webhook URL:**
```
https://your-domain.com/midtrans/notification
```

Daftarkan URL ini di Midtrans Dashboard → Settings → Configuration

---

## 📚 Dokumentasi

Dokumentasi lengkap tersedia di folder `docs/`:

### Dokumentasi Utama

- **[User Manual](docs/USER_MANUAL.md)** - Panduan lengkap untuk pengguna (Kabupaten & Admin)
- **[API Documentation](docs/API_DOCUMENTATION.md)** - Dokumentasi API endpoints
- **[Deployment Guide](docs/DEPLOYMENT_GUIDE.md)** - Panduan deployment ke production
- **[Testing Strategy](docs/TESTING_STRATEGY.md)** - Strategi dan panduan testing

### Dokumentasi Teknis

- **[Business Logic Analysis](docs/BUSINESS_LOGIC_ANALYSIS.md)** - Analisis logika bisnis sistem
- **[Use Case Diagram](docs/USE_CASE_PURIST.md)** - Diagram use case (UML purist)
- **[Activity Diagram](docs/activity.md)** - Diagram aktivitas sistem
- **[Sequence Diagram](docs/sequence.md)** - Diagram sequence
- **[Class Diagram](docs/class.md)** - Diagram class

### Setup Guides

- **[Midtrans Integration](MIDTRANS_INTEGRATION.md)** - Panduan integrasi Midtrans
- **[SMTP Setup](SMTP_SETUP_GUIDE.md)** - Panduan konfigurasi email
- **[Ngrok Setup](SETUP_NGROK.md)** - Panduan setup ngrok untuk testing webhook

---

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` di `.env`
- [ ] Set `APP_DEBUG=false` di `.env`
- [ ] Generate production key: `php artisan key:generate`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Build assets: `npm run build`
- [ ] Setup queue worker (Supervisor/systemd)
- [ ] Configure web server (Nginx/Apache)
- [ ] Setup SSL certificate (Let's Encrypt)
- [ ] Configure Midtrans Production Keys
- [ ] Setup backup schedule
- [ ] Configure monitoring (optional)

### Web Server Configuration

**Nginx:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/sistem-iuran-pgri/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Lihat [Deployment Guide](docs/DEPLOYMENT_GUIDE.md) untuk panduan lengkap.

---

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=TransactionTest

# Run with coverage
php artisan test --coverage
```

### Manual Testing

1. **Test Payment Flow:**
   - Login sebagai kabupaten
   - Buat pembayaran baru
   - Pilih metode pembayaran
   - Selesaikan pembayaran
   - Verifikasi status berubah

2. **Test Admin Features:**
   - Login sebagai admin
   - Buat kabupaten baru
   - Verifikasi user auto-created
   - Generate laporan
   - Export PDF

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan ikuti langkah berikut:

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

### Coding Standards

- Follow PSR-12 untuk PHP
- Follow Airbnb Style Guide untuk JavaScript/TypeScript
- Gunakan TypeScript untuk semua file React
- Tulis tests untuk fitur baru
- Update dokumentasi jika diperlukan

---

## 📄 Lisensi

Sistem ini dikembangkan untuk PGRI (Persatuan Guru Republik Indonesia).

---

## 👥 Tim Pengembang

- **Developer** - [Your Name]
- **Project Manager** - [PM Name]
- **QA Tester** - [QA Name]

---

## 📞 Kontak & Support

**Email Support:**
```
support@pgri.or.id
```

**Jam Operasional:**
- Senin - Jumat: 08:00 - 17:00 WIB
- Sabtu: 08:00 - 12:00 WIB

**GitHub Issues:**
```
https://github.com/your-org/sistem-iuran-pgri/issues
```

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [React](https://reactjs.org) - UI Library
- [Inertia.js](https://inertiajs.com) - Modern Monolith
- [Midtrans](https://midtrans.com) - Payment Gateway
- [Tailwind CSS](https://tailwindcss.com) - CSS Framework
- [Shadcn/ui](https://ui.shadcn.com) - UI Components

---

<div align="center">

**Dibuat dengan ❤️ untuk PGRI Indonesia**

[Dokumentasi](docs/) • [User Manual](docs/USER_MANUAL.md) • [API Docs](docs/API_DOCUMENTATION.md)

</div>
