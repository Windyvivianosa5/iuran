# 📖 User Manual - Sistem Iuran PGRI
## Panduan Lengkap untuk Pengguna

---

## 📋 Daftar Isi

1. [Pengenalan Sistem](#pengenalan-sistem)
2. [Akses Sistem](#akses-sistem)
3. [Panduan untuk Kabupaten](#panduan-untuk-kabupaten)
4. [Panduan untuk Admin](#panduan-untuk-admin)
5. [Metode Pembayaran](#metode-pembayaran)
6. [FAQ (Pertanyaan Umum)](#faq-pertanyaan-umum)
7. [Troubleshooting](#troubleshooting)
8. [Kontak Support](#kontak-support)

---

## 🎯 Pengenalan Sistem

### Apa itu Sistem Iuran PGRI?

**Sistem Iuran PGRI** adalah platform digital untuk mengelola pembayaran iuran PGRI secara online. Sistem ini memudahkan kabupaten untuk membayar iuran dan admin untuk memantau transaksi secara real-time.

### Fitur Utama

#### Untuk Kabupaten:
✅ **Pembayaran Online** - Bayar iuran dengan 20+ metode pembayaran  
✅ **Real-time Status** - Lihat status pembayaran secara langsung  
✅ **Riwayat Transaksi** - Akses semua transaksi yang pernah dilakukan  
✅ **Email Konfirmasi** - Terima bukti pembayaran otomatis  
✅ **Dashboard Interaktif** - Pantau statistik pembayaran Anda  

#### Untuk Admin:
✅ **Monitoring Real-time** - Pantau semua transaksi masuk  
✅ **Laporan Otomatis** - Generate laporan bulanan dan per kabupaten  
✅ **Notifikasi** - Terima alert untuk setiap pembayaran baru  
✅ **Analytics** - Lihat statistik dan trend pembayaran  
✅ **Auto-Verification** - Pembayaran terverifikasi otomatis  

### Keunggulan Sistem

| Sebelum | Sesudah |
|---------|---------|
| Transfer manual via bank | Pembayaran online 20+ metode |
| Kirim bukti via WhatsApp/Email | Otomatis terverifikasi |
| Verifikasi 2-3 hari | Verifikasi real-time |
| Sulit tracking pembayaran | Dashboard lengkap |
| Laporan manual | Laporan otomatis |

---

## 🔐 Akses Sistem

### URL Sistem

**Production:**
```
https://iuran.pgri.or.id
```

**Development/Testing:**
```
http://localhost:8000
```

### Login ke Sistem

1. **Buka browser** (Chrome, Firefox, Edge, Safari)
2. **Akses URL** sistem
3. **Masukkan kredensial:**
   - Email: `email-anda@example.com`
   - Password: `password-anda`
4. **Klik "Login"**

![Login Page](../public/images/login-screenshot.png)

### Lupa Password

1. Klik **"Lupa Password?"** di halaman login
2. Masukkan **email** yang terdaftar
3. Cek **inbox email** Anda
4. Klik **link reset password**
5. Masukkan **password baru**
6. Klik **"Reset Password"**

### Logout

1. Klik **nama Anda** di pojok kanan atas
2. Pilih **"Logout"**
3. Anda akan diarahkan ke halaman login

---

## 👥 Panduan untuk Kabupaten

### A. Dashboard Kabupaten

Setelah login, Anda akan melihat dashboard dengan informasi:

#### 📊 Ringkasan
- **Total Dibayar**: Total iuran yang sudah berhasil dibayar
- **Transaksi Pending**: Pembayaran yang menunggu konfirmasi
- **Jumlah Transaksi**: Total semua transaksi
- **Pembayaran Terakhir**: Tanggal pembayaran terakhir

#### 📈 Grafik
- **Trend Bulanan**: Grafik pembayaran per bulan
- **Status Pembayaran**: Distribusi status transaksi

#### 📋 Transaksi Terbaru
- Daftar 5 transaksi terakhir
- Status setiap transaksi
- Aksi cepat (Detail, Lanjutkan Bayar)

---

### B. Membuat Pembayaran Baru

#### Langkah 1: Akses Form Pembayaran

1. Dari dashboard, klik **"Bayar Iuran"** atau
2. Klik menu **"Kelola Iuran"** → **"Tambah Iuran"**

#### Langkah 2: Isi Form Pembayaran

![Form Pembayaran](../public/images/payment-form.png)

**Field yang harus diisi:**

1. **Jumlah Iuran**
   - Masukkan nominal dalam Rupiah
   - Minimal: Rp 1.000
   - Contoh: `1000000` (untuk Rp 1.000.000)

2. **Deskripsi**
   - Keterangan pembayaran
   - Contoh: `Iuran Bulan Januari 2025`
   - Maksimal 255 karakter

3. Klik **"Bayar Sekarang"**

#### Langkah 3: Pilih Metode Pembayaran

Setelah klik "Bayar Sekarang", akan muncul **popup Midtrans** dengan pilihan metode pembayaran:

##### 💳 Credit/Debit Card
- Visa, Mastercard, JCB, Amex
- Proses instant
- Aman dengan 3D Secure

**Cara bayar:**
1. Pilih **"Credit/Debit Card"**
2. Masukkan **nomor kartu**
3. Masukkan **nama pemegang kartu**
4. Masukkan **tanggal kadaluarsa** (MM/YY)
5. Masukkan **CVV** (3 digit di belakang kartu)
6. Klik **"Pay"**
7. Masukkan **OTP** dari bank (jika diminta)
8. Selesai!

##### 🏦 Bank Transfer
- BCA, Mandiri, BNI, BRI, Permata
- Virtual Account otomatis
- Berlaku 24 jam

**Cara bayar:**
1. Pilih **"Bank Transfer"**
2. Pilih **bank** yang Anda inginkan
3. **Salin nomor Virtual Account**
4. Buka **mobile banking/ATM**
5. Transfer ke **nomor Virtual Account**
6. Status otomatis terupdate!

##### 📱 E-Wallet
- GoPay, ShopeePay, DANA, OVO
- Proses instant via QR Code

**Cara bayar:**
1. Pilih **"E-Wallet"**
2. Pilih **provider** (GoPay/ShopeePay/dll)
3. **Scan QR Code** dengan aplikasi
4. Konfirmasi pembayaran
5. Selesai!

##### 🏪 Retail Outlet
- Indomaret, Alfamart
- Bayar tunai di kasir

**Cara bayar:**
1. Pilih **"Retail Outlet"**
2. Pilih **Indomaret/Alfamart**
3. **Salin kode pembayaran**
4. Datang ke **kasir**
5. Sebutkan **kode pembayaran**
6. Bayar tunai
7. Simpan struk!

#### Langkah 4: Konfirmasi Pembayaran

Setelah pembayaran berhasil:

✅ **Status otomatis berubah** menjadi "Lunas"  
✅ **Email konfirmasi** dikirim ke email Anda  
✅ **Muncul di dashboard** admin  
✅ **Tercatat di riwayat** transaksi  

---

### C. Melihat Riwayat Transaksi

#### Akses Riwayat

1. Klik menu **"Kelola Iuran"**
2. Anda akan melihat daftar semua transaksi

#### Informasi yang Ditampilkan

| Kolom | Keterangan |
|-------|------------|
| **Order ID** | Nomor unik transaksi |
| **Jumlah** | Nominal pembayaran |
| **Deskripsi** | Keterangan pembayaran |
| **Status** | Status pembayaran (Pending/Lunas/Gagal) |
| **Tanggal** | Tanggal transaksi dibuat |
| **Aksi** | Tombol Detail/Lanjutkan Bayar |

#### Status Pembayaran

🟡 **Pending** - Menunggu pembayaran  
🟢 **Lunas (Settlement)** - Pembayaran berhasil  
🔴 **Gagal (Cancel/Expire/Deny)** - Pembayaran gagal  

#### Filter Transaksi

- **Filter by Status**: Pilih status tertentu
- **Search**: Cari berdasarkan Order ID atau deskripsi
- **Sort**: Urutkan berdasarkan tanggal/jumlah

---

### D. Melihat Detail Transaksi

#### Akses Detail

1. Dari daftar transaksi, klik **"Detail"**
2. Atau klik **Order ID** transaksi

#### Informasi Detail

**Informasi Transaksi:**
- Order ID
- Transaction ID (dari Midtrans)
- Status pembayaran
- Jumlah pembayaran

**Informasi Pembayaran:**
- Metode pembayaran (Credit Card/Bank Transfer/dll)
- Provider (Visa/BCA/GoPay/dll)
- Waktu transaksi
- Waktu settlement (jika sudah lunas)

**Informasi Tambahan:**
- Deskripsi pembayaran
- Metadata (fraud status, dll)

---

### E. Melanjutkan Pembayaran Pending

Jika Anda memiliki transaksi **Pending** yang belum dibayar:

#### Cara Melanjutkan

1. Dari daftar transaksi, cari transaksi **Pending**
2. Klik **"Lanjutkan Bayar"**
3. Popup Midtrans akan muncul kembali
4. Pilih metode pembayaran
5. Selesaikan pembayaran

#### Catatan Penting

⚠️ **Transaksi akan expire** dalam 24 jam (untuk bank transfer)  
⚠️ **Jika expire**, buat transaksi baru  
⚠️ **Jangan transfer** ke Virtual Account yang sudah expire  

---

### F. Tips untuk Kabupaten

✅ **Simpan Order ID** untuk referensi  
✅ **Cek email** setelah pembayaran  
✅ **Screenshot bukti** pembayaran  
✅ **Bayar sebelum expire** (24 jam untuk bank transfer)  
✅ **Gunakan deskripsi jelas** (contoh: "Iuran Januari 2025")  
✅ **Hubungi admin** jika ada masalah  

---

## 👨‍💼 Panduan untuk Admin

### A. Dashboard Admin

Dashboard admin menampilkan overview lengkap sistem:

#### 📊 Statistik Utama

**Card Statistik:**
- **Total Masuk**: Total iuran yang sudah diterima (Rp)
- **Jumlah Transaksi**: Total transaksi berhasil
- **Pending**: Jumlah transaksi menunggu pembayaran
- **Total Kabupaten**: Jumlah kabupaten yang terdaftar

#### 📈 Grafik & Analytics

**Trend Bulanan:**
- Grafik line chart pembayaran per bulan
- Bisa filter berdasarkan tahun

**Distribusi Metode Pembayaran:**
- Pie chart metode pembayaran
- Credit Card, Bank Transfer, E-Wallet, dll

**Top Contributors:**
- Kabupaten dengan total pembayaran tertinggi
- Ranking 10 teratas

#### 📋 Transaksi Terbaru

- Daftar 10 transaksi terakhir
- Informasi kabupaten, jumlah, status
- Aksi cepat (Detail, Export)

#### 🔔 Notifikasi

- Alert pembayaran baru
- Jumlah notifikasi belum dibaca
- Quick action (Tandai dibaca, Lihat detail)

---

### B. Melihat Semua Transaksi

#### Akses Riwayat Transaksi

1. Klik menu **"Riwayat Transaksi"**
2. Atau dari dashboard, klik **"Lihat Semua"**

#### Filter & Search

**Filter Options:**
- **Status**: Semua/Pending/Lunas/Gagal
- **Tanggal**: Range tanggal
- **Kabupaten**: Pilih kabupaten tertentu
- **Metode Pembayaran**: Credit Card/Bank Transfer/dll

**Search:**
- Cari berdasarkan Order ID
- Cari berdasarkan nama kabupaten
- Cari berdasarkan deskripsi

#### Export Data

1. Klik **"Export"**
2. Pilih format:
   - **Excel (.xlsx)** - Untuk analisis
   - **PDF** - Untuk laporan
   - **CSV** - Untuk import ke sistem lain
3. File akan otomatis terdownload

---

### C. Mengelola Notifikasi

#### Akses Notifikasi

1. Klik **icon bell** 🔔 di header
2. Atau klik menu **"Notifikasi"**

#### Jenis Notifikasi

**Pembayaran Baru:**
- Notifikasi setiap ada pembayaran berhasil
- Informasi kabupaten dan jumlah
- Link ke detail transaksi

**Pembayaran Pending:**
- Alert jika ada pembayaran pending lama
- Reminder untuk follow up

#### Mengelola Notifikasi

**Tandai Satu Notifikasi Dibaca:**
1. Klik notifikasi
2. Otomatis ditandai sebagai dibaca

**Tandai Semua Dibaca:**
1. Klik **"Tandai Semua Dibaca"**
2. Semua notifikasi menjadi read

**Hapus Notifikasi:**
1. Klik **icon trash** di notifikasi
2. Konfirmasi hapus

---

### D. Generate Laporan

#### Laporan Bulanan

**Cara Generate:**
1. Klik menu **"Laporan"** → **"Laporan Bulanan"**
2. Pilih **Bulan** dan **Tahun**
3. Klik **"Generate Laporan"**

**Isi Laporan:**
- Total penerimaan bulan tersebut
- Jumlah transaksi
- Breakdown per kabupaten
- Grafik trend
- Detail setiap transaksi

**Export:**
- Klik **"Export PDF"** untuk laporan formal
- Klik **"Export Excel"** untuk analisis

#### Laporan per Kabupaten

**Cara Generate:**
1. Klik menu **"Laporan"** → **"Laporan Kabupaten"**
2. Pilih **Kabupaten**
3. Pilih **Range Tanggal**
4. Klik **"Generate Laporan"**

**Isi Laporan:**
- Profil kabupaten
- Total pembayaran dalam periode
- Jumlah transaksi
- Average per transaksi
- Timeline pembayaran
- Detail setiap transaksi

**Export:**
- PDF untuk dokumentasi
- Excel untuk analisis

#### Laporan Custom

**Cara Generate:**
1. Klik menu **"Laporan"** → **"Laporan Custom"**
2. Pilih **kriteria**:
   - Range tanggal
   - Status transaksi
   - Metode pembayaran
   - Kabupaten (multiple select)
3. Klik **"Generate"**

---

### E. Mengelola Pengaturan

#### Akses Pengaturan

1. Klik menu **"Pengaturan"**
2. Atau klik **nama Anda** → **"Settings"**

#### Pengaturan yang Tersedia

**Profil Admin:**
- Edit nama
- Edit email
- Ganti password

**Pengaturan Email:**
- Template email konfirmasi
- Email CC untuk notifikasi
- Enable/disable email notifications

**Pengaturan Sistem:**
- Minimum amount pembayaran
- Timeout transaksi pending
- Maintenance mode

---

### F. Tips untuk Admin

✅ **Cek dashboard** setiap hari  
✅ **Monitor notifikasi** secara berkala  
✅ **Generate laporan** setiap akhir bulan  
✅ **Export data** untuk backup  
✅ **Follow up** transaksi pending lama  
✅ **Analisis trend** pembayaran  
✅ **Koordinasi** dengan kabupaten jika ada masalah  

---

## 💳 Metode Pembayaran

### 1. Credit/Debit Card

**Supported Cards:**
- Visa
- Mastercard
- JCB
- American Express

**Keunggulan:**
- ✅ Proses instant
- ✅ Aman dengan 3D Secure
- ✅ Bisa cicilan (untuk kartu kredit tertentu)

**Biaya:**
- Gratis untuk debit card
- Sesuai ketentuan bank untuk credit card

**Waktu Proses:**
- Instant (beberapa detik)

---

### 2. Bank Transfer (Virtual Account)

**Supported Banks:**
- BCA
- Mandiri
- BNI
- BRI
- Permata

**Keunggulan:**
- ✅ Nomor VA unik per transaksi
- ✅ Auto-verify setelah transfer
- ✅ Bisa via mobile banking/ATM/internet banking

**Biaya:**
- Sesuai biaya transfer bank masing-masing
- Biasanya gratis untuk sesama bank

**Waktu Proses:**
- Real-time (beberapa menit setelah transfer)

**Berlaku:**
- 24 jam setelah transaksi dibuat
- Setelah expire, buat transaksi baru

---

### 3. E-Wallet

**Supported E-Wallets:**
- GoPay
- ShopeePay
- DANA
- OVO (LinkAja)

**Keunggulan:**
- ✅ Proses instant
- ✅ Scan QR Code
- ✅ Cashback (tergantung promo)

**Biaya:**
- Gratis

**Waktu Proses:**
- Instant (beberapa detik)

---

### 4. Retail Outlet

**Supported Outlets:**
- Indomaret
- Alfamart

**Keunggulan:**
- ✅ Bayar tunai
- ✅ Tersedia di mana-mana
- ✅ Dapat struk fisik

**Biaya:**
- Rp 2.500 - Rp 5.000 (biaya admin)

**Waktu Proses:**
- Real-time setelah bayar di kasir

**Berlaku:**
- 24 jam setelah transaksi dibuat

---

## ❓ FAQ (Pertanyaan Umum)

### Untuk Kabupaten

**Q: Apakah pembayaran aman?**  
A: Ya, sangat aman. Kami menggunakan Midtrans yang PCI-DSS certified. Data kartu kredit Anda tidak disimpan di sistem kami.

**Q: Berapa lama pembayaran diverifikasi?**  
A: Instant! Setelah pembayaran berhasil, status otomatis berubah menjadi "Lunas" dalam beberapa detik.

**Q: Apakah saya dapat bukti pembayaran?**  
A: Ya, bukti pembayaran otomatis dikirim ke email Anda setelah pembayaran berhasil.

**Q: Bagaimana jika pembayaran gagal?**  
A: Anda bisa membuat transaksi baru atau melanjutkan pembayaran yang pending (jika belum expire).

**Q: Apakah ada biaya tambahan?**  
A: Tidak ada biaya tambahan dari sistem. Biaya hanya dari payment provider (bank/e-wallet) sesuai ketentuan masing-masing.

**Q: Berapa minimal pembayaran?**  
A: Minimal Rp 1.000 (seribu rupiah).

**Q: Apakah bisa bayar cicilan?**  
A: Untuk kartu kredit tertentu, bisa. Pilih opsi cicilan saat pembayaran dengan credit card.

**Q: Bagaimana jika lupa Order ID?**  
A: Cek di menu "Kelola Iuran" atau cek email konfirmasi yang dikirim saat membuat transaksi.

---

### Untuk Admin

**Q: Apakah perlu approve pembayaran manual?**  
A: Tidak perlu! Semua pembayaran via Midtrans otomatis terverifikasi.

**Q: Bagaimana cara export data?**  
A: Klik tombol "Export" di halaman Riwayat Transaksi atau Laporan, lalu pilih format (Excel/PDF/CSV).

**Q: Apakah bisa filter transaksi berdasarkan tanggal?**  
A: Ya, gunakan filter "Range Tanggal" di halaman Riwayat Transaksi.

**Q: Bagaimana cara melihat laporan bulanan?**  
A: Klik menu "Laporan" → "Laporan Bulanan", pilih bulan dan tahun, lalu klik "Generate".

**Q: Apakah ada notifikasi email untuk admin?**  
A: Ya, admin menerima email setiap ada pembayaran baru berhasil.

**Q: Bagaimana cara menambah admin baru?**  
A: Hubungi super admin atau developer untuk menambahkan user dengan role "admin".

---

## 🔧 Troubleshooting

### Masalah Login

**Problem: Tidak bisa login**

**Solusi:**
1. Pastikan email dan password benar
2. Cek CAPS LOCK tidak aktif
3. Coba reset password
4. Clear browser cache
5. Coba browser lain
6. Hubungi admin jika masih gagal

---

### Masalah Pembayaran

**Problem: Popup Midtrans tidak muncul**

**Solusi:**
1. Disable popup blocker di browser
2. Refresh halaman
3. Coba browser lain
4. Clear browser cache
5. Pastikan JavaScript enabled

**Problem: Pembayaran berhasil tapi status masih pending**

**Solusi:**
1. Tunggu 5-10 menit
2. Refresh halaman
3. Cek email konfirmasi
4. Hubungi admin dengan Order ID

**Problem: Virtual Account sudah expire**

**Solusi:**
1. Jangan transfer ke VA yang expire
2. Buat transaksi baru
3. Gunakan VA yang baru

**Problem: Sudah bayar tapi belum masuk**

**Solusi:**
1. Cek email konfirmasi
2. Cek status di halaman "Kelola Iuran"
3. Tunggu maksimal 1 jam
4. Hubungi admin dengan:
   - Order ID
   - Bukti transfer
   - Waktu pembayaran

---

### Masalah Teknis

**Problem: Halaman loading lama**

**Solusi:**
1. Cek koneksi internet
2. Refresh halaman
3. Clear browser cache
4. Coba browser lain
5. Coba device lain

**Problem: Error 500**

**Solusi:**
1. Refresh halaman
2. Tunggu beberapa menit
3. Coba lagi
4. Hubungi admin jika masih error

**Problem: Tidak menerima email**

**Solusi:**
1. Cek folder Spam/Junk
2. Cek email yang terdaftar benar
3. Whitelist email noreply@pgri.or.id
4. Hubungi admin

---

## 📞 Kontak Support

### Tim Support

**Email Support:**
```
support@pgri.or.id
```

**Jam Operasional:**
- Senin - Jumat: 08:00 - 17:00 WIB
- Sabtu: 08:00 - 12:00 WIB
- Minggu & Libur: Tutup

**Response Time:**
- Email: Maksimal 1x24 jam (hari kerja)
- Urgent: Hubungi via telepon

### Informasi yang Perlu Disiapkan

Saat menghubungi support, siapkan informasi:

✅ **Nama Kabupaten** (untuk kabupaten)  
✅ **Email** yang terdaftar  
✅ **Order ID** (jika terkait transaksi)  
✅ **Screenshot** error (jika ada)  
✅ **Deskripsi masalah** yang detail  
✅ **Langkah** yang sudah dicoba  

### Midtrans Support

Untuk masalah teknis pembayaran:

**Email:**
```
support@midtrans.com
```

**Website:**
```
https://support.midtrans.com
```

---

## 📚 Lampiran

### A. Glossary (Istilah)

| Istilah | Penjelasan |
|---------|------------|
| **Order ID** | Nomor unik untuk setiap transaksi |
| **Transaction ID** | ID dari Midtrans setelah pembayaran |
| **Virtual Account** | Nomor rekening virtual untuk transfer |
| **Snap Token** | Token untuk popup pembayaran Midtrans |
| **Settlement** | Status pembayaran berhasil |
| **Pending** | Menunggu pembayaran |
| **Webhook** | Notifikasi otomatis dari Midtrans |
| **3D Secure** | Keamanan tambahan untuk kartu kredit |
| **OTP** | One-Time Password untuk verifikasi |

### B. Shortcut Keyboard

| Shortcut | Fungsi |
|----------|--------|
| `Ctrl + K` | Quick search |
| `Ctrl + /` | Toggle sidebar |
| `Esc` | Close modal/popup |
| `F5` | Refresh page |

### C. Browser yang Disupport

✅ **Google Chrome** (Recommended) - Versi 90+  
✅ **Mozilla Firefox** - Versi 88+  
✅ **Microsoft Edge** - Versi 90+  
✅ **Safari** - Versi 14+  
⚠️ **Internet Explorer** - Tidak disupport  

---

## 📝 Changelog

### Version 1.0 (Desember 2025)
- ✅ Integrasi Midtrans Payment Gateway
- ✅ Auto-verification pembayaran
- ✅ Email notifications
- ✅ Dashboard analytics
- ✅ Export laporan (Excel/PDF)
- ✅ Multi payment methods (20+)

---

**Terakhir Diupdate:** 29 Desember 2025  
**Versi Manual:** 1.0  
**Status:** ✅ Production Ready

---

**Terima kasih telah menggunakan Sistem Iuran PGRI!** 🎉

Jika ada pertanyaan atau masalah, jangan ragu untuk menghubungi tim support kami.
