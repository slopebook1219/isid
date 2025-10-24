<?php

namespace App\Http\Controllers;

use App\Models\Game;

class AnswerController extends Controller
{
    public function show($game_id, $question_id)
{
    $game = Game::with('questions')->findOrFail($game_id);
    $question = $game->questions()->findOrFail($question_id);

    return view('answers.show', compact('game', 'question'));
}

}