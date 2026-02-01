<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassMember extends Model
{
    use HasFactory;
    
    protected $fillable = ['student_id', 'classroom_id', 'academic_year_id', 'is_active', 'attendance_percentage'];

    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function classroom() { return $this->belongsTo(Classroom::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function classMembers()
    {
        return $this->hasMany(ClassMember::class, 'student_id');
    }
}