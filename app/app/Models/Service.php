<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use hasFactory;

    protected $fillable = ['name', 'type', 'base_url', 'is_active'];
    protected $casts = [
        'config' => 'array',
    ];

    public function messageTargets()
    {
        return $this->hasMany(MessageTarget::class);
    }
}
