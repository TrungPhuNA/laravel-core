export const CORE_TOKEN_KEY = "core_api_token";
export const CORE_LOCALE_KEY = "core_api_locale";
export const CORE_SHOP_ID_KEY = "core_shop_id";

export function getCoreToken(): string {
    try {
        return (localStorage.getItem(CORE_TOKEN_KEY) ?? "").trim();
    } catch {
        return "";
    }
}

export function setCoreToken(token: string) {
    try {
        localStorage.setItem(CORE_TOKEN_KEY, String(token ?? "").trim());
    } catch {
        // ignore
    }
}

export function clearCoreToken() {
    try {
        localStorage.removeItem(CORE_TOKEN_KEY);
    } catch {
        // ignore
    }
}

export function getCoreLocale(): "vi" | "en" {
    try {
        const v = (localStorage.getItem(CORE_LOCALE_KEY) ?? "vi").trim();
        return v === "en" ? "en" : "vi";
    } catch {
        return "vi";
    }
}

export function setCoreLocale(locale: "vi" | "en") {
    try {
        localStorage.setItem(CORE_LOCALE_KEY, locale === "en" ? "en" : "vi");
    } catch {
        // ignore
    }
}

export function getCoreShopId(): number | null {
    try {
        const raw = (localStorage.getItem(CORE_SHOP_ID_KEY) ?? "").trim();
        const id = Number(raw);
        return Number.isFinite(id) && id > 0 ? Math.floor(id) : null;
    } catch {
        return null;
    }
}

export function setCoreShopId(id: number | null) {
    try {
        if (id === null) {
            localStorage.removeItem(CORE_SHOP_ID_KEY);
            return;
        }
        const v = Math.floor(Number(id));
        if (!Number.isFinite(v) || v <= 0) return;
        localStorage.setItem(CORE_SHOP_ID_KEY, String(v));
    } catch {
        // ignore
    }
}

