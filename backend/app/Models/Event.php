<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'title',
        'description',
        'date',
        'status'
    ];

    protected $hidden = [
        'userId',
    ];

    // Removed the incorrect user() method

    public function users()
    {
        return $this->belongsToMany(User::class, 'event_user');
    }
}
    