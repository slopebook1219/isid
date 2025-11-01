<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            司会者画面 - 第{{ $questionNumber }}問
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- 問題文 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">問題</h3>
                    <p class="text-xl">{{ $question->text }}</p>
                    @if($question->unit)
                        <p class="text-sm text-gray-500 mt-2">単位: {{ $question->unit }}</p>
                    @endif
                </div>
            </div>

            {{-- 回答一覧 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-900">回答一覧</h3>
                        <button 
                            id="showResultsBtn" 
                            class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded"
                            onclick="showResults()">
                            結果を投影画面に表示
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        チーム名
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        回答値
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        回答時刻
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="answersTableBody" class="bg-white divide-y divide-gray-200">
                                {{-- ポーリングで動的に更新される --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ナビゲーション --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex justify-between">
                    <div>
                        @if ($nextQuestionId)
                            <a href="{{ route('games.host', ['game_id' => $game->id, 'question_id' => $nextQuestionId]) }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                次の問題へ
                            </a>
                        @else
                            <a href="{{ route('games.result', ['game' => $game->id]) }}" 
                               class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                結果を見る
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let projectionWindow = null;
        const gameId = {{ $game->id }};
        const questionId = {{ $question->id }};
        const answersUrl = '{{ route("games.answers", ["game_id" => $game->id, "question_id" => $question->id]) }}';
        const projectionStateUrl = '{{ route("games.update-projection-state", ["game_id" => $game->id, "question_id" => $question->id]) }}';
        const projectionUrl = '{{ route("games.projection", ["game_id" => $game->id, "question_id" => $question->id]) }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // 投影画面を別ウィンドウで開く
        function openProjectionWindow() {
            if (!projectionWindow || projectionWindow.closed) {
                projectionWindow = window.open(
                    projectionUrl,
                    'projection',
                    'width=' + screen.width + ',height=' + screen.height + ',fullscreen=yes'
                );
            } else {
                projectionWindow.focus();
            }
        }

        // ページ読み込み時に投影画面を開く
        window.addEventListener('load', () => {
            openProjectionWindow();
        });

        // 結果表示ボタン
        async function showResults() {
            try {
                const response = await fetch(projectionStateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ state: 'results' }),
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log('投影画面の状態を更新しました:', data);
                }
            } catch (error) {
                console.error('エラー:', error);
            }
        }

        // 回答一覧を取得して表示
        async function fetchAnswers() {
            try {
                const response = await fetch(answersUrl);
                const data = await response.json();
                
                const tbody = document.getElementById('answersTableBody');
                tbody.innerHTML = '';

                if (data.answers && data.answers.length > 0) {
                    data.answers.forEach(answer => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${answer.team_name}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${answer.answer_value}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${answer.created_at}
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                まだ回答がありません
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('回答の取得に失敗しました:', error);
            }
        }

        // 初回読み込み
        fetchAnswers();

        // 3秒ごとにポーリング
        setInterval(fetchAnswers, 3000);
    </script>
</x-app-layout>

