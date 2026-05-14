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
                $kotaList = ['Pekanbaru', 'Dumai'];
                $tipeKabupaten = ($transaction->user && in_array($transaction->user->nama_kabupaten, $kotaList)) ? 'Kota' : 'Kabupaten';
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
                        'tipe' => $tipeKabupaten,
                    ] : null,
                    'jumlah' => $transaction->gross_amount - ($transaction->admin_fee ?? 0),
                    'tanggal' => $transaction->transaction_time ? $transaction->transaction_time->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') : $transaction->created_at->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
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
        
        $kotaList = ['Pekanbaru', 'Dumai'];
        $tipeKabupaten = ($transaction->user && in_array($transaction->user->nama_kabupaten, $kotaList)) ? 'Kota' : 'Kabupaten';

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
                'tipe' => $tipeKabupaten,
            ] : null,
            'jumlah' => $transaction->gross_amount - ($transaction->admin_fee ?? 0),
            'tanggal' => $transaction->transaction_time ? $transaction->transaction_time->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') : $transaction->created_at->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
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
        return redirect()->back()->with('error', 'Aksi dinonaktifkan. Status pembayaran dikelola sepenuhnya secara otomatis oleh sistem Midtrans.');
    }

    public function markAsCancel($id)
    {
        return redirect()->back()->with('error', 'Aksi dinonaktifkan. Pembatalan hanya dapat dilakukan otomatis oleh Midtrans.');
    }

    public function markAllAsRead()
    {
        return back()->with('error', 'Aksi dinonaktifkan. Anda tidak dapat menyetujui transaksi pending secara manual demi keamanan sistem keuangan.');
    }
}
