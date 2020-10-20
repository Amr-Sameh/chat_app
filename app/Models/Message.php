<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        "content",
        "sender_id",
        "receiver_id",
    ];

    protected $appends = [
        "human_date"
    ];

    public function sender()
    {
        return $this->belongsTo(User::class,'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class,'receiver_id');
    }

    public function getHumanDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
