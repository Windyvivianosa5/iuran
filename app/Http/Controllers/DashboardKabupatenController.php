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

            // Gunakan bulan_pembayaran jika ada
            if ($item->bulan_pembayaran) {
                [$tahun, $bulan] = explode('-', $item->bulan_pembayaran);
                $bulanLabel = Carbon::create((int)$tahun, (int)$bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
            } else {
                $bulanLabel = Carbon::parse($item->settlement_time ?? $item->created_at)->locale('id')->translatedFormat('F');
            }

            return [
                'bulan' => $bulanLabel,
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

        // Laporan bulanan — prioritaskan bulan_pembayaran, fallback ke settlement_time
        $laporans = Transaction::where('user_id', $user->id)
            ->where('status', 'settlement')
            ->get()
            ->groupBy(function ($item) {
                // Gunakan bulan dari bulan_pembayaran jika ada, else dari settlement_time
                if ($item->bulan_pembayaran) {
                    return (int) explode('-', $item->bulan_pembayaran)[1];
                }
                return (int) Carbon::parse($item->settlement_time ?? $item->created_at)->format('n');
            })
            ->map(function ($group, $bulanIndex) {
                return [
                    'bulan' => Carbon::create()->month($bulanIndex)->locale('id')->isoFormat('MMMM'),
                    'total_iuran' => (float) $group->sum('gross_amount'),
                ];
            })
            ->sortKeys()
            ->values();

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
                    'gross_amount' => $transaction->gross_amount,
                    'status' => $transaction->status,
                    'description' => $transaction->description,
                    'created_at' => $transaction->created_at->format('d M Y H:i'),
                ];
            });

        // Deteksi bulan yang belum dibayar di tahun berjalan
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        // Ambil bulan-bulan yang sudah dibayar (settlement) di tahun ini
        // Prioritaskan bulan_pembayaran, fallback ke settlement_time
        $paidMonths = Transaction::where('user_id', $user->id)
            ->where('status', 'settlement')
            ->get()
            ->filter(function ($transaction) use ($currentYear) {
                if ($transaction->bulan_pembayaran) {
                    $tahun = (int) explode('-', $transaction->bulan_pembayaran)[0];
                    return $tahun === $currentYear;
                }
                return $transaction->settlement_time &&
                       Carbon::parse($transaction->settlement_time)->year === $currentYear;
            })
            ->map(function ($transaction) {
                if ($transaction->bulan_pembayaran) {
                    return (int) explode('-', $transaction->bulan_pembayaran)[1];
                }
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
