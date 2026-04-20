<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckUnpaidUsers extends Command
{
    protected $signature = 'check:unpaid-users {--force : Abaikan pengecekan sisa 7 hari} {--email= : Nomor/alamat email spesifik}';

    protected $description = 'Kirim email pengingat ke user yang belum bayar iuran bulan ini (7 hari sebelum akhir bulan)';

    public function handle()
    {
        $today = Carbon::today();
        $lastDay = $today->copy()->endOfMonth();
        $daysUntilEndOfMonth = $today->diffInDays($lastDay, false);

        Log::info("Checking unpaid users. Days until end of month: {$daysUntilEndOfMonth}");

        // Hanya kirim reminder di 7 hari terakhir bulan, atau jika --force digunakan
        if ($daysUntilEndOfMonth > 7 && !$this->option('force')) {
            $this->info("Bukan periode reminder. Hari tersisa: {$daysUntilEndOfMonth}");
            $this->info("Gunakan opsi --force untuk memaksa pengiriman hari ini.");
            Log::info("Not in reminder period. Skipping.");
            return 0;
        }

        $this->info("Periode reminder aktif. Hari tersisa: {$daysUntilEndOfMonth}");

        // Ambil user kabupaten yang BELUM bayar bulan ini
        $query = User::where('role', 'kabupaten')
            ->whereDoesntHave('transactions', function($q) use ($today) {
                $q->where('bulan_pembayaran', $today->format('Y-m'))
                  ->where('status', 'settlement');
            });
            
        if ($this->option('email')) {
            $query->where('email', $this->option('email'));
            $this->info("Filter aktif untuk email: " . $this->option('email'));
        }

        $unpaidUsers = $query->get();

        if ($unpaidUsers->isEmpty()) {
            $this->info('Semua user sudah membayar bulan ini.');
            Log::info('All users have paid this month.');
            return 0;
        }

        $this->info("Ditemukan {$unpaidUsers->count()} user yang belum bayar.");

        $sentCount = 0;
        $failedCount = 0;

        foreach ($unpaidUsers as $user) {
            try {
                Mail::to($user->email)->send(new PaymentReminderMail($user));
                $this->info("Email terkirim ke: {$user->name} ({$user->email})");
                Log::info("Reminder sent to: {$user->email}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("Gagal kirim ke {$user->email}: " . $e->getMessage());
                Log::error("Failed to send to {$user->email}: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->info("Selesai. Terkirim: {$sentCount}, Gagal: {$failedCount}");
        return 0;
    }
}
