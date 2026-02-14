import "./bootstrap";
import { createRoot } from "react-dom/client";

function App() {
    return (
        <div style={{ padding: "50px", textAlign: "center" }}>
            <h1 style={{ color: "#ff2d20" }}>Laravel + React 起動成功！</h1>
            <p>この画面が見えていれば、Reactは正しく読み込まれています。</p>
        </div>
    );
}

const rootElement = document.getElementById("app");

if (!rootElement) {
    console.error(
        "Error: id='app' の要素が見つかりませんでした。Bladeファイルを確認してください。",
    );
} else {
    const root = createRoot(rootElement);
    root.render(<App />);
}
