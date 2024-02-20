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

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'event_user', 'event_id', 'user_id');
    }
}
