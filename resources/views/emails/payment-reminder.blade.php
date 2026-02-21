<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Pembayaran Iuran</title>
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
        .reminder-banner {
            background-color: #fef7e0;
            border-left: 4px solid #f9ab00;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .reminder-icon {
            font-size: 40px;
            margin-bottom: 12px;
        }
        .reminder-title {
            font-size: 16px;
            font-weight: 500;
            color: #b06000;
            margin: 0 0 8px 0;
        }
        .reminder-text {
            font-size: 14px;
            color: #7c4a00;
            margin: 0;
        }
        .message-text {
            font-size: 14px;
            color: #5f6368;
            margin: 16px 0;
            line-height: 1.6;
        }
        .info-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }
        .info-card-title {
            font-size: 14px;
            font-weight: 500;
            color: #5f6368;
            margin: 0 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .info-label {
            font-size: 14px;
            color: #5f6368;
            margin: 0 5px 0 0;
        }
        .info-value {
            font-size: 14px;
            color: #202124;
            font-weight: 400;
        }
        .note-box {
            background-color: #e8f0fe;
            border-left: 4px solid #1a73e8;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .note-box p {
            font-size: 14px;
            color: #174ea6;
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

            <div class="reminder-banner">
                <h4 class="reminder-title">Pengingat Pembayaran Iuran</h4>
                <p class="reminder-text">Kami ingin mengingatkan Anda tentang pembayaran iuran PGRI bulan ini.</p>
            </div>

            <p class="message-text">
                Berdasarkan catatan kami, pembayaran iuran PGRI untuk bulan ini belum kami terima dari Anda. 
                Kami menghargai kontribusi Anda dan ingin memastikan bahwa keanggotaan Anda tetap aktif.
            </p>

            <div class="info-card">
                <h3 class="info-card-title">Informasi Akun</h3>
                <div class="info-row">
                    <span class="info-label">Nama : </span>
                    <span class="info-value">{{ $user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email : </span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Pengingat : </span>
                    <span class="info-value">{{ now()->format('d F Y') }}</span>
                </div>
            </div>

            <div class="note-box">
                <p><strong>Langkah Selanjutnya:</strong></p>
                <p>• Silakan lakukan pembayaran iuran sebelum akhir bulan</p>
                <p>• Jika Anda sudah melakukan pembayaran, mohon abaikan email ini</p>
                <p>• Hubungi administrator jika ada pertanyaan atau kendala</p>
            </div>

            <p class="closing">
                Terima kasih atas perhatian dan kerja sama Anda. Kontribusi Anda sangat berarti bagi kemajuan PGRI Provinsi Riau.
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