<?php

namespace App\Http\Controllers;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Display Questions page.
     */
    public function index(): View
    {
        // $questions = Auth::user()->questions()->latest()->get();
        return view('questions.index');
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
}
