<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\PaymentSuccessNotification;
use App\Mail\PaymentReceivedNotification;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
        
        // Fix for local development SSL issue
        if (app()->environment('local')) {
            Config::$curlOptions = [
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPHEADER => ['X-Dummy-Header: true'], // Fix Midtrans SDK array key bug
            ];
        }
    }

    /**
     * Create a new transaction
     */
    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
            'bulan_pembayaran' => ['required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        ]);

        try {
            $user = Auth::user();
            
            // Check if user account is active
            if ($user->status === 'nonaktif') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator untuk mengaktifkan akun.',
                ], 403);
            }
            
            // Mencegah pembuatan double data invoice (Pending) untuk bulan yang sama.
            $existingPending = Transaction::where('user_id', $user->id)
                ->where('bulan_pembayaran', $request->bulan_pembayaran)
                ->where('status', 'pending')
                ->first();

            if ($existingPending) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat transaksi: Masih ada tagihan Anda yang SEDANG DIPROSES untuk bulan tersebut. Selesaikan pembayaran di halaman riwayat.',
                ], 422);
            }
            
            $orderId = 'TRX-' . $user->id . '-' . strtoupper(Str::random(10));

            // Hitung Biaya Admin (Flat Fee)
            $taxAmount = 10; // Rp 4.000 per transaksi untuk semua metode pembayaran
            $taxName = 'Biaya Admin / Layanan';

            $grossAmount = $request->amount + $taxAmount;

            DB::beginTransaction();

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'admin_fee' => $taxAmount,
                'description' => $request->description ?? 'Pembayaran Iuran PGRI',
                'bulan_pembayaran' => $request->bulan_pembayaran,
                'status' => 'pending',
            ]);

            // Prepare Midtrans transaction details
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
                'item_details' => [
                    [
                        'id' => 'IURAN-001',
                        'price' => $request->amount,
                        'quantity' => 1,
                        'name' => $request->description ?? 'Iuran PGRI',
                    ],
                    [
                        'id' => 'FEE-001',
                        'price' => $taxAmount,
                        'quantity' => 1,
                        'name' => $taxName,
                    ],
                ],
                'enabled_payments' => [
                    // Virtual Account
                    'bca_va', 'bni_va', 'echannel', 'bri_va',
                    // QRIS
                    'qris',
                    // E-Wallet / Dompet Digital
                    'gopay', 'shopeepay',
                ],
                'callbacks' => [
                    'finish' => url('/kabupaten/dashboard/iuran'),
                    'unfinish' => url('/kabupaten/dashboard/iuran'),
                    'error' => url('/kabupaten/dashboard/iuran'),
                ],
            ];

            // Get Snap Token
            $snapToken = Snap::getSnapToken($params);
            
            // Update transaction with snap token
            $transaction->update(['snap_token' => $snapToken]);

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction creation failed: ' . $e->getMessage() . ' di baris ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Midtrans notification callback
     */
    public function notification(Request $request)
    {
        // Log incoming webhook
        Log::info('=== MIDTRANS WEBHOOK RECEIVED ===');
        Log::info('Raw Body: ' . $request->getContent());
        Log::info('Request Data: ' . json_encode($request->all()));
        
        try {
            // Jika tidak ada data, kembalikan 200 agar Midtrans tidak retry
            $rawBody = $request->getContent();
            if (empty($rawBody)) {
                Log::warning('Empty webhook body received');
                return response()->json(['success' => true, 'message' => 'empty body'], 200);
            }

            $transactionStatus = $request->transaction_status;
            $orderId = $request->order_id;
            $fraudStatus = $request->fraud_status ?? null;

            Log::info("Processing webhook for Order ID: {$orderId}");
            Log::info("Transaction Status: {$transactionStatus}");
            Log::info("Fraud Status: " . ($fraudStatus ?? 'null'));

            $transaction = Transaction::where('order_id', $orderId)->firstOrFail();
            
            Log::info("Transaction found in database. Current status: {$transaction->status}");
            Log::info("User email: {$transaction->user->email}");

            // Validate signature FIRST (SEBELUM UPDATE DATABASE)
            $serverKey = config('midtrans.server_key');
            $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
            
            if ($request->signature_key !== $hashed) {
                Log::warning('Invalid Midtrans signature for order: ' . $request->order_id);
                Log::warning('Expected: ' . $hashed);
                Log::warning('Received: ' . $request->signature_key);
                return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
            }
            
            Log::info("Signature validation passed");

            // Update transaction details
            $transaction->update([
                'transaction_id' => $request->transaction_id,
                'payment_type' => $request->payment_type,
                'transaction_time' => $request->transaction_time,
            ]);
            
            Log::info("Transaction details updated");
            
            // Handle transaction status
            if ($transactionStatus == 'capture') {
                Log::info("Status is CAPTURE. Fraud status: {$fraudStatus}");
                if ($fraudStatus == 'accept') {
                    $transaction->update(['status' => 'settlement']);
                    Log::info("Status updated to settlement (from capture)");
                }
            } elseif ($transactionStatus == 'settlement') {
                if ($transaction->status === 'settlement' && $transaction->settlement_time) {
                    Log::info("=== ALREADY PROCESSED SETTLEMENT - SKIPPING EMAIL ===");
                    return response()->json(['success' => true]);
                }

                Log::info("=== STATUS IS SETTLEMENT - PROCESSING EMAIL ===");
                
                $transaction->update([
                    'status' => 'settlement',
                    'settlement_time' => now(),
                ]);
                
                Log::info("Transaction status updated to settlement");
                Log::info("Settlement time: " . now());
                
                // Send email notifications
                try {
                    Log::info("=== STARTING EMAIL SENDING PROCESS ===");
                    
                    // Refresh transaction to get updated data
                    $transaction->refresh();
                    Log::info("Transaction refreshed from database");
                    
                    // Check if user exists
                    if (!$transaction->user) {
                        Log::error("ERROR: Transaction has no associated user!");
                        throw new \Exception("Transaction has no user");
                    }
                    
                    Log::info("User found: {$transaction->user->name} ({$transaction->user->email})");
                    
                    // Send success email to user (Kabupaten)
                    Log::info("Attempting to send email to user: {$transaction->user->email}");
                    
                    Mail::to($transaction->user->email)->send(
                        new PaymentSuccessNotification($transaction)
                    );
                    
                    Log::info("✅ SUCCESS: Email sent to user {$transaction->user->email}");
                    
                    // Send notification email to admin
                    $adminEmail = config('mail.admin_email', env('ADMIN_EMAIL', 'admin@pgri-riau.id'));
                    Log::info("Attempting to send email to admin: {$adminEmail}");
                    
                    Mail::to($adminEmail)->send(
                        new PaymentReceivedNotification($transaction)
                    );
                    
                    Log::info("✅ SUCCESS: Email sent to admin {$adminEmail}");
                    Log::info("=== ALL EMAILS SENT SUCCESSFULLY ===");
                    
                } catch (\Exception $e) {
                    Log::error("=== EMAIL SENDING FAILED ===");
                    Log::error("Error Message: " . $e->getMessage());
                    Log::error("Error File: " . $e->getFile());
                    Log::error("Error Line: " . $e->getLine());
                    Log::error("Stack Trace: " . $e->getTraceAsString());
                    // Don't fail the webhook if email fails
                }
            } elseif ($transactionStatus == 'pending') {
                Log::info("Status is PENDING");
                $transaction->update(['status' => 'pending']);
            } elseif ($transactionStatus == 'deny') {
                Log::info("Status is DENY");
                $transaction->update(['status' => 'deny']);
            } elseif ($transactionStatus == 'expire') {
                Log::info("Status is EXPIRE");
                $transaction->update(['status' => 'expire']);
            } elseif ($transactionStatus == 'cancel') {
                Log::info("Status is CANCEL");
                $transaction->update(['status' => 'cancel']);
            }

            Log::info("=== WEBHOOK PROCESSING COMPLETED SUCCESSFULLY ===");
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('=== WEBHOOK PROCESSING FAILED ===');
            Log::error('Error Message: ' . $e->getMessage());
            Log::error('Error File: ' . $e->getFile());
            Log::error('Error Line: ' . $e->getLine());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            // Return 200 agar Midtrans tidak retry terus-menerus
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal server'], 200);
        }
    }

    /**
     * Check transaction status
     */
    public function checkStatus($orderId)
    {
        try {
            $transaction = Transaction::where('order_id', $orderId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Get user transactions
     */
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Cancel a pending transaction
     */
    public function cancelTransaction($orderId)
    {
        try {
            $transaction = Transaction::where('order_id', $orderId)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->firstOrFail();

            // Cancel at Midtrans
            try {
                \Midtrans\Transaction::cancel($orderId);
            } catch (\Exception $e) {
                // Ignore if it's already expired or not found in midtrans
                Log::warning('Midtrans cancel error: ' . $e->getMessage());
            }

            // Update local DB
            $transaction->update(['status' => 'cancel']);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan atau tidak dapat dibatalkan.'
            ], 400);
        }
    }
}
