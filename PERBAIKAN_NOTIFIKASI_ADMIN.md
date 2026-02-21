# Perbaikan Notifikasi Admin

## Masalah yang Ditemukan

Halaman notifikasi admin tidak bisa dijalankan karena:

1. **Field `terverifikasi` tidak ada di tabel `transactions`**
   - Frontend menggunakan field `terverifikasi` (pending/diterima/ditolak)
   - Backend mengambil data dari tabel `transactions` yang menggunakan field `status` (pending/settlement/cancel/deny/expire)
   - Tidak ada mapping antara `status` dan `terverifikasi`

2. **Relasi `kabupaten` tidak ada**
   - Frontend mengharapkan data `kabupaten` dari relasi
   - Model `User` tidak memiliki relasi ke model `Kabupaten`
   - Data kabupaten sebenarnya disimpan langsung di tabel `users` (field `nama_kabupaten` dan `kode_kabupaten`)

## Solusi yang Diterapkan

### 1. Mapping Status ke Terverifikasi

Di `app/Http/Controllers/Admin/NotifikasiController.php`, saya menambahkan mapping:

```php
'terverifikasi' => $transaction->status === 'settlement' ? 'diterima' : 
                  ($transaction->status === 'cancel' || $transaction->status === 'deny' || $transaction->status === 'expire' ? 'ditolak' : 'pending')
```

**Mapping:**
- `settlement` → `diterima`
- `cancel`, `deny`, `expire` → `ditolak`
- `pending` dan lainnya → `pending`

### 2. Mapping Data Kabupaten

Mengubah dari relasi yang tidak ada menjadi mengambil data langsung dari user:

```php
'kabupaten' => $transaction->user ? [
    'name' => $transaction->user->nama_kabupaten,
    'kode' => $transaction->user->kode_kabupaten,
    'tipe' => 'Kabupaten', // Default tipe
] : null
```

### 3. Menambahkan Field yang Dibutuhkan Frontend

Menambahkan field-field yang diharapkan oleh frontend:
- `jumlah` → dari `gross_amount`
- `tanggal` → dari `transaction_time` atau `created_at`
- `deskripsi` → dari `description` dengan default "Pembayaran Iuran PGRI"

## File yang Diubah

- `app/Http/Controllers/Admin/NotifikasiController.php`
  - Method `index()` - untuk halaman list notifikasi
  - Method `show($id)` - untuk halaman detail notifikasi

## Cara Menggunakan

1. Pastikan server Laravel sudah berjalan (`php artisan serve`)
2. Pastikan npm dev server sudah berjalan (`npm run dev`)
3. Login sebagai admin
4. Akses halaman notifikasi di `/admin/dashboard/notifikasi`

## Fitur yang Tersedia

1. **Tab Filter**
   - Semua - menampilkan semua notifikasi
   - Menunggu - notifikasi dengan status pending
   - Disetujui - notifikasi dengan status settlement (diterima)
   - Ditolak - notifikasi dengan status cancel/deny/expire (ditolak)

2. **Aksi pada Notifikasi Pending**
   - Setujui - mengubah status menjadi `settlement`
   - Tolak - mengubah status menjadi `cancel`

3. **Detail Notifikasi**
   - Melihat detail lengkap transaksi
   - Informasi kabupaten/kota
   - Jumlah pembayaran
   - Tanggal transaksi
   - Status verifikasi

## Catatan Penting

- Sistem menggunakan tabel `transactions` untuk menyimpan data pembayaran
- Field `status` di tabel `transactions` adalah sumber kebenaran untuk status transaksi
- Data kabupaten disimpan langsung di tabel `users`, bukan di tabel terpisah
- Mapping `terverifikasi` hanya untuk keperluan tampilan di frontend, data asli tetap menggunakan field `status`
