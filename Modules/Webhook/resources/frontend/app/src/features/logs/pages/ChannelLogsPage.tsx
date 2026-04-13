import React from "react";
import { Link, useParams } from "react-router-dom";
import Card from "@shared/ui/Card";
import Button from "@shared/ui/Button";
import Input from "@shared/ui/Input";
import Select from "@shared/ui/Select";
import Alert from "@shared/ui/Alert";
import Modal from "@shared/ui/Modal";
import Pagination from "@shared/ui/Pagination";
import type { ApiMetaPagination, ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { formatDateTime, prettyJson, shortText } from "@shared/lib/format";
import type { WebhookRequestLog, WebhookRequestLogDetail } from "../types";
import { getLog, listLogs, pruneLogs } from "../services/logsApi";

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

export default function ChannelLogsPage() {
    const params = useParams();
    const webhookId = Number(params.id ?? 0);

    const [loading, setLoading] = React.useState(false);
    const [error, setError] = React.useState<Err>(null);

    const [items, setItems] = React.useState<WebhookRequestLog[]>([]);
    const [meta, setMeta] = React.useState<ApiMetaPagination>({
        page: 1,
        per_page: 20,
        total: 0,
        last_page: 1,
        from: null,
        to: null,
    });

    const [filters, setFilters] = React.useState({
        method: "all" as "all" | "GET" | "POST",
        ip: "",
        received_at: "",
    });

    const [detailOpen, setDetailOpen] = React.useState(false);
    const [detailLoading, setDetailLoading] = React.useState(false);
    const [detail, setDetail] = React.useState<WebhookRequestLogDetail | null>(null);

    const [pruneOpen, setPruneOpen] = React.useState(false);
    const [pruneDays, setPruneDays] = React.useState("30");
    const [pruneResult, setPruneResult] = React.useState<string>("");

    async function reload(next?: Partial<{ page: number; per_page: number }>) {
        const page = next?.page ?? meta.page;
        const per_page = next?.per_page ?? meta.per_page;

        setLoading(true);
        setError(null);
        try {
            const res = await listLogs(webhookId, {
                page,
                per_page,
                filters: {
                    method: filters.method === "all" ? undefined : (filters.method as any),
                    ip: filters.ip,
                    received_at: filters.received_at,
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
        if (!webhookId) return;
        reload({ page: 1 });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [webhookId]);

    async function openDetail(id: number) {
        setDetailOpen(true);
        setDetailLoading(true);
        setDetail(null);
        try {
            const d = await getLog(webhookId, id);
            setDetail(d);
        } catch (e) {
            setError(e);
            setDetailOpen(false);
        } finally {
            setDetailLoading(false);
        }
    }

    async function doPrune() {
        const days = Number(pruneDays);
        if (!Number.isFinite(days) || days < 0) return;
        if (!confirm(`Xoá logs cũ hơn ${days} ngày?`)) return;

        setLoading(true);
        setError(null);
        try {
            const res = await pruneLogs(webhookId, { days });
            setPruneResult(`Đã xoá: ${res.deleted} logs. Before: ${res.before}`);
            await reload({ page: 1 });
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
                <div className="flex-1 min-w-0">
                    <div className="text-xl font-extrabold tracking-tight text-slate-900 mb-1">Logs webhook #{webhookId}</div>
                    <div className="text-xs sm:text-sm text-slate-500 font-medium">Theo dõi các request nhận về (headers/query/body) để debug và kiểm tra tích hợp.</div>
                </div>
                <div className="flex items-center gap-2 shrink-0">
                    <Link className="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors" to="/channels">
                        Quay lại
                    </Link>
                    <Button variant="ghost" className="h-8 text-xs font-bold" onClick={() => reload()} disabled={loading}>
                        Tải lại
                    </Button>
                    <Button variant="primary" className="h-8 text-xs font-bold" onClick={() => setPruneOpen(true)} disabled={loading}>
                        Prune
                    </Button>
                </div>
            </div>

            {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}
            {pruneResult ? <Alert tone="success" title="Prune thành công" details={pruneResult} /> : null}

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
                        <div className="text-xs font-medium text-slate-600">Method</div>
                        <Select className="mt-1" value={filters.method} onChange={(e) => setFilters({ ...filters, method: e.target.value as any })}>
                            <option value="all">Tất cả</option>
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                        </Select>
                    </div>
                    <div>
                        <div className="text-xs font-medium text-slate-600">IP</div>
                        <Input className="mt-1" value={filters.ip} onChange={(e) => setFilters({ ...filters, ip: e.target.value })} placeholder="127.0.0.1" />
                    </div>
                    <div>
                        <div className="text-xs font-medium text-slate-600">Received at (from,to)</div>
                        <Input
                            className="mt-1"
                            value={filters.received_at}
                            onChange={(e) => setFilters({ ...filters, received_at: e.target.value })}
                            placeholder="2026-03-01,2026-03-31"
                        />
                    </div>
                </div>
            </Card>

            <Card title="Danh sách Logs" bodyClassName="p-0 sm:p-6" className="overflow-hidden">
                <div className="hidden sm:block overflow-auto">
                    <table className="ui-table">
                        <thead className="ui-thead">
                            <tr>
                                <th className="ui-th">ID</th>
                                <th className="ui-th">Method</th>
                                <th className="ui-th w-32">IP</th>
                                <th className="ui-th w-44">Received at</th>
                                <th className="ui-th">Body Preview</th>
                            </tr>
                        </thead>
                        <tbody>
                            {items.map((it) => (
                                <tr
                                    key={it.id}
                                    className="ui-tr cursor-pointer"
                                    onClick={() => openDetail(it.id)}
                                >
                                    <td className="ui-td font-bold text-slate-900">#{it.id}</td>
                                    <td className="ui-td">
                                        <span className={`px-2 py-0.5 rounded-md text-[10px] font-bold ${it.method === 'POST' ? 'bg-indigo-50 text-indigo-600' : 'bg-sky-50 text-sky-600'}`}>
                                            {it.method}
                                        </span>
                                    </td>
                                    <td className="ui-td text-slate-500 font-mono text-[11px]">{it.ip ?? "-"}</td>
                                    <td className="ui-td text-slate-600 font-medium whitespace-nowrap">{formatDateTime(it.received_at)}</td>
                                    <td className="ui-td text-slate-400 font-mono text-[11px]">{shortText(it.body_preview ?? "", 80)}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {/* Mobile list view */}
                <div className="sm:hidden divide-y divide-slate-100">
                    {items.map((it) => (
                        <div 
                            key={it.id} 
                            className="p-4 active:bg-slate-50 flex items-center justify-between gap-4"
                            onClick={() => openDetail(it.id)}
                        >
                            <div className="min-w-0">
                                <div className="flex items-center gap-2 mb-1">
                                    <span className="font-bold text-slate-900 text-sm">#{it.id}</span>
                                    <span className={`px-1.5 py-0.5 rounded text-[10px] font-bold uppercase ${it.method === 'POST' ? 'bg-indigo-50 text-indigo-600' : 'bg-sky-50 text-sky-600'}`}>
                                        {it.method}
                                    </span>
                                </div>
                                <div className="text-[11px] text-slate-500 font-medium mb-1">{formatDateTime(it.received_at)}</div>
                                <div className="text-[10px] text-slate-400 font-mono truncate">{shortText(it.body_preview ?? "", 40)}</div>
                            </div>
                            <div className="text-slate-300">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>
                    ))}
                </div>

                {items.length === 0 ? (
                    <div className="py-12 text-center text-slate-500 italic text-sm">
                        Không có dữ liệu log nào được ghi nhận.
                    </div>
                ) : null}

                <div className="px-4 pb-4 sm:p-0">
                    <Pagination
                        meta={meta}
                        onChange={(next) => {
                            setMeta((m) => ({ ...m, page: next.page, per_page: next.per_page }));
                            reload(next);
                        }}
                    />
                </div>
            </Card>

            <Modal open={detailOpen} title={detail ? `Log #${detail.id}` : "Chi tiết log"} onClose={() => setDetailOpen(false)}>
                {detailLoading ? <div className="text-sm text-slate-600">Đang tải...</div> : null}
                {detail ? (
                    <div className="space-y-3">
                        <div className="grid grid-cols-1 gap-2 md:grid-cols-3">
                            <Info label="Method" value={detail.method} />
                            <Info label="IP" value={detail.ip ?? "-"} />
                            <Info label="Received at" value={detail.received_at ?? "-"} />
                        </div>
                        <div>
                            <div className="text-xs font-medium text-slate-600">Headers</div>
                            <pre className="mt-1 overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs">
                                {JSON.stringify(detail.headers ?? {}, null, 2)}
                            </pre>
                        </div>
                        <div>
                            <div className="text-xs font-medium text-slate-600">Query</div>
                            <pre className="mt-1 overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs">
                                {JSON.stringify(detail.query ?? {}, null, 2)}
                            </pre>
                        </div>
                        <div>
                            <div className="text-xs font-medium text-slate-600">Body</div>
                            <pre className="mt-1 overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs">
                                {detail.body ?? ""}
                            </pre>
                        </div>
                    </div>
                ) : null}
            </Modal>

            <Modal
                open={pruneOpen}
                title="Prune logs"
                onClose={() => setPruneOpen(false)}
                footer={
                    <div className="flex items-center justify-end gap-2">
                        <Button variant="ghost" onClick={() => setPruneOpen(false)}>
                            Huỷ
                        </Button>
                        <Button variant="danger" onClick={doPrune} disabled={loading}>
                            Xoá
                        </Button>
                    </div>
                }
            >
                <Alert
                    tone="warning"
                    title="Cẩn thận"
                    details="Prune sẽ xoá log cũ khỏi DB. Bạn nên xuất log ra hệ thống lưu trữ khác nếu cần lưu dài hạn."
                />
                <div className="mt-3">
                    <div className="text-xs font-medium text-slate-600">Xoá log cũ hơn N ngày</div>
                    <Input className="mt-1" value={pruneDays} onChange={(e) => setPruneDays(e.target.value)} placeholder="30" />
                </div>
            </Modal>
        </div>
    );
}

function Info(props: { label: string; value: string }) {
    return (
        <div className="rounded-lg border border-slate-100 bg-white px-3 py-2">
            <div className="text-xs text-slate-500">{props.label}</div>
            <div className="text-sm font-medium">{props.value || "-"}</div>
        </div>
    );
}
