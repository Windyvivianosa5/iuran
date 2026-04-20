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

    public function laporan()
    {
        // Ambil SEMUA transaksi yang sudah settlement dari SEMUA kabupaten
        $transactions = \App\Models\Transaction::with('user')
            ->where('status', 'settlement')
            ->get();
        
        // Map transactions ke format yang diharapkan oleh frontend
        // bulan_pembayaran digunakan oleh filterLaporan.ts untuk menentukan kolom yang benar
        $mappedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'kabupaten_id' => $transaction->user_id,
                'gross_amount' => $transaction->gross_amount,
                'jumlah' => $transaction->gross_amount,
                'bulan_pembayaran' => $transaction->bulan_pembayaran,
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

        // Kabupaten list untuk filter
        $kabupatens = \App\Models\User::where('role', 'kabupaten')
            ->select('id', 'nama_kabupaten', 'jumlah_anggota')
            ->get();

        // Rekap bulanan milik kabupaten yang login
        // Prioritaskan bulan_pembayaran, fallback ke settlement_time/created_at
        $laporans = \App\Models\Transaction::where('user_id', Auth::id())
            ->where('status', 'settlement')
            ->get()
            ->groupBy(function ($item) {
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

        return Inertia::render('kabupaten/laporan/index', [
            'iuran' => $mappedTransactions,
            'kabupatens' => $kabupatens,
            'laporans' => $laporans,
        ]);
    }

    /**
     * Tampilkan detail transaksi
     */
    public function showTransaction($id)
    {
        $transaction = \App\Models\Transaction::findOrFail($id);

        return Inertia::render('kabupaten/iuran/show', [
            'transaction' => $transaction,
        ]);
    }

}