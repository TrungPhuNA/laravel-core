import React from "react";
import { Navigate, Route, Routes } from "react-router-dom";
import AppLayout from "./layout/AppLayout";
import DomainsPage from "../features/domains/pages/DomainsPage";
import SettingsPage from "../features/settings/pages/SettingsPage";

export default function App() {
    return (
        <Routes>
            <Route element={<AppLayout />}>
                <Route index element={<Navigate to="/domains" replace />} />
                <Route path="/domains" element={<DomainsPage />} />
                <Route path="/settings" element={<SettingsPage />} />
                <Route path="*" element={<div className="text-sm text-slate-600">Không tìm thấy trang.</div>} />
            </Route>
        </Routes>
    );
}