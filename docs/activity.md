# Activity Diagram - Sistem Iuran PGRI

## Daftar Activity Diagram

Dokumen ini berisi beberapa activity diagram untuk proses-proses utama dalam Sistem Iuran PGRI:

1. [Proses Login](#1-proses-login)
2. [Proses Pembayaran Iuran via Midtrans (Kabupaten)](#2-proses-pembayaran-iuran-via-midtrans-kabupaten)
3. [Proses Kelola Transaksi Pembayaran (Kabupaten)](#3-proses-kelola-transaksi-pembayaran-kabupaten)
4. [Proses Melihat Riwayat Transaksi (Admin)](#4-proses-melihat-riwayat-transaksi-admin)
5. [Proses Webhook Midtrans](#5-proses-webhook-midtrans)

---

## 1. Proses Login

### Deskripsi
Activity diagram untuk proses autentikasi pengguna (Kabupaten dan Admin)

### PlantUML Code

```plantuml
@startuml
title Proses Login - Sistem Iuran PGRI

start
:User membuka halaman login;
:Input email dan password;

if (Validasi input?) then (valid)
  :Kirim request ke server;
  
  if (Kredensial benar?) then (ya)
    :Buat session user;
    
    if (Role user?) then (kabupaten)
      :Redirect ke Dashboard Kabupaten;
    elseif (admin) then
      :Redirect ke Dashboard Admin;
    else (user)
      :Redirect ke Dashboard User;
    endif
    
    :Tampilkan pesan sukses;
    stop
    
  else (tidak)
    :Tampilkan error "Email/Password salah";
    :Kembali ke form login;
    stop
  endif
  
else (tidak valid)
  :Tampilkan error validasi;
  :Kembali ke form login;
  stop
endif

@enduml
```

---

## 2. Proses Pembayaran Iuran via Midtrans (Kabupaten)

### Deskripsi
Activity diagram untuk proses pembayaran iuran menggunakan Midtrans Payment Gateway

### PlantUML Code

```plantuml
@startuml
title Proses Pembayaran Iuran via Midtrans

|Kabupaten|
start
:Login ke sistem;
:Buka halaman pembayaran iuran;
:Input jumlah pembayaran;
:Input deskripsi (opsional);

|Sistem|
:Validasi input;

if (Data valid?) then (tidak)
  |Kabupaten|
  :Tampilkan error validasi;
  stop
else (ya)
  |Sistem|
  :Generate Order ID unik;
  :Simpan data transaksi (status: pending);
  :Kirim request ke Midtrans API;
  
  |Midtrans|
  :Proses request pembayaran;
  :Generate Snap Token;
  
  |Sistem|
  :Terima Snap Token;
  :Update transaksi dengan Snap Token;
  
  |Kabupaten|
  :Tampilkan halaman Snap Midtrans;
  :Pilih metode pembayaran;
  :Lakukan pembayaran;
  
  |Midtrans|
  :Proses pembayaran;
  
  if (Pembayaran berhasil?) then (ya)
    :Kirim notifikasi webhook ke sistem;
    
    |Sistem|
    :Terima webhook notification;
    :Update status transaksi (settlement);
    :Buat record iuran otomatis;
    :Set status verifikasi: diterima;
    :Kirim email konfirmasi ke Kabupaten;
    :Kirim email notifikasi ke Admin;
    
    |Kabupaten|
    :Tampilkan halaman sukses;
    :Redirect ke dashboard;
    stop
    
  else (gagal/expire/cancel)
    :Kirim notifikasi webhook ke sistem;
    
    |Sistem|
    :Update status transaksi (gagal);
    
    |Kabupaten|
    :Tampilkan halaman gagal;
    :Kembali ke halaman pembayaran;
    stop
  endif
endif

@enduml
```

---

## 3. Proses Kelola Transaksi Pembayaran (Kabupaten)

### Deskripsi
Activity diagram untuk proses kelola transaksi pembayaran via Midtrans (view, continue payment, view detail). Tidak ada upload bukti manual karena sistem sudah terintegrasi dengan Midtrans.

### PlantUML Code

```plantuml
@startuml
title Proses Kelola Transaksi Pembayaran - Kabupaten

|Kabupaten|
start

:Login ke sistem;
:Buka halaman daftar transaksi;

|Sistem|
:Ambil semua transaksi user;
:Tampilkan daftar dengan status;
note right
  Status yang ditampilkan:
  - Settlement (Lunas)
  - Pending (Belum bayar)
  - Expire/Cancel/Deny
end note

|Kabupaten|
:Lihat daftar transaksi;

if (Aksi yang dipilih?) then (Buat Pembayaran Baru)
  :Klik tombol "Bayar Iuran";
  :Input jumlah pembayaran;
  :Input deskripsi (opsional);
  
  |Sistem|
  :Validasi input;
  
  if (Data valid?) then (tidak)
    |Kabupaten|
    :Tampilkan error validasi;
    stop
  else (ya)
    |Sistem|
    :Generate Order ID unik;
    :Simpan transaksi (status: pending);
    :Kirim request ke Midtrans API;
    
    |Midtrans|
    :Generate Snap Token;
    
    |Sistem|
    :Terima Snap Token;
    :Update transaksi dengan token;
    
    |Kabupaten|
    :Tampilkan popup Midtrans Snap;
    :Pilih metode pembayaran;
    note right
      Metode yang tersedia:
      - Credit Card
      - Bank Transfer
      - E-Wallet (GoPay, OVO, dll)
      - QRIS
      - Convenience Store
    end note
    :Lakukan pembayaran;
    
    |Midtrans|
    :Proses pembayaran;
    
    if (Pembayaran berhasil?) then (ya)
      :Kirim webhook ke sistem;
      
      |Sistem|
      :Update status: settlement;
      
      |Kabupaten|
      :Tampilkan halaman sukses;
      :Redirect ke daftar transaksi;
      stop
    else (gagal/cancel)
      |Kabupaten|
      :Tampilkan halaman gagal;
      :Kembali ke daftar transaksi;
      stop
    endif
  endif

elseif (Lanjutkan Pembayaran Pending) then
  :Pilih transaksi pending;
  :Klik tombol "Lanjutkan Bayar";
  
  |Sistem|
  :Ambil Snap Token dari transaksi;
  
  if (Snap Token masih valid?) then (ya)
    |Kabupaten|
    :Tampilkan popup Midtrans Snap;
    :Lanjutkan proses pembayaran;
    note right
      User melanjutkan pembayaran
      yang sempat dibatalkan/ditunda
    end note
    stop
  else (tidak/expire)
    |Sistem|
    :Update status: expire;
    
    |Kabupaten|
    :Tampilkan pesan "Transaksi expired";
    :Sarankan buat transaksi baru;
    stop
  endif

elseif (Lihat Detail Transaksi) then
  :Pilih transaksi;
  :Klik tombol "Detail";
  
  |Sistem|
  :Ambil detail transaksi lengkap;
  :Ambil data iuran terkait (jika lunas);
  
  |Kabupaten|
  :Tampilkan detail transaksi;
  note right
    Info yang ditampilkan:
    - Order ID
    - Transaction ID
    - Jumlah pembayaran
    - Metode pembayaran
    - Status pembayaran
    - Tanggal transaksi
    - Tanggal settlement (jika lunas)
    - Deskripsi
  end note
  
  if (Status = Settlement?) then (ya)
    :Tampilkan badge "LUNAS";
    :Tampilkan info settlement;
  elseif (Status = Pending?) then
    :Tampilkan badge "PENDING";
    :Tampilkan tombol "Lanjutkan Bayar";
  else (Expire/Cancel/Deny)
    :Tampilkan badge status;
    :Tampilkan info kegagalan;
  endif
  
  stop

else (Lihat Laporan)
  |Sistem|
  :Ambil transaksi settlement;
  :Hitung total pembayaran;
  :Hitung jumlah transaksi;
  :Generate grafik bulanan;
  
  |Kabupaten|
  :Tampilkan laporan iuran;
  :Tampilkan statistik;
  :Tampilkan grafik;
  note right
    Laporan menampilkan:
    - Total iuran dibayar
    - Jumlah transaksi berhasil
    - Riwayat pembayaran
    - Grafik trend pembayaran
  end note
  stop
endif

@enduml
```

---

## 4. Proses Melihat Riwayat Transaksi (Admin)

### Deskripsi
Activity diagram untuk proses monitoring dan melihat riwayat transaksi yang sudah otomatis terverifikasi via webhook Midtrans. Admin hanya melakukan view/read, tidak ada action untuk mengubah status pembayaran.

### PlantUML Code

```plantuml
@startuml
title Proses Melihat Riwayat Transaksi - Admin

|Admin|
start
:Login ke sistem;
:Buka halaman dashboard admin;

if (Aksi yang dipilih?) then (Lihat Riwayat Transaksi)
  :Klik menu "Riwayat Transaksi";
  
  |Sistem|
  :Ambil semua transaksi dari database;
  :Urutkan berdasarkan tanggal terbaru;
  
  |Admin|
  :Tampilkan daftar transaksi;
  note right
    Status ditampilkan:
    - Lunas (otomatis via webhook)
    - Pending (belum bayar)
    - Gagal/Expire/Cancel
  end note
  
  if (Filter data?) then (ya)
    :Pilih filter (tanggal/status/kabupaten);
    
    |Sistem|
    :Filter data sesuai kriteria;
    :Tampilkan hasil filter;
    
    |Admin|
    :Review data terfilter;
  endif
  
  if (Lihat detail transaksi?) then (ya)
    :Klik transaksi tertentu;
    
    |Sistem|
    :Ambil detail transaksi lengkap;
    :Ambil data kabupaten terkait;
    :Ambil data iuran terkait (jika ada);
    
    |Admin|
    :Tampilkan detail transaksi;
    :Review informasi pembayaran;
    note right
      Info yang ditampilkan:
      - Order ID
      - Transaction ID
      - Jumlah pembayaran
      - Metode pembayaran
      - Status (sudah lunas otomatis)
      - Tanggal & waktu settlement
      - Data kabupaten
    end note
    
    :Kembali ke daftar;
  endif
  
  if (Cetak/Export laporan?) then (ya)
    :Pilih periode laporan;
    :Pilih format (PDF/Excel);
    
    |Sistem|
    :Generate laporan sesuai periode;
    :Format data sesuai pilihan;
    
    |Admin|
    :Download file laporan;
    :Simpan laporan;
  endif
  
  stop

elseif (Lihat Dashboard/Statistik) then
  |Sistem|
  :Ambil data transaksi terverifikasi;
  :Hitung total pembayaran;
  :Hitung jumlah transaksi sukses;
  :Hitung jumlah transaksi pending;
  :Generate grafik bulanan;
  :Generate rekap per kabupaten;
  
  |Admin|
  :Tampilkan dashboard statistik;
  :Tampilkan grafik/chart;
  :Tampilkan summary cards;
  note right
    Dashboard menampilkan:
    - Total Masuk (Rp)
    - Jumlah Transaksi Berhasil
    - Jumlah Transaksi Pending
    - Grafik trend bulanan
    - Top 5 kabupaten
  end note
  
  stop

else (Lihat Notifikasi)
  |Sistem|
  :Ambil notifikasi pembayaran baru;
  :Urutkan dari terbaru;
  
  |Admin|
  :Tampilkan daftar notifikasi;
  note right
    Notifikasi berisi info:
    - Kabupaten yang bayar
    - Jumlah pembayaran
    - Waktu pembayaran
    - Status: Sudah Lunas (otomatis)
  end note
  
  if (Tandai notifikasi dibaca?) then (ya)
    :Klik notifikasi;
    
    |Sistem|
    :Update status notifikasi: dibaca;
    :Redirect ke detail transaksi;
    
    |Admin|
    :Lihat detail transaksi;
  endif
  
  if (Tandai semua dibaca?) then (ya)
    :Klik "Tandai Semua Dibaca";
    
    |Sistem|
    :Update semua notifikasi: dibaca;
    
    |Admin|
    :Tampilkan pesan sukses;
    :Refresh halaman;
  endif
  
  stop
endif

@enduml
```

---

## 5. Proses Webhook Midtrans

### Deskripsi
Activity diagram untuk proses handling webhook notification dari Midtrans

### PlantUML Code

```plantuml
    @startuml
    title Proses Webhook Midtrans Notification

    |Midtrans|
    start
    :Pembayaran selesai diproses;
    :Kirim HTTP POST ke webhook URL;
    note right
    URL: /midtrans/notification
    Method: POST
    Body: JSON notification data
    end note

    |Sistem|
    :Terima webhook request;
    :Parse notification data;
    :Ambil transaction_status;
    :Ambil order_id;
    :Ambil fraud_status (jika ada);

    :Cari transaksi berdasarkan order_id;

    if (Transaksi ditemukan?) then (tidak)
    :Log error "Transaksi tidak ditemukan";
    :Return response 404;
    stop
    else (ya)
    :Update transaction_id;
    :Update payment_type;
    :Update transaction_time;
    
    if (Status transaksi?) then (capture)
        if (Fraud status = accept?) then (ya)
        :Update status: settlement;
        else (tidak)
        :Update status: pending;
        endif
        
    elseif (settlement) then
        :Update status: settlement;
        :Set settlement_time: now();
        
        partition "Create Iuran Record" {
        :Cek apakah iuran sudah ada;
        
        if (Iuran belum ada?) then (ya)
            :Buat record iuran baru;
            :Set kabupaten_id dari user_id;
            :Set jumlah dari gross_amount;
            :Set tanggal dari created_at;
            :Set deskripsi dari description;
            :Set terverifikasi: diterima;
            :Set bukti_transaksi: order_id;
            :Simpan iuran;
            :Log "Iuran created";
        else (tidak)
            :Skip create iuran;
            :Log "Iuran already exists";
        endif
        }
        
        partition "Send Email Notifications" {
        :Kirim email konfirmasi ke Kabupaten;
        note right
            Subject: Pembayaran Berhasil
            Content: Detail transaksi
        end note
        
        :Kirim email notifikasi ke Admin;
        note right
            Subject: Pembayaran Baru Diterima
            Content: Info pembayaran baru
        end note
        }
        
    elseif (pending) then
        :Update status: pending;
        
    elseif (deny) then
        :Update status: deny;
        
    elseif (expire) then
        :Update status: expire;
        
    else (cancel)
        :Update status: cancel;
    endif
    
    :Simpan perubahan transaksi;
    :Log status update;
    :Return response 200 OK;
    stop
    endif

    @enduml
```

---

## Cara Menggunakan

1. Buka [plantuml.com](https://www.plantuml.com/plantuml/uml/)
2. Pilih salah satu diagram yang ingin ditampilkan
3. Salin kode PlantUML (dari `@startuml` sampai `@enduml`)
4. Paste di editor PlantUML
5. Diagram akan otomatis ter-generate
6. Download diagram dalam format PNG, SVG, atau format lainnya

## Penjelasan Diagram

### 1. Proses Login
- Menggambarkan alur autentikasi user
- Validasi kredensial
- Redirect berdasarkan role (kabupaten/admin/user)

### 2. Proses Pembayaran via Midtrans
- Alur lengkap pembayaran dari input sampai konfirmasi
- Integrasi dengan Midtrans Snap
- Webhook notification handling
- Auto-create iuran record
- Email notification otomatis

### 3. Proses Kelola Transaksi Pembayaran
- **View daftar transaksi** dengan berbagai status (Settlement/Pending/Expire)
- **Buat pembayaran baru** via Midtrans (tidak ada upload bukti manual)
- **Lanjutkan pembayaran pending** yang sempat dibatalkan
- **Lihat detail transaksi** dengan info lengkap dari Midtrans
- **Lihat laporan** dan statistik pembayaran
- **Tidak ada CRUD manual** - semua via Midtrans

### 4. Proses Melihat Riwayat Transaksi (Admin)
- **Monitoring only** - Admin tidak melakukan approval manual
- View dan filter riwayat transaksi yang sudah otomatis lunas via webhook
- Export/cetak laporan transaksi
- Lihat dashboard statistik dan grafik
- Kelola notifikasi pembayaran (mark as read)
- **Tidak ada action untuk mengubah status pembayaran** (sudah otomatis)

### 5. Proses Webhook Midtrans
- Technical flow webhook handling
- Status mapping dari Midtrans
- **Auto-verification** untuk pembayaran Midtrans (tanpa campur tangan admin)
- Auto-create iuran record dengan status 'diterima'
- Email notification trigger

## Catatan

- Semua diagram menggunakan **swimlane** untuk memisahkan actor/sistem
- Menggunakan **decision node** untuk conditional flow
- Menggunakan **partition** untuk grouping proses terkait
- Menggunakan **note** untuk informasi tambahan
- Diagram dibuat berdasarkan implementasi aktual di controllers

## Teknologi yang Digunakan

- **Laravel**: Framework backend
- **Inertia.js**: Frontend framework
- **Midtrans**: Payment gateway
- **Email Notification**: Laravel Mail
- **Database**: MySQL/PostgreSQL
