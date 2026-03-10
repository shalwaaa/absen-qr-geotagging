<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = ['teacher_id', 'subject_id', 'classroom_id', 'day', 'start_time', 'end_time', 'week_type'];

    public function teacher() { return $this->belongsTo(User::class, 'teacher_id'); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function classroom() { return $this->belongsTo(Classroom::class); }
}
