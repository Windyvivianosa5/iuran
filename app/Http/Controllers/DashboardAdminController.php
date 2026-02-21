<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index()
    {
        // Fetch all settled transactions with related user (kabupaten) data
        $transactions = Transaction::with('user')
            ->where('status', 'settlement')
            ->latest()
            ->get();

        // Calculate total contributions and transaction count
        $totalMasuk = $transactions->sum('gross_amount');
        $jumlahTransaksi = $transactions->count();

        // Fetch all kabupaten users from database (dynamic)
        $allKabupaten = User::where('role', 'kabupaten')->orderBy('nama_kabupaten')->get();
        
        // Helper function to determine if it's Kota or Kabupaten
        $getKabupatenType = function($namaKabupaten) {
            // List of cities (Kota) - you can also add a 'tipe' field to database if preferred
            $kotaList = ['Pekanbaru', 'Dumai'];
            return in_array($namaKabupaten, $kotaList) ? 'Kota' : 'Kabupaten';
        };

        // Map latest 5 transactions
        $transaksiTerbaru = $transactions->take(5)->map(function ($item) use ($getKabupatenType) {
            $kabupatenName = $item->user->nama_kabupaten ?? 'Tidak Diketahui';
            $type = $kabupatenName !== 'Tidak Diketahui' ? $getKabupatenType($kabupatenName) : '';
            $formattedName = $kabupatenName !== 'Tidak Diketahui' ? "PGRI {$type} {$kabupatenName}" : 'Tidak Diketahui';

            return [
                'bulan' => \Carbon\Carbon::parse($item->settlement_time ?? $item->created_at)->locale('id')->translatedFormat('F'),
                'kabupaten' => $formattedName,
                'total_iuran' => $item->gross_amount,
            ];
        });

        // Map latest 5 notifications for settled transactions
        $notifikasi = $transactions->whereNotNull('user_id')->take(5)->map(function ($item) use ($getKabupatenType) {
            $kabupatenName = $item->user->nama_kabupaten ?? 'Tidak Diketahui';
            $type = $kabupatenName !== 'Tidak Diketahui' ? $getKabupatenType($kabupatenName) : '';
            $formattedName = $kabupatenName !== 'Tidak Diketahui' ? "PGRI {$type} {$kabupatenName}" : 'Tidak Diketahui';

            return [
                'id' => $item->id,
                'pesan' => "{$formattedName} mengirim iuran baru",
                'waktu' => \Carbon\Carbon::parse($item->created_at)->format('H:i'),
            ];
        });

        // Fetch monthly contribution report
        $laporans = Transaction::select(
            DB::raw('MONTH(settlement_time) as bulan'),
            DB::raw('SUM(gross_amount) as total_iuran')
        )
            ->where('status', 'settlement')
            ->whereNotNull('settlement_time')
            ->groupBy(DB::raw('MONTH(settlement_time)'))
            ->orderBy(DB::raw('MONTH(settlement_time)'))
            ->get()
            ->map(function ($item) {
                return [
                    'bulan' => \Carbon\Carbon::create()->month($item->bulan)->locale('id')->isoFormat('MMMM'),
                    'total_iuran' => (float) $item->total_iuran, // Ensure numeric type
                ];
            });

        // Ambil data transaksi yang sudah ada di database
        $existingTransactions = Transaction::select(
            'user_id',
            DB::raw('SUM(gross_amount) as total_iuran'),
            DB::raw('COUNT(*) as jumlah_transaksi')
        )
            ->with('user')
            ->where('status', 'settlement')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user.nama_kabupaten');

        // Laporan per kabupaten/kota dengan semua kabupaten dari database
        $laporanKabupaten = $allKabupaten->map(function ($kabupaten) use ($existingTransactions, $getKabupatenType) {
            $kabupatenName = $kabupaten->nama_kabupaten;
            $type = $getKabupatenType($kabupatenName);
            $formattedName = "PGRI {$type} {$kabupatenName}";
            
            // Cari data yang ada di database
            $existingData = $existingTransactions->get($kabupatenName);
            
            return [
                'kabupaten' => $formattedName,
                'total_iuran' => $existingData ? (float) $existingData->total_iuran : 0,
                'jumlah_transaksi' => $existingData ? $existingData->jumlah_transaksi : 0,
                'status' => $existingData ? 'Ada Data' : 'Tidak Ada Data',
                'total_iuran_formatted' => $existingData ? 
                    'Rp ' . number_format($existingData->total_iuran, 0, ',', '.') : 
                    'Rp. -'
            ];
        })->sortByDesc('total_iuran')->values();

        return Inertia::render('admin/dashboard/index', [
            'totalMasuk' => (float) $totalMasuk,
            'jumlahTransaksi' => $jumlahTransaksi,
            'transaksiTerbaru' => $transaksiTerbaru,
            'notifikasi' => $notifikasi,
            'laporans' => $laporans,
            'laporanKabupaten' => $laporanKabupaten,
        ]);
    }
}