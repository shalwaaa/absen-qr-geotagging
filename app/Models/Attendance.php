<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'meeting_id', 'student_id', 'status', 
        'latitude_student', 'longitude_student', 'distance_meters', 'scan_time'
    ];

    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function meeting() { return $this->belongsTo(Meeting::class); }
}