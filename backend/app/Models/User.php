<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail; // Add this line

class User extends Authenticatable implements MustVerifyEmail // Add this line
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
        'emailVerification',
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
        return $this->belongsToMany(Event::class, 'event_user', 'user_id', 'event_id');
    }
}
