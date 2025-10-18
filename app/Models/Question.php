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
}
