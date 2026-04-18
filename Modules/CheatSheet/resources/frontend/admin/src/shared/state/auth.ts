import { useMemo, useSyncExternalStore } from "react";

type Locale = "vi" | "en";

const CORE_TOKEN_KEY = "core_api_token";
const CORE_LOCALE_KEY = "core_api_locale";

const LEGACY_TOKEN_KEY = "cheatsheet_admin_token";
const LEGACY_LOCALE_KEY = "cheatsheet_admin_locale";

type Snapshot = {
  token: string;
  locale: Locale;
};

const store = (() => {
  let snapshot: Snapshot = {
    token: "",
    locale: "vi",
  };

  const listeners = new Set<() => void>();

  function emit() {
    for (const l of listeners) l();
  }

  function loadFromStorage() {
    try {
      const coreToken = (localStorage.getItem(CORE_TOKEN_KEY) ?? "").trim();
      const legacyToken = (localStorage.getItem(LEGACY_TOKEN_KEY) ?? "").trim();

      const coreLocale = (localStorage.getItem(CORE_LOCALE_KEY) ?? "").trim();
      const legacyLocale = (localStorage.getItem(LEGACY_LOCALE_KEY) ?? "").trim();

      const token = coreToken || legacyToken;
      const localeRaw = (coreLocale || legacyLocale || "vi").trim() as Locale;

      snapshot = {
        token,
        locale: localeRaw === "en" ? "en" : "vi",
      };

      if (!coreToken && legacyToken) localStorage.setItem(CORE_TOKEN_KEY, legacyToken);
      if (!coreLocale && legacyLocale) localStorage.setItem(CORE_LOCALE_KEY, legacyLocale);
    } catch {
      // ignore
    }
  }

  loadFromStorage();

  return {
    subscribe(fn: () => void) {
      listeners.add(fn);
      return () => listeners.delete(fn);
    },
    getSnapshot() {
      return snapshot;
    },
    setToken(token: string) {
      snapshot = { ...snapshot, token };
      emit();
    },
    setLocale(locale: Locale) {
      snapshot = { ...snapshot, locale };
      emit();
    },
    persist() {
      try {
        const token = snapshot.token.trim();
        localStorage.setItem(CORE_TOKEN_KEY, token);
        localStorage.setItem(CORE_LOCALE_KEY, snapshot.locale);
        localStorage.setItem(LEGACY_TOKEN_KEY, token);
        localStorage.setItem(LEGACY_LOCALE_KEY, snapshot.locale);
      } catch {
        // ignore
      }
      loadFromStorage();
      emit();
    },
    clear() {
      snapshot = { ...snapshot, token: "" };
      try {
        localStorage.removeItem(CORE_TOKEN_KEY);
        localStorage.removeItem(LEGACY_TOKEN_KEY);
      } catch {
        // ignore
      }
      emit();
    },
  };
})();

export function useAuth() {
  const snap = useSyncExternalStore(store.subscribe, store.getSnapshot, store.getSnapshot);

  return useMemo(() => {
    return {
      token: snap.token,
      locale: snap.locale,
      hasToken: snap.token.trim() !== "",
      setToken: (v: string) => store.setToken(v),
      setLocale: (v: Locale) => store.setLocale(v),
      persist: () => store.persist(),
      clear: () => store.clear(),
    };
  }, [snap.token, snap.locale]);
}

export type { Locale };

