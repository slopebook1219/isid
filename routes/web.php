<?php
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
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
});

require __DIR__.'/auth.php';
