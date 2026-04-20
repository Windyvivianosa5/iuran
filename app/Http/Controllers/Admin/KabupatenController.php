<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KabupatenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kabupaten = User::where('role', 'kabupaten')
            ->orderBy('nama_kabupaten', 'asc')
            ->get();

        return Inertia::render('admin/kabupaten/index', [
            'kabupaten' => $kabupaten,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/kabupaten/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log incoming request
        \Log::info('Kabupaten Store Request', $request->all());

        // Validate ALL fields upfront (before transaction)
        $validated = $request->validate([
            'nama_kabupaten' => 'required|string|max:255',
            'kode_kabupaten' => 'required|string|max:5|unique:users,kode_kabupaten',
            'jumlah_anggota' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
            // User validation
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|unique:users,email',
            'user_password' => 'required|string|min:8|confirmed',
        ], [
            'nama_kabupaten.required' => 'Nama kabupaten harus diisi',
            'kode_kabupaten.required' => 'Kode kabupaten harus diisi',
            'kode_kabupaten.unique' => 'Kode kabupaten sudah digunakan',
            'jumlah_anggota.required' => 'Jumlah anggota harus diisi',
            'jumlah_anggota.integer' => 'Jumlah anggota harus berupa angka',
            'jumlah_anggota.min' => 'Jumlah anggota tidak boleh kurang dari 0',
            'status.required' => 'Status harus dipilih',
            'user_name.required' => 'Nama user harus diisi',
            'user_email.required' => 'Email harus diisi',
            'user_email.email' => 'Format email tidak valid',
            'user_email.unique' => 'Email sudah terdaftar',
            'user_password.required' => 'Password harus diisi',
            'user_password.min' => 'Password minimal 8 karakter',
            'user_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        \DB::beginTransaction();
        try {
            // Create user with kabupaten role
            $user = User::create([
                'name' => $validated['user_name'],
                'email' => $validated['user_email'],
                'password' => \Hash::make($validated['user_password']),
                'role' => 'kabupaten',
                'nama_kabupaten' => $validated['nama_kabupaten'],
                'kode_kabupaten' => $validated['kode_kabupaten'],
                'anggota' => $validated['jumlah_anggota'],
                'jumlah_anggota' => $validated['jumlah_anggota'],
                'status' => $validated['status'],
                'email_verified_at' => now(), // Auto-verify user yang dibuat admin
            ]);

            \Log::info('Kabupaten user created', ['id' => $user->id]);

            \DB::commit();
            \Log::info('Transaction committed successfully');

            return redirect()->route('admin.dashboard.kabupaten.index')
                ->with('success', 'Kabupaten berhasil ditambahkan');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error creating kabupaten', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $kabupaten)
    {
        return Inertia::render('admin/kabupaten/edit', [
            'kabupaten' => $kabupaten,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $kabupaten)
    {
        $validated = $request->validate([
            'nama_kabupaten' => 'required|string|max:255',
            'kode_kabupaten' => 'required|string|max:10|unique:users,kode_kabupaten,' . $kabupaten->id,
            'jumlah_anggota' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama_kabupaten.required' => 'Nama kabupaten harus diisi',
            'kode_kabupaten.required' => 'Kode kabupaten harus diisi',
            'kode_kabupaten.unique' => 'Kode kabupaten sudah digunakan',
            'jumlah_anggota.required' => 'Jumlah anggota harus diisi',
            'jumlah_anggota.integer' => 'Jumlah anggota harus berupa angka',
            'jumlah_anggota.min' => 'Jumlah anggota tidak boleh kurang dari 0',
            'status.required' => 'Status harus dipilih',
        ]);

        $kabupaten->update([
            'nama_kabupaten' => $validated['nama_kabupaten'],
            'kode_kabupaten' => $validated['kode_kabupaten'],
            'anggota' => $validated['jumlah_anggota'],
            'jumlah_anggota' => $validated['jumlah_anggota'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.dashboard.kabupaten.index')
            ->with('success', 'Kabupaten berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $kabupaten)
    {
        \DB::beginTransaction();
        try {
            // Delete kabupaten user (transactions will be cascade deleted automatically)
            $kabupaten->delete();
            
            \DB::commit();
            
            return redirect()->route('admin.dashboard.kabupaten.index')
                ->with('success', 'Kabupaten dan data terkait berhasil dihapus');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error deleting kabupaten', [
                'error' => $e->getMessage(),
                'kabupaten_id' => $kabupaten->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus kabupaten: ' . $e->getMessage());
        }
    }
}
