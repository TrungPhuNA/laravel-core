import { createApiClient } from "@shared/http/apiClient";

declare global {
    interface Window {
        __WEBHOOK_APP__?: {
            apiBase?: string;
            moduleBase?: string;
        };
    }
}

const STORAGE_TOKEN = "webhook_app_token";
const STORAGE_LOCALE = "webhook_app_locale";

function getApiBase() {
    const base = window.__WEBHOOK_APP__?.apiBase ?? "/api/v1";
    return String(base).replace(/\/+$/, "");
}

function getToken() {
    try {
        return (localStorage.getItem(STORAGE_TOKEN) ?? "").trim();
    } catch {
        return "";
    }
}

function getLocale() {
    try {
        return (localStorage.getItem(STORAGE_LOCALE) ?? "vi").trim();
    } catch {
        return "vi";
    }
}

export const api = createApiClient({
    baseURL: getApiBase(),
    getToken,
    getLocale,
});

