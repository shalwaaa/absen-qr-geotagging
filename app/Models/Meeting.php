<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    protected $fillable = ['schedule_id', 'date', 'topic', 'qr_token', 'is_active'];

    public function schedule() 
    { 
        return $this->belongsTo(Schedule::class); 
    }

    public function attendances() 
    { 
        return $this->hasMany(Attendance::class); 
    }
}