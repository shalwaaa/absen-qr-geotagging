<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

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

    // 3. Relasi ke Jadwal Mengajar (Untuk Guru)
       public function schedules()
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    // 4. Relasi untuk Siswa melihat nilai mereka sendiri
    public function myAssessments()
    {
        return $this->hasMany(Assessment::class, 'evaluatee_id');
    }

    // 5. Relasi Gamifikasi
    public function ledgers() {
        return $this->hasMany(PointLedger::class)->orderBy('created_at', 'desc');
    }
    // 6. Relasi ke UserToken
    public function tokens() {
        return $this->hasMany(UserToken::class);
    }
}