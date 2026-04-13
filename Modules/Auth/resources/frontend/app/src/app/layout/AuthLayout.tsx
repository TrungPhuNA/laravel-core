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
        <div className="min-h-screen bg-slate-50 text-slate-900 font-sans selection:bg-sky-100 selection:text-sky-900">
            <header className="fixed top-0 left-0 right-0 z-50 border-b border-slate-200/60 bg-white/70 backdrop-blur-md">
                <div className="mx-auto max-w-[1400px] px-4 py-3 flex items-center justify-between gap-4">
                    <div className="flex items-center gap-2">
                        <div className="h-8 w-8 rounded-lg bg-gradient-to-tr from-sky-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-sky-600/20">
                            <span className="font-bold text-lg">C</span>
                        </div>
                        <div className="hidden sm:block">
                            <div className="font-bold tracking-tight text-slate-900">Core Engine</div>
                            <div className="text-[10px] font-medium text-slate-500 uppercase tracking-widest leading-none mt-0.5">Authentication</div>
                        </div>
                    </div>

                    <div className="ml-auto flex items-center gap-3">
                       
                        {auth.hasToken ? (
                            <Button variant="ghost" className="h-9 px-4 text-xs font-bold" onClick={logout}>
                                Đăng xuất
                            </Button>
                        ) : (
                            <Link 
                                to={loc.pathname === "/register" ? "/login" : "/register"}
                                className="text-xs font-bold text-sky-600 hover:text-sky-700 transition-colors"
                            >
                                {loc.pathname === "/register" ? "Đã có tài khoản?" : "Tạo tài khoản mới"}
                            </Link>
                        )}
                    </div>
                </div>
            </header>

            <main className="flex min-h-screen pt-14">
                {/* Left Side: Branding/Hero */}
                <div className="hidden lg:flex w-1/2 bg-slate-900 relative overflow-hidden flex-col justify-center px-12 xl:px-20 py-20">
                    <div className="absolute inset-0 bg-gradient-to-br from-indigo-700/40 via-sky-600/30 to-blue-900/40 z-0"></div>
                    <div className="absolute -top-24 -left-24 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl transition-transform animate-pulse duration-[10s]"></div>
                    <div className="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl transition-transform animate-pulse duration-[8s]"></div>
                    
                    <div className="relative z-10 max-w-lg">
                        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm mb-6">
                            <span className="flex h-2 w-2 rounded-full bg-sky-400"></span>
                            <span className="text-[10px] font-bold text-white uppercase tracking-wider">Phiên bản 2.0</span>
                        </div>
                        
                        <h1 className="text-4xl xl:text-5xl font-extrabold text-white leading-[1.15] mb-6 tracking-tight">
                            Bắt đầu hành trình <br />
                            <span className="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-300">quản lý dữ liệu thông minh</span>
                        </h1>
                        
                        <p className="text-slate-300 text-lg leading-relaxed mb-10 max-w-md">
                            Hệ thống Core Scaffold thế hệ mới, tích hợp sẵn Auth, Webhook, Ecommerce và API Management mạnh mẽ.
                        </p>

                        <div className="space-y-6">
                            {[
                                { title: "Bảo mật tuyệt đối", desc: "Sử dụng Laravel Sanctum và HMAC xác thực đa lớp.", icon: <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg> },
                                { title: "API-First Design", desc: "Xây dựng micro-services dễ dàng với cấu trúc module chuẩn hóa.", icon: <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> },
                                { title: "Tích hợp đa kênh", desc: "Quản lý Webhook và dữ liệu Ecommerce tập trung trong một Dashboard.", icon: <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg> },
                            ].map((feature, i) => (
                                <div key={i} className="flex gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors group">
                                    <div className="h-10 w-10 shrink-0 rounded-xl bg-sky-500/20 flex items-center justify-center text-sky-400 group-hover:scale-110 transition-transform">
                                        {feature.icon}
                                    </div>
                                    <div>
                                        <div className="text-sm font-bold text-white mb-0.5">{feature.title}</div>
                                        <div className="text-xs text-slate-400 leading-normal">{feature.desc}</div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Right Side: Auth Form */}
                <div className="w-full lg:w-1/2 flex items-center justify-center p-6 bg-slate-50 relative">
                    <div className="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-slate-100/50 to-transparent lg:hidden"></div>
                    <div className="w-full max-w-md relative z-10 transition-all duration-500">
                        <div className="lg:hidden flex justify-center mb-8">
                            <div className="h-12 w-12 rounded-xl bg-gradient-to-tr from-sky-600 to-indigo-600 flex items-center justify-center text-white shadow-xl shadow-sky-600/20">
                                <span className="font-bold text-2xl">C</span>
                            </div>
                        </div>
                        <Outlet />
                        
                        <div className="mt-8 text-center text-xs text-slate-500">
                            Core Engine API Scaffold &copy; 2026. <br />
                            Xây dựng trên nền tảng Laravel 12 & React Vite.
                        </div>
                    </div>
                </div>
            </main>
        </div>
    );
}
