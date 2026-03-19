import React from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import type { ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { prettyJson } from "@shared/lib/format";
import { me, updateProfile, type ProfileUser } from "../services/profileApi";

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

export default function ProfilePage() {
    const [loading, setLoading] = React.useState(false);
    const [error, setError] = React.useState<Err>(null);
    const [saved, setSaved] = React.useState<string>("");

    const [user, setUser] = React.useState<ProfileUser | null>(null);

    const [form, setForm] = React.useState({
        name: "",
        phone: "",
        avatar_url: "",
        company: "",
        job_title: "",
        bio: "",
    });

    async function load() {
        setLoading(true);
        setError(null);
        setSaved("");
        try {
            const u = await me();
            setUser(u);
            setForm({
                name: u.name ?? "",
                phone: u.phone ?? "",
                avatar_url: u.avatar_url ?? "",
                company: u.company ?? "",
                job_title: u.job_title ?? "",
                bio: u.bio ?? "",
            });
        } catch (e) {
            setError(e);
        } finally {
            setLoading(false);
        }
    }

    React.useEffect(() => {
        load();
    }, []);

    async function submit(e: React.FormEvent) {
        e.preventDefault();
        setLoading(true);
        setError(null);
        setSaved("");
        try {
            const u = await updateProfile({
                name: form.name,
                phone: form.phone.trim() === "" ? null : form.phone.trim(),
                avatar_url: form.avatar_url.trim() === "" ? null : form.avatar_url.trim(),
                company: form.company.trim() === "" ? null : form.company.trim(),
                job_title: form.job_title.trim() === "" ? null : form.job_title.trim(),
                bio: form.bio.trim() === "" ? null : form.bio.trim(),
            });
            setUser(u);
            setSaved("Đã cập nhật thông tin thành công");
        } catch (e) {
            setError(e);
        } finally {
            setLoading(false);
        }
    }

    const errView = error ? normalizeError(error) : null;

    return (
        <Card title="Cập nhật thông tin">
            {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}
            {saved ? <Alert tone="success" title={saved} /> : null}

            {user ? (
                <div className="mt-3 text-xs text-slate-600">
                    <div>
                        Email: <span className="font-semibold text-slate-900">{user.email ?? "-"}</span>
                    </div>
                    <div>
                        User type: <span className="font-semibold text-slate-900">{user.user_type}</span>
                    </div>
                </div>
            ) : null}

            <form className="mt-4 space-y-3" onSubmit={submit}>
                <div>
                    <div className="text-xs font-medium text-slate-600">Họ tên</div>
                    <Input className="mt-1" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} placeholder="Nguyễn Văn A" />
                </div>
                <div>
                    <div className="text-xs font-medium text-slate-600">Số điện thoại</div>
                    <Input className="mt-1" value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} placeholder="0986xxxxxx" />
                </div>
                <div>
                    <div className="text-xs font-medium text-slate-600">Avatar URL</div>
                    <Input
                        className="mt-1"
                        value={form.avatar_url}
                        onChange={(e) => setForm({ ...form, avatar_url: e.target.value })}
                        placeholder="https://example.com/avatar.png"
                    />
                    {form.avatar_url.trim() ? (
                        <div className="mt-2 flex items-center gap-2">
                            <img className="h-10 w-10 rounded-full object-cover border border-slate-200" src={form.avatar_url} alt="avatar" />
                            <div className="text-xs text-slate-500">Preview</div>
                        </div>
                    ) : null}
                </div>

                <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <div className="text-xs font-medium text-slate-600">Công ty</div>
                        <Input className="mt-1" value={form.company} onChange={(e) => setForm({ ...form, company: e.target.value })} placeholder="Core Co" />
                    </div>
                    <div>
                        <div className="text-xs font-medium text-slate-600">Chức danh</div>
                        <Input className="mt-1" value={form.job_title} onChange={(e) => setForm({ ...form, job_title: e.target.value })} placeholder="Engineer" />
                    </div>
                </div>

                <div>
                    <div className="text-xs font-medium text-slate-600">Bio</div>
                    <textarea
                        className={[
                            "mt-1 w-full rounded-md border border-slate-200 bg-white p-3 text-sm outline-none shadow-sm",
                            "focus:border-slate-400 focus:ring-2 focus:ring-slate-200",
                        ].join(" ")}
                        rows={5}
                        value={form.bio}
                        onChange={(e) => setForm({ ...form, bio: e.target.value })}
                    />
                </div>

                <Button className="w-full" type="submit" variant="primary" disabled={loading}>
                    Lưu thay đổi
                </Button>
            </form>
        </Card>
    );
}
