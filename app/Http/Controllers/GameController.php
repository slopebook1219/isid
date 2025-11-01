<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller; 
use App\Models\Room;
use App\Models\Game;
use App\Models\Answer;
use Illuminate\Support\Facades\Session;

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
        //中間テーブルにデータを保存
        $game->questions()->attach($request->question_ids);

        $firstQuestionId = $request->question_ids[0];
        return redirect()->route('games.host', ['game_id' => $game->id, 'question_id' => $firstQuestionId])->with('success', 'ゲームを開始しました。');
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

    public function result($game_id)
    {
        $game = Game::with('questions')->findOrFail($game_id);

        return view('games.result', compact('game'));
    }

    public function host($game_id, $question_id)
    {
        $game = Game::with(['questions', 'room.teams'])->findOrFail($game_id);
        $question = $game->questions()->findOrFail($question_id);
        $questionIds = $game->questions->pluck('id')->toArray();
        $currentIndex = array_search($question_id, $questionIds);
        $nextQuestionId = $questionIds[$currentIndex + 1] ?? null;
        $prevQuestionId = ($currentIndex > 0) ? $questionIds[$currentIndex - 1] : null;
        $questionNumber = $currentIndex + 1;
        $teams = $game->room->teams->sortBy('name');

        // 投影画面の状態を初期化（QRコード表示）
        $projectionStateKey = "projection_state_{$game_id}_{$question_id}";
        if (!Session::has($projectionStateKey)) {
            Session::put($projectionStateKey, 'qr_code');
        }

        return view('games.host', compact('game', 'question', 'nextQuestionId', 'prevQuestionId', 'questionNumber', 'teams'));
    }

    public function projection($game_id, $question_id)
    {
        $game = Game::with('questions')->findOrFail($game_id);
        $question = $game->questions()->findOrFail($question_id);
        $questionIds = $game->questions->pluck('id')->toArray();
        $currentIndex = array_search($question_id, $questionIds);
        $questionNumber = $currentIndex + 1;

        return view('games.projection', compact('game', 'question', 'questionNumber'));
    }

    public function getAnswers($game_id, $question_id)
    {
        $answers = Answer::where('game_id', $game_id)
            ->where('question_id', $question_id)
            ->with('team')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'answers' => $answers->map(function ($answer) {
                return [
                    'id' => $answer->id,
                    'team_id' => $answer->team_id,
                    'team_name' => $answer->team->name,
                    'answer_value' => $answer->answer_value,
                    'created_at' => $answer->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    public function getStats($game_id, $question_id)
    {
        $answers = Answer::where('game_id', $game_id)
            ->where('question_id', $question_id)
            ->with('team')
            ->get();

        if ($answers->isEmpty()) {
            return response()->json([
                'max' => null,
                'min' => null,
                'median' => null,
                'max_team' => null,
                'min_team' => null,
                'median_team' => null,
            ]);
        }

        $values = $answers->pluck('answer_value')->toArray();
        $maxValue = max($values);
        $minValue = min($values);

        // 中央値計算
        sort($values);
        $count = count($values);
        if ($count % 2 === 0) {
            $medianValue = ($values[$count / 2 - 1] + $values[$count / 2]) / 2;
        } else {
            $medianValue = $values[intval($count / 2)];
        }

        // 該当チームを取得
        $maxTeam = $answers->firstWhere('answer_value', $maxValue)->team->name ?? null;
        $minTeam = $answers->firstWhere('answer_value', $minValue)->team->name ?? null;
        
        // 中央値に最も近い値のチームを取得
        $medianAnswer = $answers->sortBy(function ($answer) use ($medianValue) {
            return abs($answer->answer_value - $medianValue);
        })->first();
        $medianTeam = $medianAnswer ? $medianAnswer->team->name : null;

        return response()->json([
            'max' => $maxValue,
            'min' => $minValue,
            'median' => $medianValue,
            'max_team' => $maxTeam,
            'min_team' => $minTeam,
            'median_team' => $medianTeam,
        ]);
    }

    public function getProjectionState($game_id, $question_id)
    {
        $projectionStateKey = "projection_state_{$game_id}_{$question_id}";
        $state = Session::get($projectionStateKey, 'qr_code');

        return response()->json(['state' => $state]);
    }

    public function updateProjectionState(Request $request, $game_id, $question_id)
    {
        $request->validate([
            'state' => 'required|in:qr_code,results',
        ]);

        $projectionStateKey = "projection_state_{$game_id}_{$question_id}";
        Session::put($projectionStateKey, $request->state);

        return response()->json(['success' => true, 'state' => $request->state]);
    }
}
