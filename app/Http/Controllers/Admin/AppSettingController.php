<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppSettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $adminFee = (int) Setting::getValue('admin_fee', 4000);

        return Inertia::render('admin/settings/index', [
            'adminFee' => $adminFee,
        ]);
    }

    /**
     * Update the application settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'adminFee' => 'required|integer|min:0',
        ], [
            'adminFee.required' => 'Biaya admin harus diisi',
            'adminFee.integer' => 'Biaya admin harus berupa angka',
            'adminFee.min' => 'Biaya admin tidak boleh kurang dari 0',
        ]);

        Setting::setValue('admin_fee', $validated['adminFee']);

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan');
    }
}
