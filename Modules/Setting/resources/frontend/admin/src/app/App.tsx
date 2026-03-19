import React from "react";
import { Navigate, Route, Routes } from "react-router-dom";
import AdminLayout from "./layout/AdminLayout";
import SettingsPage from "../features/settings/pages/SettingsPage";
import QueueOverviewPage from "../features/queue/pages/QueueOverviewPage";
import QueueJobsPage from "../features/queue/pages/QueueJobsPage";
import QueueFailedJobsPage from "../features/queue/pages/QueueFailedJobsPage";
import QueueBatchesPage from "../features/queue/pages/QueueBatchesPage";

export default function App() {
  return (
    <Routes>
      <Route element={<AdminLayout />}>
        <Route index element={<Navigate to="/settings" replace />} />
        <Route path="/settings" element={<SettingsPage />} />

        <Route path="/queue" element={<QueueOverviewPage />} />
        <Route path="/queue/jobs" element={<QueueJobsPage />} />
        <Route path="/queue/failed-jobs" element={<QueueFailedJobsPage />} />
        <Route path="/queue/batches" element={<QueueBatchesPage />} />

        <Route path="*" element={<div className="text-sm text-slate-600">Không tìm thấy trang.</div>} />
      </Route>
    </Routes>
  );
}

