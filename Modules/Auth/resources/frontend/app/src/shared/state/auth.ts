import { useMemo, useSyncExternalStore } from "react";
import {
    clearCoreToken,
    CORE_LOCALE_KEY,
    CORE_TOKEN_KEY,
    getCoreLocale,
    getCoreToken,
    setCoreLocale,
    setCoreToken,
} from "@shared/state/authStorage";

type Locale = "vi" | "en";

type Snapshot = {
    token: string;
    locale: Locale;
};

const store = (() => {
    let snapshot: Snapshot = {
        token: getCoreToken(),
        locale: getCoreLocale(),
    };

    const listeners = new Set<() => void>();

    function emit() {
        for (const l of listeners) l();
    }

    function reload() {
        snapshot = {
            token: getCoreToken(),
            locale: getCoreLocale(),
        };
    }

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
            setCoreToken(snapshot.token);
            setCoreLocale(snapshot.locale);
            reload();
            emit();
        },
        clear() {
            clearCoreToken();
            try {
                localStorage.removeItem(CORE_TOKEN_KEY);
                localStorage.removeItem(CORE_LOCALE_KEY);
            } catch {
                // ignore
            }
            reload();
            emit();
        },
    };
})();

export function useAuth() {
    const snap = useSyncExternalStore(store.subscribe, store.getSnapshot, store.getSnapshot);

    return useMemo(() => {
        const token = snap.token.trim();
        const isValidToken = token !== "" && token !== "undefined" && token !== "null";

        return {
            token: snap.token,
            locale: snap.locale,
            hasToken: isValidToken,
            setToken: (v: string) => store.setToken(v),
            setLocale: (v: Locale) => store.setLocale(v),
            persist: () => store.persist(),
            clear: () => store.clear(),
        };
    }, [snap.token, snap.locale]);
}

