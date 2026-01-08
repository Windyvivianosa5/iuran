# 🧪 Testing Strategy - Sistem Iuran PGRI
## Comprehensive Testing Guide

---

## 📋 Table of Contents

1. [Testing Overview](#testing-overview)
2. [Testing Pyramid](#testing-pyramid)
3. [Unit Testing](#unit-testing)
4. [Integration Testing](#integration-testing)
5. [Feature Testing](#feature-testing)
6. [End-to-End Testing](#end-to-end-testing)
7. [API Testing](#api-testing)
8. [Security Testing](#security-testing)
9. [Performance Testing](#performance-testing)
10. [Manual Testing Checklist](#manual-testing-checklist)
11. [Test Data & Fixtures](#test-data--fixtures)
12. [CI/CD Integration](#cicd-integration)

---

## 🎯 Testing Overview

### Testing Goals

1. **Reliability** - Ensure system works as expected
2. **Security** - Prevent vulnerabilities
3. **Performance** - Meet performance requirements
4. **Maintainability** - Easy to refactor with confidence
5. **Documentation** - Tests as living documentation

### Testing Metrics

**Target Coverage:**
- Unit Tests: 80%+ coverage
- Integration Tests: 70%+ coverage
- Feature Tests: 90%+ critical paths
- E2E Tests: 100% user flows

**Quality Gates:**
- All tests must pass before merge
- No decrease in code coverage
- No critical security issues
- Performance benchmarks met

---

## 🔺 Testing Pyramid

```
           ╱╲
          ╱  ╲
         ╱ E2E╲          ← 10% (Slow, Expensive)
        ╱──────╲
       ╱Feature╲         ← 20% (Medium Speed)
      ╱─────────╲
     ╱Integration╲       ← 30% (Fast, Focused)
    ╱─────────────╲
   ╱  Unit Tests   ╲     ← 40% (Very Fast, Isolated)
  ╱─────────────────╲
```

---

## 🧩 Unit Testing

### Setup PHPUnit

**Install PHPUnit:**
```bash
# Already included in composer.json
composer require --dev phpunit/phpunit
```

**Configuration:** `phpunit.xml`
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
</phpunit>
```

### Model Tests

**File:** `tests/Unit/Models/TransactionTest.php`

```php
<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(User::class, $transaction->user);
        $this->assertEquals($user->id, $transaction->user->id);
    }

    /** @test */
    public function it_can_check_if_pending()
    {
        $transaction = Transaction::factory()->create([
            'status' => 'pending'
        ]);

        $this->assertTrue($transaction->isPending());
        $this->assertFalse($transaction->isSuccess());
        $this->assertFalse($transaction->isFailed());
    }

    /** @test */
    public function it_can_check_if_success()
    {
        $transaction = Transaction::factory()->create([
            'status' => 'settlement'
        ]);

        $this->assertTrue($transaction->isSuccess());
        $this->assertFalse($transaction->isPending());
        $this->assertFalse($transaction->isFailed());
    }

    /** @test */
    public function it_can_check_if_failed()
    {
        $statuses = ['cancel', 'deny', 'expire', 'failure'];

        foreach ($statuses as $status) {
            $transaction = Transaction::factory()->create([
                'status' => $status
            ]);

            $this->assertTrue($transaction->isFailed());
            $this->assertFalse($transaction->isPending());
            $this->assertFalse($transaction->isSuccess());
        }
    }

    /** @test */
    public function it_casts_metadata_to_array()
    {
        $metadata = ['fraud_status' => 'accept', 'bank' => 'bca'];
        
        $transaction = Transaction::factory()->create([
            'metadata' => $metadata
        ]);

        $this->assertIsArray($transaction->metadata);
        $this->assertEquals($metadata, $transaction->metadata);
    }

    /** @test */
    public function it_casts_dates_correctly()
    {
        $transaction = Transaction::factory()->create([
            'transaction_time' => now(),
            'settlement_time' => now()->addMinutes(5)
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $transaction->transaction_time);
        $this->assertInstanceOf(\Carbon\Carbon::class, $transaction->settlement_time);
    }
}
```

### Service Tests

**File:** `tests/Unit/Services/MidtransServiceTest.php`

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class MidtransServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_unique_order_id()
    {
        $user = User::factory()->create();
        
        $orderId1 = $this->generateOrderId($user->id);
        $orderId2 = $this->generateOrderId($user->id);

        $this->assertNotEquals($orderId1, $orderId2);
        $this->assertStringStartsWith('ORDER-', $orderId1);
    }

    /** @test */
    public function it_validates_signature_correctly()
    {
        $orderId = 'ORDER-123';
        $statusCode = '200';
        $grossAmount = '100000.00';
        $serverKey = config('midtrans.server_key');

        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $invalidSignature = 'invalid-signature';

        $this->assertTrue($this->verifySignature($orderId, $statusCode, $grossAmount, $validSignature));
        $this->assertFalse($this->verifySignature($orderId, $statusCode, $grossAmount, $invalidSignature));
    }

    // Helper methods
    private function generateOrderId($userId)
    {
        return 'ORDER-' . time() . '-' . $userId;
    }

    private function verifySignature($orderId, $statusCode, $grossAmount, $signature)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        return $hashed === $signature;
    }
}
```

### Run Unit Tests

```bash
# Run all unit tests
php artisan test --testsuite=Unit

# Run specific test file
php artisan test tests/Unit/Models/TransactionTest.php

# Run with coverage
php artisan test --coverage

# Run with coverage report
php artisan test --coverage-html coverage
```

---

## 🔗 Integration Testing

### Database Integration Tests

**File:** `tests/Integration/TransactionIntegrationTest.php`

```php
<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Iuran;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_iuran_when_transaction_settles()
    {
        $user = User::factory()->create(['role' => 'kabupaten']);
        
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'gross_amount' => 1000000,
            'description' => 'Test Payment'
        ]);

        // Simulate settlement
        $transaction->update([
            'status' => 'settlement',
            'settlement_time' => now()
        ]);

        // Create iuran (this would be done by controller)
        $iuran = Iuran::create([
            'kabupaten_id' => $user->id,
            'jumlah' => $transaction->gross_amount,
            'bukti_transaksi' => $transaction->order_id,
            'tanggal' => $transaction->settlement_time,
            'deskripsi' => $transaction->description,
            'terverifikasi' => 'diterima'
        ]);

        $this->assertDatabaseHas('iurans', [
            'kabupaten_id' => $user->id,
            'jumlah' => 1000000,
            'terverifikasi' => 'diterima'
        ]);

        $this->assertEquals($transaction->order_id, $iuran->bukti_transaksi);
    }

    /** @test */
    public function it_maintains_referential_integrity()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id]);

        // Delete user should cascade delete transactions
        $user->delete();

        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id
        ]);
    }
}
```

### External API Integration Tests

**File:** `tests/Integration/MidtransIntegrationTest.php`

```php
<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class MidtransIntegrationTest extends TestCase
{
    /** @test */
    public function it_can_create_snap_token()
    {
        // Mock Midtrans API
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'fake-snap-token',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/fake-snap-token'
            ], 201)
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-TEST-123',
                'gross_amount' => 100000
            ],
            'customer_details' => [
                'first_name' => 'Test User',
                'email' => 'test@example.com'
            ]
        ];

        $response = Http::withBasicAuth(config('midtrans.server_key'), '')
            ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $params);

        $this->assertEquals(201, $response->status());
        $this->assertArrayHasKey('token', $response->json());
    }
}
```

---

## 🎭 Feature Testing

### Authentication Tests

**File:** `tests/Feature/Auth/LoginTest.php`

```php
<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/kabupaten/dashboard');
    }

    /** @test */
    public function user_cannot_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function admin_redirected_to_admin_dashboard()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password123'
        ]);

        $response->assertRedirect('/admin/dashboard');
    }
}
```

### Transaction Tests

**File:** `tests/Feature/TransactionTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function kabupaten_can_create_transaction()
    {
        $user = User::factory()->create(['role' => 'kabupaten']);

        // Mock Midtrans API
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'fake-snap-token'
            ], 201)
        ]);

        $response = $this->actingAs($user)->post('/kabupaten/iuran', [
            'jumlah' => 1000000,
            'deskripsi' => 'Iuran Januari 2025'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'snap_token',
            'order_id',
            'transaction'
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'gross_amount' => 1000000,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function kabupaten_can_view_own_transactions()
    {
        $user = User::factory()->create(['role' => 'kabupaten']);
        $transaction = Transaction::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/kabupaten/iuran');

        $response->assertStatus(200);
        $response->assertSee($transaction->order_id);
    }

    /** @test */
    public function kabupaten_cannot_view_other_transactions()
    {
        $user1 = User::factory()->create(['role' => 'kabupaten']);
        $user2 = User::factory()->create(['role' => 'kabupaten']);
        
        $transaction = Transaction::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->get("/kabupaten/iuran/{$transaction->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_all_transactions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'kabupaten']);
        
        $transaction = Transaction::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee($transaction->order_id);
    }
}
```

### Webhook Tests

**File:** `tests/Feature/WebhookTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentSuccessNotification;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_processes_settlement_notification()
    {
        Mail::fake();

        $user = User::factory()->create(['role' => 'kabupaten']);
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'order_id' => 'ORDER-TEST-123',
            'gross_amount' => 100000,
            'status' => 'pending'
        ]);

        $serverKey = config('midtrans.server_key');
        $signature = hash('sha512', 'ORDER-TEST-123' . '200' . '100000.00' . $serverKey);

        $payload = [
            'order_id' => 'ORDER-TEST-123',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'transaction_id' => 'TRX-MIDTRANS-123',
            'payment_type' => 'credit_card',
            'settlement_time' => now()->toDateTimeString()
        ];

        $response = $this->postJson('/user/payment/callback', $payload);

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('settlement', $transaction->status);
        $this->assertNotNull($transaction->settlement_time);

        // Check iuran created
        $this->assertDatabaseHas('iurans', [
            'kabupaten_id' => $user->id,
            'jumlah' => 100000,
            'terverifikasi' => 'diterima'
        ]);

        // Check email sent
        Mail::assertSent(PaymentSuccessNotification::class);
    }

    /** @test */
    public function it_rejects_invalid_signature()
    {
        $transaction = Transaction::factory()->create([
            'order_id' => 'ORDER-TEST-123',
            'status' => 'pending'
        ]);

        $payload = [
            'order_id' => 'ORDER-TEST-123',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'signature_key' => 'invalid-signature',
            'transaction_status' => 'settlement'
        ];

        $response = $this->postJson('/user/payment/callback', $payload);

        $response->assertStatus(403);

        $transaction->refresh();
        $this->assertEquals('pending', $transaction->status);
    }
}
```

---

## 🌐 End-to-End Testing

### Setup Playwright (TypeScript)

**Install:**
```bash
npm install -D @playwright/test
npx playwright install
```

**Configuration:** `playwright.config.ts`
```typescript
import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  timeout: 30000,
  use: {
    baseURL: 'http://localhost:8000',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },
  projects: [
    {
      name: 'chromium',
      use: { browserName: 'chromium' },
    },
  ],
});
```

### E2E Test Example

**File:** `tests/e2e/payment-flow.spec.ts`

```typescript
import { test, expect } from '@playwright/test';

test.describe('Payment Flow', () => {
  test('kabupaten can complete payment', async ({ page }) => {
    // Login
    await page.goto('/login');
    await page.fill('input[name="email"]', 'kabupaten@example.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Wait for dashboard
    await expect(page).toHaveURL(/.*kabupaten\/dashboard/);

    // Navigate to create payment
    await page.click('text=Bayar Iuran');
    await expect(page).toHaveURL(/.*kabupaten\/iuran\/create/);

    // Fill payment form
    await page.fill('input[name="jumlah"]', '1000000');
    await page.fill('textarea[name="deskripsi"]', 'Iuran Januari 2025');
    
    // Submit form
    await page.click('button:has-text("Bayar Sekarang")');

    // Wait for Midtrans popup (in real test, you'd interact with it)
    await page.waitForSelector('.midtrans-popup', { timeout: 5000 });

    // In sandbox, you can auto-complete payment
    // This is a simplified version
    await expect(page.locator('.payment-status')).toContainText('Pending');
  });

  test('admin can view transactions', async ({ page }) => {
    // Login as admin
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@example.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Navigate to dashboard
    await expect(page).toHaveURL(/.*admin\/dashboard/);

    // Check statistics visible
    await expect(page.locator('.stat-total-masuk')).toBeVisible();
    await expect(page.locator('.stat-jumlah-transaksi')).toBeVisible();

    // Check transactions table
    await expect(page.locator('table')).toBeVisible();
  });
});
```

**Run E2E Tests:**
```bash
npx playwright test
npx playwright test --headed  # With browser UI
npx playwright test --debug   # Debug mode
```

---

## 🔌 API Testing

### Using Postman/Insomnia

**Collection:** `tests/api/iuran-pgri.postman_collection.json`

```json
{
  "info": {
    "name": "Sistem Iuran PGRI API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Authentication",
      "item": [
        {
          "name": "Login",
          "request": {
            "method": "POST",
            "url": "{{base_url}}/login",
            "body": {
              "mode": "json",
              "raw": "{\n  \"email\": \"kabupaten@example.com\",\n  \"password\": \"password123\"\n}"
            }
          }
        }
      ]
    },
    {
      "name": "Transactions",
      "item": [
        {
          "name": "Create Transaction",
          "request": {
            "method": "POST",
            "url": "{{base_url}}/kabupaten/iuran",
            "header": [
              {
                "key": "X-CSRF-TOKEN",
                "value": "{{csrf_token}}"
              }
            ],
            "body": {
              "mode": "json",
              "raw": "{\n  \"jumlah\": 1000000,\n  \"deskripsi\": \"Iuran Januari 2025\"\n}"
            }
          }
        }
      ]
    }
  ]
}
```

### Using PHPUnit for API Tests

**File:** `tests/Api/TransactionApiTest.php`

```php
<?php

namespace Tests\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_validation_error_for_invalid_amount()
    {
        $user = User::factory()->create(['role' => 'kabupaten']);

        $response = $this->actingAs($user)->postJson('/kabupaten/iuran', [
            'jumlah' => -1000,
            'deskripsi' => 'Test'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['jumlah']);
    }

    /** @test */
    public function it_returns_correct_json_structure()
    {
        $user = User::factory()->create(['role' => 'kabupaten']);

        $response = $this->actingAs($user)->getJson('/kabupaten/iuran');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'transactions' => [
                'data' => [
                    '*' => [
                        'id',
                        'order_id',
                        'gross_amount',
                        'status',
                        'created_at'
                    ]
                ]
            ]
        ]);
    }
}
```

---

## 🔒 Security Testing

### OWASP Top 10 Tests

**File:** `tests/Security/SecurityTest.php`

```php
<?php

namespace Tests\Security;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_prevents_sql_injection()
    {
        $user = User::factory()->create(['role' => 'kabupaten']);

        $response = $this->actingAs($user)->getJson('/kabupaten/iuran?search=\' OR 1=1--');

        $response->assertStatus(200);
        // Should not return all records
    }

    /** @test */
    public function it_prevents_xss_attacks()
    {
        $user = User::factory()->create(['role' => 'kabupaten']);

        $response = $this->actingAs($user)->postJson('/kabupaten/iuran', [
            'jumlah' => 1000000,
            'deskripsi' => '<script>alert("XSS")</script>'
        ]);

        // Description should be escaped
        $this->assertDatabaseMissing('transactions', [
            'description' => '<script>alert("XSS")</script>'
        ]);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/kabupaten/iuran');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_enforces_authorization()
    {
        $kabupaten = User::factory()->create(['role' => 'kabupaten']);

        $response = $this->actingAs($kabupaten)->getJson('/admin/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function it_validates_csrf_token()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/kabupaten/iuran', [
            'jumlah' => 1000000,
            'deskripsi' => 'Test'
        ], [
            'X-CSRF-TOKEN' => 'invalid-token'
        ]);

        $response->assertStatus(419); // CSRF token mismatch
    }
}
```

---

## ⚡ Performance Testing

### Load Testing with Apache Bench

```bash
# Test homepage
ab -n 1000 -c 10 http://localhost:8000/

# Test dashboard (with authentication)
ab -n 100 -c 5 -C "laravel_session=your_session_token" http://localhost:8000/kabupaten/dashboard

# Results should show:
# - Requests per second > 100
# - Average response time < 200ms
# - No failed requests
```

### Database Query Performance

**File:** `tests/Performance/QueryPerformanceTest.php`

```php
<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class QueryPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dashboard_query_is_optimized()
    {
        // Create test data
        $user = User::factory()->create(['role' => 'kabupaten']);
        Transaction::factory()->count(100)->create(['user_id' => $user->id]);

        // Enable query log
        DB::enableQueryLog();

        $this->actingAs($user)->get('/kabupaten/dashboard');

        $queries = DB::getQueryLog();

        // Should use eager loading, max 5 queries
        $this->assertLessThan(5, count($queries));
    }
}
```

---

## ✅ Manual Testing Checklist

### Pre-Release Checklist

#### Authentication
- [ ] Login with valid credentials
- [ ] Login with invalid credentials
- [ ] Logout functionality
- [ ] Password reset flow
- [ ] Session timeout

#### Kabupaten Features
- [ ] View dashboard
- [ ] Create new payment
- [ ] View transaction list
- [ ] View transaction detail
- [ ] Continue pending payment
- [ ] Filter transactions
- [ ] Export data

#### Admin Features
- [ ] View admin dashboard
- [ ] View all transactions
- [ ] Generate monthly report
- [ ] Generate kabupaten report
- [ ] Manage notifications
- [ ] Export reports

#### Payment Flow
- [ ] Credit card payment
- [ ] Bank transfer payment
- [ ] E-wallet payment
- [ ] Retail outlet payment
- [ ] Webhook processing
- [ ] Email notifications

#### Security
- [ ] HTTPS enforced
- [ ] CSRF protection
- [ ] XSS prevention
- [ ] SQL injection prevention
- [ ] Authorization checks

#### Performance
- [ ] Page load time < 3s
- [ ] API response < 500ms
- [ ] No N+1 queries
- [ ] Assets optimized

---

## 📊 Test Data & Fixtures

### Database Factories

**File:** `database/factories/TransactionFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_id' => 'ORDER-' . time() . '-' . $this->faker->randomNumber(5),
            'transaction_id' => 'TRX-' . $this->faker->uuid(),
            'gross_amount' => $this->faker->numberBetween(100000, 10000000),
            'payment_type' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'gopay']),
            'payment_method' => $this->faker->randomElement(['visa', 'bca', 'gopay']),
            'status' => 'pending',
            'description' => $this->faker->sentence(),
            'transaction_time' => now(),
        ];
    }

    public function settlement()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'settlement',
            'settlement_time' => now(),
        ]);
    }
}
```

### Database Seeders

**File:** `database/seeders/TestDataSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Transaction;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        $admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'password' => bcrypt('password123')
        ]);

        // Create kabupaten users
        $kabupaten1 = User::factory()->create([
            'name' => 'Kabupaten Jakarta',
            'email' => 'jakarta@test.com',
            'role' => 'kabupaten',
            'anggota' => 150,
            'password' => bcrypt('password123')
        ]);

        // Create transactions
        Transaction::factory()->count(5)->settlement()->create([
            'user_id' => $kabupaten1->id
        ]);

        Transaction::factory()->count(2)->create([
            'user_id' => $kabupaten1->id,
            'status' => 'pending'
        ]);
    }
}
```

---

## 🔄 CI/CD Integration

### GitHub Actions Workflow

**File:** `.github/workflows/tests.yml`

```yaml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  tests:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: testing
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
    - uses: actions/checkout@v3

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
        extensions: mbstring, xml, ctype, json, mysql
        coverage: xdebug

    - name: Install Dependencies
      run: composer install --prefer-dist --no-progress

    - name: Copy .env
      run: php -r "file_exists('.env') || copy('.env.testing', '.env');"

    - name: Generate key
      run: php artisan key:generate

    - name: Run Migrations
      run: php artisan migrate --force
      env:
        DB_CONNECTION: mysql
        DB_HOST: 127.0.0.1
        DB_PORT: 3306
        DB_DATABASE: testing
        DB_USERNAME: root
        DB_PASSWORD: password

    - name: Run Tests
      run: php artisan test --coverage --min=80
      env:
        DB_CONNECTION: mysql
        DB_HOST: 127.0.0.1
        DB_PORT: 3306
        DB_DATABASE: testing
        DB_USERNAME: root
        DB_PASSWORD: password

    - name: Upload Coverage
      uses: codecov/codecov-action@v3
      with:
        files: ./coverage.xml
```

---

## 📝 Test Reports

### Generate Coverage Report

```bash
# HTML coverage report
php artisan test --coverage-html coverage

# Open in browser
open coverage/index.html

# Text coverage report
php artisan test --coverage-text

# Minimum coverage threshold
php artisan test --coverage --min=80
```

---

## 🎯 Testing Best Practices

1. **AAA Pattern** - Arrange, Act, Assert
2. **One assertion per test** (when possible)
3. **Descriptive test names** - `it_does_something_when_condition`
4. **Test edge cases** - null, empty, negative values
5. **Mock external services** - Don't hit real APIs in tests
6. **Use factories** - Don't create models manually
7. **Clean up after tests** - Use RefreshDatabase
8. **Fast tests** - Unit tests should run in milliseconds
9. **Independent tests** - No test should depend on another
10. **Continuous testing** - Run tests on every commit

---

**Last Updated:** 29 Desember 2025  
**Version:** 1.0  
**Status:** ✅ Production Ready
