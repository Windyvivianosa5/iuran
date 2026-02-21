<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasis = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'order_id' => $transaction->order_id,
                    'transaction_id' => $transaction->transaction_id,
                    'gross_amount' => $transaction->gross_amount,
                    'payment_type' => $transaction->payment_type,
                    'payment_method' => $transaction->payment_method,
                    'status' => $transaction->status,
                    'description' => $transaction->description,
                    'transaction_time' => $transaction->transaction_time,
                    'settlement_time' => $transaction->settlement_time,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                    'user' => $transaction->user,
                    'kabupaten' => $transaction->user ? [
                        'name' => $transaction->user->nama_kabupaten,
                        'kode' => $transaction->user->kode_kabupaten,
                        'tipe' => 'Kabupaten', // Default tipe
                    ] : null,
                    'jumlah' => $transaction->gross_amount,
                    'tanggal' => $transaction->transaction_time ? $transaction->transaction_time->format('Y-m-d') : $transaction->created_at->format('Y-m-d'),
                    'deskripsi' => $transaction->description ?? 'Pembayaran Iuran PGRI',
                    // Map status to terverifikasi
                    'terverifikasi' => $transaction->status === 'settlement' ? 'diterima' : 
                                      ($transaction->status === 'cancel' || $transaction->status === 'deny' || $transaction->status === 'expire' ? 'ditolak' : 'pending'),
                ];
            });

        return Inertia::render('admin/notifikasi/index', [
            'notifikasis' => $notifikasis
        ]);
    }

    public function show($id)
    {
        $transaction = Transaction::with('user')->findOrFail($id);
        
        $notifikasi = [
            'id' => $transaction->id,
            'order_id' => $transaction->order_id,
            'transaction_id' => $transaction->transaction_id,
            'gross_amount' => $transaction->gross_amount,
            'payment_type' => $transaction->payment_type,
            'payment_method' => $transaction->payment_method,
            'status' => $transaction->status,
            'description' => $transaction->description,
            'transaction_time' => $transaction->transaction_time,
            'settlement_time' => $transaction->settlement_time,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
            'user' => $transaction->user,
            'kabupaten' => $transaction->user ? [
                'name' => $transaction->user->nama_kabupaten,
                'kode' => $transaction->user->kode_kabupaten,
                'tipe' => 'Kabupaten', // Default tipe
            ] : null,
            'jumlah' => $transaction->gross_amount,
            'tanggal' => $transaction->transaction_time ? $transaction->transaction_time->format('Y-m-d') : $transaction->created_at->format('Y-m-d'),
            'deskripsi' => $transaction->description ?? 'Pembayaran Iuran PGRI',
            // Map status to terverifikasi
            'terverifikasi' => $transaction->status === 'settlement' ? 'diterima' : 
                              ($transaction->status === 'cancel' || $transaction->status === 'deny' || $transaction->status === 'expire' ? 'ditolak' : 'pending'),
        ];

        return Inertia::render('admin/notifikasi/show', [
            'notifikasi' => $notifikasi,
        ]);
    }


    public function markAsRead($id)
    {
        $notifikasi = Transaction::findOrFail($id);

        $notifikasi->status = 'settlement';
        $notifikasi->settlement_time = now();
        $notifikasi->save();

        return redirect()->back()->with('success', 'Transaksi telah dikonfirmasi (settlement).');
    }

    public function markAsCancel($id)
    {
        $notifikasi = Transaction::findOrFail($id);

        $notifikasi->status = 'cancel';
        $notifikasi->save();

        return redirect()->back()->with('success', 'Transaksi telah dibatalkan.');
    }

    public function markAllAsRead()
    {
        // Approve all pending transactions
        Transaction::where('status', 'pending')
            ->update(['status' => 'settlement']);

        return back()->with('success', 'Semua transaksi pending berhasil di-ACC (settlement)');
    }
}
