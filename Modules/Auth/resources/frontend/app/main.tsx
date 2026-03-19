import React from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import App from "./src/app/App";

const el = document.getElementById("auth-app-root");
if (!el) {
    throw new Error("Missing #auth-app-root");
}

createRoot(el).render(
    <React.StrictMode>
        <BrowserRouter basename="/auth">
            <App />
        </BrowserRouter>
    </React.StrictMode>
);

