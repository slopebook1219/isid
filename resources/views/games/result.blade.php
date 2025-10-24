<div class="container mt-5 text-center">
    <h2>ゲーム結果</h2>
    <p>ゲームID：{{ $game->id }}</p>

    <div class="mt-4 text-start mx-auto" style="max-width: 600px;">
ゲーム終了画面です
    </div>

    <a href="{{ route('rooms.index') }}" class="btn btn-primary mt-4">部屋一覧へ戻る</a>
</div>