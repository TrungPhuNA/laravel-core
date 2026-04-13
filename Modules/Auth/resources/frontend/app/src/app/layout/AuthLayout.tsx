import React from "react";
import { Link, Outlet, useLocation, useNavigate } from "react-router-dom";
import Card from "@shared/ui/Card";
import Button from "@shared/ui/Button";
import Select from "@shared/ui/Select";
import { useAuth } from "../../shared/state/auth";
import { api } from "../../shared/lib/api";

function linkClass(pathname: string, target: string) {
    const active = pathname === target;
    return active ? "text-slate-900 font-semibold" : "text-slate-600 hover:text-slate-900";
}

export default function AuthLayout() {
    const loc = useLocation();
    const nav = useNavigate();
    const auth = useAuth();

    React.useEffect(() => {
        if (auth.hasToken) {
            // Nếu đã login rồi thì không cho ở lại trang login/register nữa
            nav("/webhook");
        }
    }, [auth.hasToken, nav]);

    async function logout() {
        try {
            await api.post("/auth/logout", {});
        } catch {
            // ignore
        } finally {
            auth.clear();
            nav("/login");
        }
    }

    return (
        <div className="min-h-dvh text-slate-900">
            <header className="border-b bg-white/80 backdrop-blur">
                <div className="mx-auto max-w-5xl px-4 py-3 flex flex-wrap items-center gap-3">
                    <div className="font-semibold tracking-tight">
                        Core Auth
                        <span className="ml-2 text-xs font-normal text-slate-500">Login/Register (Sanctum)</span>
                    </div>

                    <nav className="flex items-center gap-4 text-sm">
                        {!auth.hasToken && (
                            <>
                                <Link className={linkClass(loc.pathname, "/login")} to="/login">
                                    Đăng nhập
                                </Link>
                                <Link className={linkClass(loc.pathname, "/register")} to="/register">
                                    Đăng ký
                                </Link>
                            </>
                        )}
                        {auth.hasToken ? (
                            <Link className={linkClass(loc.pathname, "/profile")} to="/profile">
                                Hồ sơ
                            </Link>
                        ) : null}
                    </nav>

                    <div className="ml-auto flex items-center gap-2">
                        <Select
                            value={auth.locale}
                            onChange={(e) => {
                                auth.setLocale(e.target.value as "vi" | "en");
                                auth.persist();
                            }}
                            aria-label="Ngôn ngữ"
                        >
                            <option value="vi">VI</option>
                            <option value="en">EN</option>
                        </Select>

                        {auth.hasToken ? (
                            <Button variant="ghost" onClick={logout}>
                                Đăng xuất
                            </Button>
                        ) : null}
                    </div>
                </div>
            </header>

            <main className="mx-auto max-w-5xl px-4 py-8">
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div className="space-y-3">
                        <div className="text-2xl font-semibold tracking-tight">Đăng nhập để dùng Admin SPA</div>
                        <div className="text-sm text-slate-600">
                            Sau khi login/register, token sẽ được lưu vào <code className="rounded bg-slate-100 px-1 py-0.5">localStorage</code>{" "}
                            (key <code className="rounded bg-slate-100 px-1 py-0.5">core_api_token</code>) để các module khác tự nhận.
                        </div>

                        <Card title="Lối tắt">
                            <div className="flex flex-col gap-2 text-sm">
                                <a className="font-semibold text-slate-900 hover:underline" href="/admin/settings">
                                    Setting Admin
                                </a>
                                <a className="font-semibold text-slate-900 hover:underline" href="/webhook">
                                    Webhook
                                </a>
                                <a className="font-semibold text-slate-900 hover:underline" href="/docs">
                                    API Docs
                                </a>
                            </div>
                        </Card>
                    </div>

                    <Outlet />
                </div>
            </main>
        </div>
    );
}
