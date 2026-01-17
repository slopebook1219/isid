<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>投影画面</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-jp:400,700,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: white;
            color: black;
            font-family: 'Noto Sans JP', sans-serif;
        }

        #qrCodeContainer {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .qr-header {
            height: 12vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .qr-header h2 {
            font-size: 6vh;
            font-weight: bold;
            margin: 0;
        }

        .qr-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2vh 4vw;
            min-height: 0;
        }

        .qr-question {
            text-align: center;
            margin-bottom: 3vh;
            max-width: 90%;
        }

        .qr-question-text {
            font-size: 7vh;
            font-weight: bold;
            line-height: 1.2;
            margin: 0 0 2vh 0;
            word-wrap: break-word;
        }

        .qr-unit {
            font-size: 4vh;
            font-weight: bold;
            color: #374151;
            margin: 0;
        }

        .qr-code-wrapper {
            background: white;
            padding: 2vh;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-code-wrapper svg {
            width: 30vh !important;
            height: 30vh !important;
            max-width: 40vw;
            max-height: 40vh;
        }

        .qr-footer {
            height: 10vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            gap: 1vh;
        }

        .qr-footer-text {
            font-size: 3vh;
            margin: 0;
            color: #4B5563;
        }

        .qr-footer-link {
            font-size: 2vh;
        }

        .qr-footer-link a {
            color: #2563EB;
            text-decoration: underline;
        }

        /* 結果表示モード */
        #resultsContainer {
            width: 100%;
            height: 100%;
            display: none;
            flex-direction: column;
            padding: 3vh 4vw;
        }

        .results-header {
            text-align: center;
            margin-bottom: 2vh;
            flex-shrink: 0;
        }

        .results-header h2 {
            font-size: 8vh;
            font-weight: bold;
            margin: 0;
        }

        .results-list {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2vh;
            overflow: hidden;
        }

        .result-item {
            background: #F3F4F6;
            padding: 3vh 4vw;
            border-radius: 16px;
            border: 1px solid #E5E7EB;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 0;
        }

        .result-label {
            font-size: 4vh;
            color: #6B7280;
            margin-bottom: 2vh;
            font-weight: 600;
        }

        .result-value {
            font-size: 12vh;
            font-weight: bold;
            margin-bottom: 2vh;
            line-height: 1;
        }

        .result-team {
            font-size: 6vh;
            color: #1F2937;
            font-weight: 600;
        }
    </style>
</head>

