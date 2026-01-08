# 📋 Task List - Kamis, 26 Desember 2025

## 🎯 Target Hari Ini
Memperbaiki **bug kritis** dan **error** yang bisa menyebabkan aplikasi tidak berjalan.

---

## ✅ Checklist Tasks

### 🔴 Prioritas TINGGI (Wajib Selesai Hari Ini)

#### Task 1: Fix Typo Import di User.php
- [ ] Buka file `app/Models/User.php`
- [ ] Ubah baris 7 dari:
  ```php
  use Illumnate\Database\Eloquent\Relations\HasMany;
  ```
  Menjadi:
  ```php
  use Illuminate\Database\Eloquent\Relations\HasMany;
  ```
- [ ] Save file
- **Estimasi:** 5 menit

---

#### Task 2: Fix Missing Storage Import di KabupatenController.php
- [ ] Buka file `app/Http/Controllers/KabupatenController.php`
- [ ] Tambahkan import di bagian atas file:
  ```php
  use Illuminate\Support\Facades\Storage;
  ```
- [ ] Hapus `\Storage` inline (baris 89 & 158), ganti dengan `Storage`
- [ ] Save file
- **Estimasi:** 10 menit

---

#### Task 3: Fix Migration Drop Table Inconsistency
- [ ] Buka file `database/migrations/2025_05_18_110047_create_iuran.php`
- [ ] Ubah baris 34 dari:
  ```php
  Schema::dropIfExists('iuran');
  ```
  Menjadi:
  ```php
  Schema::dropIfExists('iurans');
  ```
- [ ] Save file
- **Estimasi:** 5 menit

---

#### Task 4: Tambah Validasi Signature Midtrans di Webhook
- [ ] Buka file `app/Http/Controllers/TransactionController.php`
- [ ] Tambahkan validasi signature di method `notification()` setelah baris 97:
  ```php
  // Validate signature
  $serverKey = config('midtrans.server_key');
  $hashed = hash('sha512', $notification->order_id . $notification->status_code . $notification->gross_amount . $serverKey);
  if ($notification->signature_key !== $hashed) {
      Log::warning('Invalid Midtrans signature for order: ' . $notification->order_id);
      return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
  }
  ```
- [ ] Save file
- **Estimasi:** 15 menit

---

### 🟠 Prioritas SEDANG (Sebaiknya Selesai)

#### Task 5: Pastikan .env Tidak Ter-commit
- [ ] Buka file `.gitignore`
- [ ] Pastikan ada baris `.env` di dalamnya
- [ ] Jika sudah ada, skip task ini
- **Estimasi:** 5 menit

---

#### Task 6: Test Aplikasi Berjalan Normal
- [ ] Buka terminal di folder project
- [ ] Jalankan:
  ```bash
  composer install
  php artisan migrate:fresh --seed
  npm install
  npm run dev
  ```
- [ ] Buka browser, test login dengan akun test
- [ ] Test halaman dashboard Kabupaten
- [ ] Test halaman dashboard Admin
- **Estimasi:** 30 menit

---

### 🟡 Prioritas RENDAH (Bonus Jika Sempat)

#### Task 7: Buat ERD (Entity Relationship Diagram)
- [ ] Buat file `docs/erd.md`
- [ ] Dokumentasikan relasi antar tabel:
  - users ↔ iurans (one-to-many)
  - users ↔ transactions (one-to-many)
- **Estimasi:** 45 menit

---

#### Task 8: Update README.md
- [ ] Tambahkan cara instalasi lengkap
- [ ] Tambahkan screenshot aplikasi
- [ ] Tambahkan kredensial akun demo
- **Estimasi:** 30 menit

---

## ⏰ Jadwal Waktu (Rekomendasi)

| Waktu | Task |
|-------|------|
| 09:00 - 09:30 | Task 1, 2, 3 (Fix bugs) |
| 09:30 - 10:00 | Task 4 (Validasi Midtrans) |
| 10:00 - 10:15 | Task 5 (.gitignore check) |
| 10:15 - 11:00 | Task 6 (Testing aplikasi) |
| 11:00 - 12:00 | Task 7 (ERD) - Optional |
| 13:00 - 14:00 | Task 8 (README) - Optional |

---

## 📝 Notes

- Jika ada error saat testing, **screenshot error-nya**
- Simpan perubahan dengan **git commit** setelah setiap task selesai:
  ```bash
  git add .
  git commit -m "Fix: [nama task]"
  ```
- Jika butuh bantuan, tanyakan kembali dengan menyertakan **pesan error**

---

## 🏁 Target Akhir Hari Ini
- [ ] Semua bug kritis sudah diperbaiki
- [ ] Aplikasi bisa dijalankan tanpa error
- [ ] Bisa login sebagai Admin dan Kabupaten
- [ ] Bisa melihat dashboard masing-masing role

---

**Selamat mengerjakan! 💪**
