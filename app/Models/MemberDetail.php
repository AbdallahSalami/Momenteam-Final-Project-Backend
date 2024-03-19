<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberDetail extends Model
{
    use HasFactory;
    protected $table = 'memberDetails'; // This line is important if the table name is not the plural form of the model name

    protected $fillable = [
        'userId',
        'statusDegree',
        'majorDegree',
        'year',
        'dateOfJoining',
        'location',
        'status',
        'image' // Add the image attribute to the fillable array
    ];

    protected $hidden = [
        'userId', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Accessor for the image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return null;
    }
}
