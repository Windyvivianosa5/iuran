<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Transaction as MidtransTransaction;

class TransactionStatusController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
    }

    /**
     * Check and update transaction status from Midtrans
     */
    public function checkAndUpdate($orderId)
    {
        try {
            $transaction = Transaction::where('order_id', $orderId)->firstOrFail();

            // Get status from Midtrans
            $status = MidtransTransaction::status($orderId);

            // Update transaction
            $transaction->update([
                'status' => $status->transaction_status,
                'payment_type' => $status->payment_type ?? null,
                'transaction_id' => $status->transaction_id ?? null,
                'settlement_time' => $status->settlement_time ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'status' => $status->transaction_status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk check all pending transactions
     */
    public function checkAllPending()
    {
        try {
            $pendingTransactions = Transaction::where('status', 'pending')->get();
            $updated = 0;

            foreach ($pendingTransactions as $transaction) {
                try {
                    $status = MidtransTransaction::status($transaction->order_id);
                    
                    $transaction->update([
                        'status' => $status->transaction_status,
                        'payment_type' => $status->payment_type ?? null,
                        'transaction_id' => $status->transaction_id ?? null,
                        'settlement_time' => $status->settlement_time ?? null,
                    ]);

                    $updated++;
                } catch (\Exception $e) {
                    // Skip if error
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil memperbarui {$updated} transaksi",
                'updated' => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage(),
            ], 500);
        }
    }
}   