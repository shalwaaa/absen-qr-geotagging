<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;
    protected $fillable =['evaluator_id', 'evaluatee_id', 'academic_year_id', 'period_month', 'assessment_date', 'general_notes'];

    // Relasi ke Guru (Penilai) menghubungkan objek dengan objek                                                                 contohnya objek Assessment dengan objek User (guru) yang menjadi evaluatornya. Dengan relasi ini, kita bisa dengan mudah mengambil data guru yang melakukan penilaian dari sebuah objek Assessment.
    public function evaluator() 
    { 
        return $this->belongsTo(User::class, 'evaluator_id'); 
    }
    
    // Relasi ke Siswa (Yang dinilai)
    public function evaluatee() 
    { 
        return $this->belongsTo(User::class, 'evaluatee_id'); 
    }
    
    // Relasi ke Detail Nilai 
    public function details() 
    { 
        return $this->hasMany(AssessmentDetail::class); 
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}