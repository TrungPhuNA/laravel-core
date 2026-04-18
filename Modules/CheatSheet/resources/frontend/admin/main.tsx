import React from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import App from "./src/app/App";

const el = document.getElementById("cheatsheet-admin-root");

if (!el) {
  throw new Error("Missing #cheatsheet-admin-root");
}

createRoot(el).render(
  <React.StrictMode>
    <BrowserRouter basename="/admin/cheat-sheets">
      <App />
    </BrowserRouter>
  </React.StrictMode>
);

