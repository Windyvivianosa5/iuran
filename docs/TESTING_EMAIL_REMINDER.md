# Panduan Testing Email Reminder Pembayaran

## 📧 Cara Mengirim Test Email Reminder

### Metode 1: Menggunakan Command Test (Recommended)

Command ini dibuat khusus untuk testing dan bisa langsung mengirim email tanpa perlu menunggu kondisi waktu tertentu.

#### Langkah 1: Lihat Daftar User Kabupaten

```bash
php artisan test:reminder-email
```

Output akan menampilkan daftar user kabupaten yang tersedia:
```
Daftar User Kabupaten:
=====================
- Kabupaten Pekanbaru (pekanbaru@pgri.com)
- Kabupaten Kampar (kampar@pgri.com)
- Kabupaten Bengkalis (bengkalis@pgri.com)

Gunakan: php artisan test:reminder-email {email}
Contoh: php artisan test:reminder-email pekanbaru@pgri.com
```

#### Langkah 2: Kirim Test Email ke User Tertentu

```bash
php artisan test:reminder-email pekanbaru@pgri.com
```

Output jika berhasil:
```
Mengirim test email reminder ke: Kabupaten Pekanbaru (pekanbaru@pgri.com)
✓ Email berhasil dikirim!

Silakan cek inbox email: pekanbaru@pgri.com
```

---

### Metode 2: Menggunakan Command Asli (Production)

Command ini adalah command yang digunakan di production dan memiliki kondisi:
- Hanya berjalan di 7 hari terakhir bulan
- Hanya mengirim ke user yang belum bayar bulan ini

```bash
php artisan check:unpaid-users
```

**Catatan:** Command ini mungkin tidak mengirim email jika:
- Bukan periode 7 hari terakhir bulan
- Semua user sudah membayar bulan ini

---

### Metode 3: Menggunakan Tinker (Manual Testing)

Untuk testing manual menggunakan Laravel Tinker:

```bash
php artisan tinker
```

Kemudian jalankan kode berikut:

```php
// Ambil user kabupaten
$user = \App\Models\User::where('role', 'kabupaten')->first();

// Kirim email
\Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\PaymentReminderMail($user));

// Cek hasilnya
echo "Email terkirim ke: " . $user->email;
```

---

## ⚙️ Konfigurasi Email (Penting!)

Pastikan file `.env` sudah dikonfigurasi dengan benar:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="PGRI Provinsi Riau"
```

### Cara Mendapatkan App Password Gmail:

1. Buka https://myaccount.google.com/security
2. Aktifkan **2-Step Verification** jika belum
3. Cari **App passwords**
4. Pilih **Mail** dan **Other (Custom name)**
5. Masukkan nama: "Laravel PGRI"
6. Copy password yang digenerate
7. Paste ke `MAIL_PASSWORD` di `.env`

---

## 🔍 Troubleshooting

### Error: "Connection could not be established"

**Solusi:**
1. Pastikan internet aktif
2. Cek konfigurasi MAIL_HOST dan MAIL_PORT
3. Pastikan menggunakan App Password, bukan password Gmail biasa

### Error: "User not found"

**Solusi:**
1. Pastikan email yang dimasukkan benar
2. Cek apakah user ada di database dengan role 'kabupaten'
3. Gunakan command tanpa parameter untuk melihat daftar user

### Email Tidak Masuk ke Inbox

**Solusi:**
1. Cek folder **Spam/Junk**
2. Tunggu beberapa menit (kadang ada delay)
3. Cek log Laravel: `storage/logs/laravel.log`
4. Pastikan `MAIL_FROM_ADDRESS` valid

---

## 📊 Monitoring

### Cek Log Email

```bash
# Windows
type storage\logs\laravel.log | findstr "Reminder"

# Linux/Mac
tail -f storage/logs/laravel.log | grep "Reminder"
```

### Cek Email yang Terkirim (Jika MAIL_MAILER=log)

Jika menggunakan `MAIL_MAILER=log` untuk testing, email akan tersimpan di:
```
storage/logs/laravel.log
```

---

## 🤖 Automasi (Production)

Untuk menjalankan otomatis setiap hari, tambahkan ke `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Cek unpaid users setiap hari jam 9 pagi
    $schedule->command('check:unpaid-users')
             ->dailyAt('09:00')
             ->timezone('Asia/Jakarta');
}
```

Kemudian jalankan scheduler:

```bash
# Development (manual)
php artisan schedule:work

# Production (cron job)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📝 Contoh Lengkap Testing

```bash
# 1. Lihat daftar user
php artisan test:reminder-email

# 2. Kirim test email
php artisan test:reminder-email pekanbaru@pgri.com

# 3. Cek log
type storage\logs\laravel.log

# 4. Cek inbox email pekanbaru@pgri.com
```

---

## ✅ Checklist Sebelum Testing

- [ ] Konfigurasi `.env` sudah benar
- [ ] App Password Gmail sudah dibuat
- [ ] Ada user dengan role 'kabupaten' di database
- [ ] Internet aktif
- [ ] Laravel server berjalan (`php artisan serve`)

---

## 📞 Support

Jika masih ada masalah, cek:
1. `storage/logs/laravel.log` untuk error detail
2. Konfigurasi email di `.env`
3. Koneksi internet
4. Gmail App Password valid
