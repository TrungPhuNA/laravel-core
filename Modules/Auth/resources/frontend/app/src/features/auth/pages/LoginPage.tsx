import React from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import type { ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { prettyJson } from "@shared/lib/format";
import { useNavigate } from "react-router-dom";
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
        <Card title="Đăng nhập">
            {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

            <form className="mt-3 space-y-3" onSubmit={submit}>
                <div>
                    <div className="text-xs font-medium text-slate-600">Email</div>
                    <Input className="mt-1" value={email} onChange={(e) => setEmail(e.target.value)} placeholder="you@example.com" />
                </div>
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

                <Button className="w-full" type="submit" variant="primary" disabled={loading}>
                    Đăng nhập
                </Button>

                <div className="text-xs text-slate-500">
                    Token sẽ được lưu vào <code className="rounded bg-slate-100 px-1 py-0.5">core_api_token</code> để các module khác tự nhận.
                </div>
            </form>
        </Card>
    );
}
