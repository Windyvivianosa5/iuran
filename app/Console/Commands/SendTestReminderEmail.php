<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReminderMail;
use Illuminate\Console\Command;

class SendTestReminderEmail extends Command
{
    protected $signature = 'test:reminder-email {email?}';

    protected $description = 'Kirim test email reminder pembayaran ke user tertentu (untuk testing)';

    public function handle()
    {
        $email = $this->argument('email');

        // Jika tidak ada email, tampilkan daftar user kabupaten
        if (!$email) {
            $this->info('Daftar User Kabupaten:');
            $this->info('=====================');
            
            $users = User::where('role', 'kabupaten')->get();
            
            if ($users->isEmpty()) {
                $this->error('Tidak ada user dengan role kabupaten.');
                return 1;
            }

            foreach ($users as $user) {
                $this->line("- {$user->name} ({$user->email})");
            }

            $this->info('');
            $this->info('Gunakan: php artisan test:reminder-email {email}');
            $this->info('Contoh: php artisan test:reminder-email kabupaten@example.com');
            return 0;
        }

        // Cari user berdasarkan email
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User dengan email {$email} tidak ditemukan.");
            return 1;
        }

        // Kirim email
        try {
            $this->info("Mengirim test email reminder ke: {$user->name} ({$user->email})");
            
            Mail::to($user->email)->send(new PaymentReminderMail($user));
            
            $this->info('✓ Email berhasil dikirim!');
            $this->info('');
            $this->info('Silakan cek inbox email: ' . $user->email);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Gagal mengirim email: ' . $e->getMessage());
            $this->error('');
            $this->error('Pastikan konfigurasi email di .env sudah benar:');
            $this->error('- MAIL_MAILER=smtp');
            $this->error('- MAIL_HOST=smtp.gmail.com');
            $this->error('- MAIL_PORT=587');
            $this->error('- MAIL_USERNAME=your-email@gmail.com');
            $this->error('- MAIL_PASSWORD=your-app-password');
            
            return 1;
        }
    }
}
