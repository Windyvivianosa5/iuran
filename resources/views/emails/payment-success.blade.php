<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
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
            gap: 12px;
        }
        .logo-text {
            font-size: 20px;
            font-weight: 500;
            color: #1a73e8;
            margin: 0;
        }
        .email-body {
            padding: 32px 40px;
        }
        .greeting {
            font-size: 16px;
            color: #202124;
            margin: 0 0 24px 0;
        }
        .success-indicator {
            background-color: #e6f4ea;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
            text-align: center;
        }
        .success-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }
        .success-title {
            font-size: 18px;
            font-weight: 500;
            color: #137333;
            margin: 0 0 8px 0;
        }
        .success-subtitle {
            font-size: 14px;
            color: #5f6368;
            margin: 0;
        }
        .amount-display {
            font-size: 36px;
            font-weight: 400;
            color: #202124;
            text-align: center;
            margin: 24px 0;
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
        .info-box {
            background-color: #e8f0fe;
            border-left: 4px solid #1a73e8;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .info-box-title {
            font-size: 14px;
            font-weight: 500;
            color: #1967d2;
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
        .note-box {
            background-color: #fef7e0;
            border-left: 4px solid #f9ab00;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .note-box-title {
            font-size: 14px;
            font-weight: 500;
            color: #b06000;
            margin: 0 0 8px 0;
        }
        .note-box ul {
            margin: 8px 0 0 0;
            padding-left: 20px;
        }
        .note-box li {
            font-size: 14px;
            color: #7c4a00;
            margin: 4px 0;
        }
        .closing {
            font-size: 14px;
            color: #5f6368;
            margin: 32px 0 0 0;
            line-height: 1.6;
        }
        .signature {
            font-size: 14px;
            color: #202124;
            margin: 16px 0 0 0;
        }
        .signature-name {
            font-weight: 500;
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
        .footer-text a {
            color: #1a73e8;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="logo-area">
                <h1 class="logo-text">PGRI Provinsi Riau</h1>
            </div>
        </div>

        <div class="email-body">
            <p class="greeting">Halo <strong>{{ $user->name }}</strong>,</p>

            <div class="success-indicator">
                <div class="success-icon">✅</div>
                <h2 class="success-title">Pembayaran Berhasil</h2>
                <p class="success-subtitle">Transaksi Anda telah berhasil diproses</p>
            </div>

            <div class="amount-display">
                Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}
            </div>

            <p style="font-size: 14px; color: #5f6368; margin: 24px 0;">
                Pembayaran iuran PGRI Anda telah berhasil diverifikasi dan tercatat dalam sistem kami pada {{ \Carbon\Carbon::parse($settlementTime)->format('d F Y') }} pukul {{ \Carbon\Carbon::parse($settlementTime)->format('H:i') }} WIB.
            </p>

            <div class="details-section">
                <h3 class="details-title">Detail Transaksi</h3>
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value"><code>{{ $transaction->order_id }}</code></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Transaction ID:</span>
                    <span class="detail-value"><code>{{ $transaction->transaction_id ?? '-' }}</code></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Transaksi:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }} WIB</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Settlement:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($settlementTime)->format('d M Y, H:i') }} WIB</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bulan Pembayaran:</span>
                    <span class="detail-value">
                        @if($transaction->bulan_pembayaran)
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $transaction->bulan_pembayaran)->locale('id')->translatedFormat('F Y') }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Metode Pembayaran:</span>
                    <span class="detail-value">{{ ucwords(str_replace('_', ' ', $transaction->payment_type ?? 'Midtrans')) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Deskripsi:</span>
                    <span class="detail-value">{{ $transaction->description }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="status-badge">Berhasil</span></span>
                </div>
            </div>

            <div class="info-box">
                <h4 class="info-box-title">Yang Perlu Anda Ketahui</h4>
                <ul>
                    <li>Pembayaran telah terverifikasi secara otomatis oleh sistem</li>
                    <li>Status pembayaran sudah tercatat dalam database PGRI Riau</li>
                    <li>Email ini dapat digunakan sebagai bukti pembayaran resmi</li>
                    <li>Tidak diperlukan konfirmasi manual ke administrator</li>
                </ul>
            </div>

            <div class="note-box">
                <h4 class="note-box-title">Catatan Penting</h4>
                <ul>
                    <li>Simpan email ini untuk arsip dan referensi Anda</li>
                    <li>Jika ada pertanyaan, silakan hubungi administrator dengan menyertakan Order ID</li>
                    <li>Pastikan data pembayaran Anda sesuai dengan yang tertera di atas</li>
                </ul>
            </div>

            <p class="closing">
                Terima kasih atas kontribusi dan kepercayaan Anda kepada PGRI Provinsi Riau. Dukungan Anda sangat berarti bagi kemajuan organisasi kami.
            </p>

            <div class="signature">
                <p style="margin: 0;">Salam hormat,</p>
                <p class="signature-name" style="margin: 4px 0 0 0;">Tim PGRI Provinsi Riau</p>
            </div>
        </div>

        <div class="email-footer">
            <p class="footer-text"><strong>PGRI Provinsi Riau</strong></p>
            <p class="footer-text">Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
            <p class="footer-text">&copy; {{ date('Y') }} PGRI Provinsi Riau. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
