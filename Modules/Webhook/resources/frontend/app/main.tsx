import React from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import App from "./src/app/App";

const el = document.getElementById("webhook-app-root");
if (!el) {
    throw new Error("Missing #webhook-app-root");
}

createRoot(el).render(
    <React.StrictMode>
        <BrowserRouter basename="/webhook">
            <App />
        </BrowserRouter>
    </React.StrictMode>
);

