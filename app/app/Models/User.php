<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable {
    use HasFactory;

    protected $fillable = ['username', 'password', 'role_id'];

    protected $hidden = ['password'];

    public function role() {
        return $this->belongsTo(Role::class);
    }

    // Encriptar password automáticamente
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }
}

