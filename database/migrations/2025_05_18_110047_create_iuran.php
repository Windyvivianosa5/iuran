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
        Schema::create('iurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_id')->contrained(
                table:'users',
                indexName: 'iuran_kabupaten_id'
            );
            $table->integer('jumlah');
            $table->string('bukti_transaksi');
            $table->dateTime('tanggal');
            $table->string('deskripsi');
            $table->enum('terverifikasi',['pending','ditolak','diterima'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iurans');
    }
};
