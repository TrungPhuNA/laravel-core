import React from "react";
import { Route, Routes } from "react-router-dom";
import PublicLayout from "./layout/PublicLayout";
import TopicsPublicPage from "../features/publicCheatSheets/pages/TopicsPublicPage";
import PublicCheatSheetsPage from "../features/publicCheatSheets/pages/PublicCheatSheetsPage";
import PublicCheatSheetDetailPage from "../features/publicCheatSheets/pages/PublicCheatSheetDetailPage";

export default function App() {
  return (
    <Routes>
      <Route element={<PublicLayout />}>
        <Route index element={<TopicsPublicPage />} />
        <Route path="/all" element={<PublicCheatSheetsPage />} />
        <Route path="/topic/:slug" element={<PublicCheatSheetsPage />} />
        <Route path="/:id" element={<PublicCheatSheetDetailPage />} />
        <Route path="*" element={<div className="text-sm text-slate-300">Not found.</div>} />
      </Route>
    </Routes>
  );
}

