<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Transaction;

echo "=== TRANSAKSI PENDING ===\n\n";

$transactions = Transaction::where('status', 'pending')
    ->orderBy('created_at', 'desc')
    ->take(10)
    ->get(['id', 'order_id', 'gross_amount', 'status', 'created_at', 'user_id']);

if ($transactions->isEmpty()) {
    echo "Tidak ada transaksi pending.\n";
} else {
    foreach ($transactions as $tx) {
        echo "ID: {$tx->id}\n";
        echo "Order ID: {$tx->order_id}\n";
        echo "Amount: Rp " . number_format($tx->gross_amount, 0, ',', '.') . "\n";
        echo "Status: {$tx->status}\n";
        echo "Created: {$tx->created_at}\n";
        echo "User ID: {$tx->user_id}\n";
        echo str_repeat('-', 50) . "\n";
    }
}
