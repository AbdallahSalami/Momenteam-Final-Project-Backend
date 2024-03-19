<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'eventId', // Add this line
        'title',
        'description',
        'date',
        'secretaryId',
        'secretaryFirstDate',
        'managerId',
        'managerApprovelDate',
        'secretarySecondDate',
        'qrCode',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function event() // Add this method
    {
        return $this->belongsTo(Event::class, 'eventId');
    }
// In your Certificate model
public function secretary()
{
    return $this->belongsTo(MemberDetail::class, 'secretaryId');
}

public function manager()
{
    return $this->belongsTo(MemberDetail::class, 'managerId');
}

}
