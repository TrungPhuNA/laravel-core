import React from "react";
import { Link } from "react-router-dom";
import Card from "@shared/ui/Card";
import Button from "@shared/ui/Button";
import Input from "@shared/ui/Input";
import Select from "@shared/ui/Select";
import Badge from "@shared/ui/Badge";
import Alert from "@shared/ui/Alert";
import Modal from "@shared/ui/Modal";
import Pagination from "@shared/ui/Pagination";
import type { ApiMetaPagination, ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { prettyJson, shortText } from "@shared/lib/format";
import type { WebhookChannel } from "../types";
import { createChannel, deleteChannel, listChannels, rotateSecret, rotateToken, updateChannel } from "../services/webhooksApi";

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

export default function ChannelsPage() {
    const [loading, setLoading] = React.useState(false);
    const [error, setError] = React.useState<Err>(null);

    const [items, setItems] = React.useState<WebhookChannel[]>([]);
    const [meta, setMeta] = React.useState<ApiMetaPagination>({
        page: 1,
        per_page: 20,
        total: 0,
        last_page: 1,
        from: null,
        to: null,
    });

    const [filters, setFilters] = React.useState({
        name: "",
        auth_type: "all" as "all" | "none" | "token" | "hmac",
        is_active: "all" as "all" | "1" | "0",
    });

    const [editorOpen, setEditorOpen] = React.useState(false);
    const [editorMode, setEditorMode] = React.useState<"create" | "edit">("create");
    const [editor, setEditor] = React.useState({
        id: 0,
        name: "",
        is_active: true,
        allowed_methods: ["POST"] as Array<"GET" | "POST">,
        auth_type: "none" as "none" | "token" | "hmac",
        rotate_token: false,
        rotate_secret: false,
        description: "",
        validation_rules_json: '{\n  "email": "required|email"\n}',
    });

    const [secretOpen, setSecretOpen] = React.useState(false);
    const [secretTitle, setSecretTitle] = React.useState("");
    const [secretValue, setSecretValue] = React.useState("");
    const [receiveUrl, setReceiveUrl] = React.useState("");

    async function reload(next?: Partial<{ page: number; per_page: number }>) {
        const page = next?.page ?? meta.page;
        const per_page = next?.per_page ?? meta.per_page;

        setLoading(true);
        setError(null);
        try {
            const res = await listChannels({
                page,
                per_page,
                filters: {
                    name: filters.name,
                    auth_type: filters.auth_type === "all" ? undefined : (filters.auth_type as any),
                    is_active: filters.is_active === "all" ? undefined : (Number(filters.is_active) as any),
                },
            });
            setItems(res.items);
            setMeta(res.meta);
        } catch (e) {
            setError(e);
        } finally {
            setLoading(false);
        }
    }

    React.useEffect(() => {
        reload({ page: 1 });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    function openCreate() {
        setEditorMode("create");
        setEditor({
            id: 0,
            name: "",
            is_active: true,
            allowed_methods: ["POST"],
            auth_type: "none",
            rotate_token: false,
            rotate_secret: false,
            description: "",
            validation_rules_json: '{\n  "email": "required|email"\n}',
        });
        setEditorOpen(true);
    }

    function openEdit(ch: WebhookChannel) {
        setEditorMode("edit");
        setEditor({
            id: ch.id,
            name: ch.name,
            is_active: ch.is_active,
            allowed_methods: (ch.allowed_methods && ch.allowed_methods.length ? (ch.allowed_methods as any) : ["GET", "POST"]),
            auth_type: ch.auth_type,
            rotate_token: false,
            rotate_secret: false,
            description: ch.description ?? "",
            validation_rules_json: ch.validation_rules ? JSON.stringify(ch.validation_rules, null, 2) : "{\n}",
        });
        setEditorOpen(true);
    }

    function showSecret(title: string, value: string, url: string) {
        setSecretTitle(title);
        setSecretValue(value);
        setReceiveUrl(url);
        setSecretOpen(true);
    }

    async function submitEditor() {
        setLoading(true);
        setError(null);
        try {
            let rules: any = undefined;
            try {
                rules = JSON.parse(editor.validation_rules_json);
            } catch {
                rules = undefined;
            }

            if (editorMode === "create") {
                const res = await createChannel({
                    name: editor.name,
                    is_active: editor.is_active,
                    allowed_methods: editor.allowed_methods,
                    auth_type: editor.auth_type,
                    description: editor.description.trim() === "" ? null : editor.description,
                    validation_rules: rules,
                });
                setEditorOpen(false);
                await reload({ page: 1 });

                if (res.auth_token) showSecret("Token Webhook (chỉ hiển thị 1 lần)", res.auth_token, res.receive_url);
                if (res.auth_secret) showSecret("Secret HMAC (chỉ hiển thị 1 lần)", res.auth_secret, res.receive_url);
            } else {
                const res = await updateChannel(editor.id, {
                    name: editor.name,
                    is_active: editor.is_active,
                    allowed_methods: editor.allowed_methods,
                    auth_type: editor.auth_type,
                    rotate_token: editor.rotate_token,
                    rotate_secret: editor.rotate_secret,
                    description: editor.description.trim() === "" ? null : editor.description,
                    validation_rules: rules,
                });
                setEditorOpen(false);
                await reload();

                if (res.auth_token) showSecret("Token mới (chỉ hiển thị 1 lần)", res.auth_token, res.receive_url);
                if (res.auth_secret) showSecret("Secret mới (chỉ hiển thị 1 lần)", res.auth_secret, res.receive_url);
            }
        } catch (e) {
            setError(e);
        } finally {
            setLoading(false);
        }
    }

    async function doDelete(id: number) {
        if (!confirm(`Xoá webhook #${id}?`)) return;
        setLoading(true);
        setError(null);
        try {
            await deleteChannel(id);
            await reload({ page: 1 });
        } catch (e) {
            setError(e);
        } finally {
            setLoading(false);
        }
    }

    async function doRotateToken(ch: WebhookChannel) {
        if (!confirm(`Tạo token mới cho "${ch.name}"?`)) return;
        setLoading(true);
        setError(null);
        try {
            const res = await rotateToken(ch.id);
            await reload();
            showSecret("Token mới (chỉ hiển thị 1 lần)", res.auth_token, res.receive_url);
        } catch (e) {
            setError(e);
        } finally {
            setLoading(false);
        }
    }

    async function doRotateSecret(ch: WebhookChannel) {
        if (!confirm(`Tạo secret mới cho "${ch.name}"?`)) return;
        setLoading(true);
        setError(null);
        try {
            const res = await rotateSecret(ch.id);
            await reload();
            showSecret("Secret mới (chỉ hiển thị 1 lần)", res.auth_secret, res.receive_url);
        } catch (e) {
            setError(e);
        } finally {
            setLoading(false);
        }
    }

    const errView = error ? normalizeError(error) : null;

    return (
        <div className="space-y-4">
            <div className="flex items-end justify-between gap-3">
                <div>
                    <div className="text-lg font-semibold">Kênh webhook</div>
                    <div className="text-sm text-slate-600">Tạo nhiều kênh webhook cho riêng bạn, có logs và auth tuỳ chọn.</div>
                </div>
                <div className="flex items-center gap-2">
                    <Button variant="ghost" onClick={() => reload()} disabled={loading}>
                        Tải lại
                    </Button>
                    <Button variant="primary" onClick={openCreate} disabled={loading}>
                        Tạo kênh
                    </Button>
                </div>
            </div>

            {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

            <Card
                title="Bộ lọc"
                actions={
                    <Button variant="primary" onClick={() => reload({ page: 1 })} disabled={loading}>
                        Áp dụng
                    </Button>
                }
            >
                <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div>
                        <div className="text-xs font-medium text-slate-600">Tên (LIKE)</div>
                        <Input value={filters.name} onChange={(e) => setFilters({ ...filters, name: e.target.value })} placeholder="payment" />
                    </div>
                    <div>
                        <div className="text-xs font-medium text-slate-600">Auth</div>
                        <Select value={filters.auth_type} onChange={(e) => setFilters({ ...filters, auth_type: e.target.value as any })}>
                            <option value="all">Tất cả</option>
                            <option value="none">none</option>
                            <option value="token">token</option>
                            <option value="hmac">hmac</option>
                        </Select>
                    </div>
                    <div>
                        <div className="text-xs font-medium text-slate-600">Trạng thái</div>
                        <Select value={filters.is_active} onChange={(e) => setFilters({ ...filters, is_active: e.target.value as any })}>
                            <option value="all">Tất cả</option>
                            <option value="1">Đang bật</option>
                            <option value="0">Đang tắt</option>
                        </Select>
                    </div>
                </div>
            </Card>

            <Card title="Danh sách">
                <div className="overflow-auto">
                    <table className="min-w-full text-sm">
                        <thead className="text-left text-slate-600">
                            <tr className="border-b">
                                <th className="py-2 pr-4">ID</th>
                                <th className="py-2 pr-4">Tên</th>
                                <th className="py-2 pr-4">Auth</th>
                                <th className="py-2 pr-4">Methods</th>
                                <th className="py-2 pr-4">Receive URL</th>
                                <th className="py-2 pr-4">Last</th>
                                <th className="py-2 pr-2">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            {items.map((it) => {
                                const methods = it.allowed_methods?.length ? it.allowed_methods.join(",") : "GET,POST";
                                const url = `${window.location.origin}/api/v1/webhooks/receive/${it.public_id}`;
                                return (
                                    <tr key={it.id} className="border-b last:border-b-0">
                                        <td className="py-2 pr-4 font-medium">{it.id}</td>
                                        <td className="py-2 pr-4">
                                            <div className="font-medium">{it.name}</div>
                                            <div className="text-xs text-slate-500">{it.description ?? "-"}</div>
                                        </td>
                                        <td className="py-2 pr-4">
                                            <Badge tone={it.auth_type === "hmac" ? "info" : it.auth_type === "token" ? "warning" : "success"}>
                                                {it.auth_type}
                                            </Badge>
                                        </td>
                                        <td className="py-2 pr-4 text-slate-700">{methods}</td>
                                        <td className="py-2 pr-4 font-mono text-xs text-slate-700">{shortText(url, 54)}</td>
                                        <td className="py-2 pr-4 text-slate-600">{it.last_received_at ?? "-"}</td>
                                        <td className="py-2 pr-2">
                                            <div className="flex flex-wrap items-center gap-2">
                                                <Button variant="ghost" onClick={() => openEdit(it)}>
                                                    Sửa
                                                </Button>
                                                <Button variant="ghost" onClick={() => doDelete(it.id)} disabled={loading}>
                                                    Xoá
                                                </Button>
                                                <Link className="text-sm font-semibold text-slate-900 hover:underline" to={`/channels/${it.id}/logs`}>
                                                    Logs
                                                </Link>
                                                {it.auth_type === "token" ? (
                                                    <Button variant="primary" onClick={() => doRotateToken(it)} disabled={loading}>
                                                        Rotate token
                                                    </Button>
                                                ) : null}
                                                {it.auth_type === "hmac" ? (
                                                    <Button variant="primary" onClick={() => doRotateSecret(it)} disabled={loading}>
                                                        Rotate secret
                                                    </Button>
                                                ) : null}
                                            </div>
                                        </td>
                                    </tr>
                                );
                            })}
                            {items.length === 0 ? (
                                <tr>
                                    <td colSpan={7} className="py-6 text-center text-slate-500">
                                        Không có dữ liệu.
                                    </td>
                                </tr>
                            ) : null}
                        </tbody>
                    </table>
                </div>

                <Pagination
                    meta={meta}
                    onChange={(next) => {
                        setMeta((m) => ({ ...m, page: next.page, per_page: next.per_page }));
                        reload(next);
                    }}
                />
            </Card>

            <Modal
                open={editorOpen}
                title={editorMode === "create" ? "Tạo kênh webhook" : `Cập nhật kênh #${editor.id}`}
                onClose={() => setEditorOpen(false)}
                footer={
                    <div className="flex items-center justify-end gap-2">
                        <Button variant="ghost" onClick={() => setEditorOpen(false)}>
                            Huỷ
                        </Button>
                        <Button variant="primary" onClick={submitEditor} disabled={loading}>
                            Lưu
                        </Button>
                    </div>
                }
            >
                <div className="space-y-3">
                    <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <div className="text-xs font-medium text-slate-600">Tên</div>
                            <Input value={editor.name} onChange={(e) => setEditor({ ...editor, name: e.target.value })} placeholder="Payment callback" />
                        </div>
                        <div>
                            <div className="text-xs font-medium text-slate-600">Auth</div>
                            <Select value={editor.auth_type} onChange={(e) => setEditor({ ...editor, auth_type: e.target.value as any })}>
                                <option value="none">none</option>
                                <option value="token">token</option>
                                <option value="hmac">hmac</option>
                            </Select>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <div className="text-xs font-medium text-slate-600">Trạng thái</div>
                            <Select
                                value={editor.is_active ? "1" : "0"}
                                onChange={(e) => setEditor({ ...editor, is_active: e.target.value === "1" })}
                            >
                                <option value="1">Bật</option>
                                <option value="0">Tắt</option>
                            </Select>
                        </div>
                        <div>
                            <div className="text-xs font-medium text-slate-600">Methods</div>
                            <Select
                                value={editor.allowed_methods.join(",")}
                                onChange={(e) => {
                                    const v = e.target.value;
                                    setEditor({ ...editor, allowed_methods: v === "GET" ? ["GET"] : v === "POST" ? ["POST"] : ["GET", "POST"] });
                                }}
                            >
                                <option value="GET,POST">GET + POST</option>
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                            </Select>
                        </div>
                        <div>
                            <div className="text-xs font-medium text-slate-600">Mô tả</div>
                            <Input value={editor.description} onChange={(e) => setEditor({ ...editor, description: e.target.value })} placeholder="Nhận callback..." />
                        </div>
                    </div>

                    {editorMode === "edit" && editor.auth_type === "token" ? (
                        <label className="flex items-center gap-2 text-sm">
                            <input type="checkbox" checked={editor.rotate_token} onChange={(e) => setEditor({ ...editor, rotate_token: e.target.checked })} />
                            Rotate token (tạo token mới)
                        </label>
                    ) : null}

                    {editorMode === "edit" && editor.auth_type === "hmac" ? (
                        <label className="flex items-center gap-2 text-sm">
                            <input type="checkbox" checked={editor.rotate_secret} onChange={(e) => setEditor({ ...editor, rotate_secret: e.target.checked })} />
                            Rotate secret (tạo secret mới)
                        </label>
                    ) : null}

                    <div>
                        <div className="text-xs font-medium text-slate-600">Validation rules (JSON)</div>
                        <textarea
                            className={[
                                "mt-1 w-full rounded-md border border-slate-200 bg-white p-3 font-mono text-xs outline-none shadow-sm",
                                "focus:border-slate-400 focus:ring-2 focus:ring-slate-200",
                            ].join(" ")}
                            rows={10}
                            value={editor.validation_rules_json}
                            onChange={(e) => setEditor({ ...editor, validation_rules_json: e.target.value })}
                        />
                        <div className="mt-1 text-xs text-slate-500">Để trống hoặc `{}` nếu không muốn validate payload.</div>
                    </div>
                </div>
            </Modal>

            <Modal
                open={secretOpen}
                title={secretTitle}
                onClose={() => setSecretOpen(false)}
                footer={
                    <div className="flex items-center justify-between gap-2">
                        <div className="text-xs text-slate-600">
                            Receive URL: <span className="font-mono">{receiveUrl}</span>
                        </div>
                        <Button variant="ghost" onClick={() => setSecretOpen(false)}>
                            Đóng
                        </Button>
                    </div>
                }
            >
                <Alert
                    tone="warning"
                    title="Lưu lại ngay"
                    details="Token/secret chỉ hiển thị 1 lần. Mất token/secret thì phải rotate để tạo cái mới."
                />
                <pre className="mt-3 overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs">{secretValue}</pre>
            </Modal>
        </div>
    );
}

