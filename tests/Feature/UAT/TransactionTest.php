<?php

namespace Tests\Feature\UAT;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_create_transaction()
    {
        // Mock Midtrans Snap untuk mencegah hit ke API Midtrans sungguhan saat testing
        Mockery::mock('alias:Midtrans\Snap')
            ->shouldReceive('getSnapToken')
            ->andReturn('mocked-snap-token');

        // Buat user kabupaten yang aktif
        $user = User::factory()->create([
            'role' => 'kabupaten',
            'status' => 'aktif'
        ]);

        // Coba hit endpoint pembuatan transaksi
        $response = $this->actingAs($user)->postJson('/kabupaten/transaction/create', [
            'amount' => 500000,
            'description' => 'Iuran Bulan Ini',
            'bulan_pembayaran' => '2024-05',
            'payment_method' => 'virtual_account'
        ]);

        // Pastikan sukses dan mereturn token
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'snap_token' => 'mocked-snap-token',
            ]);

        // Pastikan data tersimpan di database (dengan tambahan biaya admin VA Rp 4.000)
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'gross_amount' => 504000,
            'bulan_pembayaran' => '2024-05',
            'status' => 'pending',
        ]);
    }

    public function test_inactive_user_cannot_create_transaction()
    {
        // Buat user kabupaten yang NON-AKTIF
        $user = User::factory()->create([
            'role' => 'kabupaten',
            'status' => 'nonaktif'
        ]);

        $response = $this->actingAs($user)->postJson('/kabupaten/transaction/create', [
            'amount' => 500000,
            'description' => 'Iuran Bulan Ini',
            'bulan_pembayaran' => '2024-05',
            'payment_method' => 'virtual_account'
        ]);

        // Pastikan ditolak dengan status 403 Forbidden
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator untuk mengaktifkan akun.',
            ]);
    }

    public function test_user_cannot_create_double_pending_transaction()
    {
        $user = User::factory()->create([
            'role' => 'kabupaten',
            'status' => 'aktif'
        ]);

        // Buat transaksi yang sudah ada (status masih PENDING) di bulan yang sama
        Transaction::create([
            'user_id' => $user->id,
            'order_id' => 'TRX-' . $user->id . '-12345',
            'gross_amount' => 500000,
            'status' => 'pending',
            'bulan_pembayaran' => '2024-05'
        ]);

        // Coba buat lagi untuk bulan yang sama
        $response = $this->actingAs($user)->postJson('/kabupaten/transaction/create', [
            'amount' => 500000,
            'description' => 'Iuran Bulan Ini',
            'bulan_pembayaran' => '2024-05',
            'payment_method' => 'qris'
        ]);

        // Pastikan ditolak karena dicegah oleh sistem (422 Unprocessable Entity)
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
