import { useMemo, useSyncExternalStore } from "react";

type Locale = "vi" | "en";

const STORAGE_TOKEN = "webhook_app_token";
const STORAGE_LOCALE = "webhook_app_locale";

type Snapshot = {
    token: string;
    locale: Locale;
};

const store = (() => {
    let snapshot: Snapshot = { token: "", locale: "vi" };
    const listeners = new Set<() => void>();

    function emit() {
        for (const l of listeners) l();
    }

    function load() {
        try {
            const token = (localStorage.getItem(STORAGE_TOKEN) ?? "").trim();
            const locale = (localStorage.getItem(STORAGE_LOCALE) ?? "vi").trim();
            snapshot = { token, locale: locale === "en" ? "en" : "vi" };
        } catch {
            // ignore
        }
    }

    load();

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
                localStorage.setItem(STORAGE_TOKEN, snapshot.token.trim());
                localStorage.setItem(STORAGE_LOCALE, snapshot.locale);
            } catch {
                // ignore
            }
            load();
            emit();
        },
        clear() {
            snapshot = { ...snapshot, token: "" };
            try {
                localStorage.removeItem(STORAGE_TOKEN);
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

