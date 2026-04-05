import { createApiClient } from "@shared/http/apiClient";
import { CORE_LOCALE_KEY, CORE_TOKEN_KEY, getCoreShopId } from "@shared/state/authStorage";

// Legacy keys (giu tam thoi). Token dung chung se luu vao core keys.
const LEGACY_TOKEN_KEY = "setting_admin_token";
const LEGACY_LOCALE_KEY = "setting_admin_locale";

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
    const core = (localStorage.getItem(CORE_TOKEN_KEY) ?? "").trim();
    if (core) return core;
    const legacy = (localStorage.getItem(LEGACY_TOKEN_KEY) ?? "").trim();
    if (legacy) localStorage.setItem(CORE_TOKEN_KEY, legacy);
    return legacy;
  } catch {
    return "";
  }
}

function getLocaleFromStorage() {
  try {
    const core = (localStorage.getItem(CORE_LOCALE_KEY) ?? "").trim();
    if (core) return core;
    const legacy = (localStorage.getItem(LEGACY_LOCALE_KEY) ?? "vi").trim();
    if (legacy) localStorage.setItem(CORE_LOCALE_KEY, legacy);
    return legacy;
  } catch {
    return "vi";
  }
}

// Axios client singleton.
export const api = createApiClient({
  baseURL: getApiBase(),
  getToken: getTokenFromStorage,
  getLocale: getLocaleFromStorage,
  getShopId: getCoreShopId,
});
