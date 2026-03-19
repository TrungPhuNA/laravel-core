export const CORE_TOKEN_KEY = "core_api_token";
export const CORE_LOCALE_KEY = "core_api_locale";

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

