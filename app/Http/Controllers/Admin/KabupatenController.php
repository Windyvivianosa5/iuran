<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KabupatenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kabupaten = Kabupaten::with('users')
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
            'nama_kabupaten' => 'required|string|max:255|unique:kabupatens,nama_kabupaten',
            'kode_kabupaten' => 'required|string|max:5|unique:kabupatens,kode_kabupaten',
            'jumlah_anggota' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
            // User validation (conditional)
            'create_user' => 'nullable|boolean',
            'user_name' => 'required_if:create_user,true,1|nullable|string|max:255',
            'user_email' => 'required_if:create_user,true,1|nullable|email|unique:users,email',
            'user_password' => 'required_if:create_user,true,1|nullable|string|min:8|confirmed',
        ], [
            'nama_kabupaten.required' => 'Nama kabupaten harus diisi',
            'nama_kabupaten.unique' => 'Nama kabupaten sudah terdaftar',
            'kode_kabupaten.required' => 'Kode kabupaten harus diisi',
            'kode_kabupaten.unique' => 'Kode kabupaten sudah digunakan',
            'jumlah_anggota.required' => 'Jumlah anggota harus diisi',
            'jumlah_anggota.integer' => 'Jumlah anggota harus berupa angka',
            'jumlah_anggota.min' => 'Jumlah anggota tidak boleh kurang dari 0',
            'status.required' => 'Status harus dipilih',
            'user_name.required_if' => 'Nama user harus diisi',
            'user_email.required_if' => 'Email harus diisi',
            'user_email.email' => 'Format email tidak valid',
            'user_email.unique' => 'Email sudah terdaftar',
            'user_password.required_if' => 'Password harus diisi',
            'user_password.min' => 'Password minimal 8 karakter',
            'user_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        \DB::beginTransaction();
        try {
            // Create kabupaten
            $kabupaten = Kabupaten::create([
                'nama_kabupaten' => $validated['nama_kabupaten'],
                'kode_kabupaten' => $validated['kode_kabupaten'],
                'jumlah_anggota' => $validated['jumlah_anggota'],
                'status' => $validated['status'],
            ]);

            \Log::info('Kabupaten created', ['id' => $kabupaten->id]);

            // Create user if requested
            if ($request->create_user == true || $request->create_user == 1 || $request->create_user === 'true') {
                \Log::info('Creating user for kabupaten', ['kabupaten_id' => $kabupaten->id]);
                
                $user = \App\Models\User::create([
                    'name' => $validated['user_name'],
                    'email' => $validated['user_email'],
                    'password' => \Hash::make($validated['user_password']),
                    'role' => 'kabupaten',
                    'kabupaten_id' => $kabupaten->id,
                    'anggota' => $kabupaten->jumlah_anggota,
                    'email_verified_at' => now(), // Auto-verify user yang dibuat admin
                ]);

                \Log::info('User created successfully', ['user_id' => $user->id]);
            }

            \DB::commit();
            \Log::info('Transaction committed successfully');

            return redirect()->route('admin.dashboard.kabupaten.index')
                ->with('success', 'Kabupaten' . ($request->create_user ? ' dan akun user' : '') . ' berhasil ditambahkan');

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
    public function edit(Kabupaten $kabupaten)
    {
        return Inertia::render('admin/kabupaten/edit', [
            'kabupaten' => $kabupaten,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kabupaten $kabupaten)
    {
        $validated = $request->validate([
            'nama_kabupaten' => 'required|string|max:255|unique:kabupatens,nama_kabupaten,' . $kabupaten->id,
            'kode_kabupaten' => 'required|string|max:10|unique:kabupatens,kode_kabupaten,' . $kabupaten->id,
            'jumlah_anggota' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama_kabupaten.required' => 'Nama kabupaten harus diisi',
            'nama_kabupaten.unique' => 'Nama kabupaten sudah terdaftar',
            'kode_kabupaten.required' => 'Kode kabupaten harus diisi',
            'kode_kabupaten.unique' => 'Kode kabupaten sudah digunakan',
            'jumlah_anggota.required' => 'Jumlah anggota harus diisi',
            'jumlah_anggota.integer' => 'Jumlah anggota harus berupa angka',
            'jumlah_anggota.min' => 'Jumlah anggota tidak boleh kurang dari 0',
            'status.required' => 'Status harus dipilih',
        ]);

        $kabupaten->update($validated);

        return redirect()->route('admin.dashboard.kabupaten.index')
            ->with('success', 'Kabupaten berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kabupaten $kabupaten)
    {
        // Check if kabupaten has users
        if ($kabupaten->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Kabupaten tidak dapat dihapus karena masih memiliki user terdaftar');
        }

        // Check if kabupaten has iuran
        if ($kabupaten->iurans()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Kabupaten tidak dapat dihapus karena masih memiliki data iuran');
        }

        $kabupaten->delete();

        return redirect()->route('admin.dashboard.kabupaten.index')
            ->with('success', 'Kabupaten berhasil dihapus');
    }
}
