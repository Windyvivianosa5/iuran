<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Baru Diterima</title>
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .notification-icon {
            font-size: 60px;
            margin: 10px 0;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background: #d1ecf1;
            border-left: 4px solid #0c5460;
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
            color: #f5576c;
            text-align: center;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        .kabupaten-name {
            font-size: 20px;
            font-weight: bold;
            color: #f5576c;
            text-align: center;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            
            <h1>Pembayaran Baru Diterima!</h1>
            <p>Notifikasi untuk Admin PGRI</p>
        </div>

        <div class="content">
            <div class="alert-box">
                <strong>Notifikasi Pembayaran</strong>
                <p style="margin: 10px 0 0 0;">Ada pembayaran iuran baru yang telah berhasil diproses melalui Midtrans.</p>
            </div>

            <div class="kabupaten-name">
                {{ $user->name }}
            </div>

            <div class="amount">
                Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}
            </div>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #666;">Detail Transaksi</h3>
                
                <div class="info-row">
                    <span class="info-label">Order ID</span>
                    <span class="info-value"><code>{{ $transaction->order_id }}</code></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Transaction ID</span>
                    <span class="info-value"><code>{{ $transaction->transaction_id ?? '-' }}</code></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kabupaten/Kota</span>
                    <span class="info-value">{{ $user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Pembayaran</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d F Y, H:i') }} WIB</span>
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
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        <span style="display: inline-block; padding: 5px 15px; background: #28a745; color: white; border-radius: 20px; font-size: 12px; font-weight: bold;">
                            SETTLEMENT
                        </span>
                    </span>
                </div>
            </div>

            <p style="text-align: center;">
                <a href="{{ $dashboardUrl }}" class="button">Buka Dashboard Admin</a>
            </p>

            <div style="background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <strong>Informasi:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Pembayaran telah terverifikasi otomatis oleh Midtrans</li>
                    <li>Data telah masuk ke sistem dan dashboard</li>
                    <li>Tidak perlu approval manual</li>
                </ul>
            </div>

            <p style="color: #666; font-size: 14px;">
                <strong>Catatan:</strong> Email ini dikirim otomatis setiap ada pembayaran baru yang berhasil diproses.
            </p>
        </div>

        <div class="footer">
            <p><strong>PGRI Provinsi Riau - Admin Dashboard</strong></p>
            <p>Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} PGRI Riau. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
