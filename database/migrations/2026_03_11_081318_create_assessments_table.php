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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade'); // Wali Kelas
            $table->foreignId('evaluatee_id')->constrained('users')->onDelete('cascade'); // Siswa
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade'); // Tahun Ajar
            
            $table->string('period_month'); //(Bulan-Tahun) untuk periode bulanan
            $table->date('assessment_date'); // Kapan dinilai
            $table->text('general_notes')->nullable();
            
            $table->timestamps();
            
            // Mencegah Wali kelas menilai siswa yang sama 2x di bulan yang sama
            $table->unique(['evaluatee_id', 'period_month', 'academic_year_id'], 'unique_monthly_assessment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
