<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentDetail extends Model
{
    use HasFactory;
    
    protected $fillable = ['assessment_id', 'question_id', 'score']; 
    
    /**
     * Relasi ke Question
     */
    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_id');
    }
}