<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'username',
        'email',
        'password',
        'firstName',
        'secondName',
        'lastName',
        'highestDegree',
        'major',
        'educationalInstitution',
        'phoneNumber',
        'emailVerification', // Your custom column
        'roleId',
        'status',
    ];

    protected $hidden = [
        'password',
        'roleId', // Hide the roleId attribute by default
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'roleId');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_user');
    }
    

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return $this->emailVerification === 'verified';
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'emailVerification' => 'verified',
        ])->save();
    }
}
