<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomQuestion extends Model
{
    protected $table = 'room_question';
    
    public $timestamps = true; 

    protected $fillable = [
        'room_id',
        'question_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
