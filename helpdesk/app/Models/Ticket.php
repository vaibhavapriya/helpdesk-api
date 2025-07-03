<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'priority', 'filetype', 'filelink',
        'status', 'department', 'requester_id', 'last_reply', 'last_replier',
    ];
    // protected $hidden = ['imaage', 'requester', 'replies']; 

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
    
    protected static function booted()
    {
        static::deleting(function ($profile) {
            $profile->image()->delete();
        });
    }

}
