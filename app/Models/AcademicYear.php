<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean', // Agar otomatis jadi true/false
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function classMembers()
{
    return $this->hasMany(ClassMember::class);
}

}