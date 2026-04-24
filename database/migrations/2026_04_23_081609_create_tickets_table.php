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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique(); // Kode tiket unik, misal: TKT-20260423-XYZ

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pelapor (Siswa/Guru)
            $table->foreignId('operator_id')->nullable()->constrained('users')->onDelete('set null'); // Operator yang menangani

            $table->string('category'); // Misal: Gagal Scan, GPS Error, Lupa Absen
            $table->string('subject');
            $table->text('description');
            $table->string('attachment')->nullable(); // Foto screenshot error

            $table->enum('priority', ['low', 'mid', 'high'])->default('mid');
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');

            $table->timestamps(); // otomatis jadi patokan Response & Resolution Time

            // FITUR CERDAS: Full-Text Search Index untuk Anti-Duplikat
            $table->fullText(['subject', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
