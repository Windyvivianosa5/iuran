# 📊 Use Case Diagram - Sistem Iuran PGRI

**Tanggal Update:** 6 Januari 2026  
**Versi:** 3.0 (UML Purist - Akademik)  
**Status:** ✅ Final - Layak Sidang

---

## 🎯 Aktor dalam Sistem

1. **Kabupaten** - Pengguna yang melakukan pembayaran iuran
2. **Admin** - Pengguna yang mengelola sistem dan memonitor transaksi

---

## 📋 Use Case Diagram

```
┌──────────────────────────────────────────────────────────────────┐
│                     SISTEM IURAN PGRI                            │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│   ┌─────────┐                                                    │
│   │KABUPATEN│                                                    │
│   └────┬────┘                                                    │
│        │                                                         │
│        ├──► UC-01: Login ◄──────────────────────────┐            │
│        │                                            │            │
│        ├──► UC-02: Lihat Dashboard ──────►include───┘            │
│        │                                                         │
│        ├──► UC-03: Bayar Iuran                                   │
│        │         │                                               │
│        │         └──►extend► UC-04: Lihat Riwayat Transaksi     │
│        │                           │                             │
│        │                           ├──►extend► UC-12: Lihat Detail│
│        │                           │                             │
│        │                           └──►extend► UC-13: Lanjutkan Bayar│
│        │                                                         │
│        ├──► UC-05: Lihat Laporan Iuran                           │
│        │                                                         │
│        └──► UC-06: Logout                                        │
│                                                                  │
│                                                                  │
│   ┌─────────┐                                                    │
│   │  ADMIN  │                                                    │
│   └────┬────┘                                                    │
│        │                                                         │
│        ├──► UC-01: Login (shared) ◄─────────────────┐            │
│        │                                            │            │
│        ├──► UC-07: Lihat Dashboard Admin ──►include─┘            │
│        │                                                         │
│        ├──► UC-08: Lihat Riwayat Transaksi (All)                 │
│        │         │                                               │
│        │         └──►extend► UC-12: Lihat Detail Transaksi       │
│        │                                                         │
│        ├──► UC-09: Kelola Notifikasi                             │
│        │                                                         │
│        ├──► UC-10: Buat Laporan                                  │
│        │                                                         │
│        ├──► UC-11: Kelola Data Kabupaten                         │
│        │                                                         │
│        └──► UC-06: Logout (shared)                               │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘
```

---
@startuml
title Use Case Diagram - Sistem Iuran PGRI

left to right direction
skinparam packageStyle rectangle

actor "Kabupaten" as Kabupaten
actor "Admin" as Admin

rectangle "Sistem Iuran PGRI" {
  ' Use Cases Autentikasi (Shared)
  usecase "UC-01: Login" as UC01
  usecase "UC-06: Logout" as UC06
  
  ' Use Cases Kabupaten
  usecase "UC-02: Lihat Dashboard\nKabupaten" as UC02
  usecase "UC-03: Bayar Iuran" as UC03
  usecase "UC-04: Lihat Riwayat\nTransaksi" as UC04
  usecase "UC-05: Lihat Laporan\nIuran" as UC05
  
  ' Use Cases Admin
  usecase "UC-07: Lihat Dashboard\nAdmin" as UC07
  usecase "UC-08: Lihat Riwayat\nTransaksi (All)" as UC08
  usecase "UC-09: Kelola Notifikasi" as UC09
  usecase "UC-10: Buat Laporan" as UC10
  usecase "UC-11: Kelola Data\nKabupaten" as UC11
  
  ' Use Cases Tambahan (untuk extend)
  usecase "UC-12: Lihat Detail\nTransaksi" as UC12
  usecase "UC-13: Lanjutkan\nPembayaran" as UC13
}

' Relasi Kabupaten
Kabupaten --> UC01
Kabupaten --> UC02
Kabupaten --> UC03
Kabupaten --> UC04
Kabupaten --> UC05
Kabupaten --> UC06

' Relasi Admin
Admin --> UC01
Admin --> UC07
Admin --> UC08
Admin --> UC09
Admin --> UC10
Admin --> UC11
Admin --> UC06

' Include & Extend Relationships
UC02 ..> UC01 : <<include>>
UC07 ..> UC01 : <<include>>

UC04 ..> UC12 : <<extend>>
UC04 ..> UC13 : <<extend>>
UC08 ..> UC12 : <<extend>>

UC03 ..> UC04 : <<extend>>

