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

    Schema::create('coas', function (Blueprint $table) {
        $table->id();
        $table->string('kd_akun')->unique(); // Contoh: 1101
        $table->string('nama_akun');           // Contoh: Kas Kecil
        $table->string('jenis_akun');           // Contoh: Aset, Kewajiban, Modal, Pendapatan, Beban
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coas');
    }
};
