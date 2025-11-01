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
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
            font-size: 6vh;
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
            padding: 2vh 3vw;
            border-radius: 16px;
            border: 1px solid #E5E7EB;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 0;
        }
        .result-label {
            font-size: 2.5vh;
            color: #6B7280;
            margin-bottom: 1vh;
        }
        .result-value {
            font-size: 8vh;
            font-weight: bold;
            margin-bottom: 1vh;
            line-height: 1;
        }
        .result-team {
            font-size: 4vh;
            color: #1F2937;
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
                @if($question->unit)
                    <p class="qr-unit">単位: {{ $question->unit }}</p>
                @endif
            </div>
            
            <div class="qr-code-wrapper">
                {!! QrCode::size(400)->generate(route('answers.show', ['game_id' => $game->id, 'question_id' => $question->id])) !!}
            </div>
        </div>
        
        <div class="qr-footer">
            <p class="qr-footer-text">QRコードをスキャンして回答してください</p>
            <div class="qr-footer-link">
                <a href="{{ route('answers.show', ['game_id' => $game->id, 'question_id' => $question->id]) }}" target="_blank">
                    回答ページへ（開発用リンク）
                </a>
            </div>
        </div>
    </div>

    {{-- 結果表示モード --}}
    <div id="resultsContainer">
        <div class="results-header">
            <h2>結果</h2>
        </div>
        
        <div class="results-list">
            <div class="result-item">
                <div class="result-label">最大値</div>
                <div id="maxValue" class="result-value">-</div>
                <div id="maxTeam" class="result-team">-</div>
            </div>
            
            <div class="result-item">
                <div class="result-label">最小値</div>
                <div id="minValue" class="result-value">-</div>
                <div id="minTeam" class="result-team">-</div>
            </div>
            
            <div class="result-item">
                <div class="result-label">中央値</div>
                <div id="medianValue" class="result-value">-</div>
                <div id="medianTeam" class="result-team">-</div>
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
        const statElements = {
            max: { value: document.getElementById('maxValue'), team: document.getElementById('maxTeam') },
            min: { value: document.getElementById('minValue'), team: document.getElementById('minTeam') },
            median: { value: document.getElementById('medianValue'), team: document.getElementById('medianTeam') }
        };

        // 全画面表示
        const requestFullscreen = () => {
            const el = document.documentElement;
            const methods = ['requestFullscreen', 'webkitRequestFullscreen', 'mozRequestFullScreen', 'msRequestFullscreen'];
            const method = methods.find(m => el[m]);
            if (method) el[m]().catch(() => {});
        };

        // 表示切り替え
        const switchView = (state) => {
            if (state === 'qr_code') {
                qrCodeContainer.style.display = 'flex';
                resultsContainer.style.display = 'none';
            } else if (state === 'results') {
                qrCodeContainer.style.display = 'none';
                resultsContainer.style.display = 'flex';
            }
        };

        // 統計情報表示
        const displayStats = (data) => {
            ['max', 'min', 'median'].forEach(key => {
                const value = data[key];
                const unit = config.questionUnit || '';
                statElements[key].value.textContent = value !== null ? `${value}${unit}` : '-';
                statElements[key].team.textContent = data[`${key}_team`] || '-';
            });
        };

        // API呼び出し
        const fetchData = async (url) => {
            try {
                const res = await fetch(url);
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
            
            if (stateData.state === 'results') {
                const statsData = await fetchData(config.urls.stats);
                if (statsData) displayStats(statsData);
            }
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
