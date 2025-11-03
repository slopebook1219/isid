<?php
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnswerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //質問に関するルート
    Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
    
    // JSON API ルート
    Route::prefix('api')->middleware('api')->group(function () {
        Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
        Route::get('/questions/{question}', [QuestionController::class, 'show'])
             ->name('questions.show');
        Route::put('/questions/{question}', [QuestionController::class, 'update'])
             ->name('questions.update');
        Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])
             ->name('questions.destroy');
    });
        //ルームに関するルート
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');

     // チーム名設定機能のルートを追加
    Route::get('/rooms/{room}/setup', [RoomController::class, 'setup'])->name('rooms.setup');
    Route::put('/rooms/{room}/setup', [RoomController::class, 'storeTeams'])->name('rooms.store_teams');

    // ゲームに関するルート
    Route::get('/games/create', [GameController::class, 'create'])->name('games.create');
    Route::post('/games/confirm', [GameController::class, 'confirm'])->name('games.confirm');
    Route::post('/games/start', [GameController::class, 'start'])->name('games.start');
    Route::get('/games/{game_id}/play/{question_id}', [GameController::class, 'play'])->name('games.play');

    Route::get('/games/{game_id}/questions/{question_id}/play', [GameController::class, 'play'])->name('games.play');
    Route::get('/games/{game}/result', [GameController::class, 'result'])->name('games.result');
});

Route::get('/games/{game_id}/questions/{question_id}/answer', [AnswerController::class, 'show'])->name('answers.show');
Route::post('/answers/store', [AnswerController::class, 'store'])->name('answers.store');

require __DIR__.'/auth.php';
