<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Tambah role Headmaster
        // Catatan: Karena enum sulit diubah via migration biasa di beberapa DB, 
        // kita asumsikan kolom role bisa menerima string atau kita modify kolomnya.
        // Cara aman raw SQL:
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student', 'headmaster') DEFAULT 'student'");

        // 2. Bikin classroom_id jadi nullable (Karena Guru tidak punya kelas induk)
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->change();
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