<body>

    {{-- QRコード表示モード --}}
    <div id="qrCodeContainer">
        <div class="qr-header">
            <h2>第{{ $questionNumber }}問</h2>
        </div>

        <div class="qr-main">
            <div class="qr-question">
                <p class="qr-question-text">{{ $question->text }}</p>
                <p class="qr-unit">単位: {{ $question->unit }}</p>
            </div>

            <div class="qr-code-wrapper">
                {!! QrCode::size(400)->generate(route('answers.show', ['game_id' => $game->id, 'question_id' =>
                $question->id])) !!}
            </div>
        </div>

        <div class="qr-footer">
            <p class="qr-footer-text">QRコードをスキャンして回答してください</p>
            <div class="qr-footer-link">
                <a href="{{ route('answers.show', ['game_id' => $game->id, 'question_id' => $question->id]) }}"
                    target="_blank">
                    回答ページへ（開発用リンク）
                </a>
            </div>
        </div>
    </div>

    {{-- 結果表示モード --}}
    <div id="resultsContainer" style="display: none;">
        <div class="results-header">
            <h2 id="resultTitle">結果</h2>
        </div>

        <div class="results-list">
            <div class="result-item">
                <div class="result-label" id="currentLabel">-</div>
                <div id="currentValue" class="result-value" style="display: none;">-</div>
                <div id="currentTeam" class="result-team">-</div>
            </div>
        </div>
    </div>

    <script>
        const config = {
            gameId: {{ $game->id }},
            questionId: {{ $question->id }},
            questionUnit: @json($question->unit),
            urls: {
                projectionState: '{{ route("games.projection-state", ["game_id" => $game->id, "question_id" => $question->id]) }}',
                stats: '{{ route("games.stats", ["game_id" => $game->id, "question_id" => $question->id]) }}'
            }
        };

        const qrCodeContainer = document.getElementById('qrCodeContainer');
        const resultsContainer = document.getElementById('resultsContainer');
        const resultTitle = document.getElementById('resultTitle');
        const currentLabel = document.getElementById('currentLabel');
        const currentValue = document.getElementById('currentValue');
        const currentTeam = document.getElementById('currentTeam');

        // 全画面表示
        const requestFullscreen = () => {
            const el = document.documentElement;
            const methods = ['requestFullscreen', 'webkitRequestFullscreen', 'mozRequestFullScreen', 'msRequestFullscreen'];
            const method = methods.find(m => el[m]);
            if (method) el[method]().catch(() => {});
        };

        // 表示切り替え
        const switchView = (state) => {
            if (state === 'qr_code') {
                qrCodeContainer.style.display = 'flex';
                resultsContainer.style.display = 'none';
            } else if (state.startsWith('result_')) {
                qrCodeContainer.style.display = 'none';
                resultsContainer.style.display = 'flex';
                displayResultStep(state);
            }
        };

        let statsData = null;

        // 統計情報を取得して保存
        async function loadStats() {
            const data = await fetchData(config.urls.stats);
            if (data) statsData = data;
            return data;
        }

        // 段階的な結果表示
        async function displayResultStep(state) {
            if (!statsData) {
                await loadStats();
            }

            if (!statsData) {
                currentLabel.textContent = 'データなし';
                currentTeam.textContent = '-';
                currentValue.style.display = 'none';
                return;
            }

            const unit = config.questionUnit || '';

            if (state === 'result_max_team') {
                resultTitle.textContent = '結果 - 最大値';
                currentLabel.textContent = '最大値';
                currentTeam.textContent = statsData.max_team ? `チーム : ${statsData.max_team}` : '-';
                currentValue.style.display = 'none';
            } else if (state === 'result_max_value') {
                resultTitle.textContent = '結果 - 最大値';
                currentLabel.textContent = '最大値';
                currentTeam.textContent = statsData.max_team ? `チーム : ${statsData.max_team}` : '-';
                currentValue.textContent = statsData.max !== null ? `${Math.round(statsData.max)}${unit}` : '-';
                currentValue.style.display = 'block';
            } else if (state === 'result_min_team') {
                resultTitle.textContent = '結果 - 最小値';
                currentLabel.textContent = '最小値';
                currentTeam.textContent = statsData.min_team ? `チーム : ${statsData.min_team}` : '-';
                currentValue.style.display = 'none';
            } else if (state === 'result_min_value') {
                resultTitle.textContent = '結果 - 最小値';
                currentLabel.textContent = '最小値';
                currentTeam.textContent = statsData.min_team ? `チーム : ${statsData.min_team}` : '-';
                currentValue.textContent = statsData.min !== null ? `${Math.round(statsData.min)}${unit}` : '-';
                currentValue.style.display = 'block';
            } else if (state === 'result_median_team') {
                resultTitle.textContent = '結果 - 中央値';
                currentLabel.textContent = '中央値';
                currentTeam.textContent = statsData.median_team ? `チーム : ${statsData.median_team}` : '-';
                currentValue.style.display = 'none';
            } else if (state === 'result_median_value') {
                resultTitle.textContent = '結果 - 中央値';
                currentLabel.textContent = '中央値';
                currentTeam.textContent = statsData.median_team ? `チーム : ${statsData.median_team}` : '-';
                currentValue.textContent = statsData.median !== null ? `${Math.round(statsData.median)}${unit}` : '-';
                currentValue.style.display = 'block';
            }
        }

        // API呼び出し
        const fetchData = async (url) => {
            try {
                const res = await fetch(url, {
                    credentials: 'same-origin'
                });
                return await res.json();
            } catch (error) {
                console.error('API呼び出しエラー:', error);
                return null;
            }
        };

        // 状態確認と更新
        const checkState = async () => {
            const stateData = await fetchData(config.urls.projectionState);
            if (!stateData) return;
            
            switchView(stateData.state);
        };

        // 初期化
        window.addEventListener('load', () => {
            requestFullscreen();
            checkState();
            setInterval(checkState, 3000);
        });
    </script>
</body>

</html>