
<div class="container">
    <h2 class="text-2xl font-bold mb-4">回答ページ</h2>

    {{-- 問題文 --}}
    <div class="mb-6">
        <h3 class="text-xl font-semibold">問題：</h3>
        <p class="text-lg mt-2">{{ $question->text }}</p>
    </div>

    {{-- チーム選択フォーム --}}
    <form action="{{ route('answers.store') }}" method="POST">
        @csrf
        <input type="hidden" name="game_id" value="{{ $game->id }}">
        <input type="hidden" name="question_id" value="{{ $question->id }}">

        <div class="mb-4">
            <label for="team_id" class="block font-medium mb-1">チームを選択：</label>
            <select name="team_id" id="team_id" class="border border-gray-300 rounded px-3 py-2 w-full" required>
                <option value="">-- チームを選択 --</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="answer_value" class="block font-medium mb-1">あなたの回答（数値）:</label>
            <input type="number" step="0.01" name="answer_value" id="answer_value" class="border border-gray-300 rounded px-3 py-2 w-full" required>
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            回答を送信
        </button>
    </form>
</div>