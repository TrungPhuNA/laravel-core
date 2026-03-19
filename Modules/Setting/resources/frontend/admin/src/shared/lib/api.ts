import { createApiClient } from "@shared/http/apiClient";

const STORAGE_TOKEN = "setting_admin_token";
const STORAGE_LOCALE = "setting_admin_locale";

declare global {
  interface Window {
    __SETTING_ADMIN__?: {
      apiBase?: string;
      moduleBase?: string;
    };
  }
}

function getApiBase() {
  const base = window.__SETTING_ADMIN__?.apiBase ?? "/api/v1";
  return String(base).replace(/\/+$/, "");
}

function getTokenFromStorage() {
  try {
    return (localStorage.getItem(STORAGE_TOKEN) ?? "").trim();
  } catch {
    return "";
  }
}

function getLocaleFromStorage() {
  try {
    return (localStorage.getItem(STORAGE_LOCALE) ?? "vi").trim();
  } catch {
    return "vi";
  }
}

// Axios client singleton.
export const api = createApiClient({
  baseURL: getApiBase(),
  getToken: getTokenFromStorage,
  getLocale: getLocaleFromStorage,
});
