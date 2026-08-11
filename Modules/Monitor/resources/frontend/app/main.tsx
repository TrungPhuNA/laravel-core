import React from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import App from "./src/app/App";

const el = document.getElementById("monitor-app-root");
if (!el) {
    throw new Error("Missing #monitor-app-root");
}

createRoot(el).render(
    <React.StrictMode>
        <BrowserRouter basename="/monitor">
            <App />
        </BrowserRouter>
    </React.StrictMode>
);