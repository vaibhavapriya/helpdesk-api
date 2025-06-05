<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /** @use HasFactory<\Database\Factories\ProfileFactory> */
    use HasFactory;

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    protected static function booted()
    {
        static::deleting(function ($profile) {
            $profile->image()->delete();
        });
    }
}
