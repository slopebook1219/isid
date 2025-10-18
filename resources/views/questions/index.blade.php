<div>
    ここが問題一覧ページです。

    <a href="{{ route('questions.create') }}" 
            class="inline-block bg-indigo-600 text-black font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 ease-in-out">
            問題作成はこちら
    </a>
     <ul>
        @foreach($questions as $question)
            <li class="border p-2 mb-2 rounded">
                <strong>{{ $question->text }}</strong> (単位: {{ $question->unit }})
            </li>
        @endforeach
    </ul>
</div>