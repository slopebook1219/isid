<?php

namespace App\Http\Controllers;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller; 
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    /**
     * Display Questions page.
     */
    public function index(): View
    {
        $questions = Auth::user()->questions()->latest()->get();
        return view('questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create(): View
    {
        return view('questions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'unit' => 'required|string',
        ]);

        Question::create([
            'user_id' => Auth::id(),
            'text' => $request->text,
            'unit' => $request->unit,
        ]);

        return redirect()->route('questions.index')->with('success', '質問を作成しました。');
    }

    /**
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Question $question): JsonResponse
    {
        return response()->json($question);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse
     */

    public function update(Request $request, Question $question): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'unit' => 'required|string',
        ]);

        $question->update($validated);
        return response()->json([
            'message' => '質問を更新しました。',
            'question' => $question->fresh(),
        ]);
    }

    /**
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Question $question): JsonResponse
    {
        $question->delete();
        return response()->json(['message' => '質問を削除しました。']);
    }
}
