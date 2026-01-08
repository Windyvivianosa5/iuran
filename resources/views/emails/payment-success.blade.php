<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil & SUKSES</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .success-icon {
            font-size: 60px;
            margin: 10px 0;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: #333;
            text-align: right;
        }
        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            opacity: 0.9;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            background: #28a745;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .lunas-badge {
            display: inline-block;
            padding: 8px 20px;
            background: #28a745;
            color: white;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
            box-shadow: 0 2px 5px rgba(40, 167, 69, 0.3);
        }
        .settlement-box {
            background: #d4edda;
            border: 2px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            
            <h1>Pembayaran Berhasil !</h1>
            <p>Terima kasih atas pembayaran iuran Anda</p>
        </div>

        <div class="content">
            <p>Yth. <strong>{{ $user->name }}</strong>,</p>
            
            <p>Pembayaran iuran PGRI Anda telah <strong>berhasil diproses dan dinyatakan SUKSES</strong> melalui Midtrans.</p>

            <div class="settlement-box">
                <div class="lunas-badge">SUKSES</div>
                <p style="margin: 10px 0 0 0; color: #155724; font-weight: 600;">
                    Status Settlement: {{ \Carbon\Carbon::parse($settlementTime)->format('d F Y, H:i') }} WIB
                </p>
            </div>

            <div class="amount">
                Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}
            </div>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Order ID</span>
                    <span class="info-value"><code>{{ $transaction->order_id }}</code></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Transaction ID</span>
                    <span class="info-value"><code>{{ $transaction->transaction_id ?? '-' }}</code></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Transaksi</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d F Y, H:i') }} WIB</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Settlement</span>
                    <span class="info-value"><strong style="color: #28a745;">{{ \Carbon\Carbon::parse($settlementTime)->format('d F Y, H:i') }} WIB</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode Pembayaran</span>
                    <span class="info-value">{{ ucwords(str_replace('_', ' ', $transaction->payment_type ?? 'Midtrans')) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Deskripsi</span>
                    <span class="info-value">{{ $transaction->description }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status Pembayaran</span>
                    <span class="info-value"><span class="status-badge">SUKSES</span></span>
                </div>
            </div>

            <div style="background: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; margin: 20px 0; border-radius: 4px; color: #0c5460;">
                <strong>Konfirmasi Pembayaran:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Pembayaran Anda telah <strong>SUKSES</strong> dan terverifikasi otomatis</li>
                    <li>Status settlement tercatat pada sistem</li>
                    <li>Bukti pembayaran ini dapat digunakan sebagai arsip resmi</li>
                    <li>Tidak perlu melakukan konfirmasi manual ke admin</li>
                </ul>
            </div>

            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <strong>Catatan Penting:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Simpan email ini sebagai bukti pembayaran yang sah</li>
                    <li>Pembayaran sudah tercatat di sistem PGRI Riau</li>
                    <li>Jika ada pertanyaan, hubungi admin PGRI dengan menyertakan Order ID</li>
                </ul>
            </div>

            <p>Terima kasih atas kontribusi dan kepercayaan Anda kepada PGRI Riau.</p>

            <p>Salam,<br>
            <strong>Tim PGRI Riau</strong></p>
        </div>

        <div class="footer">
            <p><strong>PGRI Provinsi Riau</strong></p>
            <p>Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} PGRI Riau. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
