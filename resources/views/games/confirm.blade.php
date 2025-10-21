<h2>この内容でゲームを始めますか？</h2>

<p><strong>ルーム名：</strong> {{ $room->name }}</p>

<h3>選択した問題</h3>
<ul>
@foreach($questions as $question)
    <li>{{ $question->text }}</li>
@endforeach
</ul>

<form method="POST" action="{{ route('games.start') }}">
    @csrf
    <input type="hidden" name="room_id" value="{{ $room->id }}">
    @foreach($questions as $question)
        <input type="hidden" name="question_ids[]" value="{{ $question->id }}">
    @endforeach

    <button type="submit">スタート</button>
</form>
