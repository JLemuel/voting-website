<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'room_name',
        'set_timer',
        'timer_seconds',
        'pick_random_participant',
        'num_questions',
        'question_level',
        'active',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function participants()
    {
        return $this->hasMany(RoomParticipant::class);
    }
}
