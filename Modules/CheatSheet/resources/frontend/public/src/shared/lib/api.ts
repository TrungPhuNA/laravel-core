import { createApiClient } from "@shared/http/apiClient";

declare global {
  interface Window {
    __CHEATSHEET_PUBLIC__?: {
      apiBase?: string;
      moduleBase?: string;
    };
  }
}

function getApiBase() {
  const base = window.__CHEATSHEET_PUBLIC__?.apiBase ?? "/api/v1";
  return String(base).replace(/\/+$/, "");
}

export const api = createApiClient({
  baseURL: getApiBase(),
  getToken: () => "",
  getLocale: () => "vi",
});

