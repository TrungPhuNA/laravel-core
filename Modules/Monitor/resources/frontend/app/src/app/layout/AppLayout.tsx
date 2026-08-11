import React from "react";
import { Link, Outlet, useLocation, useNavigate } from "react-router-dom";
import Button from "@shared/ui/Button";
import Input from "@shared/ui/Input";
import Modal from "@shared/ui/Modal";
import { useAuth } from "../../shared/state/auth";
import { api } from "../../shared/lib/api";

function linkClass(pathname: string, targetPrefix: string) {
    const active = pathname === targetPrefix || pathname.startsWith(targetPrefix + "/");
    return active ? "text-slate-900 font-semibold" : "text-slate-600 hover:text-slate-900";
}

export default function AppLayout() {
    const loc = useLocation();
    const auth = useAuth();

    const [tokenModalOpen, setTokenModalOpen] = React.useState(false);
    const [tokenDraft, setTokenDraft] = React.useState(auth.token);

    React.useEffect(() => {
        setTokenDraft(auth.token);
    }, [auth.token]);

    function saveToken() {
        auth.setToken(tokenDraft);
        auth.persist();
        setTokenModalOpen(false);
    }

    return (
        <div className="min-h-dvh text-slate-900">
            <header className="sticky top-0 z-10 border-b bg-white/80 backdrop-blur">
                <div className="mx-auto max-w-[1600px] px-4 py-3 flex items-center justify-between gap-3">
                    <div className="flex items-center gap-4 min-w-0">
                        <Link to="/domains" className="min-w-0 group block hover:opacity-80 transition-opacity">
                            <div className="font-semibold tracking-tight leading-5 truncate group-hover:text-sky-600 transition-colors">
                                Monitor
                            </div>
                            <div className="text-xs text-slate-600 truncate">Domain & hạn dùng</div>
                        </Link>

                        <nav className="hidden md:flex items-center gap-4 text-sm min-w-0">
                            <Link className={linkClass(loc.pathname, "/domains")} to="/domains">
                                Domain
                            </Link>
                            <Link className={linkClass(loc.pathname, "/settings")} to="/settings">
                                Cấu hình
                            </Link>
                        </nav>
                    </div>

                    <div className="flex items-center gap-2 w-full md:w-auto justify-end md:flex-nowrap">
                        {auth.hasToken ? (
                            <Button
                                variant="ghost"
                                className="whitespace-nowrap"
                                onClick={() => setTokenModalOpen(true)}
                            >
                                Token
                            </Button>
                        ) : (
                            <>
                                <Input
                                    className="w-full md:w-[380px] min-w-0"
                                    placeholder="Dán token Sanctum của bạn (đăng nhập ở /auth/login)"
                                    value={auth.token}
                                    onChange={(e) => auth.setToken(e.target.value)}
                                />
                                <Button onClick={() => auth.persist()} variant="primary">
                                    Lưu token
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
                            Chưa có token. Vui lòng dán token Sanctum (tạo ở /auth/login) vào ô bên phải để sử dụng.
                        </div>
                    </div>
                ) : null}
            </header>

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
                    <code className="rounded bg-slate-100 px-1 py-0.5">core_api_token</code>. Đăng nhập tại{" "}
                    <a className="font-semibold text-slate-900 hover:underline" href="/auth/login">
                        /auth/login
                    </a>{" "}
                    để lấy token.
                </div>
                <div className="mt-3">
                    <div className="text-xs font-medium text-slate-600">Bearer token</div>
                    <Input value={tokenDraft} onChange={(e) => setTokenDraft(e.target.value)} placeholder="1|..." />
                </div>
            </Modal>
        </div>
    );
}