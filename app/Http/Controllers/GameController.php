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

        return redirect()->route('games.create')->with('success', 'ゲームを作成しました。');
    }

}
