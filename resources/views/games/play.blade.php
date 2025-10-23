<div class="container">
    <h2>第 {{ $questionNumber }} 問</h2>
    <p>{{ $question->text }}</p>

    @if ($nextQuestionId)
        <a href="{{ route('games.play', ['game_id' => $game->id, 'question_id' => $nextQuestionId]) }}" class="btn btn-primary">
            次の問題へ
        </a>
    @else
        <p>全ての問題が終了しました！</p>
        <a href="{{ route('dashboard') }}" class="btn btn-success">戻る</a>
    @endif
</div>
