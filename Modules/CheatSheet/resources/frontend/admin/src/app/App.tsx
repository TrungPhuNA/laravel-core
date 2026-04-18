import React from "react";
import { Route, Routes } from "react-router-dom";
import AdminLayout from "./layout/AdminLayout";
import CheatSheetsPage from "../features/cheatSheets/pages/CheatSheetsPage";
import TopicsPage from "../features/cheatSheets/pages/TopicsPage";

export default function App() {
  return (
    <Routes>
      <Route element={<AdminLayout />}>
        <Route index element={<CheatSheetsPage />} />
        <Route path="/cheat-sheets" element={<CheatSheetsPage />} />
        <Route path="/topics" element={<TopicsPage />} />
        <Route path="*" element={<div className="text-sm text-slate-600">Không tìm thấy trang.</div>} />
      </Route>
    </Routes>
  );
}
