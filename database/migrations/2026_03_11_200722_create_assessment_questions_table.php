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
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('assessment_categories')->onDelete('cascade');
            $table->string('question'); // Contoh: "Siswa selalu hadir tepat waktu"
            $table->timestamps();
        });

        // Sekalian kita ubah tabel assessment_details yang lama
        Schema::table('assessment_details', function (Blueprint $table) {
            // Hapus relasi ke kategori yang lama
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            
            // Tambahkan relasi ke pertanyaan yang baru
            $table->foreignId('question_id')->constrained('assessment_questions')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_questions');
    }
};
