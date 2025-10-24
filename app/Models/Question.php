<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['user_id', 'text', 'unit'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //中間テーブルを使用した多対多のリレーション
    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_questions')
                    ->withTimestamps();
    }
}
