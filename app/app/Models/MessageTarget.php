<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTarget extends Model
{
    protected $fillable = [
        'message_id', 'service_id', 'recipient', 'status', 'provider_response'
    ];

    protected $casts = [
        'provider_response' => 'array',
        'metadata' => 'array',
    ];
    
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
