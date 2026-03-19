import React from "react";
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
        <Card title="Đăng ký">
            {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

            <form className="mt-3 space-y-3" onSubmit={submit}>
                <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <div className="text-xs font-medium text-slate-600">Họ tên</div>
                        <Input className="mt-1" value={name} onChange={(e) => setName(e.target.value)} placeholder="Nguyễn Văn A" />
                    </div>
                    <div>
                        <div className="text-xs font-medium text-slate-600">Số điện thoại</div>
                        <Input className="mt-1" value={phone} onChange={(e) => setPhone(e.target.value)} placeholder="0986xxxxxx" />
                    </div>
                </div>

                <div>
                    <div className="text-xs font-medium text-slate-600">Email</div>
                    <Input className="mt-1" value={email} onChange={(e) => setEmail(e.target.value)} placeholder="you@example.com" />
                </div>

                <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <div className="text-xs font-medium text-slate-600">Mật khẩu</div>
                        <Input
                            className="mt-1"
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            placeholder="••••••••"
                        />
                    </div>
                    <div>
                        <div className="text-xs font-medium text-slate-600">Nhập lại mật khẩu</div>
                        <Input
                            className="mt-1"
                            type="password"
                            value={passwordConfirmation}
                            onChange={(e) => setPasswordConfirmation(e.target.value)}
                            placeholder="••••••••"
                        />
                    </div>
                </div>

                <Button className="w-full" type="submit" variant="primary" disabled={loading}>
                    Tạo tài khoản
                </Button>
            </form>
        </Card>
    );
}
