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
   
}
