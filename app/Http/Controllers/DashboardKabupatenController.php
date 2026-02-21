<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Inertia\Inertia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardKabupatenController extends Controller
{
   public function index()
    {
        $user = Auth::user();
        $kotaList = ['Pekanbaru', 'Dumai'];

        $isActive = Auth::user()->status;

        // Ambil transaksi yang sukses (settlement) untuk statistik
        $settledTransactions = Transaction::where('user_id', $user->id)
            ->with('user')
            ->where('status', 'settlement')
            ->latest()
            ->get();

        $totalMasuk = $settledTransactions->sum('gross_amount');
        $jumlahTransaksi = $settledTransactions->count();

        $transaksiTerbaru = $settledTransactions->take(5)->map(function ($item) use ($kotaList) {
            $kabupatenName = $item->user->nama_kabupaten ?? 'Tidak Diketahui';
            $type = in_array($kabupatenName, $kotaList) ? 'Kota' : 'Kabupaten';
            $formattedName = "PGRI {$type} {$kabupatenName}";

            return [
                'bulan' => \Carbon\Carbon::parse($item->settlement_time ?? $item->created_at)->locale('id')->translatedFormat('F'),
                'kabupaten' => $formattedName,
                'total_iuran' => $item->gross_amount,
            ];
        });

        // Notifikasi dari transaksi sukses
        $notifikasi = $settledTransactions->take(5)->map(function ($item) use ($kotaList) {
            $kabupatenName = $item->user->nama_kabupaten ?? 'Tidak Diketahui';
            $type = in_array($kabupatenName, $kotaList) ? 'Kota' : 'Kabupaten';
            $formattedName = "PGRI {$type} {$kabupatenName}";

            return [
                'id' => $item->id,
                'pesan' => "{$formattedName} melakukan pembayaran",
                'waktu' => \Carbon\Carbon::parse($item->created_at)->format('H:i'),
            ];
        });

        // Laporan bulanan untuk user login based on transactions
        $laporans = Transaction::select(
            DB::raw('MONTH(COALESCE(settlement_time, created_at)) as bulan'),
            DB::raw('SUM(gross_amount) as total_iuran')
        )
            ->where('user_id', $user->id)
            ->where('status', 'settlement')
            ->groupBy(DB::raw('MONTH(COALESCE(settlement_time, created_at))'))
            ->orderBy(DB::raw('MONTH(COALESCE(settlement_time, created_at))'))
            ->get()
            ->map(function ($item) {
                return [
                    'bulan' => \Carbon\Carbon::create()->month($item->bulan)->locale('id')->isoFormat('MMMM'),
                    'total_iuran' => (float) $item->total_iuran,
                ];
            });

        $kabupatenName = $user->nama_kabupaten ?? $user->name;
        $type = in_array($kabupatenName, $kotaList) ? 'Kota' : 'Kabupaten';
        $formattedName = "{$type} {$kabupatenName}";
           
        // Get recent transactions (all statuses)
        $recentTransactions = Transaction::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'order_id' => $transaction->order_id,
                    'amount' => $transaction->gross_amount,
                    'status' => $transaction->status,
                    'description' => $transaction->description,
                    'created_at' => $transaction->created_at->format('d M Y H:i'),
                ];
            });

        // Deteksi bulan yang belum dibayar di tahun berjalan
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        // Ambil bulan-bulan yang sudah dibayar (settlement) di tahun ini
        $paidMonths = Transaction::where('user_id', $user->id)
            ->where('status', 'settlement')
            ->whereYear('settlement_time', $currentYear)
            ->get()
            ->map(function ($transaction) {
                return Carbon::parse($transaction->settlement_time)->month;
            })
            ->unique()
            ->values()
            ->toArray();
        
        // Buat list bulan yang belum dibayar (dari Januari sampai bulan sekarang)
        $unpaidMonths = [];
        for ($month = 1; $month <= $currentMonth; $month++) {
            if (!in_array($month, $paidMonths)) {
                $unpaidMonths[] = [
                    'month' => $month,
                    'name' => Carbon::create($currentYear, $month, 1)->locale('id')->translatedFormat('F Y'),
                    'year' => $currentYear,
                ];
            }
        }

        return Inertia::render('kabupaten/dashboard/index', [
            'totalMasuk' => (float) $totalMasuk,
            'jumlahTransaksi' => $jumlahTransaksi,
            'transaksiTerbaru' => $transaksiTerbaru,
            'notifikasi' => $notifikasi,
            'laporans' => $laporans,
            'jumlahAnggota' => $user->jumlah_anggota ?? $user->anggota,
            'namaUser' => $formattedName,
            'recentTransactions' => $recentTransactions,
            'midtransClientKey' => config('midtrans.client_key'),
            'isActive' => $isActive,
            'unpaidMonths' => $unpaidMonths,
        ]);
    }

}
