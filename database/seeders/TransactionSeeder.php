<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Get all kabupaten users
        $kabupatens = User::where('role', 'kabupaten')->get();

        foreach ($kabupatens as $kabupaten) {
            // Create 3-5 random transactions per kabupaten
            $count = rand(3, 5);
            
            for ($i = 0; $i < $count; $i++) {
                $month = rand(1, 12);
                $day = rand(1, 28);
                $year = 2026; // Current year in context
                $amount = rand(500000, 5000000); 
                $date = Carbon::create($year, $month, $day);
                
                Transaction::create([
                    'user_id' => $kabupaten->id,
                    'order_id' => 'TRX-' . $kabupaten->id . '-' . uniqid(),
                    'transaction_id' => uniqid('midtrans-'),
                    'gross_amount' => $amount,
                    'payment_type' => 'bank_transfer',
                    'status' => 'settlement', // All settled for report visibility
                    'description' => 'Iuran bulan ' . $date->locale('id')->translatedFormat('F'),
                    'transaction_time' => $date,
                    'settlement_time' => $date, // Assume instant settlement for seeder
                ]);
            }
        }
    }
}