' Notes
note right of UC03
  Kabupaten melakukan pembayaran
  melalui payment gateway.
  Sistem memproses pembayaran
  secara otomatis di backend.
end note

note right of UC08
  Admin memonitor semua transaksi
  dari seluruh kabupaten.
  Tidak ada verifikasi manual.
end note

note bottom of UC01
  **Use Case Shared:**
  Login digunakan oleh kedua aktor.
  Sistem akan redirect ke dashboard
  sesuai dengan role pengguna.
end note

@enduml
## 🎨 PlantUML Code

Salin kode di bawah ini dan jalankan di [plantuml.com](https://www.plantuml.com/plantuml/uml/)

```plantuml

```

---

## 📝 Deskripsi Use Case

### 🔵 Use Case SHARED (Kabupaten & Admin)

#### **UC-01: Login**
- **Aktor:** Kabupaten, Admin
- **Deskripsi:** Pengguna melakukan autentikasi ke dalam sistem menggunakan kredensial yang telah terdaftar
- **Precondition:** Pengguna memiliki akun yang valid
- **Postcondition:** Pengguna berhasil masuk ke sistem dan diarahkan ke dashboard sesuai role
- **Flow Normal:**
  1. Pengguna membuka halaman login
  2. Pengguna memasukkan email dan password
  3. Sistem memvalidasi kredensial
  4. Sistem mengarahkan pengguna ke dashboard sesuai role

#### **UC-06: Logout**
- **Aktor:** Kabupaten, Admin
- **Deskripsi:** Pengguna mengakhiri sesi dan keluar dari sistem
- **Precondition:** Pengguna sudah login
- **Postcondition:** Sesi pengguna berakhir dan diarahkan ke halaman login
- **Flow Normal:**
  1. Pengguna memilih menu logout
  2. Sistem mengakhiri sesi
  3. Sistem mengarahkan ke halaman login

---

### 🔵 Use Case KABUPATEN

#### **UC-02: Lihat Dashboard Kabupaten**
- **Aktor:** Kabupaten
- **Deskripsi:** Kabupaten melihat ringkasan informasi transaksi dan statistik pembayaran iuran
- **Precondition:** Kabupaten sudah login
- **Postcondition:** Dashboard ditampilkan dengan informasi terkini
- **Flow Normal:**
  1. Kabupaten membuka halaman dashboard
  2. Sistem menampilkan ringkasan transaksi
  3. Sistem menampilkan statistik pembayaran
  4. Sistem menampilkan grafik visualisasi data

#### **UC-03: Bayar Iuran**
- **Aktor:** Kabupaten
- **Deskripsi:** Kabupaten melakukan pembayaran iuran PGRI melalui payment gateway
- **Precondition:** Kabupaten sudah login
- **Postcondition:** Transaksi pembayaran tercatat dalam sistem
- **Flow Normal:**
  1. Kabupaten memilih menu "Bayar Iuran"
  2. Kabupaten mengisi jumlah pembayaran dan keterangan
  3. Kabupaten mengklik tombol "Bayar"
  4. Sistem menampilkan halaman payment gateway
  5. Kabupaten memilih metode pembayaran
  6. Kabupaten menyelesaikan pembayaran
  7. Sistem mencatat transaksi
- **Flow Alternatif:**
  - Jika pembayaran dibatalkan, sistem menampilkan notifikasi dan kembali ke halaman utama

#### **UC-04: Lihat Riwayat Transaksi**
- **Aktor:** Kabupaten
- **Deskripsi:** Kabupaten melihat daftar semua transaksi pembayaran yang pernah dilakukan
- **Precondition:** Kabupaten sudah login
- **Postcondition:** Daftar transaksi ditampilkan
- **Flow Normal:**
  1. Kabupaten membuka halaman riwayat transaksi
  2. Sistem menampilkan daftar transaksi
  3. Kabupaten dapat melihat detail transaksi
  4. Kabupaten dapat melanjutkan pembayaran yang pending

#### **UC-05: Lihat Laporan Iuran**
- **Aktor:** Kabupaten
- **Deskripsi:** Kabupaten melihat laporan pembayaran iuran yang telah berhasil
- **Precondition:** Kabupaten sudah login
- **Postcondition:** Laporan iuran ditampilkan
- **Flow Normal:**
  1. Kabupaten membuka halaman laporan
  2. Sistem menampilkan daftar iuran yang telah dibayar
  3. Kabupaten dapat melihat detail setiap iuran

#### **UC-12: Lihat Detail Transaksi**
- **Aktor:** Kabupaten, Admin
- **Deskripsi:** Pengguna melihat informasi detail dari suatu transaksi
- **Precondition:** Pengguna sudah membuka halaman riwayat transaksi
- **Postcondition:** Detail transaksi ditampilkan
- **Flow Normal:**
  1. Pengguna memilih transaksi dari daftar
  2. Sistem menampilkan detail transaksi (ID, tanggal, jumlah, status, metode pembayaran, dll)
- **Relasi:** <<extend>> dari UC-04 dan UC-08
- **Catatan:** Use case ini bersifat opsional, user tidak harus melihat detail

#### **UC-13: Lanjutkan Pembayaran**
- **Aktor:** Kabupaten
- **Deskripsi:** Kabupaten melanjutkan pembayaran untuk transaksi yang masih berstatus pending
- **Precondition:** 
  - Kabupaten sudah membuka halaman riwayat transaksi
  - Ada transaksi dengan status pending
- **Postcondition:** Kabupaten diarahkan ke halaman payment gateway
- **Flow Normal:**
  1. Kabupaten memilih transaksi pending
  2. Kabupaten mengklik tombol "Lanjutkan Pembayaran"
  3. Sistem menampilkan halaman payment gateway
  4. Kabupaten menyelesaikan pembayaran
- **Relasi:** <<extend>> dari UC-04
- **Catatan:** Use case ini hanya muncul jika ada transaksi pending

---

### 🔴 Use Case ADMIN

#### **UC-07: Lihat Dashboard Admin**
- **Aktor:** Admin
- **Deskripsi:** Admin melihat statistik keseluruhan sistem dan ringkasan transaksi dari semua kabupaten
- **Precondition:** Admin sudah login
- **Postcondition:** Dashboard admin ditampilkan
- **Flow Normal:**
  1. Admin membuka halaman dashboard
  2. Sistem menampilkan statistik keseluruhan
  3. Sistem menampilkan grafik transaksi
  4. Sistem menampilkan ringkasan pembayaran

#### **UC-08: Lihat Riwayat Transaksi (All)**
- **Aktor:** Admin
- **Deskripsi:** Admin melihat semua transaksi dari seluruh kabupaten untuk keperluan monitoring
- **Precondition:** Admin sudah login
- **Postcondition:** Daftar semua transaksi ditampilkan
- **Flow Normal:**
  1. Admin membuka halaman riwayat transaksi
  2. Sistem menampilkan daftar semua transaksi
  3. Admin dapat memfilter berdasarkan status atau kabupaten
  4. Admin dapat melihat detail transaksi
- **Catatan:** Admin hanya dapat melihat data (read-only), tidak dapat melakukan verifikasi manual

#### **UC-09: Kelola Notifikasi**
- **Aktor:** Admin
- **Deskripsi:** Admin melihat dan mengelola notifikasi transaksi pembayaran baru
- **Precondition:** Admin sudah login
- **Postcondition:** Notifikasi dikelola
- **Flow Normal:**
  1. Admin membuka halaman notifikasi
  2. Sistem menampilkan daftar notifikasi
  3. Admin dapat melihat detail notifikasi
  4. Admin dapat menandai notifikasi sebagai sudah dibaca
  5. Admin dapat menandai semua notifikasi sebagai dibaca

#### **UC-10: Buat Laporan**
- **Aktor:** Admin
- **Deskripsi:** Admin membuat dan mengekspor laporan keuangan pembayaran iuran
- **Precondition:** Admin sudah login
- **Postcondition:** Laporan dibuat dan dapat diunduh
- **Flow Normal:**
  1. Admin membuka halaman laporan
  2. Admin memilih periode laporan
  3. Admin memilih format laporan (PDF/Excel)
  4. Sistem menghasilkan laporan
  5. Admin mengunduh laporan

#### **UC-11: Kelola Data Kabupaten**
- **Aktor:** Admin
- **Deskripsi:** Admin mengelola data kabupaten/kota yang terdaftar dalam sistem
- **Precondition:** Admin sudah login
- **Postcondition:** Data kabupaten terkelola
- **Flow Normal:**
  1. Admin membuka halaman kelola kabupaten
  2. Admin dapat melihat daftar kabupaten
  3. Admin dapat menambah kabupaten baru
  4. Admin dapat mengubah data kabupaten
  5. Admin dapat menghapus kabupaten
- **Flow Alternatif:**
  - Saat menambah kabupaten, admin dapat sekaligus membuat akun user untuk kabupaten tersebut

---

## 📌 Catatan Penting untuk Sidang

### 🎯 Prinsip UML Purist yang Diterapkan:

1. **Fokus pada Interaksi User:**
   - Diagram hanya menampilkan use case yang melibatkan interaksi langsung dengan aktor manusia
   - Tidak ada proses internal sistem (webhook, update status, kirim email)

2. **Tidak Ada Aktor Sistem Eksternal:**
   - Midtrans (Payment Gateway) tidak ditampilkan sebagai aktor
   - Payment gateway dianggap sebagai bagian dari implementasi teknis use case "Bayar Iuran"

3. **Use Case Merepresentasikan Tujuan User:**
   - Setiap use case menggambarkan tujuan yang ingin dicapai pengguna
   - Bukan mekanisme teknis atau proses internal

4. **Penggunaan <<include>> dan <<extend>> yang Tepat:**
   - **<<include>>** digunakan untuk fungsionalitas yang WAJIB dilakukan
   - **<<extend>>** digunakan untuk fungsionalitas yang OPSIONAL

### 📐 Penjelasan Relasi Include dan Extend:

#### **Relasi <<include>>:**

1. **UC-02 include UC-01** (Lihat Dashboard include Login)
   - Untuk melihat dashboard, user **WAJIB** login terlebih dahulu
   - Login adalah precondition yang tidak bisa dilewati

2. **UC-07 include UC-01** (Lihat Dashboard Admin include Login)
   - Sama seperti di atas, admin **WAJIB** login untuk melihat dashboard

**Prinsip:** Use case yang di-include **SELALU** dieksekusi sebagai bagian dari use case utama.

#### **Relasi <<extend>>:**

1. **UC-04 extend UC-12** (Lihat Riwayat Transaksi extend Lihat Detail)
   - Saat melihat riwayat transaksi, user **BISA** (opsional) melihat detail transaksi
   - User tidak harus melihat detail, cukup melihat daftar saja

2. **UC-04 extend UC-13** (Lihat Riwayat Transaksi extend Lanjutkan Pembayaran)
   - Saat melihat riwayat transaksi, user **BISA** (opsional) melanjutkan pembayaran pending
   - Hanya muncul jika ada transaksi pending

3. **UC-08 extend UC-12** (Lihat Riwayat Transaksi (All) extend Lihat Detail)
   - Admin juga **BISA** (opsional) melihat detail transaksi

4. **UC-03 extend UC-04** (Bayar Iuran extend Lihat Riwayat Transaksi)
   - Setelah melakukan pembayaran, user **BISA** (opsional) langsung melihat riwayat transaksi
   - Atau user bisa kembali ke dashboard

**Prinsip:** Use case yang di-extend **TIDAK SELALU** dieksekusi, tergantung kondisi atau pilihan user.

### 🎓 Kesederhanaan untuk Sidang:

Diagram ini menggunakan relasi include dan extend **secukupnya**, tidak berlebihan:
- ✅ Hanya 2 relasi <<include>> (untuk login yang wajib)
- ✅ Hanya 4 relasi <<extend>> (untuk fitur opsional)
- ✅ Total 13 use case (sederhana dan mudah dipahami)
- ✅ Tidak ada nested include/extend yang membingungkan

### 💡 Penjelasan untuk Reviewer/Penguji:

**Q: Bagaimana dengan proses otomatis seperti verifikasi pembayaran?**  
**A:** Proses otomatis (webhook, update status, kirim email) merupakan **implementasi teknis** yang tidak perlu ditampilkan dalam use case diagram. Proses tersebut dijelaskan dalam:
- **Activity Diagram** - untuk alur proses detail
- **Sequence Diagram** - untuk interaksi antar komponen
- **Dokumentasi Arsitektur Sistem** - untuk detail teknis

**Q: Mengapa Midtrans tidak muncul sebagai aktor?**  
**A:** Menurut prinsip UML, aktor adalah entitas eksternal yang **berinteraksi langsung** dengan sistem untuk mencapai tujuan. Midtrans adalah **sistem eksternal** yang digunakan oleh sistem kita sebagai **tools/service**, bukan aktor yang memiliki tujuan sendiri.

**Q: Bagaimana sistem tahu pembayaran berhasil?**  
**A:** Ini adalah **detail implementasi** yang tidak perlu muncul di use case diagram. Dari perspektif user:
- User melakukan pembayaran (UC-03)
- Sistem mencatat transaksi
- User melihat status di riwayat transaksi (UC-04)

Mekanisme webhook dan verifikasi otomatis dijelaskan di diagram lain (Activity/Sequence).

---

## 🔄 Perubahan dari Versi Sebelumnya

### ❌ Use Case yang DIHAPUS:

| Use Case Lama | Alasan Dihapus |
|---------------|----------------|
| **UC-16 s/d UC-20 (Proses Backend)** | Bukan interaksi user, merupakan proses internal sistem |
| **Aktor Midtrans** | Sistem eksternal, bukan aktor yang memiliki tujuan |
| **Lihat Detail Transaksi** | Digabung dengan "Lihat Riwayat Transaksi" |
| **Lanjutkan Pembayaran** | Merupakan bagian dari "Lihat Riwayat Transaksi" |
| **Lihat Detail Notifikasi** | Merupakan bagian dari "Kelola Notifikasi" |
| **Tandai Semua Dibaca** | Merupakan bagian dari "Kelola Notifikasi" |

### ✅ Penyederhanaan yang Dilakukan:

1. **Menggunakan relasi <<include>> dan <<extend>> dengan tepat:**
   - <<include>> untuk fungsionalitas wajib (Login)
   - <<extend>> untuk fungsionalitas opsional (Lihat Detail, Lanjutkan Pembayaran)
   
2. **Menggabungkan use case yang terlalu detail:**
   - "Lihat Detail Notifikasi" dan "Tandai Semua Dibaca" → bagian dari "Kelola Notifikasi"
   
3. **Menghapus proses backend:** 
   - Hanya interaksi user yang ditampilkan
   
4. **Menghapus aktor sistem eksternal:** 
   - Hanya aktor manusia yang ditampilkan

### 📊 Ringkasan Use Case:

**Total Use Case:** 13
- UC-01: Login (shared)
- UC-02: Lihat Dashboard Kabupaten
- UC-03: Bayar Iuran
- UC-04: Lihat Riwayat Transaksi
- UC-05: Lihat Laporan Iuran
- UC-06: Logout (shared)
- UC-07: Lihat Dashboard Admin
- UC-08: Lihat Riwayat Transaksi (All)
- UC-09: Kelola Notifikasi
- UC-10: Buat Laporan
- UC-11: Kelola Data Kabupaten
- UC-12: Lihat Detail Transaksi (extend)
- UC-13: Lanjutkan Pembayaran (extend)

**Relasi:**
- 2 relasi <<include>>
- 4 relasi <<extend>>

---

## 📊 Diagram Sequence (Contoh: Bayar Iuran)

```
Kabupaten          Sistem           Payment Gateway
    │                │                    │
    │─── Bayar Iuran ──────────────►│     │
    │                │                    │
    │                │─── Request Payment ────────►│
    │                │                    │
    │◄─── Redirect to Payment Page ───────────────│
    │                │                    │
    │─── Complete Payment ─────────────────────────►│
    │                │                    │
    │                │◄─── Callback ──────│
    │                │                    │
    │◄─── Confirmation ──────────────│     │
    │                │                    │
```

**Catatan:** Callback dari payment gateway ke sistem adalah **implementasi teknis** yang tidak muncul di use case diagram.

---

## 📝 Kesimpulan

Diagram use case ini telah disusun sesuai dengan **prinsip UML purist** dan **standar akademik** untuk keperluan sidang:

✅ **Fokus pada interaksi user dengan sistem**  
✅ **Tidak ada proses internal sistem**  
✅ **Tidak ada aktor sistem eksternal**  
✅ **Sederhana dan mudah dipahami**  
✅ **Setiap use case merepresentasikan tujuan user**  
✅ **Menggunakan <<include>> dan <<extend>> dengan tepat**  
✅ **Total 13 use case dengan 6 relasi**  
✅ **Layak untuk presentasi sidang**

### 📐 Struktur Diagram:

- **2 Aktor:** Kabupaten dan Admin
- **13 Use Case:** 11 use case utama + 2 use case extension
- **2 Shared Use Case:** Login dan Logout
- **2 Relasi <<include>>:** Dashboard → Login (wajib)
- **4 Relasi <<extend>>:** Fitur opsional (detail transaksi, lanjutkan pembayaran)

---

**Dibuat oleh:** AI Assistant  
**Versi:** 3.0 (UML Purist - Akademik)  
**Tanggal:** 6 Januari 2026
