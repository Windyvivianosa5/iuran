<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Baru Diterima</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #202124;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            padding: 24px 40px;
            border-bottom: 1px solid #e8eaed;
        }
        .logo-area {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo-text {
            font-size: 20px;
            font-weight: 500;
            color: #1a73e8;
            margin: 0;
        }
        .admin-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #fef7e0;
            color: #b06000;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .email-body {
            padding: 32px 40px;
        }
        .notification-banner {
            background-color: #e8f0fe;
            border-left: 4px solid #1a73e8;
            padding: 16px;
            margin: 0 0 24px 0;
            border-radius: 4px;
        }
        .notification-icon {
            font-size: 40px;
            margin-bottom: 12px;
        }
        .notification-title {
            font-size: 14px;
            font-weight: 500;
            color: #1967d2;
            margin: 0 0 4px 0;
        }
        .notification-text {
            font-size: 14px;
            color: #174ea6;
            margin: 0;
        }
        .kabupaten-highlight {
            font-size: 20px;
            font-weight: 500;
            color: #202124;
            margin: 24px 0 16px 0;
            text-align: center;
        }
        .amount-display {
            font-size: 36px;
            font-weight: 400;
            color: #202124;
            text-align: center;
            margin: 16px 0 24px 0;
            letter-spacing: -0.5px;
        }
        .details-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }
        .details-title {
            font-size: 14px;
            font-weight: 500;
            color: #5f6368;
            margin: 0 0 16px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e8eaed;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-size: 14px;
            color: #5f6368;
            margin: 0 5px 0 0;
        }
        .detail-value {
            font-size: 14px;
            color: #202124;
            text-align: right;
            font-weight: 400;
        }
        .detail-value code {
            background-color: #e8eaed;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 13px;
            font-family: 'Courier New', monospace;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #e6f4ea;
            color: #137333;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .action-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #1a73e8;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            margin: 24px 0;
        }
        .action-button:hover {
            background-color: #1765cc;
        }
        .info-box {
            background-color: #e6f4ea;
            border-left: 4px solid #1e8e3e;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .info-box-title {
            font-size: 14px;
            font-weight: 500;
            color: #137333;
            margin: 0 0 8px 0;
        }
        .info-box ul {
            margin: 8px 0 0 0;
            padding-left: 20px;
        }
        .info-box li {
            font-size: 14px;
            color: #174ea6;
            margin: 4px 0;
        }
        .note-text {
            font-size: 13px;
            color: #5f6368;
            margin: 24px 0 0 0;
            font-style: italic;
        }
        .email-footer {
            padding: 24px 40px;
            border-top: 1px solid #e8eaed;
            background-color: #f8f9fa;
        }
        .footer-text {
            font-size: 12px;
            color: #5f6368;
            text-align: center;
            margin: 4px 0;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="logo-area">
                <h1 class="logo-text">PGRI Provinsi Riau</h1>
                <span class="admin-badge">Admin</span>
            </div>
        </div>

        <div class="email-body">
            <div class="notification-banner">
                <h4 class="notification-title">Notifikasi Pembayaran Baru</h4>
                <p class="notification-text">Ada pembayaran iuran yang telah berhasil diproses dan diverifikasi secara otomatis.</p>
            </div>

            <p class="kabupaten-highlight">{{ $user->name }}</p>

            <div class="amount-display">
                Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}
            </div>

            <div class="details-section">
                <h3 class="details-title">Detail Transaksi</h3>
                <div class="detail-row">
                    <span class="detail-label">Order ID : </span>
                    <span class="detail-value"><code>{{ $transaction->order_id }}</code></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Transaction ID : </span>
                    <span class="detail-value"><code>{{ $transaction->transaction_id ?? '-' }}</code></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Kabupaten/Kota : </span>
                    <span class="detail-value">{{ $user->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email : </span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Pembayaran : </span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }} WIB</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bulan Pembayaran : </span>
                    <span class="detail-value">
                        @if($transaction->bulan_pembayaran)
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $transaction->bulan_pembayaran)->locale('id')->translatedFormat('F Y') }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Metode Pembayaran : </span>
                    <span class="detail-value">{{ ucwords(str_replace('_', ' ', $transaction->payment_type ?? 'Midtrans')) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Deskripsi : </span>
                    <span class="detail-value">{{ $transaction->description }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status : </span>
                    <span class="detail-value"><span class="status-badge">Settlement</span></span>
                </div>
            </div>

            <div class="info-box">
                <h4 class="info-box-title">Informasi</h4>
                <ul>
                    <li>Pembayaran telah terverifikasi otomatis oleh sistem Midtrans</li>
                    <li>Data transaksi sudah masuk ke database dan dashboard</li>
                    <li>Tidak diperlukan approval atau konfirmasi manual</li>
                </ul>
            </div>

            <p class="note-text">
                <strong>Catatan:</strong> Email ini dikirim secara otomatis setiap ada pembayaran baru yang berhasil diproses.
            </p>
        </div>

        <div class="email-footer">
            <p class="footer-text"><strong>PGRI Provinsi Riau - Admin Dashboard</strong></p>
            <p class="footer-text">Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
            <p class="footer-text">&copy; {{ date('Y') }} PGRI Provinsi Riau. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
