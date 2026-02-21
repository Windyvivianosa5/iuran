<?php
/**
 * Manual Webhook Test Script
 * 
 * Gunakan script ini untuk trigger webhook manual ke sistem
 * untuk update status transaksi yang sudah settlement di Midtrans
 */

// GANTI DATA INI SESUAI TRANSAKSI ANDA
$orderId = 'TRX-2-1767878796'; // Order ID dari database (yang status pending)
$grossAmount = '10000.00'; // Jumlah pembayaran
$serverKey = 'GANTI_DENGAN_SERVER_KEY_ANDA'; // GANTI dengan Server Key dari .env

// Data webhook sesuai format Midtrans
$webhookData = [
    'transaction_time' => date('Y-m-d H:i:s'),
    'transaction_status' => 'settlement',
    'transaction_id' => 'manual-test-' . time(),
    'status_message' => 'midtrans payment notification',
    'status_code' => '200',
    'payment_type' => 'qris',
    'order_id' => $orderId,
    'merchant_id' => 'G812345678',
    'gross_amount' => $grossAmount,
    'fraud_status' => 'accept',
    'currency' => 'IDR'
];

// Generate signature (PENTING untuk validasi)
$signatureString = $webhookData['order_id'] . $webhookData['status_code'] . $webhookData['gross_amount'] . $serverKey;
$webhookData['signature_key'] = hash('sha512', $signatureString);

echo "=== SENDING WEBHOOK TO LOCAL SERVER ===\n";
echo "Order ID: {$orderId}\n";
echo "Amount: Rp {$grossAmount}\n";
echo "Signature: " . substr($webhookData['signature_key'], 0, 20) . "...\n\n";

// Send POST request ke webhook endpoint
$ch = curl_init('http://127.0.0.1:8000/midtrans/notification');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "=== WEBHOOK RESPONSE ===\n";
echo "HTTP Code: {$httpCode}\n";
echo "Response: {$response}\n\n";

if ($httpCode == 200) {
    echo "✅ SUCCESS! Webhook berhasil dikirim.\n";
    echo "Cek halaman transaksi, status seharusnya sudah berubah menjadi 'Berhasil'.\n";
} else {
    echo "❌ FAILED! Webhook gagal.\n";
    echo "Cek log Laravel di storage/logs/laravel.log untuk detail error.\n";
}
