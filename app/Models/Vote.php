<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'question_id',
        'participant_id',
        'voted_by'
        // Add other vote-related fields as needed
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
