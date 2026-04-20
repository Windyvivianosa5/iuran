<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Inertia\Inertia;

class LaporanController extends Controller
{
    public function index()
    {
        // Get all settled transactions with user relationship
        // bulan_pembayaran is included by default via the model
        $iuran = Transaction::with('user')
            ->where('status', 'settlement')
            ->get();
        
        // Get all kabupaten users
        $kabupatens = User::where('role', 'kabupaten')
            ->orderBy('nama_kabupaten')
            ->get(['id', 'nama_kabupaten', 'jumlah_anggota']);
        
        return Inertia::render('admin/laporan/index', [
            'iuran' => $iuran,
            'kabupatens' => $kabupatens,
        ]);
    }

    public function store(Request $request)
    {
      
    }

    public function destroy(Transaction $laporan)
    {
        // Adjust dependent on how we want to handle deletion
        $laporan->delete();

        return redirect()->back()->with('success', 'Laporan berhasil dihapus.');
    }
}
