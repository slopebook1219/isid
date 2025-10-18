<div>
    ルーム作成ページです。
</div>
<form method="post" action="{{ route('rooms.store') }}">
    @csrf
    <div>
        <label for="name">部屋名</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div>
        <label for="total_teams">チーム数</label>
        <input type="number" id="total_teams" name="total_teams" required>
    </div>
    <div>
        <button type="submit"
            class="inline-block bg-indigo-600 text-black font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 ease-in-out">
            決定
        </button>
    </div>
</form>