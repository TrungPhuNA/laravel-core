import { createApiClient } from "@shared/http/apiClient";
import { CORE_LOCALE_KEY, CORE_TOKEN_KEY, getCoreShopId } from "@shared/state/authStorage";

const LEGACY_TOKEN_KEY = "cheatsheet_admin_token";
const LEGACY_LOCALE_KEY = "cheatsheet_admin_locale";

declare global {
  interface Window {
    __CHEATSHEET_ADMIN__?: {
      apiBase?: string;
      moduleBase?: string;
    };
  }
}

function getApiBase() {
  const base = window.__CHEATSHEET_ADMIN__?.apiBase ?? "/api/v1";
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

export const api = createApiClient({
  baseURL: getApiBase(),
  getToken: getTokenFromStorage,
  getLocale: getLocaleFromStorage,
  getShopId: getCoreShopId,
});

