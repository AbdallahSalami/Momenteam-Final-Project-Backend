<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'memberId',
        'title',
        'description',
        'scientificAuditorId',
        'scientificAuditorApprovelDate',
        'linguisticCheckerId',
        'linguisticCheckerApprovelDate',
        'socialMediaId',
        'socialMediaApprovelDate',
        'status'
    ];

    public function member()
    {
        return $this->belongsTo(MemberDetail::class, 'memberId');
    }

    public function scientificAuditor()
    {
        return $this->belongsTo(MemberDetail::class, 'scientificAuditorId');
    }

    public function linguisticChecker()
    {
        return $this->belongsTo(MemberDetail::class, 'linguisticCheckerId');
    }

    public function socialMedia()
    {
        return $this->belongsTo(MemberDetail::class, 'socialMediaId');
    }
}
