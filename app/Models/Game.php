<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'user_id',
        'room_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'game_questions')
                    ->withTimestamps();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}