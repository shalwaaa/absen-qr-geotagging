<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;
    protected $fillable = [
    'name', 
    'latitude', 
    'longitude',
    'latitude2',
    'longitude2',
    'radius_meters', 
    'grade_level',         
    'homeroom_teacher_id' 
    ];
    
    // Relasi: 1 Kelas punya banyak Siswa
    public function students()
    {
        return $this->hasMany(User::class, 'classroom_id');
    }

    // Relasi Wali Kelas
    public function homeroomTeacher()
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    // Relasi Anggota Rombel (History)
    public function classMembers()
    {
        return $this->hasMany(ClassMember::class);
    }
}