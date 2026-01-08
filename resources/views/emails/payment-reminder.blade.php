<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Pembayaran Iuran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 5px 0;
        }
        .footer {
            background-color: #ecf0f1;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #7f8c8d;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pengingat Pembayaran Iuran PGRI</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Halo, {{ $user->name }}
            </div>
            
            <div class="message">
                <p>Kami ingin mengingatkan Anda bahwa iuran PGRI untuk bulan ini belum kami terima.</p>
            </div>
            
            <div class="info-box">
                <p><strong>Informasi Akun:</strong></p>
                <p>Nama: {{ $user->name }}</p>
                <p>Email: {{ $user->email }}</p>
                <p>Tanggal: {{ now()->format('d F Y') }}</p>
            </div>
            
            <div class="message">
                <p>Mohon segera melakukan pembayaran iuran sebelum akhir bulan untuk menghindari keterlambatan.</p>
                <p>Jika Anda sudah melakukan pembayaran, mohon abaikan email ini.</p>
            </div>
            
            <p style="margin-top: 30px;">
                Terima kasih atas perhatian dan kerjasamanya.
            </p>
            
            <p style="margin-top: 20px; font-weight: bold;">
                Salam,<br>
                Tim PGRI
            </p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} PGRI. All rights reserved.</p>
        </div>
    </div>
</body>
</html>