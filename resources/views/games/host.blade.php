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
                <div class="p-6">
                    <div class="mb-4 text-center">
                        <p class="text-sm text-gray-600 font-medium">
                            📺 投影画面操作
                        </p>

                    </div>
                    
                    {{-- 反映中メッセージ --}}
                    <div id="projectionProgressContainer" class="mb-4" style="height: 24px; display: flex; align-items: center; justify-content: center;">
                        <p class="text-sm text-blue-600 text-center font-medium" style="visibility: hidden;">投影画面に反映中...</p>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div>
                            <button 
                                id="prevBtn"
                                type="button"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                                onclick="navigatePrevious()">
                                ← 前へ
                            </button>
                        </div>
                        <div>
                            <button 
                                id="nextBtn"
                                type="button"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                                onclick="navigateNext()">
                                次へ →
                            </button>
                        </div>
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
        const projectionStateGetUrl = '{{ route("games.projection-state", ["game_id" => $game->id, "question_id" => $question->id]) }}';
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

        // 状態の順序定義
        const stateOrder = [
            'qr_code',
            'result_max_team',
            'result_max_value',
            'result_min_team',
            'result_min_value',
            'result_median_team',
            'result_median_value'
        ];

        let currentState = 'qr_code';
        const hasNextQuestion = {{ $nextQuestionId ? 'true' : 'false' }};
        const hasPrevQuestion = {{ $prevQuestionId ? 'true' : 'false' }};

        // 現在の状態を取得
        async function getCurrentState() {
            try {
                const response = await fetch(projectionStateGetUrl);
                const data = await response.json();
                return data.state || 'qr_code';
            } catch (error) {
                return 'qr_code';
            }
        }

        // 反映中メッセージ管理
        let progressInterval = null;
        function showProgress() {
            const container = document.getElementById('projectionProgressContainer');
            const message = container.querySelector('p');
            message.style.visibility = 'visible';
        }

        function hideProgress() {
            const container = document.getElementById('projectionProgressContainer');
            const message = container.querySelector('p');
            message.style.visibility = 'hidden';
            if (progressInterval) {
                clearInterval(progressInterval);
                progressInterval = null;
            }
        }

        // 状態を更新
        async function updateState(newState) {
            try {
                showProgress();
                
                const response = await fetch(projectionStateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ state: newState }),
                });
                
                if (response.ok) {
                    currentState = newState;
                    updateButtonStates();
                    
                    // 投影画面への反映を確認
                    // 投影画面のポーリング間隔（3秒）を考慮して、1秒ごとに3回チェック
                    let checkCount = 0;
                    const maxChecks = 3;
                    
                    if (progressInterval) clearInterval(progressInterval);
                    
                    // 最初の1秒待機（投影画面の次のポーリングサイクルで取得できるように）
                    setTimeout(() => {
                        progressInterval = setInterval(async () => {
                            checkCount++;
                            
                            try {
                                const verifyResponse = await fetch(projectionStateGetUrl);
                                const verifyData = await verifyResponse.json();
                                
                                if (verifyData.state === newState) {
                                    hideProgress();
                                    clearInterval(progressInterval);
                                    progressInterval = null;
                                } else if (checkCount >= maxChecks) {
                                    hideProgress();
                                    clearInterval(progressInterval);
                                    progressInterval = null;
                                }
                            } catch (error) {
                                if (checkCount >= maxChecks) {
                                    hideProgress();
                                    clearInterval(progressInterval);
                                    progressInterval = null;
                                }
                            }
                        }, 1000); // 1秒ごとにチェック（投影画面のポーリング間隔3秒に合わせて）
                    }, 1000);
                } else {
                    hideProgress();
                }
            } catch (error) {
                console.error('エラー:', error);
                hideProgress();
            }
        }

        // ボタンの有効/無効を更新
        async function updateButtonStates() {
            const currentStateIndex = stateOrder.indexOf(currentState);
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            // 前へボタン：最初の状態でない、または前の問題がある場合
            if (currentStateIndex > 0 || hasPrevQuestion) {
                prevBtn.disabled = false;
            } else {
                prevBtn.disabled = true;
            }

            // 次へボタン：最後の状態でない、または次の問題がある場合
            if (currentStateIndex < stateOrder.length - 1 || hasNextQuestion) {
                nextBtn.disabled = false;
            } else {
                nextBtn.disabled = true;
            }
        }

        // 次へ
        async function navigateNext() {
            const currentStateIndex = stateOrder.indexOf(currentState);
            
            if (currentStateIndex < stateOrder.length - 1) {
                // 次の状態に進む
                const nextState = stateOrder[currentStateIndex + 1];
                await updateState(nextState);
            } else if (hasNextQuestion) {
                // 次の問題へ
                if (confirm('次の問題に進みますか？\n\nこの操作により、投影画面も次の問題のQRコード表示に切り替わります。')) {
                    window.location.href = '{{ $nextQuestionId ? route("games.host", ["game_id" => $game->id, "question_id" => $nextQuestionId]) : "#" }}';
                }
            } else {
                // 結果画面へ
                if (confirm('結果画面に移動しますか？\n\nこの操作により、投影画面が閉じられる可能性があります。')) {
                    window.location.href = '{{ route("games.result", ["game" => $game->id]) }}';
                }
            }
        }

        // 前へ
        async function navigatePrevious() {
            const currentStateIndex = stateOrder.indexOf(currentState);
            
            if (currentStateIndex > 0) {
                // 前の状態に戻る
                const prevState = stateOrder[currentStateIndex - 1];
                await updateState(prevState);
            } else if (hasPrevQuestion) {
                // 前の問題へ
                if (confirm('前の問題に戻りますか？\n\nこの操作により、投影画面も前の問題のQRコード表示に切り替わります。')) {
                    window.location.href = '{{ $prevQuestionId ? route("games.host", ["game_id" => $game->id, "question_id" => $prevQuestionId]) : "#" }}';
                }
            }
        }

        // 初期化
        window.addEventListener('load', async () => {
            currentState = await getCurrentState();
            updateButtonStates();
        });

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

