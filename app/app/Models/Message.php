<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'content', 'metadata', 'sent_at'];

    protected $casts = [
        'metadata' => 'array', // convierte JSON a array automáticamente
        'sent_at' => 'datetime',
    ];

    public function targets()
    {
        return $this->hasMany(MessageTarget::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}