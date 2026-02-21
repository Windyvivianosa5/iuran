# 📧 Cara Cepat Test Email Reminder

## Langkah 1: Lihat Daftar User

```bash
php artisan test:reminder-email
```

Ini akan menampilkan daftar semua user kabupaten yang ada.

---

## Langkah 2: Kirim Test Email

Pilih salah satu email dari daftar, lalu jalankan:

```bash
php artisan test:reminder-email [email-user]
```

**Contoh:**
```bash
php artisan test:reminder-email pekanbaru@pgri.com
```

---

## Langkah 3: Cek Email

Buka inbox email yang Anda gunakan untuk testing dan cek apakah email reminder sudah masuk.

**Jika email tidak masuk:**
1. Cek folder **Spam/Junk**
2. Pastikan konfigurasi email di `.env` sudah benar
3. Pastikan menggunakan Gmail App Password (bukan password biasa)

---

## ⚙️ Konfigurasi Email (Wajib!)

Edit file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-digit-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="PGRI Provinsi Riau"
```

### Cara Dapat App Password Gmail:

1. Buka: https://myaccount.google.com/apppasswords
2. Login dengan akun Gmail Anda
3. Pilih "Mail" dan "Other (Custom name)"
4. Ketik: "Laravel PGRI"
5. Klik "Generate"
6. Copy password 16 digit yang muncul
7. Paste ke `MAIL_PASSWORD` di file `.env`

---

## 🎯 Testing Cepat

```bash
# Lihat daftar user
php artisan test:reminder-email

# Kirim ke user pertama yang muncul
php artisan test:reminder-email [copy-email-dari-daftar]

# Cek log jika ada error
type storage\logs\laravel.log
```

---

## ✅ Selesai!

Setelah email terkirim, cek tampilan email yang baru (desain Gmail style yang natural) di inbox Anda.
