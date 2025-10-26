<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="Modelo de usuario del sistema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="username", type="string", example="ale123"),
 *     @OA\Property(property="email", type="string", example="ale@example.com"),
 *     @OA\Property(property="role_id", type="integer", example=2, nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-25T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-10-25T12:34:56Z")
 * )
 */

class User extends Authenticatable implements JWTSubject
{
    use HasFactory; 

    protected $fillable = ['username', 'email', 'password', 'role_id'];
    protected $hidden = ['password', 'remember_token'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin'; // o según tu implementación
    }
}
