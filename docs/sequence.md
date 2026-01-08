# Sequence Diagram - Sistem Iuran PGRI

## Daftar Sequence Diagram

Dokumen ini berisi beberapa sequence diagram untuk proses-proses utama dalam Sistem Iuran PGRI:

1. [Proses Login](#1-proses-login)
2. [Proses Pembayaran Iuran via Midtrans](#2-proses-pembayaran-iuran-via-midtrans)
3. [Proses Webhook Midtrans](#3-proses-webhook-midtrans)
4. [Proses Tambah Iuran Manual](#4-proses-tambah-iuran-manual)
5. [Proses Verifikasi Pembayaran oleh Admin](#5-proses-verifikasi-pembayaran-oleh-admin)
6. [Proses Lihat Laporan Iuran](#6-proses-lihat-laporan-iuran)

---

## 1. Proses Login

### Deskripsi
Sequence diagram untuk proses autentikasi pengguna (Kabupaten dan Admin)

### PlantUML Code

```plantuml
@startuml
title Sequence Diagram - Proses Login

actor User
participant "Browser" as Browser
participant "AuthController" as Auth
participant "Middleware" as MW
participant "Database" as DB
participant "Session" as Session

User -> Browser: Buka halaman login
Browser -> Auth: GET /login
Auth --> Browser: Tampilkan form login

User -> Browser: Input email & password\nKlik "Login"
Browser -> Auth: POST /login\n{email, password}

Auth -> Auth: Validasi input
alt Input tidak valid
    Auth --> Browser: Return error validasi
    Browser --> User: Tampilkan error
else Input valid
    Auth -> DB: Query user by email
    DB --> Auth: Return user data
    
    alt User tidak ditemukan
        Auth --> Browser: Return error "User tidak ditemukan"
        Browser --> User: Tampilkan error
    else User ditemukan
        Auth -> Auth: Verify password
        
        alt Password salah
            Auth --> Browser: Return error "Password salah"
            Browser --> User: Tampilkan error
        else Password benar
            Auth -> Session: Create session
            Session --> Auth: Session created
            
            Auth -> MW: Check user role
            MW --> Auth: Role: kabupaten/admin/user
            
            alt Role = kabupaten
                Auth --> Browser: Redirect /kabupaten/dashboard
            else Role = admin
                Auth --> Browser: Redirect /admin/dashboard
            else Role = user
                Auth --> Browser: Redirect /dashboard
            end
            
            Browser --> User: Tampilkan dashboard
        end
    end
end

@enduml
```

---

## 2. Proses Pembayaran Iuran via Midtrans

### Deskripsi
Sequence diagram untuk proses pembayaran iuran menggunakan Midtrans Payment Gateway

### PlantUML Code

```plantuml
@startuml
title Sequence Diagram - Proses Pembayaran via Midtrans

actor Kabupaten
participant "Browser" as Browser
participant "TransactionController" as TC
participant "Database" as DB
participant "Midtrans API" as Midtrans
participant "Snap Payment Page" as Snap

Kabupaten -> Browser: Buka halaman pembayaran
Browser -> TC: GET /kabupaten/dashboard/iuran/create
TC --> Browser: Tampilkan form pembayaran

Kabupaten -> Browser: Input jumlah & deskripsi\nKlik "Bayar"
Browser -> TC: POST /kabupaten/transaction/create\n{amount, description}

TC -> TC: Validasi input
alt Input tidak valid
    TC --> Browser: Return error validasi
    Browser --> Kabupaten: Tampilkan error
else Input valid
    TC -> TC: Generate Order ID\n(TRX-{user_id}-{timestamp})
    
    TC -> DB: INSERT transaction\n(status: pending)
    DB --> TC: Transaction created
    
    TC -> TC: Prepare Midtrans params\n{order_id, amount, customer}
    
    TC -> Midtrans: POST /snap/v1/transactions\ngetSnapToken(params)
    Midtrans -> Midtrans: Validate & process request
    Midtrans --> TC: Return snap_token
    
    TC -> DB: UPDATE transaction\nSET snap_token = ?
    DB --> TC: Updated
    
    TC --> Browser: Return JSON\n{success: true, snap_token, order_id}
    
    Browser -> Snap: Open Snap Payment Page\nwith snap_token
    Snap --> Kabupaten: Tampilkan pilihan pembayaran
    
    Kabupaten -> Snap: Pilih metode & bayar
    Snap -> Midtrans: Process payment
    
    alt Pembayaran berhasil
        Midtrans --> Snap: Payment success
        Snap --> Browser: Redirect ke success page
        Browser --> Kabupaten: Tampilkan halaman sukses
        
        note right of Midtrans
            Midtrans akan mengirim
            webhook notification
            (lihat diagram webhook)
        end note
        
    else Pembayaran gagal/dibatalkan
        Midtrans --> Snap: Payment failed
        Snap --> Browser: Redirect ke failure page
        Browser --> Kabupaten: Tampilkan halaman gagal
    end
end

@enduml
```

---

## 3. Proses Webhook Midtrans

### Deskripsi
Sequence diagram untuk proses handling webhook notification dari Midtrans setelah pembayaran

### PlantUML Code

```plantuml
@startuml
title Sequence Diagram - Webhook Midtrans Notification

participant "Midtrans Server" as Midtrans
participant "TransactionController" as TC
participant "Database" as DB
participant "Iuran Model" as Iuran
participant "Mail Service" as Mail
participant "Kabupaten Email" as KabEmail
participant "Admin Email" as AdminEmail

Midtrans -> TC: POST /midtrans/notification\n{transaction data}

TC -> TC: Parse notification\nCreate Notification object

TC -> TC: Extract data:\n- transaction_status\n- order_id\n- fraud_status\n- transaction_id\n- payment_type

TC -> DB: SELECT * FROM transactions\nWHERE order_id = ?
DB --> TC: Return transaction

alt Transaction tidak ditemukan
    TC -> TC: Log error
    TC --> Midtrans: Return 404
else Transaction ditemukan
    TC -> DB: UPDATE transaction\nSET transaction_id, payment_type,\ntransaction_time
    DB --> TC: Updated
    
    alt Status = capture
        alt Fraud status = accept
            TC -> DB: UPDATE status = 'settlement'
            DB --> TC: Updated
        else Fraud status != accept
            TC -> DB: UPDATE status = 'pending'
            DB --> TC: Updated
        end
        
    else Status = settlement
        TC -> DB: UPDATE status = 'settlement'\nSET settlement_time = NOW()
        DB --> TC: Updated
        
        TC -> Iuran: createIuranFromTransaction(transaction)
        
        Iuran -> DB: SELECT iuran WHERE\nkabupaten_id = ? AND\njumlah = ? AND tanggal = ?
        DB --> Iuran: Check if exists
        
        alt Iuran belum ada
            Iuran -> DB: INSERT INTO iuran\n(kabupaten_id, jumlah, tanggal,\ndeskripsi, terverifikasi='diterima',\nbukti_transaksi)
            DB --> Iuran: Iuran created
            Iuran -> Iuran: Log "Iuran created"
        else Iuran sudah ada
            Iuran -> Iuran: Log "Iuran already exists"
        end
        
        Iuran --> TC: Return
        
        TC -> Mail: Send PaymentSuccessNotification\nto Kabupaten
        Mail -> KabEmail: Email: Pembayaran Berhasil\n{order_id, amount, status}
        KabEmail --> Mail: Email sent
        Mail --> TC: Success
        
        TC -> Mail: Send PaymentReceivedNotification\nto Admin
        Mail -> AdminEmail: Email: Pembayaran Baru\n{kabupaten, amount, date}
        AdminEmail --> Mail: Email sent
        Mail --> TC: Success
        
    else Status = pending
        TC -> DB: UPDATE status = 'pending'
        DB --> TC: Updated
        
    else Status = deny
        TC -> DB: UPDATE status = 'deny'
        DB --> TC: Updated
        
    else Status = expire
        TC -> DB: UPDATE status = 'expire'
        DB --> TC: Updated
        
    else Status = cancel
        TC -> DB: UPDATE status = 'cancel'
        DB --> TC: Updated
    end
    
    TC -> TC: Log status update
    TC --> Midtrans: Return 200 OK\n{success: true}
end

@enduml
```

---

## 4. Proses Tambah Iuran Manual

### Deskripsi
Sequence diagram untuk proses menambah data iuran secara manual dengan upload bukti transfer

### PlantUML Code

```plantuml
@startuml
title Sequence Diagram - Tambah Iuran Manual

actor Kabupaten
participant "Browser" as Browser
participant "KabupatenController" as KC
participant "Request Validator" as Validator
participant "Storage" as Storage
participant "Database" as DB

Kabupaten -> Browser: Klik "Tambah Iuran"
Browser -> KC: GET /kabupaten/dashboard/iuran/create
KC --> Browser: Tampilkan form tambah iuran

Kabupaten -> Browser: Input data:\n- Jumlah\n- Tanggal\n- Upload bukti\n- Keterangan
Kabupaten -> Browser: Klik "Simpan"

Browser -> KC: POST /kabupaten/dashboard/iuran\n{jumlah, tanggal, bukti, keterangan}

KC -> Validator: Validate request data
Validator -> Validator: Check:\n- jumlah: required|integer|min:0\n- tanggal: required|date\n- bukti: required|file\n- keterangan: required|string

alt Validasi gagal
    Validator --> KC: Return validation errors
    KC --> Browser: Return errors
    Browser --> Kabupaten: Tampilkan error validasi
else Validasi berhasil
    Validator --> KC: Data valid
    
    KC -> Storage: Store file bukti\nPath: storage/bukti/
    Storage -> Storage: Save file
    Storage --> KC: Return file path
    
    KC -> KC: Get authenticated user_id
    
    KC -> DB: INSERT INTO iuran\n(kabupaten_id, jumlah, tanggal,\nbukti_transaksi, deskripsi,\nterverifikasi='pending')
    DB --> KC: Iuran created
    
    KC --> Browser: Redirect to /kabupaten/dashboard/iuran\nwith success message
    Browser --> Kabupaten: Tampilkan halaman index\ndengan pesan sukses
end

@enduml
```

---

## 5. Proses Verifikasi Pembayaran oleh Admin

### Deskripsi
Sequence diagram untuk proses verifikasi (approve/reject) pembayaran iuran oleh Admin

### PlantUML Code

```plantuml
@startuml
title Sequence Diagram - Verifikasi Pembayaran Admin

actor Admin
participant "Browser" as Browser
participant "NotifikasiController" as NC
participant "Database" as DB

Admin -> Browser: Login & buka dashboard
Browser -> NC: GET /admin/dashboard/notifikasi
NC -> DB: SELECT * FROM iuran\nORDER BY created_at DESC
DB --> NC: Return list iuran
NC --> Browser: Tampilkan daftar notifikasi/iuran
Browser --> Admin: Tampilkan list iuran

Admin -> Browser: Klik detail iuran
Browser -> NC: GET /admin/dashboard/notifikasi/{id}
NC -> DB: SELECT * FROM iuran\nWHERE id = ?\nWITH kabupaten
DB --> NC: Return iuran detail
NC --> Browser: Tampilkan detail iuran
Browser --> Admin: Tampilkan:\n- Jumlah\n- Tanggal\n- Bukti transaksi\n- Status\n- Data kabupaten

alt Admin memilih ACC/Approve
    Admin -> Browser: Klik "ACC"
    Browser -> NC: POST /admin/notifikasi/acc/{id}
    
    NC -> DB: SELECT iuran WHERE id = ?
    DB --> NC: Return iuran
    
    NC -> DB: UPDATE iuran\nSET terverifikasi = 'diterima'\nWHERE id = ?
    DB --> NC: Updated
    
    NC --> Browser: Redirect back\nwith success message
    Browser --> Admin: Tampilkan pesan:\n"Notifikasi dan terverifikasi\niuran telah dikonfirmasi"
    
else Admin memilih Tolak/Cancel
    Admin -> Browser: Klik "Tolak"
    Browser -> NC: POST /admin/dashboard/notifikasi/{id}/mark-as-cancel
    
    NC -> DB: SELECT iuran WHERE id = ?
    DB --> NC: Return iuran
    
    NC -> DB: UPDATE iuran\nSET terverifikasi = 'ditolak'\nWHERE id = ?
    DB --> NC: Updated
    
    NC --> Browser: Redirect back\nwith success message
    Browser --> Admin: Tampilkan pesan:\n"Notifikasi dan status iuran\ntelah dibatalkan"
    
else Admin memilih ACC Semua
    Admin -> Browser: Klik "ACC Semua"
    Browser -> NC: POST /dashboard/notifikasi/mark-all-read
    
    NC -> DB: SELECT * FROM iuran\nWHERE terverifikasi = 'pending'
    DB --> NC: Return list pending iuran
    
    loop Untuk setiap iuran pending
        NC -> DB: UPDATE iuran\nSET terverifikasi = 'diterima'\nWHERE id = ?
        DB --> NC: Updated
    end
    
    NC --> Browser: Redirect back\nwith success message
    Browser --> Admin: Tampilkan pesan:\n"Semua notifikasi berhasil di-ACC"
end

@enduml
```

---

## 6. Proses Lihat Laporan Iuran

### Deskripsi
Sequence diagram untuk proses melihat laporan iuran (untuk Kabupaten dan Admin)

### PlantUML Code

```plantuml
@startuml
title Sequence Diagram - Lihat Laporan Iuran

actor User as "Kabupaten/Admin"
participant "Browser" as Browser
participant "Controller" as Controller
participant "Database" as DB
participant "Inertia" as Inertia

User -> Browser: Klik menu "Laporan"

alt User = Kabupaten
    Browser -> Controller: GET /kabupaten/dashboard/laporan
    Controller -> Controller: Get authenticated user_id
    
    Controller -> DB: SELECT * FROM iuran\nWHERE terverifikasi = 'diterima'\nWITH kabupaten
    DB --> Controller: Return verified iuran list
    
    Controller -> DB: SELECT MONTH(tanggal) as bulan,\nSUM(jumlah) as total_iuran\nFROM iuran\nWHERE terverifikasi = 'diterima'\nGROUP BY MONTH(tanggal)\nORDER BY MONTH(tanggal)
    DB --> Controller: Return monthly summary
    
    Controller -> Controller: Format data:\n- Map bulan ke nama bulan (Indonesia)\n- Format total_iuran
    
    Controller -> Inertia: Render 'kabupaten/laporan/index'\nwith {iuran, laporans}
    Inertia --> Browser: Return page with data
    
else User = Admin
    Browser -> Controller: GET /admin/dashboard
    Controller -> Controller: Get statistics
    
    Controller -> DB: SELECT COUNT(*) FROM iuran\nWHERE terverifikasi = 'diterima'
    DB --> Controller: Total verified iuran
    
    Controller -> DB: SELECT COUNT(*) FROM iuran\nWHERE terverifikasi = 'pending'
    DB --> Controller: Total pending iuran
    
    Controller -> DB: SELECT SUM(jumlah) FROM iuran\nWHERE terverifikasi = 'diterima'
    DB --> Controller: Total amount
    
    Controller -> DB: SELECT * FROM iuran\nORDER BY created_at DESC\nLIMIT 10
    DB --> Controller: Recent transactions
    
    Controller -> Inertia: Render 'admin/dashboard'\nwith statistics
    Inertia --> Browser: Return dashboard with charts
end

Browser --> User: Tampilkan laporan:\n- Tabel iuran\n- Grafik bulanan\n- Statistik

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
- Menggambarkan interaksi antara User, Browser, AuthController, Middleware, Database, dan Session
- Menunjukkan alur validasi kredensial dan pembuatan session
- Redirect berdasarkan role user

### 2. Proses Pembayaran via Midtrans
- Interaksi lengkap dari input pembayaran sampai Snap Payment Page
- Komunikasi dengan Midtrans API untuk mendapatkan Snap Token
- Penyimpanan data transaksi ke database
- Flow pembayaran di Snap Midtrans

### 3. Proses Webhook Midtrans
- Detail teknis handling webhook notification dari Midtrans
- Update status transaksi berdasarkan notification
- Auto-create iuran record untuk pembayaran sukses
- Pengiriman email notification ke Kabupaten dan Admin

### 4. Proses Tambah Iuran Manual
- Alur upload dan validasi data iuran manual
- Penyimpanan file bukti transaksi ke storage
- Insert data ke database dengan status pending

### 5. Proses Verifikasi Admin
- Interaksi admin untuk approve/reject pembayaran
- Bulk approve untuk efisiensi
- Update status verifikasi di database

### 6. Proses Lihat Laporan
- Query data iuran terverifikasi
- Generate statistik dan rekap bulanan
- Render data ke view dengan Inertia.js

## Komponen Utama

### Actors
- **Kabupaten**: User dengan role kabupaten
- **Admin**: User dengan role admin

### Participants
- **Browser**: Client-side interface
- **Controllers**: TransactionController, KabupatenController, NotifikasiController, DashboardController
- **Database**: MySQL/PostgreSQL database
- **Midtrans API**: External payment gateway service
- **Mail Service**: Laravel Mail untuk email notification
- **Storage**: File storage untuk bukti transaksi
- **Inertia**: Inertia.js untuk rendering pages

## Notasi PlantUML

- `->` : Synchronous message
- `-->` : Return message
- `alt/else/end` : Alternative flow (conditional)
- `loop/end` : Loop/iteration
- `note right/left` : Catatan tambahan
- `participant` : Object/komponen dalam sistem

## Catatan

- Semua diagram menggambarkan **interaksi antar komponen** dalam urutan waktu
- Diagram dibuat berdasarkan **implementasi aktual** di controllers
- Menunjukkan **alur data** dari request sampai response
- Mencakup **error handling** dan **alternative flows**

## Teknologi yang Digunakan

- **Laravel**: Framework backend
- **Inertia.js**: Frontend framework
- **Midtrans**: Payment gateway
- **Laravel Mail**: Email notification
- **Database**: MySQL/PostgreSQL
- **Storage**: Laravel File Storage
