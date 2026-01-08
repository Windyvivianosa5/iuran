<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentSuccessNotification;
use App\Mail\PaymentReceivedNotification;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class TransactionController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create a new transaction
     */
    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $user = Auth::user();
            $orderId = 'TRX-' . $user->id . '-' . time();

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'gross_amount' => $request->amount,
                'description' => $request->description ?? 'Pembayaran Iuran PGRI',
                'status' => 'pending',
            ]);

            // Prepare Midtrans transaction details
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $request->amount,
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
                ],
            ];

            // Get Snap Token
            $snapToken = Snap::getSnapToken($params);
            
            // Update transaction with snap token
            $transaction->update(['snap_token' => $snapToken]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            Log::error('Transaction creation failed: ' . $e->getMessage());
            
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
        Log::info('Request Data: ' . json_encode($request->all()));
        
        try {
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;
            $fraudStatus = $notification->fraud_status ?? null;

            Log::info("Processing webhook for Order ID: {$orderId}");
            Log::info("Transaction Status: {$transactionStatus}");
            Log::info("Fraud Status: " . ($fraudStatus ?? 'null'));

            $transaction = Transaction::where('order_id', $orderId)->firstOrFail();
            
            Log::info("Transaction found in database. Current status: {$transaction->status}");
            Log::info("User email: {$transaction->user->email}");

            // Update transaction details
            $transaction->update([
                'transaction_id' => $notification->transaction_id,
                'payment_type' => $notification->payment_type,
                'transaction_time' => $notification->transaction_time,
            ]);
            
            Log::info("Transaction details updated");
            
            // Validate signature
            $serverKey = config('midtrans.server_key');
            $hashed = hash('sha512', $notification->order_id . $notification->status_code . $notification->gross_amount . $serverKey);
            
            if ($notification->signature_key !== $hashed) {
                Log::warning('Invalid Midtrans signature for order: ' . $notification->order_id);
                Log::warning('Expected: ' . $hashed);
                Log::warning('Received: ' . $notification->signature_key);
                return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
            }
            
            Log::info("Signature validation passed");
            
            // Handle transaction status
            if ($transactionStatus == 'capture') {
                Log::info("Status is CAPTURE. Fraud status: {$fraudStatus}");
                if ($fraudStatus == 'accept') {
                    $transaction->update(['status' => 'settlement']);
                    Log::info("Status updated to settlement (from capture)");
                }
            } elseif ($transactionStatus == 'settlement') {
                Log::info("=== STATUS IS SETTLEMENT - PROCESSING EMAIL ===");
                
                $transaction->update([
                    'status' => 'settlement',
                    'settlement_time' => now(),
                ]);
                
                Log::info("Transaction status updated to settlement");
                Log::info("Settlement time: " . now());
                
                // Create iuran record for admin dashboard
                Log::info("Creating iuran record...");
                $this->createIuranFromTransaction($transaction);
                Log::info("Iuran record created");
                
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
            return response()->json(['success' => false], 500);
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
     * Create iuran record from successful transaction
     */
    private function createIuranFromTransaction(Transaction $transaction)
    {
        try {
            // Check if iuran already exists for this transaction
            $existingIuran = \App\Models\Iuran::where('kabupaten_id', $transaction->user_id)
                ->where('jumlah', $transaction->gross_amount)
                ->where('tanggal', $transaction->created_at->format('Y-m-d'))
                ->first();

            if (!$existingIuran) {
                \App\Models\Iuran::create([
                    'kabupaten_id' => $transaction->user_id,
                    'jumlah' => $transaction->gross_amount,
                    'tanggal' => $transaction->created_at,
                    'deskripsi' => $transaction->description,
                    'terverifikasi' => 'diterima', // Auto-approve Midtrans payments
                    'bukti_transaksi' => 'midtrans_' . $transaction->order_id, // Store order_id as proof
                ]);

                Log::info("Iuran created from transaction: {$transaction->order_id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to create iuran from transaction: " . $e->getMessage());
        }
    }
}
