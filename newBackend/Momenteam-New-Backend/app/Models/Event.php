<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'memberId',
        'title',
        'description',
        'location',
        'date',
        'status',
        'image'
    ];

    protected $hidden = [
        'memberId',
    ];

    // Ensure the date attribute is cast to a datetime type
    protected $casts = [
        'date' => 'datetime',
    ];

    // Accessor for the date attribute
    public function getDateAttribute($value)
    {
        $date = Carbon::parse($value);
        // Log the original date as a string
        Log::info('Original Date: ' . $date->toDateTimeString());
        $dateInBeirut = $date->timezone('Asia/Beirut');
        // Log the date in Beirut as a string
        Log::info('Date in Beirut: ' . $dateInBeirut->toDateTimeString());
        return $dateInBeirut->format('Y-m-d H:i:s');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'event_user');
    }
}
