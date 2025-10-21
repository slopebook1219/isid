<form method="POST" action="{{ route('games.confirm') }}">
    @csrf
    <h3>Roomを選択</h3>
    <select name="room_id" required>
        @foreach($rooms as $room)
            <option value="{{ $room->id }}">{{ $room->name }}</option>
        @endforeach
    </select>

    <h3>Questionを選択</h3>
    @foreach($questions as $question)
        <label>
            <input type="checkbox" name="question_ids[]" value="{{ $question->id }}">
            {{ $question->text }}
        </label><br>
    @endforeach

    <button type="submit">ゲーム開始</button>
</form>
