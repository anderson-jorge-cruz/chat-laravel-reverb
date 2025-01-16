<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    /** @use HasFactory<\Database\Factories\ChatMessageFactory> */
    use HasFactory;

    protected $guarded = [];

    public function SentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
