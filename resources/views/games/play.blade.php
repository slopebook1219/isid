<x-app-layout>
    <div class="container text-center mt-5">
    <h2>第{{ $questionNumber }}問</h2>

    <div class="card mx-auto mt-4" style="max-width: 600px;">
        <div class="card-body">
            <h4 class="card-text">{{ $question->text }}</h4>
        </div>
    </div>

    {{-- 回答用QRコード --}}
    <div class="mt-4">
        <p>下のQRコードから回答ページへアクセスできます</p>
        <div>
            {!! QrCode::size(200)->generate(route('answers.show', ['game_id' => $game->id, 'question_id' => $question->id])) !!}
        </div>
    </div>
    <div>
        PCで画面遷移可能な開発用のリンクを作成
        <a href="{{ route('answers.show', ['game_id' => $game->id, 'question_id' => $question->id]) }}">
            回答ページへ（開発用リンク）
        </a>
    </div>

    {{-- ナビゲーション --}}
    <div class="mt-4">
        @if ($nextQuestionId)
            <a href="{{ route('games.play', ['game_id' => $game->id, 'question_id' => $nextQuestionId]) }}" class="btn btn-primary">
                次の問題へ
            </a>
        @else
            <a href="{{ route('games.result', ['game' => $game->id]) }}" class="btn btn-success">
                結果を見る
            </a>
        @endif
    </div>
</div>
</x-app-layout>