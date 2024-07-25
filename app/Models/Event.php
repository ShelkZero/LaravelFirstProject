<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'poster', 'event_date', 'venue_id'];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
