<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;
    
    protected $fillable = ['category_id', 'question', 'is_active'];
    
    /**
     * Relasi ke Category
     */
    public function category()
    {
        return $this->belongsTo(AssessmentCategory::class, 'category_id');
    }
}