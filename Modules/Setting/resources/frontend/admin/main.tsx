import React from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import App from "./src/app/App";

const el = document.getElementById("setting-admin-root");

if (!el) {
  throw new Error("Missing #setting-admin-root");
}

createRoot(el).render(
  <React.StrictMode>
    <BrowserRouter basename="/admin/settings">
      <App />
    </BrowserRouter>
  </React.StrictMode>
);
