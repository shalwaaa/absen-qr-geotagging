<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentCategory extends Model
{
    use HasFactory;
    protected $fillable =['name', 'description', 'is_active'];

    // Satu Kategori punya Banyak Pertanyaan
    public function questions()
    {
        return $this->hasMany(AssessmentQuestion::class, 'category_id');
    }
}