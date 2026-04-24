<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable =[
        'ticket_code', 'user_id', 'operator_id', 'category',
        'subject', 'description', 'attachment', 'priority', 'status'
    ];

    // Pelapor
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Operator
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    // Obrolan
    public function responses()
    {
        return $this->hasMany(TicketResponse::class);
    }

    // Rating
    public function rating()
    {
        return $this->hasOne(SatisfactionRating::class);
    }
}
