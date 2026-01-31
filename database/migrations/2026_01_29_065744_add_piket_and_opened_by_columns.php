<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Tambah status Piket di tabel Users
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_piket')->default(false)->after('role'); // True = Guru Piket
        });

        // 2. Tambah info siapa yang buka sesi di tabel Meetings
        Schema::table('meetings', function (Blueprint $table) {
            // Menyimpan ID Guru yang memencet tombol "Buka Kelas"
            $table->foreignId('opened_by')->nullable()->constrained('users')->onDelete('set null')->after('qr_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
