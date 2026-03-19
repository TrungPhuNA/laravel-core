import React from "react";
import { Navigate, Route, Routes } from "react-router-dom";
import AuthLayout from "./layout/AuthLayout";
import LoginPage from "../features/auth/pages/LoginPage";
import RegisterPage from "../features/auth/pages/RegisterPage";
import ProfilePage from "../features/profile/pages/ProfilePage";

export default function App() {
    return (
        <Routes>
            <Route element={<AuthLayout />}>
                <Route index element={<Navigate to="/login" replace />} />
                <Route path="/login" element={<LoginPage />} />
                <Route path="/register" element={<RegisterPage />} />
                <Route path="/profile" element={<ProfilePage />} />
                <Route path="*" element={<div className="text-sm text-slate-600">Không tìm thấy trang.</div>} />
            </Route>
        </Routes>
    );
}
