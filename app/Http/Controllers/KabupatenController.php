<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        'isActive' => Auth::user()->status,
    ]);
    }

    public function create()
    {
        return Inertia::render('kabupaten/iuran/create', [
            'midtransClientKey' => config('midtrans.client_key'),
              'isActive' => Auth::user()->status,
        ]);
    }

    // REMOVED: store() method - Not needed with Midtrans integration
    // All iuran records are created automatically from successful Midtrans transactions

    // REMOVED: edit() and update() methods - Not needed with Midtrans integration
    // Transaction data from Midtrans should not be manually edited
    
    // REMOVED: show() method - Replaced by showTransaction() which uses Transaction model directly
    public function laporan()
    {
        // Ambil SEMUA transaksi yang sudah settlement dari SEMUA kabupaten
        $transactions = \App\Models\Transaction::with('user')
            ->where('status', 'settlement')
            ->get();
        
        // Map transactions ke format yang diharapkan oleh frontend
        $iuran = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'kabupaten_id' => $transaction->user_id,
                'gross_amount' => $transaction->gross_amount,
                'jumlah' => $transaction->gross_amount,
                'tanggal' => $transaction->transaction_time ? $transaction->transaction_time->format('Y-m-d') : $transaction->created_at->format('Y-m-d'),
                'settlement_time' => $transaction->settlement_time ? $transaction->settlement_time->format('Y-m-d H:i:s') : null,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                'deskripsi' => $transaction->description ?? 'Pembayaran Iuran PGRI',
                'status' => $transaction->status,
                'terverifikasi' => 'diterima',
                'user' => [
                    'id' => $transaction->user->id,
                    'nama_kabupaten' => $transaction->user->nama_kabupaten ?? $transaction->user->name,
                    'jumlah_anggota' => $transaction->user->jumlah_anggota ?? 0,
                ],
                'kabupaten' => [
                    'id' => $transaction->user->id,
                    'name' => $transaction->user->nama_kabupaten ?? $transaction->user->name,
                    'jumlah_anggota' => $transaction->user->jumlah_anggota ?? 0,
                ],
            ];
        });

        // Ambil semua kabupaten (users dengan role kabupaten)
        $kabupatens = \App\Models\User::where('role', 'kabupaten')
            ->select('id', 'nama_kabupaten', 'jumlah_anggota')
            ->get();

        // Buat rekap bulanan dari transactions untuk user yang login
        $laporans = \App\Models\Transaction::select(
                DB::raw('MONTH(COALESCE(transaction_time, created_at)) as bulan'),
                DB::raw('SUM(gross_amount) as total_iuran')
            )
            ->where('user_id', Auth::id())
            ->where('status', 'settlement')
            ->groupBy(DB::raw('MONTH(COALESCE(transaction_time, created_at))'))
            ->orderBy(DB::raw('MONTH(COALESCE(transaction_time, created_at))'))
            ->get()
            ->map(function ($item) {
                return [
                    'bulan' => Carbon::create()->month($item->bulan)->locale('id')->isoFormat('MMMM'),
                    'total_iuran' => (float) $item->total_iuran,
                ];
            });

        return Inertia::render('kabupaten/laporan/index', [
            'iuran' => $iuran,
            'kabupatens' => $kabupatens,
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