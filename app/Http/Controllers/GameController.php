<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller; 
use App\Models\Room;
use App\Models\Game;

class GameController extends Controller
{
    public function create()
    {
        $questions = Auth::user()->questions()->latest()->get();
        $rooms = Auth::user()->rooms()->latest()->get();
        return view('games.create', compact('rooms', 'questions'));
    }
    public function confirm(Request $request)
    {
    $room_id = $request->room_id;
    $question_ids = $request->question_ids;

    $room = Room::findOrFail($room_id);
    $questions = Question::whereIn('id', $question_ids)->get();

    return view('games.confirm', compact('room', 'questions'));
    }

    public function start(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $game = Game::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
        ]);

        $game->questions()->attach($request->question_ids);

        $firstQuestionId = $request->question_ids[0];
        return redirect()->route('games.play', ['game_id' => $game->id, 'question_id' => $firstQuestionId])->with('success', 'ゲームを作成しました。');
    }

    public function play($game_id, $question_id)
    {
        $game = Game::with('questions')->findOrFail($game_id);
        $question = $game->questions()->findOrFail($question_id);

        $questionIds = $game->questions->pluck('id')->toArray();
        $currentIndex = array_search($question_id, $questionIds);
        $nextQuestionId = $questionIds[$currentIndex + 1] ?? null;
        $questionNumber = $currentIndex + 1;

        return view('games.play', compact('game', 'question', 'nextQuestionId', 'questionNumber'));
    }



}
