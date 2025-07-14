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
        Schema::create('ujians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('mapel_id')->constrained('mata_pelajarans')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('gurus')->onDelete('cascade');
            $table->dateTime('jadwal');
            $table->dateTime('waktu_selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_ujian');
    }
};
