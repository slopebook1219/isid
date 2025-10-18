<?php

namespace App\Http\Controllers;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * Display Questions page.
     */
    public function index(): View
    {
        return view('questions.index');
    }

    /**
     * Show the form for creating a new question.
     */
    public function create(): View
    {
        return view('questions.create');
    }
}
