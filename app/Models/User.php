<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use  Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
       'lastname',
        'phone',
        'address',
        'poste',
        'role',
        'password',
    ];
    public function image()
    {
        return $this->hasOne(Image::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     // Méthode pour obtenir l'identifiant JWT de l'utilisateur
     public function getJWTIdentifier()
     {
         return $this->getKey();
     }
 
     // Méthode pour obtenir les informations personnalisées du JWT
     public function getJWTCustomClaims()
     {
         return [];
     }
}
