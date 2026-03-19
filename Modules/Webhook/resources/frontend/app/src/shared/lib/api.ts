import { createApiClient } from "@shared/http/apiClient";
import { CORE_LOCALE_KEY, CORE_TOKEN_KEY } from "@shared/state/authStorage";

declare global {
    interface Window {
        __WEBHOOK_APP__?: {
            apiBase?: string;
            moduleBase?: string;
        };
    }
}

function getApiBase() {
    const base = window.__WEBHOOK_APP__?.apiBase ?? "/api/v1";
    return String(base).replace(/\/+$/, "");
}

function getToken() {
    try {
        return (localStorage.getItem(CORE_TOKEN_KEY) ?? "").trim();
    } catch {
        return "";
    }
}

function getLocale() {
    try {
        return (localStorage.getItem(CORE_LOCALE_KEY) ?? "vi").trim();
    } catch {
        return "vi";
    }
}

export const api = createApiClient({
    baseURL: getApiBase(),
    getToken,
    getLocale,
});
