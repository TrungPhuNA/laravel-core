import React from "react";
import { Link, useParams } from "react-router-dom";
import Card from "@shared/ui/Card";
import Button from "@shared/ui/Button";
import Input from "@shared/ui/Input";
import Select from "@shared/ui/Select";
import Alert from "@shared/ui/Alert";
import Badge from "@shared/ui/Badge";
import Modal from "@shared/ui/Modal";
import Pagination from "@shared/ui/Pagination";
import type { ApiMetaPagination, ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { formatDateTime, prettyJson, shortText } from "@shared/lib/format";
import type { WebhookDispatchLog, WebhookDispatchLogDetail } from "../types";
import { getDispatch, listDispatches } from "../services/forwardApi";

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

export default function DispatchesPage() {
    const params = useParams();
    const webhookId = Number(params.id ?? 0);

    const [loading, setLoading] = React.useState(false);
    const [error, setError] = React.useState<Err>(null);
    const [items, setItems] = React.useState<WebhookDispatchLog[]>([]);
    const [meta, setMeta] = React.useState<ApiMetaPagination>({
        page: 1,
        per_page: 20,
        total: 0,
        last_page: 1,
        from: null,
        to: null,
    });

    const [filters, setFilters] = React.useState({
        status: "all" as "all" | "pending" | "success" | "failed",
        destination_id: "",
    });

    const [detailOpen, setDetailOpen] = React.useState(false);
    const [detailLoading, setDetailLoading] = React.useState(false);
    const [detail, setDetail] = React.useState<WebhookDispatchLogDetail | null>(null);

    async function reload(next?: Partial<{ page: number; per_page: number }>) {
        if (!webhookId) return;
        const page = next?.page ?? meta.page;
        const per_page = next?.per_page ?? meta.per_page;

        setLoading(true);
        setError(null);
        try {
            const destinationId = Number(filters.destination_id || 0);
            const res = await listDispatches(webhookId, {
                page,
                per_page,
                filters: {
                    status: filters.status === "all" ? undefined : (filters.status as any),
                    destination_id: destinationId ? destinationId : undefined,
                } as any,
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
    }, [webhookId]);

    async function openDetail(dispatchId: number) {
        if (!webhookId) return;
        setDetailOpen(true);
        setDetail(null);
        setDetailLoading(true);
        try {
            const res = await getDispatch(webhookId, dispatchId);
            setDetail(res.dispatch);
        } catch (e) {
            setError(e);
            setDetailOpen(false);
        } finally {
            setDetailLoading(false);
        }
    }

    const err = error ? normalizeError(error) : null;

    function badgeTone(status: WebhookDispatchLog["status"]) {
        if (status === "success") return "success";
        if (status === "failed") return "danger";
        return "warning";
    }

    return (
        <div className="space-y-6 pb-10">
            <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <div className="flex-1">
                    <div className="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">
                        <Link to="/channels" className="hover:text-sky-600 transition-colors">Danh sách kênh</Link>
                        <span>/</span>
                        <span>Kênh #{webhookId}</span>
                    </div>
                    <h1 className="text-2xl font-black text-slate-900 tracking-tight">Log bắn (Dispatch)</h1>
                    <p className="text-sm text-slate-500 mt-1">Theo dõi kết quả bắn webhook sang các điểm nhận.</p>
                </div>
                <div className="flex items-center gap-2">
                    <Button onClick={() => reload({ page: meta.page })} disabled={loading} variant="ghost">Tải lại</Button>
                </div>
            </div>

            {err && <Alert tone="danger" title={err.title} details={err.details} />}

            <Card title="Bộ lọc" className="rounded-3xl overflow-hidden border-slate-100 shadow-sm">
                <div className="p-5 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <div className="text-xs font-bold text-slate-500 mb-1">Status</div>
                        <Select value={filters.status} onChange={(e) => setFilters((s) => ({ ...s, status: e.target.value as any }))}>
                            <option value="all">Tất cả</option>
                            <option value="pending">pending</option>
                            <option value="success">success</option>
                            <option value="failed">failed</option>
                        </Select>
                    </div>
                    <div>
                        <div className="text-xs font-bold text-slate-500 mb-1">Destination ID</div>
                        <Input value={filters.destination_id} onChange={(e) => setFilters((s) => ({ ...s, destination_id: e.target.value }))} placeholder="vd: 12" />
                    </div>
                    <div className="flex items-end gap-2">
                        <Button
                            onClick={() => {
                                setMeta((m) => ({ ...m, page: 1 }));
                                reload({ page: 1 });
                            }}
                            disabled={loading}
                        >
                            Áp dụng
                        </Button>
                    </div>
                </div>
            </Card>

            <Card title={`Danh sách (${meta.total ?? items.length})`} className="rounded-3xl overflow-hidden border-slate-100 shadow-sm">
                <div className="overflow-x-auto">
                    <table className="min-w-full text-sm">
                        <thead className="bg-slate-50 text-slate-500">
                            <tr>
                                <th className="text-left px-4 py-3 font-bold">Thời gian</th>
                                <th className="text-left px-4 py-3 font-bold">Destination</th>
                                <th className="text-left px-4 py-3 font-bold">Kết quả</th>
                                <th className="text-left px-4 py-3 font-bold">HTTP</th>
                                <th className="text-left px-4 py-3 font-bold">Duration</th>
                                <th className="text-left px-4 py-3 font-bold">Lỗi</th>
                                <th className="text-right px-4 py-3 font-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {items.map((it) => (
                                <tr key={it.id} className="hover:bg-slate-50/40 transition-colors">
                                    <td className="px-4 py-3 text-slate-700 text-xs">{formatDateTime(it.created_at ?? null)}</td>
                                    <td className="px-4 py-3 text-slate-900 font-semibold">
                                        #{it.destination_id}
                                        <div className="mt-1 text-[11px] text-slate-500 font-mono">req#{it.webhook_request_id}</div>
                                    </td>
                                    <td className="px-4 py-3"><Badge tone={badgeTone(it.status)}>{it.status}</Badge></td>
                                    <td className="px-4 py-3 text-slate-700 font-mono text-xs">{it.response_status ?? "—"}</td>
                                    <td className="px-4 py-3 text-slate-700 font-mono text-xs">{it.duration_ms != null ? `${it.duration_ms}ms` : "—"}</td>
                                    <td className="px-4 py-3 text-slate-600 text-xs">
                                        {it.error_message ? shortText(it.error_message, 80) : "—"}
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <Button variant="ghost" onClick={() => openDetail(it.id)} disabled={loading}>Xem</Button>
                                    </td>
                                </tr>
                            ))}
                            {items.length === 0 && (
                                <tr>
                                    <td colSpan={7} className="py-14 text-center text-slate-400 font-medium">Chưa có log bắn.</td>
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

            <Modal open={detailOpen} onClose={() => setDetailOpen(false)} title={detail ? `Dispatch #${detail.id}` : "Dispatch"}>
                {detailLoading && <div className="text-sm text-slate-600">Đang tải...</div>}
                {!detailLoading && detail && (
                    <div className="space-y-4">
                        <div className="flex flex-wrap gap-2 items-center">
                            <Badge tone={badgeTone(detail.status)}>{detail.status}</Badge>
                            <div className="text-xs text-slate-600 font-mono">dest#{detail.destination_id} • req#{detail.webhook_request_id}</div>
                            <div className="text-xs text-slate-600 font-mono">http: {detail.response_status ?? "—"} • {detail.duration_ms != null ? `${detail.duration_ms}ms` : "—"}</div>
                        </div>

                        {detail.error_message && <Alert tone="danger" title={detail.error_type ?? "error"} details={detail.error_message} />}

                        <div>
                            <div className="text-xs font-bold text-slate-500 mb-1">Request body</div>
                            <textarea className="ui-input w-full font-mono text-xs min-h-[120px]" readOnly value={detail.request_body ?? ""} />
                        </div>
                        <div>
                            <div className="text-xs font-bold text-slate-500 mb-1">Response headers</div>
                            <textarea className="ui-input w-full font-mono text-xs min-h-[90px]" readOnly value={prettyJson(detail.response_headers ?? {})} />
                        </div>
                        <div>
                            <div className="text-xs font-bold text-slate-500 mb-1">Response body</div>
                            <textarea className="ui-input w-full font-mono text-xs min-h-[140px]" readOnly value={detail.response_body ?? ""} />
                        </div>
                    </div>
                )}
            </Modal>
        </div>
    );
}

