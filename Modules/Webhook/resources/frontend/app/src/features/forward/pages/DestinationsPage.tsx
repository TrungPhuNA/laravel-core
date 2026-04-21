import React from "react";
import { Link, useParams } from "react-router-dom";
import Card from "@shared/ui/Card";
import Button from "@shared/ui/Button";
import Input from "@shared/ui/Input";
import Select from "@shared/ui/Select";
import Alert from "@shared/ui/Alert";
import Modal from "@shared/ui/Modal";
import Pagination from "@shared/ui/Pagination";
import Badge from "@shared/ui/Badge";
import type { ApiMetaPagination, ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { prettyJson, formatDateTime, shortText } from "@shared/lib/format";
import type { WebhookDestination, WebhookFieldMapping } from "../types";
import { createDestination, deleteDestination, listDestinations, updateDestination } from "../services/forwardApi";

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

function newId() {
    return `${Date.now()}-${Math.random().toString(16).slice(2)}`;
}

type MappingRow = WebhookFieldMapping & { id: string };

export default function DestinationsPage() {
    const params = useParams();
    const webhookId = Number(params.id ?? 0);

    const [loading, setLoading] = React.useState(false);
    const [error, setError] = React.useState<Err>(null);

    const [items, setItems] = React.useState<WebhookDestination[]>([]);
    const [meta, setMeta] = React.useState<ApiMetaPagination>({
        page: 1,
        per_page: 20,
        total: 0,
        last_page: 1,
        from: null,
        to: null,
    });

    const [editorOpen, setEditorOpen] = React.useState(false);
    const [editorMode, setEditorMode] = React.useState<"create" | "edit">("create");
    const [editor, setEditor] = React.useState({
        id: 0,
        name: "",
        url: "",
        http_method: "POST" as "GET" | "POST" | "PUT" | "PATCH" | "DELETE",
        is_active: true,
        send_mode: "merge" as "merge" | "mapped_only",
        drop_mapped_sources: false,
        timeout_seconds: 10,
        headers_raw: "{}",
        headers_error: null as string | null,
        mappings: [{ id: newId(), from: "username", to: "u_username" }] as MappingRow[],
    });

    async function reload(next?: Partial<{ page: number; per_page: number }>) {
        if (!webhookId) return;
        const page = next?.page ?? meta.page;
        const per_page = next?.per_page ?? meta.per_page;

        setLoading(true);
        setError(null);
        try {
            const res = await listDestinations(webhookId, { page, per_page });
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
    }, [webhookId]);

    function openCreate() {
        setEditorMode("create");
        setEditor({
            id: 0,
            name: "",
            url: "",
            http_method: "POST",
            is_active: true,
            send_mode: "merge",
            drop_mapped_sources: false,
            timeout_seconds: 10,
            headers_raw: "{}",
            headers_error: null,
            mappings: [{ id: newId(), from: "username", to: "u_username" }],
        });
        setEditorOpen(true);
    }

    function openEdit(it: WebhookDestination) {
        setEditorMode("edit");
        setEditor({
            id: it.id,
            name: it.name ?? "",
            url: it.url ?? "",
            http_method: (it.http_method as any) || "POST",
            is_active: !!it.is_active,
            send_mode: it.send_mode ?? "merge",
            drop_mapped_sources: !!it.drop_mapped_sources,
            timeout_seconds: Number(it.timeout_seconds ?? 10),
            headers_raw: prettyJson(it.headers ?? {}),
            headers_error: null,
            mappings: (it.field_mappings ?? []).map((m) => ({ id: newId(), from: m.from, to: m.to })),
        });
        setEditorOpen(true);
    }

    function parseHeaders(): Record<string, unknown> | null {
        const raw = (editor.headers_raw ?? "").trim();
        if (raw === "") return null;
        try {
            const val = JSON.parse(raw);
            if (val === null) return null;
            if (typeof val !== "object" || Array.isArray(val)) throw new Error("Headers phải là JSON object");
            setEditor((s) => ({ ...s, headers_error: null }));
            return val as Record<string, unknown>;
        } catch (e: any) {
            setEditor((s) => ({ ...s, headers_error: String(e?.message ?? e) }));
            return null;
        }
    }

    async function save() {
        if (!webhookId) return;

        const headers = parseHeaders();
        if (editor.headers_error) return;

        const mappings = editor.mappings
            .map((m) => ({ from: (m.from ?? "").trim(), to: (m.to ?? "").trim() }))
            .filter((m) => m.from && m.to);

        setLoading(true);
        setError(null);
        try {
            if (editorMode === "create") {
                await createDestination(webhookId, {
                    name: editor.name.trim(),
                    url: editor.url.trim(),
                    http_method: editor.http_method,
                    is_active: editor.is_active,
                    headers,
                    send_mode: editor.send_mode,
                    field_mappings: mappings.length ? mappings : null,
                    drop_mapped_sources: editor.drop_mapped_sources,
                    timeout_seconds: Number(editor.timeout_seconds ?? 10),
                });
            } else {
                await updateDestination(webhookId, editor.id, {
                    name: editor.name.trim(),
                    url: editor.url.trim(),
                    http_method: editor.http_method,
                    is_active: editor.is_active,
                    headers,
                    send_mode: editor.send_mode,
                    field_mappings: mappings.length ? mappings : null,
                    drop_mapped_sources: editor.drop_mapped_sources,
                    timeout_seconds: Number(editor.timeout_seconds ?? 10),
                });
            }

            setEditorOpen(false);
            await reload();
        } catch (e) {
            setError(e);
        } finally {
            setLoading(false);
        }
    }

    async function doDelete(destinationId: number) {
        if (!webhookId) return;
        const ok = window.confirm("Xoá điểm nhận này?");
        if (!ok) return;

        setLoading(true);
        setError(null);
        try {
            await deleteDestination(webhookId, destinationId);
            await reload();
        } catch (e) {
            setError(e);
        } finally {
            setLoading(false);
        }
    }

    const err = error ? normalizeError(error) : null;

    return (
        <div className="space-y-6 pb-10">
            <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <div className="flex-1">
                    <div className="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">
                        <Link to="/channels" className="hover:text-sky-600 transition-colors">Danh sách kênh</Link>
                        <span>/</span>
                        <span>Kênh #{webhookId}</span>
                    </div>
                    <h1 className="text-2xl font-black text-slate-900 tracking-tight">Điểm nhận (Forward)</h1>
                    <p className="text-sm text-slate-500 mt-1">Cấu hình bắn dữ liệu webhook sang các URL đích, kèm mapping key.</p>
                </div>
                <div className="flex items-center gap-2">
                    <Button onClick={() => reload({ page: meta.page })} disabled={loading} variant="ghost">Tải lại</Button>
                    <Button onClick={openCreate} disabled={loading}>+ Thêm điểm nhận</Button>
                </div>
            </div>

            {err && <Alert tone="danger" title={err.title} details={err.details} />}

            <Card title={`Danh sách (${meta.total ?? items.length})`} className="rounded-3xl overflow-hidden border-slate-100 shadow-sm">
                <div className="overflow-x-auto">
                    <table className="min-w-full text-sm">
                        <thead className="bg-slate-50 text-slate-500">
                            <tr>
                                <th className="text-left px-4 py-3 font-bold">Tên</th>
                                <th className="text-left px-4 py-3 font-bold">URL</th>
                                <th className="text-left px-4 py-3 font-bold">Mode</th>
                                <th className="text-left px-4 py-3 font-bold">Trạng thái</th>
                                <th className="text-left px-4 py-3 font-bold">Cập nhật</th>
                                <th className="text-right px-4 py-3 font-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {items.map((it) => (
                                <tr key={it.id} className="hover:bg-slate-50/40 transition-colors">
                                    <td className="px-4 py-3 font-semibold text-slate-900">
                                        {it.name}
                                        <div className="mt-1 text-[11px] text-slate-500 font-mono">#{it.id} • {it.http_method}</div>
                                    </td>
                                    <td className="px-4 py-3 text-slate-700">
                                        <div className="max-w-[520px] truncate font-mono text-[12px]">{it.url}</div>
                                        <div className="mt-1 text-[11px] text-slate-500">
                                            timeout: {it.timeout_seconds ?? 10}s • drop_sources: {it.drop_mapped_sources ? "yes" : "no"}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3 text-slate-700">
                                        <Badge tone={it.send_mode === "mapped_only" ? "warning" : "info"}>{it.send_mode}</Badge>
                                        <div className="mt-1 text-[11px] text-slate-500">
                                            mappings: {it.field_mappings?.length ?? 0}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3">
                                        <Badge tone={it.is_active ? "success" : "danger"}>{it.is_active ? "Active" : "Disabled"}</Badge>
                                    </td>
                                    <td className="px-4 py-3 text-slate-600 text-xs">
                                        {formatDateTime(it.updated_at ?? it.created_at ?? null)}
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <div className="flex justify-end gap-2">
                                            <Button variant="ghost" onClick={() => openEdit(it)} disabled={loading}>Sửa</Button>
                                            <Button variant="ghost" className="text-rose-600" onClick={() => doDelete(it.id)} disabled={loading}>Xoá</Button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                            {items.length === 0 && (
                                <tr>
                                    <td colSpan={6} className="py-14 text-center text-slate-400 font-medium">Chưa có điểm nhận nào.</td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                <div className="p-4">
                    <Pagination
                        meta={meta}
                        onChange={(next) => {
                            setMeta((m) => ({ ...m, page: next.page, per_page: next.per_page }));
                            reload({ page: next.page, per_page: next.per_page });
                        }}
                    />
                </div>
            </Card>

            <Modal open={editorOpen} onClose={() => setEditorOpen(false)} title={editorMode === "create" ? "Thêm điểm nhận" : `Sửa điểm nhận #${editor.id}`}>
                <div className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <div className="text-xs font-bold text-slate-500 mb-1">Tên</div>
                            <Input value={editor.name} onChange={(e) => setEditor((s) => ({ ...s, name: e.target.value }))} placeholder="Partner A" />
                        </div>
                        <div>
                            <div className="text-xs font-bold text-slate-500 mb-1">HTTP Method</div>
                            <Select value={editor.http_method} onChange={(e) => setEditor((s) => ({ ...s, http_method: e.target.value as any }))}>
                                {["POST", "GET", "PUT", "PATCH", "DELETE"].map((m) => <option key={m} value={m}>{m}</option>)}
                            </Select>
                        </div>
                        <div className="md:col-span-2">
                            <div className="text-xs font-bold text-slate-500 mb-1">URL</div>
                            <Input value={editor.url} onChange={(e) => setEditor((s) => ({ ...s, url: e.target.value }))} placeholder="https://example.com/webhook" />
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center gap-4">
                        <label className="flex items-center gap-2 text-sm">
                            <input type="checkbox" checked={editor.is_active} onChange={(e) => setEditor((s) => ({ ...s, is_active: e.target.checked }))} />
                            Active
                        </label>
                        <label className="flex items-center gap-2 text-sm">
                            <input type="checkbox" checked={editor.drop_mapped_sources} onChange={(e) => setEditor((s) => ({ ...s, drop_mapped_sources: e.target.checked }))} />
                            Drop key gốc sau khi map
                        </label>
                        <div className="flex items-center gap-2">
                            <div className="text-xs font-bold text-slate-500">Send mode</div>
                            <Select value={editor.send_mode} onChange={(e) => setEditor((s) => ({ ...s, send_mode: e.target.value as any }))}>
                                <option value="merge">merge</option>
                                <option value="mapped_only">mapped_only</option>
                            </Select>
                        </div>
                        <div className="flex items-center gap-2">
                            <div className="text-xs font-bold text-slate-500">Timeout (s)</div>
                            <Input
                                type="number"
                                min={1}
                                max={60}
                                value={editor.timeout_seconds}
                                onChange={(e) => setEditor((s) => ({ ...s, timeout_seconds: Number(e.target.value) }))}
                                className="w-24"
                            />
                        </div>
                    </div>

                    <div>
                        <div className="flex items-center justify-between mb-1">
                            <div className="text-xs font-bold text-slate-500">Headers (JSON object)</div>
                            <Button
                                variant="ghost"
                                onClick={() => setEditor((s) => ({ ...s, headers_raw: prettyJson({ "X-Api-Key": "your-key" }) }))}
                                disabled={loading}
                            >
                                Ví dụ
                            </Button>
                        </div>
                        <textarea
                            className="ui-input w-full font-mono text-xs min-h-[90px]"
                            value={editor.headers_raw}
                            onChange={(e) => setEditor((s) => ({ ...s, headers_raw: e.target.value, headers_error: null }))}
                            placeholder='{"Authorization":"Bearer ..."}'
                        />
                        {editor.headers_error && <div className="mt-1 text-xs text-rose-600">{shortText(editor.headers_error, 200)}</div>}
                    </div>

                    <div>
                        <div className="flex items-center justify-between mb-2">
                            <div className="text-xs font-bold text-slate-500">Field mappings (from → to)</div>
                            <Button
                                variant="ghost"
                                onClick={() => setEditor((s) => ({ ...s, mappings: [...s.mappings, { id: newId(), from: "", to: "" }] }))}
                                disabled={loading}
                            >
                                + Thêm mapping
                            </Button>
                        </div>
                        <div className="space-y-2">
                            {editor.mappings.map((m) => (
                                <div key={m.id} className="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-2 items-center">
                                    <Input value={m.from} onChange={(e) => setEditor((s) => ({ ...s, mappings: s.mappings.map((x) => x.id === m.id ? { ...x, from: e.target.value } : x) }))} placeholder="username" />
                                    <Input value={m.to} onChange={(e) => setEditor((s) => ({ ...s, mappings: s.mappings.map((x) => x.id === m.id ? { ...x, to: e.target.value } : x) }))} placeholder="u_username" />
                                    <Button
                                        variant="ghost"
                                        className="text-rose-600"
                                        onClick={() => setEditor((s) => ({ ...s, mappings: s.mappings.filter((x) => x.id !== m.id) }))}
                                        disabled={loading || editor.mappings.length <= 1}
                                    >
                                        Xoá
                                    </Button>
                                </div>
                            ))}
                        </div>
                        <div className="mt-2 text-xs text-slate-500">
                            Ví dụ: nhận <span className="font-mono">username</span> → bắn sang <span className="font-mono">u_username</span>.
                        </div>
                    </div>

                    <div className="flex justify-end gap-2 pt-2">
                        <Button variant="ghost" onClick={() => setEditorOpen(false)} disabled={loading}>Huỷ</Button>
                        <Button onClick={save} disabled={loading || !editor.name.trim() || !editor.url.trim()}>Lưu</Button>
                    </div>
                </div>
            </Modal>
        </div>
    );
}
