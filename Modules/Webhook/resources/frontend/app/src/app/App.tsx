import React from "react";
import { Navigate, Route, Routes } from "react-router-dom";
import AppLayout from "./layout/AppLayout";
import ChannelsPage from "../features/channels/pages/ChannelsPage";
import ChannelLogsPage from "../features/logs/pages/ChannelLogsPage";
import ChannelStatsPage from "../features/logs/pages/ChannelStatsPage";
import DestinationsPage from "../features/forward/pages/DestinationsPage";
import DispatchesPage from "../features/forward/pages/DispatchesPage";

export default function App() {
    return (
        <Routes>
            <Route element={<AppLayout />}>
                <Route index element={<Navigate to="/channels" replace />} />
                <Route path="/channels" element={<ChannelsPage />} />
                <Route path="/channels/:id/logs" element={<ChannelLogsPage />} />
                <Route path="/channels/:id/stats" element={<ChannelStatsPage />} />
                <Route path="/channels/:id/destinations" element={<DestinationsPage />} />
                <Route path="/channels/:id/dispatches" element={<DispatchesPage />} />
                <Route path="*" element={<div className="text-sm text-slate-600">Không tìm thấy trang.</div>} />
            </Route>
        </Routes>
    );
}
