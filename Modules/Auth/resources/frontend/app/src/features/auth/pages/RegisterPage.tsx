import React from "react";
import { Link } from "react-router-dom";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import type { ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { prettyJson } from "@shared/lib/format";
import { useAuth } from "../../../shared/state/auth";
import { register } from "../services/authApi";

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

export default function RegisterPage() {
    const auth = useAuth();

    const [loading, setLoading] = React.useState(false);
    const [error, setError] = React.useState<Err>(null);

    const [name, setName] = React.useState("");
    const [email, setEmail] = React.useState("");
    const [phone, setPhone] = React.useState("");
    const [password, setPassword] = React.useState("");
    const [passwordConfirmation, setPasswordConfirmation] = React.useState("");

    async function submit(e: React.FormEvent) {
        e.preventDefault();
        setLoading(true);
        setError(null);
        try {
            const res = await register({
                name,
                email,
                phone: phone.trim() === "" ? null : phone.trim(),
                password,
                password_confirmation: passwordConfirmation,
            });
            auth.setToken(res.token);
            auth.persist();
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
                        <h2 className="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Tạo tài khoản</h2>
                        <p className="text-sm text-slate-500 font-medium leading-relaxed">Tham gia cùng chúng tôi để trải nghiệm hệ thống quản lý dữ liệu tốt nhất.</p>
                    </div>

                    {errView ? (
                        <div className="mb-6">
                            <Alert tone="danger" title={errView.title} details={errView.details} />
                        </div>
                    ) : null}

                    <form className="space-y-5" onSubmit={submit}>
                        <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2 ml-1">Họ tên</label>
                                <div className="relative group/input">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sky-500 transition-colors">
                                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <Input className="pl-11 h-11 bg-slate-50 border-slate-200 focus:bg-white transition-all text-sm font-medium" value={name} onChange={(e) => setName(e.target.value)} placeholder="Nguyễn Văn A" />
                                </div>
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2 ml-1">Số điện thoại</label>
                                <div className="relative group/input">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sky-500 transition-colors">
                                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </div>
                                    <Input className="pl-11 h-11 bg-slate-50 border-slate-200 focus:bg-white transition-all text-sm font-medium" value={phone} onChange={(e) => setPhone(e.target.value)} placeholder="0986xxxxxx" />
                                </div>
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2 ml-1">Email</label>
                            <div className="relative group/input">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sky-500 transition-colors">
                                    <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                                </div>
                                <Input className="pl-11 h-11 bg-slate-50 border-slate-200 focus:bg-white transition-all text-sm font-medium" value={email} onChange={(e) => setEmail(e.target.value)} placeholder="you@example.com" />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2 ml-1">Mật khẩu</label>
                                <div className="relative group/input">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sky-500 transition-colors">
                                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </div>
                                    <Input
                                        className="pl-11 h-11 bg-slate-50 border-slate-200 focus:bg-white transition-all text-sm"
                                        type="password"
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                        placeholder="••••••••"
                                    />
                                </div>
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2 ml-1">Nhập lại</label>
                                <div className="relative group/input">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sky-500 transition-colors">
                                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    </div>
                                    <Input
                                        className="pl-11 h-11 bg-slate-50 border-slate-200 focus:bg-white transition-all text-sm"
                                        type="password"
                                        value={passwordConfirmation}
                                        onChange={(e) => setPasswordConfirmation(e.target.value)}
                                        placeholder="••••••••"
                                    />
                                </div>
                            </div>
                        </div>

                        <div className="pt-4">
                            <Button className="w-full h-11 text-sm font-bold shadow-lg shadow-sky-600/20 bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-700 hover:to-indigo-700 border-none transform active:scale-[0.98] transition-all" type="submit" disabled={loading}>
                                {loading ? "Đang xử lý..." : "Tạo tài khoản ngay"}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>

            <div className="mt-8 text-center animate-in fade-in slide-in-from-bottom-2 duration-700 delay-300">
                <p className="text-sm text-slate-500 font-medium">
                    Bạn đã có tài khoản?{" "}
                    <Link to="/login" className="text-sky-600 font-bold hover:text-sky-700 hover:underline underline-offset-4 transition-all">
                        Đăng nhập tại đây
                    </Link>
                </p>
            </div>
        </div>
    );
}
