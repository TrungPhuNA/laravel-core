import { useMemo, useSyncExternalStore } from "react";

// Dùng chung key với toàn hệ thống (Auth module đã định nghĩa).
const CORE_TOKEN_KEY = "core_api_token";
const CORE_LOCALE_KEY = "core_api_locale";

type Snapshot = {
    token: string;
    locale: string;
};

const store = (() => {
    let snapshot: Snapshot = { token: "", locale: "vi" };
    const listeners = new Set<() => void>();

    function emit() {
        for (const l of listeners) l();
    }

    function load() {
        try {
            snapshot = {
                token: (localStorage.getItem(CORE_TOKEN_KEY) ?? "").trim(),
                locale: (localStorage.getItem(CORE_LOCALE_KEY) ?? "vi").trim(),
            };
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
        persist() {
            try {
                localStorage.setItem(CORE_TOKEN_KEY, snapshot.token.trim());
                localStorage.setItem(CORE_LOCALE_KEY, snapshot.locale);
            } catch {
                // ignore
            }
            load();
            emit();
        },
        clear() {
            snapshot = { ...snapshot, token: "" };
            try {
                localStorage.removeItem(CORE_TOKEN_KEY);
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
        const token = snap.token.trim();
        const hasToken = token !== "" && token !== "undefined" && token !== "null";

        return {
            token: snap.token,
            hasToken,
            setToken: (v: string) => store.setToken(v),
            persist: () => store.persist(),
            clear: () => store.clear(),
        };
    }, [snap.token]);
}