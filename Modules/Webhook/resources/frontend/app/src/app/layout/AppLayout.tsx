import React from "react";
import { Link, Outlet, useLocation, useNavigate } from "react-router-dom";
import Button from "@shared/ui/Button";
import Input from "@shared/ui/Input";
import Select from "@shared/ui/Select";
import Modal from "@shared/ui/Modal";
import { useAuth } from "../../shared/state/auth";
import { api } from "../../shared/lib/api";

function linkClass(pathname: string, targetPrefix: string) {
    const active = pathname === targetPrefix || pathname.startsWith(targetPrefix + "/");
    return active ? "text-slate-900 font-semibold" : "text-slate-600 hover:text-slate-900";
}

type MeUser = {
    id: number;
    name: string | null;
    email: string | null;
    phone: string | null;
    user_type: string;
    avatar_url: string | null;
};

function initialsFrom(user: MeUser | null) {
    const base = (user?.name ?? user?.email ?? "U").trim();
    const parts = base.split(/\s+/).filter(Boolean);
    const letters = (parts.length >= 2 ? (parts[0][0] ?? "") + (parts[1][0] ?? "") : (parts[0]?.slice(0, 2) ?? "U")).toUpperCase();
    return letters || "U";
}

export default function AppLayout() {
    const loc = useLocation();
    const nav = useNavigate();
    const auth = useAuth();

    const [me, setMe] = React.useState<MeUser | null>(null);
    const [meLoading, setMeLoading] = React.useState(false);

    const [menuOpen, setMenuOpen] = React.useState(false);
    const menuRef = React.useRef<HTMLDivElement | null>(null);
    const [tokenModalOpen, setTokenModalOpen] = React.useState(false);
    const [tokenDraft, setTokenDraft] = React.useState(auth.token);

    React.useEffect(() => {
        setTokenDraft(auth.token);
    }, [auth.token]);

    React.useEffect(() => {
        let cancelled = false;

        async function loadMe() {
            if (!auth.hasToken) {
                setMe(null);
                return;
            }

            setMeLoading(true);
            try {
                const res = await api.get("/auth/me");
                // JSend: { data: { user: ... } }
                const user = (res.data?.data?.user ?? null) as MeUser | null;
                if (!cancelled) setMe(user);
            } catch {
                if (!cancelled) setMe(null);
            } finally {
                if (!cancelled) setMeLoading(false);
            }
        }

        loadMe();
        return () => {
            cancelled = true;
        };
    }, [auth.hasToken, auth.token]);

    React.useEffect(() => {
        if (!menuOpen) return;

        function onDocDown(e: MouseEvent) {
            const el = menuRef.current;
            if (!el) return;
            if (e.target instanceof Node && el.contains(e.target)) return;
            setMenuOpen(false);
        }

        document.addEventListener("mousedown", onDocDown);
        return () => document.removeEventListener("mousedown", onDocDown);
    }, [menuOpen]);

    async function logout() {
        try {
            await api.post("/auth/logout", {});
        } catch {
            // ignore
        } finally {
            auth.clear();
            setMe(null);
            nav("/channels");
            window.location.href = "/auth/login";
        }
    }

    function openProfile() {
        setMenuOpen(false);
        window.location.href = "/auth/profile";
    }

    function openTokenModal() {
        setMenuOpen(false);
        setTokenModalOpen(true);
    }

    function saveToken() {
        auth.setToken(tokenDraft);
        auth.persist();
        setTokenModalOpen(false);
    }

    return (
        <div className="min-h-dvh text-slate-900">
            <header className="sticky top-0 z-10 border-b bg-white/80 backdrop-blur">
                <div className="mx-auto max-w-7xl px-4 py-3 flex flex-col gap-3 md:flex-row md:items-center md:justify-between md:gap-4">
                    <div className="flex items-center gap-4 min-w-0">
                        <div className="font-semibold tracking-tight shrink-0">
                            Webhook
                            <span className="ml-2 text-xs font-normal text-slate-500">Quản lý kênh + logs</span>
                        </div>

                        <nav className="flex items-center gap-4 text-sm min-w-0">
                            <Link className={linkClass(loc.pathname, "/channels")} to="/channels">
                                Kênh webhook
                            </Link>
                        </nav>
                    </div>

                    <div className="flex items-center gap-2 w-full md:w-auto justify-end md:flex-nowrap">
                        <Select
                            value={auth.locale}
                            onChange={(e) => auth.setLocale(e.target.value as "vi" | "en")}
                            aria-label="Ngôn ngữ"
                            className="w-[88px] shrink-0"
                        >
                            <option value="vi">VI</option>
                            <option value="en">EN</option>
                        </Select>

                        {auth.hasToken && me ? (
                            <div className="relative" ref={menuRef}>
                                <button
                                    type="button"
                                    className={[
                                        "h-10 inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 text-sm shadow-sm",
                                        "hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-300",
                                        "whitespace-nowrap",
                                    ].join(" ")}
                                    onClick={() => setMenuOpen((v) => !v)}
                                >
                                    {me.avatar_url ? (
                                        <img className="h-7 w-7 rounded-full object-cover" src={me.avatar_url} alt="avatar" />
                                    ) : (
                                        <span className="h-7 w-7 rounded-full bg-slate-900 text-white grid place-items-center text-xs font-semibold">
                                            {initialsFrom(me)}
                                        </span>
                                    )}
                                    <span className="max-w-[180px] truncate font-semibold whitespace-nowrap">
                                        {me.name ?? me.email ?? "Tài khoản"}
                                    </span>
                                    <span className="text-slate-500">▾</span>
                                </button>

                                {menuOpen ? (
                                    <div className="absolute right-0 mt-2 w-[260px] rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden">
                                        <div className="px-4 py-3 bg-gradient-to-b from-white to-slate-50 border-b border-slate-100">
                                            <div className="text-sm font-semibold truncate">{me.name ?? "Tài khoản"}</div>
                                            <div className="text-xs text-slate-600 truncate">{me.email ?? "-"}</div>
                                        </div>

                                        <button
                                            type="button"
                                            className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50"
                                            onClick={openProfile}
                                        >
                                            Cập nhật thông tin
                                        </button>
                                        <button
                                            type="button"
                                            className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50"
                                            onClick={openTokenModal}
                                        >
                                            Token hệ thống
                                        </button>
                                        <div className="h-px bg-slate-100" />
                                        <button
                                            type="button"
                                            className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 text-rose-700"
                                            onClick={logout}
                                        >
                                            Đăng xuất
                                        </button>
                                    </div>
                                ) : null}
                            </div>
                        ) : (
                            <>
                                <Input
                                    className="w-full md:w-[420px] min-w-0"
                                    placeholder="Dán token Sanctum của bạn (hoặc đăng nhập ở /auth/login)"
                                    value={auth.token}
                                    onChange={(e) => auth.setToken(e.target.value)}
                                    disabled={meLoading}
                                />
                                <Button onClick={() => auth.persist()} variant="primary" disabled={meLoading}>
                                    Lưu token
                                </Button>
                                <Button onClick={() => auth.clear()} variant="ghost" disabled={meLoading}>
                                    Xoá
                                </Button>
                                <a className="text-sm font-semibold text-slate-900 hover:underline" href="/auth/login">
                                    Đăng nhập
                                </a>
                            </>
                        )}
                    </div>
                </div>

                {!auth.hasToken ? (
                    <div className="border-t bg-amber-50 text-amber-900">
                        <div className="mx-auto max-w-7xl px-4 py-2 text-xs">
                            Chưa có token. Vui lòng tạo token qua API Auth, sau đó dán vào ô ở trên.
                        </div>
                    </div>
                ) : null}
            </header>

            <main className="mx-auto max-w-7xl px-4 py-6">
                <Outlet />
            </main>

            <Modal
                open={tokenModalOpen}
                title="Token hệ thống"
                onClose={() => setTokenModalOpen(false)}
                footer={
                    <div className="flex items-center justify-end gap-2">
                        <Button variant="ghost" onClick={() => setTokenModalOpen(false)}>
                            Huỷ
                        </Button>
                        <Button variant="primary" onClick={saveToken}>
                            Lưu
                        </Button>
                    </div>
                }
            >
                <div className="text-sm text-slate-600">
                    Token được lưu ở <code className="rounded bg-slate-100 px-1 py-0.5">localStorage</code> key{" "}
                    <code className="rounded bg-slate-100 px-1 py-0.5">core_api_token</code>. Sau khi login ở{" "}
                    <a className="font-semibold text-slate-900 hover:underline" href="/auth/login">
                        /auth/login
                    </a>{" "}
                    thì các module sẽ tự nhận.
                </div>
                <div className="mt-3">
                    <div className="text-xs font-medium text-slate-600">Bearer token</div>
                    <Input value={tokenDraft} onChange={(e) => setTokenDraft(e.target.value)} placeholder="1|..." />
                </div>
            </Modal>
        </div>
    );
}
