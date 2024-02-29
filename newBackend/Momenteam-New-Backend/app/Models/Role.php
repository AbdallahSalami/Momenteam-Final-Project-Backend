<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'confirm',
        'private',
    ];

    public function getConfirmAttribute($value)
    {
        return $value ? 'true' : 'false';
    }

    /**
     * Get the private attribute as a string.
     *
     * @param  bool  $value
     * @return string
     */
    public function getPrivateAttribute($value)
    {
        return $value ? 'true' : 'false';
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}