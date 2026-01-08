# 📡 API Documentation - Sistem Iuran PGRI
## RESTful API Reference

---

## 🎯 Overview

Sistem Iuran PGRI menyediakan RESTful API untuk mengelola pembayaran iuran PGRI dengan integrasi Midtrans Payment Gateway.

### Base URL
```
Development: http://localhost:8000
Production:  https://your-domain.com
```

### Authentication
Semua endpoint (kecuali webhook) memerlukan autentikasi menggunakan Laravel Session.

```http
Cookie: laravel_session=<session_token>
X-CSRF-TOKEN: <csrf_token>
```

---

## 📋 Table of Contents

1. [Authentication](#authentication)
2. [Transactions](#transactions)
3. [Iuran (Legacy)](#iuran-legacy)
4. [Dashboard](#dashboard)
5. [Reports](#reports)
6. [Notifications](#notifications)
7. [Webhooks](#webhooks)

---

## 🔐 Authentication

### Login

**Endpoint:** `POST /login`

**Description:** Authenticate user dan create session

**Request Body:**
```json
{
  "email": "kabupaten@example.com",
  "password": "password123",
  "remember": true
}
```

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "name": "Kabupaten Jakarta",
    "email": "kabupaten@example.com",
    "role": "kabupaten",
    "anggota": 150
  },
  "redirect": "/kabupaten/dashboard"
}
```

**Error Response:** `422 Unprocessable Entity`
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["These credentials do not match our records."]
  }
}
```

---

### Logout

**Endpoint:** `POST /logout`

**Description:** Destroy user session

**Response:** `302 Redirect`
```
Location: /login
```

---

## 💳 Transactions

### Create Transaction

**Endpoint:** `POST /kabupaten/iuran`

**Description:** Create new payment transaction dan generate Midtrans Snap token

**Authorization:** `role:kabupaten`

**Request Body:**
```json
{
  "jumlah": 1000000,
  "deskripsi": "Iuran Bulan Januari 2025"
}
```

**Validation Rules:**
- `jumlah`: required, integer, min:1000
- `deskripsi`: required, string, max:255

**Response:** `200 OK`
```json
{
  "success": true,
  "snap_token": "66e4fa55-fdac-4ef9-91b5-733b97d1b862",
  "order_id": "ORDER-1735477200-1",
  "transaction": {
    "id": 1,
    "user_id": 1,
    "order_id": "ORDER-1735477200-1",
    "gross_amount": 1000000,
    "status": "pending",
    "description": "Iuran Bulan Januari 2025",
    "snap_token": "66e4fa55-fdac-4ef9-91b5-733b97d1b862",
    "created_at": "2025-12-29T21:00:00.000000Z",
    "updated_at": "2025-12-29T21:00:00.000000Z"
  }
}
```

**Error Response:** `422 Unprocessable Entity`
```json
{
  "message": "The jumlah field is required.",
  "errors": {
    "jumlah": ["The jumlah field is required."]
  }
}
```

**Error Response:** `500 Internal Server Error`
```json
{
  "success": false,
  "message": "Failed to create transaction: Midtrans API error"
}
```

---

### Get User Transactions

**Endpoint:** `GET /kabupaten/iuran`

**Description:** Get all transactions for authenticated user

**Authorization:** `role:kabupaten`

**Query Parameters:**
- `status` (optional): Filter by status (pending, settlement, cancel, etc)
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 20)

**Example Request:**
```http
GET /kabupaten/iuran?status=settlement&page=1&per_page=10
```

**Response:** `200 OK`
```json
{
  "transactions": {
    "data": [
      {
        "id": 1,
        "order_id": "ORDER-1735477200-1",
        "transaction_id": "TRX-MIDTRANS-123",
        "gross_amount": 1000000,
        "payment_type": "credit_card",
        "payment_method": "visa",
        "status": "settlement",
        "description": "Iuran Bulan Januari 2025",
        "transaction_time": "2025-12-29T21:00:00.000000Z",
        "settlement_time": "2025-12-29T21:05:00.000000Z",
        "created_at": "2025-12-29T21:00:00.000000Z",
        "user": {
          "id": 1,
          "name": "Kabupaten Jakarta",
          "email": "kabupaten@example.com"
        }
      }
    ],
    "current_page": 1,
    "per_page": 10,
    "total": 1,
    "last_page": 1
  },
  "summary": {
    "total_amount": 1000000,
    "total_success": 1,
    "total_pending": 0
  }
}
```

---

### Get Transaction Detail

**Endpoint:** `GET /kabupaten/iuran/{id}`

**Description:** Get detailed information about specific transaction

**Authorization:** `role:kabupaten` (own transactions only)

**Response:** `200 OK`
```json
{
  "transaction": {
    "id": 1,
    "order_id": "ORDER-1735477200-1",
    "transaction_id": "TRX-MIDTRANS-123",
    "gross_amount": 1000000,
    "payment_type": "credit_card",
    "payment_method": "visa",
    "status": "settlement",
    "description": "Iuran Bulan Januari 2025",
    "snap_token": "66e4fa55-fdac-4ef9-91b5-733b97d1b862",
    "transaction_time": "2025-12-29T21:00:00.000000Z",
    "settlement_time": "2025-12-29T21:05:00.000000Z",
    "metadata": {
      "fraud_status": "accept",
      "bank": "bni"
    },
    "created_at": "2025-12-29T21:00:00.000000Z",
    "updated_at": "2025-12-29T21:05:00.000000Z",
    "user": {
      "id": 1,
      "name": "Kabupaten Jakarta",
      "email": "kabupaten@example.com",
      "anggota": 150
    }
  }
}
```

**Error Response:** `403 Forbidden`
```json
{
  "message": "Unauthorized access to this transaction."
}
```

**Error Response:** `404 Not Found`
```json
{
  "message": "Transaction not found."
}
```

---

### Check Transaction Status

**Endpoint:** `GET /transaction/status/{orderId}`

**Description:** Check current status of transaction from Midtrans

**Authorization:** `role:kabupaten`

**Response:** `200 OK`
```json
{
  "order_id": "ORDER-1735477200-1",
  "status": "settlement",
  "transaction_status": "settlement",
  "fraud_status": "accept",
  "payment_type": "credit_card",
  "gross_amount": "1000000.00",
  "transaction_time": "2025-12-29 21:00:00",
  "settlement_time": "2025-12-29 21:05:00"
}
```

**Error Response:** `404 Not Found`
```json
{
  "error": "Transaction not found in Midtrans"
}
```

---

## 📊 Iuran (Legacy)

### Get All Iuran (Admin)

**Endpoint:** `GET /admin/dashboard`

**Description:** Get all iuran records (includes auto-created from Midtrans)

**Authorization:** `role:admin`

**Response:** `200 OK`
```json
{
  "iurans": [
    {
      "id": 1,
      "kabupaten_id": 1,
      "jumlah": 1000000,
      "bukti_transaksi": "ORDER-1735477200-1",
      "tanggal": "2025-12-29T21:05:00.000000Z",
      "deskripsi": "Iuran Bulan Januari 2025",
      "terverifikasi": "diterima",
      "created_at": "2025-12-29T21:05:00.000000Z",
      "kabupaten": {
        "id": 1,
        "name": "Kabupaten Jakarta",
        "email": "kabupaten@example.com",
        "anggota": 150
      }
    }
  ],
  "statistics": {
    "total_masuk": 1000000,
    "jumlah_transaksi": 1,
    "pending": 0,
    "diterima": 1,
    "ditolak": 0
  }
}
```

---

## 📈 Dashboard

### Kabupaten Dashboard

**Endpoint:** `GET /kabupaten/dashboard`

**Description:** Get dashboard data for kabupaten user

**Authorization:** `role:kabupaten`

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "name": "Kabupaten Jakarta",
    "email": "kabupaten@example.com",
    "anggota": 150
  },
  "summary": {
    "total_paid": 1000000,
    "total_pending": 0,
    "transaction_count": 1,
    "last_payment": "2025-12-29T21:05:00.000000Z"
  },
  "recent_transactions": [
    {
      "id": 1,
      "order_id": "ORDER-1735477200-1",
      "gross_amount": 1000000,
      "status": "settlement",
      "created_at": "2025-12-29T21:00:00.000000Z"
    }
  ],
  "monthly_chart": [
    {
      "month": "2025-01",
      "total": 1000000,
      "count": 1
    }
  ]
}
```

---

### Admin Dashboard

**Endpoint:** `GET /admin/dashboard`

**Description:** Get dashboard data for admin user

**Authorization:** `role:admin`

**Response:** `200 OK`
```json
{
  "statistics": {
    "total_masuk": 5000000,
    "jumlah_transaksi": 5,
    "pending": 1,
    "diterima": 4,
    "ditolak": 0,
    "total_kabupaten": 3
  },
  "recent_transactions": [
    {
      "id": 1,
      "kabupaten": {
        "name": "Kabupaten Jakarta"
      },
      "jumlah": 1000000,
      "terverifikasi": "diterima",
      "tanggal": "2025-12-29T21:05:00.000000Z"
    }
  ],
  "top_contributors": [
    {
      "kabupaten": "Kabupaten Jakarta",
      "total": 3000000,
      "count": 3
    }
  ],
  "monthly_trend": [
    {
      "month": "2025-01",
      "total": 5000000,
      "count": 5
    }
  ]
}
```

---

## 📄 Reports

### Generate Monthly Report

**Endpoint:** `GET /admin/laporan/bulanan`

**Description:** Generate monthly report

**Authorization:** `role:admin`

**Query Parameters:**
- `month`: Month (1-12)
- `year`: Year (e.g., 2025)

**Example Request:**
```http
GET /admin/laporan/bulanan?month=1&year=2025
```

**Response:** `200 OK`
```json
{
  "period": {
    "month": 1,
    "year": 2025,
    "month_name": "Januari"
  },
  "summary": {
    "total_amount": 5000000,
    "transaction_count": 5,
    "kabupaten_count": 3
  },
  "transactions": [
    {
      "id": 1,
      "kabupaten": "Kabupaten Jakarta",
      "jumlah": 1000000,
      "tanggal": "2025-01-15",
      "status": "diterima"
    }
  ],
  "breakdown_by_kabupaten": [
    {
      "kabupaten": "Kabupaten Jakarta",
      "total": 3000000,
      "count": 3
    }
  ]
}
```

---

### Generate Kabupaten Report

**Endpoint:** `GET /admin/laporan/kabupaten/{kabupatenId}`

**Description:** Generate report for specific kabupaten

**Authorization:** `role:admin`

**Query Parameters:**
- `start_date`: Start date (YYYY-MM-DD)
- `end_date`: End date (YYYY-MM-DD)

**Example Request:**
```http
GET /admin/laporan/kabupaten/1?start_date=2025-01-01&end_date=2025-12-31
```

**Response:** `200 OK`
```json
{
  "kabupaten": {
    "id": 1,
    "name": "Kabupaten Jakarta",
    "email": "kabupaten@example.com",
    "anggota": 150
  },
  "period": {
    "start_date": "2025-01-01",
    "end_date": "2025-12-31"
  },
  "summary": {
    "total_amount": 3000000,
    "transaction_count": 3,
    "average_amount": 1000000
  },
  "transactions": [
    {
      "id": 1,
      "jumlah": 1000000,
      "tanggal": "2025-01-15",
      "status": "diterima",
      "payment_method": "credit_card"
    }
  ]
}
```

---

## 🔔 Notifications

### Get All Notifications

**Endpoint:** `GET /admin/notifikasi`

**Description:** Get all notifications for admin

**Authorization:** `role:admin`

**Query Parameters:**
- `status`: Filter by status (unread, read, cancelled)
- `page`: Page number

**Response:** `200 OK`
```json
{
  "notifications": {
    "data": [
      {
        "id": 1,
        "kabupaten_id": 1,
        "jumlah": 1000000,
        "deskripsi": "Iuran Bulan Januari 2025",
        "status": "unread",
        "created_at": "2025-12-29T21:05:00.000000Z",
        "kabupaten": {
          "name": "Kabupaten Jakarta"
        }
      }
    ],
    "current_page": 1,
    "total": 1
  },
  "unread_count": 1
}
```

---

### Mark Notification as Read

**Endpoint:** `PATCH /admin/notifikasi/{id}/read`

**Description:** Mark single notification as read

**Authorization:** `role:admin`

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "Notification marked as read"
}
```

---

### Mark All Notifications as Read

**Endpoint:** `POST /admin/notifikasi/mark-all-read`

**Description:** Mark all notifications as read

**Authorization:** `role:admin`

**Response:** `200 OK`
```json
{
  "success": true,
  "message": "All notifications marked as read",
  "count": 5
}
```

---

## 🔗 Webhooks

### Midtrans Payment Notification

**Endpoint:** `POST /user/payment/callback`

**Description:** Webhook endpoint untuk menerima notifikasi dari Midtrans

**Authentication:** Signature verification (no session required)

**Headers:**
```http
Content-Type: application/json
```

**Request Body (from Midtrans):**
```json
{
  "transaction_time": "2025-12-29 21:00:00",
  "transaction_status": "settlement",
  "transaction_id": "TRX-MIDTRANS-123",
  "status_message": "midtrans payment notification",
  "status_code": "200",
  "signature_key": "abc123...",
  "settlement_time": "2025-12-29 21:05:00",
  "payment_type": "credit_card",
  "order_id": "ORDER-1735477200-1",
  "merchant_id": "G123456789",
  "gross_amount": "1000000.00",
  "fraud_status": "accept",
  "currency": "IDR",
  "card_type": "credit",
  "bank": "bni"
}
```

**Response:** `200 OK`
```json
{
  "status": "success",
  "message": "Notification processed successfully"
}
```

**Error Response:** `403 Forbidden`
```json
{
  "status": "error",
  "message": "Invalid signature"
}
```

**Signature Verification:**
```php
$serverKey = config('midtrans.server_key');
$hashed = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

if ($hashed !== $signatureKey) {
    return response()->json(['error' => 'Invalid signature'], 403);
}
```

---

## 📊 Status Codes

| Code | Description |
|------|-------------|
| `200` | Success |
| `201` | Created |
| `302` | Redirect |
| `400` | Bad Request |
| `401` | Unauthorized |
| `403` | Forbidden |
| `404` | Not Found |
| `422` | Unprocessable Entity (Validation Error) |
| `500` | Internal Server Error |

---

## 🔄 Transaction Status Flow

```
pending → settlement (Success)
pending → cancel (User cancelled)
pending → expire (Timeout)
pending → deny (Rejected by bank)
pending → failure (Technical error)
```

---

## 🧪 Testing

### Sandbox Environment

**Midtrans Test Cards:**

**Credit Card (Success):**
```
Card Number: 4811 1111 1111 1114
CVV: 123
Exp: 01/25
OTP: 112233
```

**Credit Card (Denied):**
```
Card Number: 4911 1111 1111 1113
CVV: 123
Exp: 01/25
```

**Bank Transfer:**
- Virtual Account akan auto-approve di sandbox

---

## 📝 Rate Limiting

**Default Limits:**
- Authentication endpoints: 5 requests/minute
- API endpoints: 60 requests/minute
- Webhook endpoint: No limit (trusted source)

**Headers:**
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1735477260
```

---

## 🔐 Security Best Practices

1. **Always use HTTPS** in production
2. **Validate webhook signature** before processing
3. **Implement CSRF protection** for all POST requests
4. **Use environment variables** for sensitive data
5. **Log all transactions** for audit trail
6. **Implement rate limiting** to prevent abuse
7. **Sanitize user input** to prevent XSS/SQL injection

---

## 📞 Support

**Technical Issues:**
- Email: tech-support@pgri.or.id
- Documentation: https://docs.pgri.or.id

**Midtrans Support:**
- Email: support@midtrans.com
- Documentation: https://docs.midtrans.com

---

**Last Updated:** 29 Desember 2025  
**API Version:** 1.0  
**Status:** ✅ Production Ready
