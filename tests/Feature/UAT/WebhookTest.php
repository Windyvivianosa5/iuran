<?php

namespace Tests\Feature\UAT;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_updates_status_to_settlement()
    {
        // Tahan pengiriman email sungguhan saat testing
        Mail::fake(); 

        $user = User::factory()->create([
            'role' => 'kabupaten',
        ]);

        $orderId = 'TRX-' . $user->id . '-999';
        $grossAmount = 500000;

        // Buat data transaksi yang masih pending
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'gross_amount' => $grossAmount,
            'status' => 'pending',
            'bulan_pembayaran' => '2024-05'
        ]);

        // Mock Server Key Midtrans
        Config::set('midtrans.server_key', 'dummy_server_key');

        // Buat payload seolah-olah dikirim oleh Midtrans
        $payload = [
            'transaction_time' => '2024-05-09 10:00:00',
            'transaction_status' => 'settlement',
            'transaction_id' => 'midtrans-12345',
            'status_message' => 'midtrans payment notification',
            'status_code' => '200',
            // Enkripsi signature sama persis dengan algoritma Midtrans
            'signature_key' => hash('sha512', $orderId . '200' . $grossAmount . 'dummy_server_key'),
            'payment_type' => 'bank_transfer',
            'order_id' => $orderId,
            'gross_amount' => $grossAmount,
            'fraud_status' => 'accept',
        ];

        // Tembak ke endpoint Webhook kita
        $response = $this->postJson('/midtrans/notification', $payload);

        // Pastikan Webhook kita merespon 200 OK ke Midtrans
        $response->assertStatus(200);

        // Pastikan status di database berubah menjadi settlement
        $this->assertDatabaseHas('transactions', [
            'order_id' => $orderId,
            'status' => 'settlement',
            'transaction_id' => 'midtrans-12345'
        ]);
        
        // Pastikan email notifikasi dikirimkan (dikirim lewat Mail::fake)
        Mail::assertSent(\App\Mail\PaymentSuccessNotification::class);
    }

    public function test_webhook_fails_with_invalid_signature()
    {
        $user = User::factory()->create();
        
        $orderId = 'TRX-' . $user->id . '-888';
        $grossAmount = 500000;

        Transaction::create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'gross_amount' => $grossAmount,
            'status' => 'pending',
        ]);

        Config::set('midtrans.server_key', 'dummy_server_key');

        // Menggunakan signature palsu dari hacker
        $payload = [
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'signature_key' => 'invalid_signature_hash',
            'order_id' => $orderId,
            'gross_amount' => $grossAmount,
        ];

        $response = $this->postJson('/midtrans/notification', $payload);

        // Sistem kita harus menolak dengan 403 Forbidden
        $response->assertStatus(403);

        // Status di database TIDAK BOLEH berubah (tetap pending)
        $this->assertDatabaseHas('transactions', [
            'order_id' => $orderId,
            'status' => 'pending', 
        ]);
    }
}
