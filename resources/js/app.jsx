import "./bootstrap";
import { createRoot } from "react-dom/client";
import Welcome from "./pages/Welcome";

const rootElement = document.getElementById("app");

if (rootElement) {
    const root = createRoot(rootElement);
    root.render(<Welcome />);
} else {
    console.error(
        "Error: id='app' の要素が見つかりませんでした。Bladeファイルを確認してください。",
    );
}
