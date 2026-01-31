<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;
    
    protected $fillable = ['student_id', 'classroom_id', 'type', 'start_date', 'end_date', 'reason', 'attachment', 'status', 'notes'];

    public function student() { return $this->belongsTo(User::class, 'student_id'); }
}