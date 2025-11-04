<x-app-layout>
    <div>
    セットアップページです
    {{ $room->name }}を作成しました。
</div>
<form method="POST" action="{{ route('rooms.store_teams', $room) }}">
    @csrf
    @method('PUT')

    <h3>チーム名を入力してください（{{ $room->total_teams }} チーム）</h3>

    @for ($i = 0; $i < $room->total_teams; $i++)
        <div style="margin-bottom: 10px;">
            <label>チーム {{ $i + 1 }}:</label>
            <input type="text" name="teams[]" required>
        </div>
    @endfor

    <button type="submit">チームを登録</button>
</form>
</x-app-layout>