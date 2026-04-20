<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('order_id')->unique();
            $table->string('transaction_id')->nullable();
            $table->integer('gross_amount');
            $table->string('payment_type')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('status', ['pending', 'settlement', 'cancel', 'deny', 'expire', 'failure'])->default('pending');
            $table->string('snap_token')->nullable();
            $table->text('description')->nullable();
            // Format: YYYY-MM (e.g. 2025-12 for Desember 2025)
            $table->string('bulan_pembayaran', 7)->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
