# Use Case Diagram - Sistem Iuran PGRI

## PlantUML Code

Salin kode di bawah ini dan jalankan di [plantuml.com](https://www.plantuml.com/plantuml/uml/)

```plantuml
@startuml
left to right direction
skinparam packageStyle rectangle

actor "Kabupaten" as Kabupaten
actor "Admin" as Admin

rectangle "Sistem Iuran PGRI" {
  ' Use Cases Autentikasi (Shared)
  usecase "Login" as UC1
  usecase "Logout" as UC2
  
  ' Use Cases untuk Kabupaten
  usecase "Lihat Dashboard Kabupaten" as UC3
  usecase "Kelola Data Iuran" as UC4
  usecase "Tambah Iuran" as UC5
  usecase "Edit Iuran" as UC6
  usecase "Hapus Iuran" as UC7
  usecase "Lihat Detail Iuran" as UC8
  usecase "Buat Transaksi Pembayaran" as UC9
  usecase "Lihat Status Transaksi" as UC10
  usecase "Lihat Laporan Iuran" as UC11
  
  ' Use Cases untuk Admin
  usecase "Lihat Dashboard Admin" as UC12
  usecase "Kelola Laporan" as UC13
  usecase "Buat Laporan" as UC14
  usecase "Kelola Notifikasi" as UC15
  usecase "Lihat Detail Notifikasi" as UC16
  usecase "Lihat Riwayat Transaksi" as UC17
  usecase "Tandai Notifikasi Dibaca" as UC18
  usecase "Batalkan Notifikasi" as UC19
  usecase "Tandai Semua Notifikasi Dibaca" as UC20
  usecase "Kelola Pengaturan" as UC21
  
  ' Use Cases Sistem (Otomatisasi Internal)
  usecase "Proses Pembayaran via Midtrans" as UC22
  usecase "Terima Notifikasi Webhook" as UC23
  usecase "Kirim Email Konfirmasi" as UC24
  usecase "Kirim Email ke Admin" as UC25
  usecase "Update Status Otomatis" as UC26
}

' Relasi Kabupaten
Kabupaten --> UC1
Kabupaten --> UC2
Kabupaten --> UC3
Kabupaten --> UC4

' Relasi Admin
Admin --> UC1
Admin --> UC2
Admin --> UC12
Admin --> UC13
Admin --> UC15
Admin --> UC21
Admin --> UC17

' Include relationships untuk Kabupaten
UC3 ..> UC10 : <<include>>
UC3 ..> UC11 : <<include>>

UC4 ..> UC5 : <<include>>
UC4 ..> UC6 : <<include>>
UC4 ..> UC7 : <<include>>
UC4 ..> UC8 : <<include>>

UC5 ..> UC9 : <<extend>>
UC8 ..> UC9 : <<extend>>

' Include relationships untuk Admin
UC13 ..> UC14 : <<include>>

UC15 ..> UC16 : <<include>>
UC15 ..> UC18 : <<include>>
UC15 ..> UC19 : <<include>>
UC15 ..> UC20 : <<include>>

' Relasi Sistem Internal (Otomatisasi)
UC9 ..> UC22 : <<include>>
UC22 ..> UC23 : <<include>>
UC23 ..> UC26 : <<include>>
UC26 ..> UC24 : <<include>>
UC26 ..> UC25 : <<include>>

UC10 ..> UC26 : <<extend>>

note right of UC22
  Redirect ke halaman
  pembayaran Midtrans
end note

note right of UC23
  Webhook dari Midtrans
  (Background Process)
end note

note bottom of UC26
  Validasi Real-time:
  Status berubah jadi 'Lunas'
  tanpa campur tangan Admin
end note

@enduml
```

## Deskripsi Use Case

### Actor

1. **Kabupaten**: Pengguna dengan role kabupaten yang mengelola iuran dan melakukan pembayaran
2. **Admin**: Administrator sistem yang mengelola keseluruhan sistem dan verifikasi pembayaran

### Use Case Utama

#### Use Cases Bersama (Shared)
- **UC1 - Login**: Masuk ke sistem dengan kredensial
- **UC2 - Logout**: Keluar dari sistem

#### Kabupaten
- **UC3 - Lihat Dashboard Kabupaten**: Melihat dashboard khusus kabupaten dengan ringkasan iuran dan transaksi
- **UC4 - Kelola Data Iuran**: Mengelola data iuran (CRUD)
  - UC5: Tambah iuran baru
  - UC6: Edit data iuran
  - UC7: Hapus data iuran
  - UC8: Lihat detail iuran
- **UC9 - Buat Transaksi Pembayaran**: Membuat transaksi pembayaran melalui Midtrans
- **UC10 - Lihat Status Transaksi**: Melihat status pembayaran transaksi yang sudah diupdate otomatis
- **UC11 - Lihat Laporan Iuran**: Melihat laporan iuran kabupaten

#### Admin
- **UC12 - Lihat Dashboard Admin**: Melihat dashboard administrator dengan statistik sistem
- **UC13 - Kelola Laporan**: Mengelola laporan sistem
  - UC14: Membuat laporan baru
- **UC15 - Kelola Notifikasi**: Mengelola notifikasi pembayaran
  - UC16: Lihat detail notifikasi
  - UC18: Tandai notifikasi sebagai dibaca (satu notifikasi)
  - UC19: Batalkan notifikasi
  - UC20: Tandai semua notifikasi sebagai dibaca (bulk action)
- **UC17 - Lihat Riwayat Transaksi**: Memantau dan melihat riwayat semua transaksi yang sudah otomatis terverifikasi
- **UC21 - Kelola Pengaturan**: Mengelola pengaturan sistem

#### Proses Sistem Internal (Otomatisasi)
- **UC22 - Proses Pembayaran via Midtrans**: Sistem memproses pembayaran melalui Midtrans Payment Gateway
- **UC23 - Terima Notifikasi Webhook**: Sistem menerima notifikasi webhook dari Midtrans (background process)
- **UC26 - Update Status Otomatis**: Sistem otomatis mengupdate status pembayaran menjadi 'Lunas' berdasarkan webhook
- **UC24 - Kirim Email Konfirmasi**: Sistem mengirim email konfirmasi pembayaran ke kabupaten
- **UC25 - Kirim Email ke Admin**: Sistem mengirim notifikasi email pembayaran baru ke admin


## Cara Menggunakan

1. Buka [plantuml.com](https://www.plantuml.com/plantuml/uml/)
2. Salin seluruh kode PlantUML di atas (dari `@startuml` sampai `@enduml`)
3. Paste di editor PlantUML
4. Diagram akan otomatis ter-generate
5. Anda bisa download diagram dalam format PNG, SVG, atau format lainnya

## Catatan

- Diagram ini menggambarkan use case berdasarkan struktur routes dan controllers yang ada di project
- Terdapat **2 role utama**: **Kabupaten** dan **Admin**
- **Midtrans** adalah **proses sistem internal** (bukan actor eksternal)
- Sistem terintegrasi dengan Midtrans Payment Gateway untuk pemrosesan pembayaran
- **Sistem otomatis** mengupdate status pembayaran menjadi 'Lunas' via webhook (tanpa approval manual admin)
- Admin hanya **memantau** transaksi, tidak melakukan approve/ACC karena sudah otomatis
- Sistem memiliki fitur email notification otomatis untuk konfirmasi pembayaran
- Webhook dari Midtrans digunakan untuk update status pembayaran secara real-time
- Terdapat 2 fitur "Tandai Dibaca": 
  - **UC18**: Tandai 1 notifikasi spesifik
  - **UC20**: Tandai semua notifikasi sekaligus (bulk action)

