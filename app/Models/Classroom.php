<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'latitude', 'longitude', 'radius_meters'];
    
    // Relasi: 1 Kelas punya banyak Siswa
    public function students()
    {
        return $this->hasMany(User::class, 'classroom_id');
    }
}