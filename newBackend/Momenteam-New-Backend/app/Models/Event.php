<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'memberId',
        'title',
        'description',
        'location',
        'date',
        'status'
    ];

    protected $hidden = [
        'memberId',
    ];

    // Removed the incorrect user() method

    public function users()
    {
        return $this->belongsToMany(User::class, 'event_user');
    }
}
    