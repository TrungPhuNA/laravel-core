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
    const [mobileNavOpen, setMobileNavOpen] = React.useState(false);
    const mobileNavRef = React.useRef<HTMLDivElement | null>(null);
    const [tokenModalOpen, setTokenModalOpen] = React.useState(false);
    const [tokenDraft, setTokenDraft] = React.useState(auth.token);
    const [authChecked, setAuthChecked] = React.useState(false);

    React.useEffect(() => {
        if (!auth.hasToken) {
            window.location.href = "/auth/login";
        } else {
            setAuthChecked(true);
        }
    }, [auth.hasToken]);

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

    React.useEffect(() => {
        if (!mobileNavOpen) return;

        function onKeyDown(e: KeyboardEvent) {
            if (e.key === "Escape") setMobileNavOpen(false);
        }

        function onDocDown(e: MouseEvent) {
            const el = mobileNavRef.current;
            if (!el) return;
            if (e.target instanceof Node && el.contains(e.target)) return;
            setMobileNavOpen(false);
        }

        window.addEventListener("keydown", onKeyDown);
        document.addEventListener("mousedown", onDocDown);
        return () => {
            window.removeEventListener("keydown", onKeyDown);
            document.removeEventListener("mousedown", onDocDown);
        };
    }, [mobileNavOpen]);

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

    if (!auth.hasToken) {
        return null;
    }

    return (
        <div className="min-h-dvh text-slate-900">
            <header className="sticky top-0 z-10 border-b bg-white/80 backdrop-blur">
                <div className="mx-auto max-w-[1600px] px-4 py-3 flex items-center justify-between gap-3">
                    <div className="flex items-center gap-3 min-w-0">
                        <button
                            type="button"
                            className="md:hidden ui-btn ui-btn-ghost h-10 w-10 px-0 py-0 grid place-items-center"
                            aria-label="Open menu"
                            onClick={() => setMobileNavOpen(true)}
                        >
                            ☰
                        </button>

                        <Link to="/channels" className="min-w-0 group block hover:opacity-80 transition-opacity">
                            <div className="font-semibold tracking-tight leading-5 truncate group-hover:text-sky-600 transition-colors">
                                <span className="hidden sm:inline ml-2 text-xs font-normal text-slate-500">Quản lý kênh + logs</span>
                            </div>
                            <div className="md:hidden text-xs text-slate-600 truncate">Kênh webhook</div>
                        </Link>

                        <nav className="hidden md:flex items-center gap-4 text-sm min-w-0">
                            <Link className={linkClass(loc.pathname, "/channels")} to="/channels">
                                Kênh webhook
                            </Link>
                            
                            {/* Dynamic menu based on current channel ID */}
                            {(() => {
                                const match = loc.pathname.match(/\/channels\/(\d+)/);
                                if (!match) return null;
                                const cid = match[1];
                                return (
                                    <>
                                        <div className="w-px h-4 bg-slate-200 mx-1"></div>
                                        <Link className={linkClass(loc.pathname, `/channels/${cid}/logs`)} to={`/channels/${cid}/logs`}>
                                            Logs
                                        </Link>
                                        <Link className={linkClass(loc.pathname, `/channels/${cid}/stats`)} to={`/channels/${cid}/stats`}>
                                            Thống kê
                                        </Link>
                                    </>
                                );
                            })()}
                        </nav>
                    </div>

                    <div className="flex items-center gap-2 w-full md:w-auto justify-end md:flex-nowrap">
                      
                        {auth.hasToken && me ? (
                            <div className="relative" ref={menuRef}>
                                <button
                                    type="button"
                                    className={[
                                        "h-10 inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-2 md:px-3 text-sm shadow-sm",
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
                                    <span className="hidden md:inline max-w-[180px] truncate font-semibold whitespace-nowrap">
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

            {mobileNavOpen ? (
                <div className="fixed inset-0 z-20 md:hidden">
                    <div className="absolute inset-0 bg-slate-900/30 backdrop-blur-sm" />
                    <div
                        ref={mobileNavRef}
                        className={[
                            "absolute left-0 top-0 h-full w-[84vw] max-w-[320px] bg-white shadow-2xl border-r border-slate-200",
                            "flex flex-col",
                        ].join(" ")}
                    >
                        <div className="px-4 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                            <div className="min-w-0">
                                <div className="font-semibold text-slate-900 truncate">Webhook</div>
                                <div className="text-xs text-slate-600 truncate">Quản lý kênh + logs</div>
                            </div>
                            <button
                                type="button"
                                className="ui-btn ui-btn-ghost h-9 w-9 px-0 py-0 grid place-items-center"
                                aria-label="Close menu"
                                onClick={() => setMobileNavOpen(false)}
                            >
                                ×
                            </button>
                        </div>

                        {auth.hasToken && me ? (
                            <div className="px-4 py-3 border-b border-slate-100">
                                <div className="flex items-center gap-3">
                                    {me.avatar_url ? (
                                        <img className="h-10 w-10 rounded-full object-cover" src={me.avatar_url} alt="avatar" />
                                    ) : (
                                        <span className="h-10 w-10 rounded-full bg-slate-900 text-white grid place-items-center text-xs font-semibold">
                                            {initialsFrom(me)}
                                        </span>
                                    )}
                                    <div className="min-w-0">
                                        <div className="text-sm font-semibold truncate">{me.name ?? "Tài khoản"}</div>
                                        <div className="text-xs text-slate-600 truncate">{me.email ?? "-"}</div>
                                    </div>
                                </div>
                            </div>
                        ) : null}

                        <div className="px-2 py-2">
                            <Link
                                className={[
                                    "block rounded-lg px-3 py-2 text-sm",
                                    linkClass(loc.pathname, "/channels") === "text-slate-900 font-semibold"
                                        ? "bg-slate-100 text-slate-900 font-semibold"
                                        : "text-slate-700 hover:bg-slate-50 hover:text-slate-900",
                                ].join(" ")}
                                to="/channels"
                                onClick={() => setMobileNavOpen(false)}
                            >
                                Kênh webhook
                            </Link>
                        </div>

                        <div className="mt-auto border-t border-slate-100 p-4 space-y-3">
                            {auth.hasToken && me ? (
                                <div className="space-y-2">
                                    <Button
                                        variant="ghost"
                                        className="w-full justify-center"
                                        onClick={() => {
                                            setMobileNavOpen(false);
                                            openProfile();
                                        }}
                                    >
                                        Cập nhật thông tin
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        className="w-full justify-center"
                                        onClick={() => {
                                            setMobileNavOpen(false);
                                            openTokenModal();
                                        }}
                                    >
                                        Token hệ thống
                                    </Button>
                                    <Button
                                        variant="danger"
                                        className="w-full justify-center"
                                        onClick={() => {
                                            setMobileNavOpen(false);
                                            logout();
                                        }}
                                    >
                                        Đăng xuất
                                    </Button>
                                </div>
                            ) : (
                                <a className="block text-center text-sm font-semibold text-slate-900 hover:underline" href="/auth/login">
                                    Đăng nhập
                                </a>
                            )}
                        </div>
                    </div>
                </div>
            ) : null}

            <main className="mx-auto max-w-[1600px] px-4 py-6">
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
