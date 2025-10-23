<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject{
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

    /**
 * Get the identifier that will be stored in the subject claim of the JWT.
    */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    public function getAuthIdentifierName()
    {
        return 'username';
    }
}

