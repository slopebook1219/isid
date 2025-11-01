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
                            type="button"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 border-2 border-emerald-700 hover:border-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
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
                <div class="p-6 flex justify-between items-center">
                    <div>
                        @if ($prevQuestionId)
                            <a href="{{ route('games.host', ['game_id' => $game->id, 'question_id' => $prevQuestionId]) }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded"
                               onclick="return confirm('本当に前の問題に戻りますか？');">
                                ← 前の問題へ
                            </a>
                        @endif
                    </div>
                    <div>
                        @if ($nextQuestionId)
                            <a href="{{ route('games.host', ['game_id' => $game->id, 'question_id' => $nextQuestionId]) }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
                               onclick="return confirm('次の問題に進みますか？\n\nこの操作により、投影画面も次の問題のQRコード表示に切り替わります。');">
                                次の問題へ →
                            </a>
                        @else
                            <a href="{{ route('games.result', ['game' => $game->id]) }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
                               onclick="return confirm('結果画面に移動しますか？');">
                                結果を見る →
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
        const allTeams = @json($teams->values());

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
            if (!confirm('投影画面に結果を表示しますか？\n\nこの操作により、投影画面が結果表示モードに切り替わります。')) {
                return;
            }

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
                alert('エラーが発生しました。もう一度お試しください。');
            }
        }

        async function fetchAnswers() {
            try {
                const response = await fetch(answersUrl);
                const data = await response.json();
                
                const tbody = document.getElementById('answersTableBody');
                tbody.innerHTML = '';

                const answersMap = {};
                if (data.answers && data.answers.length > 0) {
                    data.answers.forEach(answer => {
                        answersMap[answer.team_id] = answer;
                    });
                }

                const answerValues = Object.values(answersMap).map(a => parseFloat(a.answer_value));
                let maxValue = null, minValue = null, medianValues = [];
                
                if (answerValues.length > 0) {
                    maxValue = Math.max(...answerValues);
                    minValue = Math.min(...answerValues);
                    
                    const sorted = [...answerValues].sort((a, b) => a - b);
                    if (sorted.length % 2 === 0) {
                        const mid = sorted.length / 2;
                        medianValues = [sorted[mid - 1], sorted[mid]];
                    } else {
                        const mid = Math.floor(sorted.length / 2);
                        medianValues = [sorted[mid]];
                    }
                }

                allTeams.forEach(team => {
                    const answer = answersMap[team.id];
                    const row = document.createElement('tr');
                    
                    let valueCell = '';
                    let labels = [];
                    let rowClass = '';
                    
                    if (answer) {
                        const value = parseFloat(answer.answer_value);
                        const intValue = Math.round(value);
                        
                        if (maxValue !== null && Math.abs(value - maxValue) < 0.0001) {
                            labels.push('<span style="padding: 4px 8px; background-color: #fee2e2; color: #991b1b; font-size: 0.75rem; font-weight: 600; border-radius: 4px;">最大値</span>');
                        }
                        if (minValue !== null && Math.abs(value - minValue) < 0.0001) {
                            labels.push('<span style="padding: 4px 8px; background-color: #dbeafe; color: #1e40af; font-size: 0.75rem; font-weight: 600; border-radius: 4px;">最小値</span>');
                        }
                        if (medianValues.length > 0 && medianValues.some(mv => Math.abs(value - mv) < 0.0001)) {
                            labels.push('<span style="padding: 6px 12px; background-color: #ffffff; color: #000000; font-size: 0.875rem; font-weight: 700; border-radius: 6px; border: 2px solid #000000; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">🎯 中央値</span>');
                            rowClass = 'bg-emerald-50 border-l-4 border-emerald-500';
                        }
                        
                        valueCell = `<span class="font-semibold">${intValue}</span>`;
                    } else {
                        valueCell = '<span class="text-gray-400">未回答</span>';
                    }
                    
                    row.className = rowClass;
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${team.name}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${valueCell}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">${labels.join('')}</div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error('回答の取得に失敗しました:', error);
            }
        }

        fetchAnswers();
        setInterval(fetchAnswers, 3000);
    </script>
</x-app-layout>

