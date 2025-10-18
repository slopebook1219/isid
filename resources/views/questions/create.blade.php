<div>
    ここが問題作成ページです。
</div>
<form method="post" action="{{ route('questions.store') }}">
    @csrf
    <div>
        <label for="text">問題文</label>
        <input type="text" id="text" name="text" required>
    </div>
    <div>
        <label for="unit">単位</label>
        <input type="text" id="" name="unit" required>
    </div>
    <div>
        <button type="submit"
            class="inline-block bg-indigo-600 text-black font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 ease-in-out">
            作成
        </button>
    </div>
</form>