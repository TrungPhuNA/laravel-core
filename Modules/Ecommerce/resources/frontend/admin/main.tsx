import React from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import App from "./src/app/App";

const el = document.getElementById("ecommerce-admin-root");

if (!el) {
  throw new Error("Missing #ecommerce-admin-root");
}

createRoot(el).render(
  <React.StrictMode>
    <BrowserRouter basename="/admin/ecommerce">
      <App />
    </BrowserRouter>
  </React.StrictMode>
);

