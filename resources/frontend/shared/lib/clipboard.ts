// Copy helper:
// - Ưu tiên Clipboard API (cần HTTPS/secure context).
// - Fallback execCommand('copy') để dùng được trên HTTP (laravel-core.test).
export async function copyToClipboard(text: string): Promise<boolean> {
    const value = String(text ?? "");

    // Clipboard API (async)
    try {
        if (typeof navigator !== "undefined" && navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(value);
            return true;
        }
    } catch {
        // ignore -> fallback
    }

    // Fallback for HTTP / restricted environments.
    try {
        const ta = document.createElement("textarea");
        ta.value = value;
        ta.setAttribute("readonly", "true");
        ta.style.position = "fixed";
        ta.style.top = "0";
        ta.style.left = "0";
        ta.style.opacity = "0";
        ta.style.pointerEvents = "none";

        document.body.appendChild(ta);
        ta.focus();
        ta.select();

        const ok = document.execCommand("copy");
        document.body.removeChild(ta);
        return ok;
    } catch {
        return false;
    }
}

