<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function show($game_id, $question_id)
    {
        $game = Game::with(['questions', 'room.teams'])->findOrFail($game_id);
        $question = $game->questions()->findOrFail($question_id);
        $teams = $game->room->teams;
        return view('answers.show', compact('game', 'question', 'teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'question_id' => 'required|exists:questions,id',
            'team_id' => 'required|exists:teams,id',
            'answer_value' => 'required|numeric',
        ]);

        Answer::updateOrCreate(
            [
                'game_id' => $validated['game_id'],
                'question_id' => $validated['question_id'],
                'team_id' => $validated['team_id'],
            ],
            ['answer_value' => $validated['answer_value']]
        );

        return redirect()->back()->with('success', '回答を送信しました');
    }
}
