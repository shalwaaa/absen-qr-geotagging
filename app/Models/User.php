<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nip_nis',
        'classroom_id',
        'is_piket', // TAMBAHKAN INI!
        'status',
        'is_headmaster',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_piket' => 'boolean', // TAMBAHKAN CASTING UNTUK BOOLEAN
            'is_headmaster' => 'boolean',
        ];
    }

    // --- RELASI ---

    // 1. Relasi ke Kelas Saat Ini (Master Data)
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // 2. Relasi ke History Kelas (Folder Tahun Ajar)
    public function classMembers()
    {
        return $this->hasMany(ClassMember::class, 'student_id');
    }
}