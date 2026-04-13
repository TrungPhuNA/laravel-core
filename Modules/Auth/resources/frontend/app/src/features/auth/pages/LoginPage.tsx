import React from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import type { ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { prettyJson } from "@shared/lib/format";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "../../../shared/state/auth";
import { login } from "../services/authApi";

type Err = ApiResponseFail | ApiResponseError | Error | unknown;

function normalizeError(err: Err): { title: string; details?: string } {
    if (err && typeof err === "object" && "status" in err) {
        const anyErr = err as ApiResponseFail | ApiResponseError;
        const code = (anyErr as any).code ? ` (${(anyErr as any).code})` : "";
        const trace = (anyErr as any).trace_id ? `\ntrace_id: ${(anyErr as any).trace_id}` : "";
        return { title: `${anyErr.message}${code}`, details: trace || prettyJson((anyErr as any).data ?? {}) };
    }
    if (err instanceof Error) return { title: err.message };
    return { title: "Có lỗi xảy ra", details: prettyJson(err) };
}

export default function LoginPage() {
    const nav = useNavigate();
    const auth = useAuth();

    const [loading, setLoading] = React.useState(false);
    const [error, setError] = React.useState<Err>(null);

    const [email, setEmail] = React.useState("");
    const [password, setPassword] = React.useState("");

    async function submit(e: React.FormEvent) {
        e.preventDefault();
        setLoading(true);
        setError(null);
        try {
            const res = await login({ email, password });
            auth.setToken(res.token);
            auth.persist();
            nav("/login?ok=1");
            window.location.href = "/webhook";
        } catch (err) {
            setError(err);
        } finally {
            setLoading(false);
        }
    }

    const errView = error ? normalizeError(error) : null;

    return (
        <div className="w-full">
            <div className="bg-white rounded-3xl shadow-[0_20px_50px_rgba(8,112,184,0.08)] border border-slate-100 p-8 md:p-10 relative overflow-hidden group">
                <div className="absolute top-0 right-0 h-32 w-32 bg-sky-50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110 duration-700"></div>
                
                <div className="relative z-10">
                    <div className="mb-8 text-center lg:text-left">
                        <h2 className="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Chào mừng trở lại</h2>
                        <p className="text-sm text-slate-500 font-medium leading-relaxed">Đăng nhập để tiếp tục quản lý các kênh webhook của bạn.</p>
                    </div>

                    {errView ? (
                        <div className="mb-6">
                            <Alert tone="danger" title={errView.title} details={errView.details} />
                        </div>
                    ) : null}

                    <form className="space-y-6" onSubmit={submit}>
                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2 ml-1">Email</label>
                            <div className="relative group/input">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sky-500 transition-colors">
                                    <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                                </div>
                                <Input 
                                    className="pl-11 h-12 bg-slate-50 border-slate-200 focus:bg-white transition-all text-sm font-medium" 
                                    value={email} 
                                    onChange={(e) => setEmail(e.target.value)} 
                                    placeholder="you@example.com" 
                                    autoComplete="email"
                                />
                            </div>
                        </div>

                        <div>
                            <div className="flex items-center justify-between mb-2 px-1">
                                <label className="block text-xs font-bold text-slate-700 uppercase tracking-widest">Mật khẩu</label>
                            </div>
                            <div className="relative group/input">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sky-500 transition-colors">
                                    <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                <Input
                                    className="pl-11 h-12 bg-slate-50 border-slate-200 focus:bg-white transition-all text-sm"
                                    type="password"
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    placeholder="••••••••"
                                    autoComplete="current-password"
                                />
                            </div>
                        </div>

                        <div className="pt-2">
                            <Button className="w-full h-12 text-sm font-bold shadow-lg shadow-sky-600/20 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-700 hover:to-indigo-700 border-none transform active:scale-[0.98] transition-all" type="submit" disabled={loading}>
                                {loading ? "Đang xử lý..." : "Đăng nhập ngay"}
                            </Button>
                        </div>

                        <div className="relative py-4">
                            <div className="absolute inset-0 flex items-center"><div className="w-full border-t border-slate-100"></div></div>
                            <div className="relative flex justify-center text-[10px] uppercase font-bold tracking-widest text-slate-400"><span className="bg-white px-4 tracking-tighter">Hoặc đăng nhập bằng</span></div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <button type="button" className="flex items-center justify-center gap-2 h-11 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors text-xs font-bold text-slate-700">
                                <svg className="w-4 h-4" viewBox="0 0 24 24"><path fill="#EA4335" d="M5.266 9.765A1.31 1.31 0 015 9V5h4v.11a5.99 5.99 0 014.253 1.777l2.847-2.846A9.932 9.932 0 0013 2c-5.523 0-10 4.477-10 10 0 1.29.245 2.52.69 3.65l2.846-2.846a5.99 5.99 0 01-.27-3.039z"></path><path fill="#FBBC05" d="M9.535 19.34a5.99 5.99 0 01-4.269-1.777l-2.847 2.846A9.931 9.931 0 0011 22c2.553 0 4.888-.958 6.668-2.536l-2.847-2.846a5.99 5.99 0 01-5.286 2.722z"></path><path fill="#4285F4" d="M22 12c0-.66-.06-1.3-.17-1.92H12v3.84h5.61c-.24 1.28-1 2.37-2.11 3.12l2.847 2.846C19.99 18.23 22 15.38 22 12z"></path><path fill="#34A853" d="M12 22c2.553 0 4.888-.958 6.668-2.536l-2.847-2.846a5.99 5.99 0 01-5.286 2.722 5.99 5.99 0 01-4.269-1.777l-2.847 2.846A9.931 9.931 0 0011 22z"></path></svg>
                                Google
                            </button>
                            <button type="button" className="flex items-center justify-center gap-2 h-11 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors text-xs font-bold text-slate-700">
                                <svg className="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path></svg>
                                Facebook
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div className="mt-8 text-center animate-in fade-in slide-in-from-bottom-2 duration-700 delay-300">
                <p className="text-sm text-slate-500 font-medium">
                    Bạn chưa có tài khoản?{" "}
                    <Link to="/register" className="text-sky-600 font-bold hover:text-sky-700 hover:underline underline-offset-4 transition-all">
                        Đăng ký nhanh tại đây
                    </Link>
                </p>
            </div>
        </div>
    );
}
