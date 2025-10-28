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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tugas');
            $table->enum('jenis_tugas', ['tugas rutin', 'tugas tambahan'])->default('tugas rutin');
            $table->enum('status', ['belum', 'sedang berlangsung', 'selesai'])->default('belum');
            $table->date('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
