<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResponse extends Model
{
    use HasFactory;

    protected $fillable =['ticket_id', 'user_id', 'message', 'is_auto_reply'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // User yang mengetik balasan
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
