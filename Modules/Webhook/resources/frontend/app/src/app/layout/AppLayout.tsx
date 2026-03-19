import React from "react";
import { Link, Outlet, useLocation } from "react-router-dom";
import Button from "@shared/ui/Button";
import Input from "@shared/ui/Input";
import Select from "@shared/ui/Select";
import { useAuth } from "../../shared/state/auth";

function linkClass(pathname: string, targetPrefix: string) {
    const active = pathname === targetPrefix || pathname.startsWith(targetPrefix + "/");
    return active ? "text-slate-900 font-semibold" : "text-slate-600 hover:text-slate-900";
}

export default function AppLayout() {
    const loc = useLocation();
    const auth = useAuth();

    return (
        <div className="min-h-dvh text-slate-900">
            <header className="sticky top-0 z-10 border-b bg-white/80 backdrop-blur">
                <div className="mx-auto max-w-6xl px-4 py-3 flex flex-wrap items-center gap-3">
                    <div className="font-semibold tracking-tight">
                        Webhook
                        <span className="ml-2 text-xs font-normal text-slate-500">Quản lý kênh + logs</span>
                    </div>

                    <nav className="flex items-center gap-4 text-sm">
                        <Link className={linkClass(loc.pathname, "/channels")} to="/channels">
                            Kênh webhook
                        </Link>
                    </nav>

                    <div className="ml-auto flex flex-wrap items-center gap-2 w-full md:w-auto">
                        <Select
                            value={auth.locale}
                            onChange={(e) => auth.setLocale(e.target.value as "vi" | "en")}
                            aria-label="Ngôn ngữ"
                        >
                            <option value="vi">VI</option>
                            <option value="en">EN</option>
                        </Select>

                        <Input
                            className="w-full md:w-[420px]"
                            placeholder="Dán token Sanctum của bạn để gọi API /api/v1/webhooks"
                            value={auth.token}
                            onChange={(e) => auth.setToken(e.target.value)}
                        />
                        <Button onClick={() => auth.persist()} variant="primary">
                            Lưu token
                        </Button>
                        <Button onClick={() => auth.clear()} variant="ghost">
                            Xoá
                        </Button>
                    </div>
                </div>

                {!auth.hasToken ? (
                    <div className="border-t bg-amber-50 text-amber-900">
                        <div className="mx-auto max-w-6xl px-4 py-2 text-xs">
                            Chưa có token. Vui lòng tạo token qua API Auth, sau đó dán vào ô ở trên.
                        </div>
                    </div>
                ) : null}
            </header>

            <main className="mx-auto max-w-6xl px-4 py-6">
                <Outlet />
            </main>
        </div>
    );
}

