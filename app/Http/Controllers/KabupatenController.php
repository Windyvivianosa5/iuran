<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Iuran;
use Carbon\Carbon;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
    

use Illuminate\Http\Request;

class KabupatenController extends Controller
{
    public function index()
    {
    // Fetch transactions instead of iuran
    $transactions = \App\Models\Transaction::where('user_id', auth()->id())
        ->latest()
        ->get();

    return Inertia::render('kabupaten/iuran/index', [
        'transactions' => $transactions,
        'midtransClientKey' => config('midtrans.client_key'),
        ]);
    }

    public function create()
    {
        return Inertia::render('kabupaten/iuran/create', [
            'midtransClientKey' => config('midtrans.client_key'),
        ]);
    }

    // REMOVED: store() method - Not needed with Midtrans integration
    // All iuran records are created automatically from successful Midtrans transactions

    // REMOVED: edit() and update() methods - Not needed with Midtrans integration
    // Transaction data from Midtrans should not be manually edited
    
    // REMOVED: show() method - Replaced by showTransaction() which uses Transaction model directly
    public function laporan()
{
    // Ambil semua iuran yang sudah diverifikasi untuk kabupaten yang sedang login
    $iuran = Iuran::with('kabupaten')
        // ->where('kabupaten_id', Auth::id())
        ->where('terverifikasi', 'diterima')
        ->get();

    // Buat rekap bulanan
    $laporans = Iuran::select(
            DB::raw('MONTH(tanggal) as bulan'),
            DB::raw('SUM(jumlah) as total_iuran')
        )
        // ->where('kabupaten_id', Auth::id())
        ->where('terverifikasi', 'diterima')
        ->groupBy(DB::raw('MONTH(tanggal)'))
        ->orderBy(DB::raw('MONTH(tanggal)'))
        ->get()
        ->map(function ($item) {
            return [
                'bulan' => Carbon::create()->month($item->bulan)->locale('id')->isoFormat('MMMM'),
                'total_iuran' => (float) $item->total_iuran,
            ];
        });

    return Inertia::render('kabupaten/laporan/index', [
        'iuran' => $iuran,
        'laporans' => $laporans,
    ]);
    
}
    // REMOVED: destroy() method - Not needed with Midtrans integration
    // Transactions should be permanent records and not deletable

    /**
     * Show transaction details
     */
    public function showTransaction($id)
    {
        $transaction = \App\Models\Transaction::findOrFail($id);
        
        // Create a dummy iuran object for backward compatibility
        $iuran = (object) [
            'id' => $transaction->id,
            'jumlah' => $transaction->gross_amount,
            'deskripsi' => $transaction->description,
            'tanggal' => $transaction->created_at,
            'terverifikasi' => $transaction->status === 'settlement' ? 'diterima' : 'pending',
            'bukti_transaksi' => null,
        ];

        return Inertia::render('kabupaten/iuran/show', [
            'iuran' => $iuran,
            'transaction' => $transaction,
        ]);
    }

}