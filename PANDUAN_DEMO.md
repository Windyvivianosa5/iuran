# 🎬 Panduan Demo Sistem Iuran PGRI

## 📋 Persiapan Sebelum Demo

### 1. Pastikan Semua Service Berjalan

Buka 3 terminal dan jalankan:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
```

**Terminal 3 - Ngrok (untuk demo Midtrans):**
```bash
ngrok http 8000
```

### 2. Akses Aplikasi

Buka browser dan akses: **http://127.0.0.1:8000**

---

## 🎭 Skenario Demo

### **BAGIAN 1: Halaman Landing (Public)**

#### Langkah 1: Tampilkan Halaman Utama
1. Buka **http://127.0.0.1:8000**
2. Jelaskan fitur:
   - Daftar iuran yang tersedia
   - Informasi PGRI
   - Tombol Login/Register

**Poin Demo:**
> "Ini adalah halaman landing publik dimana pengunjung bisa melihat daftar iuran yang tersedia dan informasi tentang sistem."

---

### **BAGIAN 2: Login sebagai Admin**

#### Langkah 2: Login Admin
1. Klik tombol **"Login"**
2. Masukkan kredensial Admin:
   - **Email:** `admin@pgri-riau.id`
   - **Password:** `password`
3. Klik **"Login"**

**Poin Demo:**
> "Admin memiliki akses penuh untuk mengelola kabupaten, melihat laporan, dan memantau semua transaksi pembayaran."

---

### **BAGIAN 3: Dashboard Admin**

#### Langkah 3: Kelola Kabupaten
1. Dari dashboard admin, klik menu **"Kelola Kabupaten"**
2. Tampilkan daftar kabupaten yang sudah ada
3. Klik **"Tambah Kabupaten"**

**Poin Demo:**
> "Admin dapat menambahkan kabupaten baru sekaligus membuat akun user untuk kabupaten tersebut."

#### Langkah 4: Tambah Kabupaten Baru
1. Isi form:
   - **Nama Kabupaten:** `Kabupaten Pekanbaru`
   - **Kode Kabupaten:** `PKU`
   - **Jumlah Anggota:** `500`
   - **Status:** `Aktif`
2. Centang **"Buat Akun User untuk Kabupaten"**
3. Isi data user:
   - **Nama User:** `Admin Pekanbaru`
   - **Email:** `pekanbaru@pgri-riau.id`
   - **Password:** `password123`
   - **Konfirmasi Password:** `password123`
4. Klik **"Simpan"**

**Poin Demo:**
> "Sistem secara otomatis membuat akun kabupaten yang dapat digunakan untuk login dan melakukan pembayaran iuran."

#### Langkah 5: Lihat Notifikasi
1. Klik icon **Bell/Notifikasi** di header
2. Tampilkan daftar notifikasi pembayaran

**Poin Demo:**
> "Admin menerima notifikasi real-time setiap ada pembayaran yang masuk dari kabupaten."

#### Langkah 6: Lihat Laporan
1. Klik menu **"Laporan"**
2. Tampilkan laporan transaksi
3. Filter berdasarkan tanggal/status

**Poin Demo:**
> "Admin dapat melihat laporan lengkap semua transaksi pembayaran dari seluruh kabupaten."

---

### **BAGIAN 4: Logout dan Login sebagai Kabupaten**

#### Langkah 7: Logout dari Admin
1. Klik profile di pojok kanan atas
2. Klik **"Logout"**

#### Langkah 8: Login sebagai Kabupaten
1. Klik **"Login"**
2. Masukkan kredensial Kabupaten (yang baru dibuat):
   - **Email:** `pekanbaru@pgri-riau.id`
   - **Password:** `password123`
3. Klik **"Login"**

**Poin Demo:**
> "Sekarang kita login sebagai user kabupaten yang akan melakukan pembayaran iuran."

---

### **BAGIAN 5: Dashboard Kabupaten**

#### Langkah 9: Lihat Dashboard Kabupaten
1. Tampilkan dashboard kabupaten
2. Jelaskan statistik yang ditampilkan:
   - Total pembayaran
   - Transaksi pending
   - Transaksi berhasil

**Poin Demo:**
> "Dashboard kabupaten menampilkan ringkasan transaksi dan status pembayaran mereka."

---

### **BAGIAN 6: Proses Pembayaran dengan Midtrans**

#### Langkah 10: Buat Pembayaran Baru
1. Klik menu **"Iuran"** atau **"Bayar Iuran"**
2. Klik tombol **"Bayar Iuran"** atau **"Tambah Pembayaran"**

**Poin Demo:**
> "Kabupaten dapat membuat pembayaran baru melalui Midtrans Payment Gateway."

#### Langkah 11: Isi Form Pembayaran
1. Isi form:
   - **Jumlah Pembayaran:** `Rp 100.000`
   - **Deskripsi:** `Iuran Bulan Januari 2026`
2. Klik **"Bayar Sekarang"**

**Poin Demo:**
> "Sistem terintegrasi dengan Midtrans, mendukung berbagai metode pembayaran seperti QRIS, Transfer Bank, E-Wallet, dll."

#### Langkah 12: Proses Pembayaran di Midtrans
1. Popup Midtrans akan muncul
2. Pilih metode pembayaran, misalnya **"QRIS"**
3. Tampilkan QR Code

**Poin Demo:**
> "User dapat memilih berbagai metode pembayaran. Untuk demo, kita gunakan QRIS."

#### Langkah 13: Simulasi Pembayaran (Sandbox)
Karena ini sandbox/testing, ada 2 cara:

**Cara 1: Gunakan Simulator Midtrans**
1. Di popup Midtrans, cari tombol **"Pay"** atau **"Simulate Payment"**
2. Klik untuk simulasi pembayaran sukses

**Cara 2: Gunakan Test Webhook Script**
1. Buka terminal baru
2. Edit file `test_webhook.php`:
   - Ganti `$orderId` dengan Order ID transaksi yang baru dibuat
   - Ganti `$serverKey` dengan Server Key Midtrans Anda
3. Jalankan:
   ```bash
   php test_webhook.php
   ```

**Poin Demo:**
> "Dalam mode sandbox, kita bisa simulasi pembayaran. Di production, user akan melakukan pembayaran sungguhan."

#### Langkah 14: Verifikasi Pembayaran Berhasil
1. Setelah pembayaran sukses, akan muncul notifikasi **"Pembayaran Berhasil!"**
2. User otomatis diarahkan ke halaman **"Daftar Transaksi"**
3. Status transaksi berubah dari **"Menunggu"** menjadi **"Berhasil"**

**Poin Demo:**
> "Sistem secara otomatis memverifikasi pembayaran melalui webhook Midtrans. Status langsung update real-time."

---

### **BAGIAN 7: Lihat Riwayat Transaksi**

#### Langkah 15: Lihat Daftar Transaksi
1. Klik menu **"Iuran"** atau **"Riwayat Transaksi"**
2. Tampilkan daftar transaksi:
   - Transaksi yang berhasil (hijau)
   - Transaksi yang pending (kuning)
3. Klik **"Detail"** untuk melihat detail transaksi

**Poin Demo:**
> "Kabupaten dapat melihat riwayat lengkap semua transaksi pembayaran mereka."

#### Langkah 16: Lanjutkan Pembayaran Pending
1. Jika ada transaksi dengan status **"Menunggu"**
2. Klik tombol **"Lanjutkan Bayar"**
3. Popup Midtrans akan muncul kembali dengan transaksi yang sama

**Poin Demo:**
> "User dapat melanjutkan pembayaran yang belum selesai tanpa perlu membuat transaksi baru."

---

### **BAGIAN 8: Notifikasi Email (Opsional)**

#### Langkah 17: Cek Email Notifikasi
Jika email sudah dikonfigurasi:
1. Buka email kabupaten
2. Tampilkan email notifikasi pembayaran berhasil
3. Buka email admin
4. Tampilkan email notifikasi pembayaran diterima

**Poin Demo:**
> "Sistem mengirim email otomatis ke kabupaten dan admin setiap ada pembayaran yang berhasil."

---

### **BAGIAN 9: Kembali ke Admin - Verifikasi**

#### Langkah 18: Logout dan Login sebagai Admin
1. Logout dari akun kabupaten
2. Login kembali sebagai admin

#### Langkah 19: Cek Notifikasi Admin
1. Klik icon notifikasi
2. Tampilkan notifikasi pembayaran baru dari kabupaten
3. Klik notifikasi untuk melihat detail

**Poin Demo:**
> "Admin menerima notifikasi real-time dan dapat memantau semua pembayaran yang masuk."

#### Langkah 20: Lihat Laporan Lengkap
1. Klik menu **"Laporan"**
2. Tampilkan laporan dengan pembayaran terbaru
3. Export laporan (jika ada fitur export)

**Poin Demo:**
> "Admin dapat melihat laporan lengkap dan melakukan analisis data pembayaran."

---

## 🎯 **Poin-Poin Penting untuk Ditekankan**

### ✅ **Fitur Utama:**
1. **Multi-Role System** - Admin, Kabupaten
2. **Integrasi Midtrans** - Payment Gateway profesional
3. **Real-time Notification** - Webhook otomatis
4. **Email Notification** - Konfirmasi pembayaran
5. **Dashboard Analytics** - Statistik dan laporan
6. **Responsive Design** - Bisa diakses dari mobile

### 🔒 **Keamanan:**
1. **Authentication & Authorization** - Role-based access
2. **CSRF Protection** - Laravel security
3. **Signature Verification** - Midtrans webhook validation
4. **Encrypted Passwords** - Bcrypt hashing

### 💳 **Metode Pembayaran:**
1. QRIS
2. Transfer Bank (BCA, BNI, BRI, Mandiri, Permata)
3. E-Wallet (GoPay, ShopeePay, OVO)
4. Kartu Kredit/Debit
5. Convenience Store (Alfamart, Indomaret)

---

## 📊 **Flow Demo Singkat (5 Menit)**

Jika waktu terbatas, gunakan flow ini:

1. **[1 menit]** Tampilkan landing page → Login Admin
2. **[1 menit]** Dashboard Admin → Tambah Kabupaten baru
3. **[1 menit]** Logout → Login sebagai Kabupaten
4. **[1.5 menit]** Buat pembayaran → Proses Midtrans → Simulasi sukses
5. **[0.5 menit]** Lihat status berubah → Kembali ke Admin → Cek notifikasi

---

## 🛠️ **Troubleshooting Demo**

### Jika Midtrans Error:
1. Cek `.env` - pastikan `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` terisi
2. Restart Laravel server: `php artisan serve`
3. Clear config: `php artisan config:clear`

### Jika Webhook Tidak Berfungsi:
1. Pastikan ngrok running
2. Cek URL webhook di Midtrans Dashboard
3. Gunakan `test_webhook.php` untuk simulasi manual

### Jika Email Tidak Terkirim:
1. Cek konfigurasi SMTP di `.env`
2. Untuk demo, bisa skip bagian email

---

## 📝 **Checklist Sebelum Demo**

- [ ] Laravel server running (`php artisan serve`)
- [ ] Vite dev server running (`npm run dev`)
- [ ] Ngrok running (`ngrok http 8000`)
- [ ] Midtrans webhook URL sudah dikonfigurasi
- [ ] Database sudah di-seed dengan data dummy
- [ ] Browser sudah dibuka di `http://127.0.0.1:8000`
- [ ] Kredensial login sudah disiapkan
- [ ] Test pembayaran sudah dicoba minimal 1x

---

## 🎓 **Tips Presentasi**

1. **Mulai dengan konteks** - Jelaskan masalah yang dipecahkan
2. **Tunjukkan user flow** - Dari perspektif user yang berbeda
3. **Highlight fitur unik** - Integrasi Midtrans, real-time notification
4. **Siapkan backup plan** - Jika demo gagal, punya screenshot/video
5. **Akhiri dengan Q&A** - Siap jawab pertanyaan teknis

---

## 📸 **Alternatif: Video Demo**

Jika ingin membuat video demo:

1. Gunakan **OBS Studio** atau **Screen Recorder**
2. Rekam semua langkah di atas
3. Edit dengan **DaVinci Resolve** (gratis) atau **Camtasia**
4. Tambahkan voice-over menjelaskan setiap langkah
5. Export dan upload ke YouTube/Google Drive

---

**Selamat Demo! 🚀**

Jika ada pertanyaan atau butuh bantuan saat demo, jangan ragu untuk bertanya!
