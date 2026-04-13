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
import type { WebhookRequestLog } from "../types";
import { listLogs, pruneLogs } from "../services/logsApi";
import LogDetailModal from "../components/LogDetailModal";

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

    const [mobileFiltersOpen, setMobileFiltersOpen] = React.useState(false);

    const [detailOpen, setDetailOpen] = React.useState(false);
    const [selectedRequestId, setSelectedRequestId] = React.useState<number | null>(null);

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
            // Tự động đóng lọc trên mobile sau khi áp dụng
            setMobileFiltersOpen(false);
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

    function openDetail(id: number) {
        setSelectedRequestId(id);
        setDetailOpen(true);
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
                    <Link className="hidden sm:block text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors" to="/channels">
                        Quay lại
                    </Link>
                    <Button 
                        variant="ghost" 
                        className={`md:hidden h-8 w-8 !p-0 ${mobileFiltersOpen ? 'bg-slate-100 text-sky-600' : ''}`} 
                        onClick={() => setMobileFiltersOpen(!mobileFiltersOpen)}
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    </Button>
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

            <div className={`${mobileFiltersOpen ? 'block' : 'hidden'} md:block`}>
                <Card
                    title="Bộ lọc"
                    bodyClassName="p-4 md:p-6"
                    actions={
                        <div className="flex items-center gap-2">
                            <Button variant="ghost" className="md:hidden h-8 text-xs font-bold" onClick={() => setMobileFiltersOpen(false)}>
                                Đóng
                            </Button>
                            <Button variant="primary" className="h-8 text-xs font-bold" onClick={() => reload({ page: 1 })} disabled={loading}>
                                Áp dụng
                            </Button>
                        </div>
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
            </div>

            <Card 
                title="Danh sách Logs" 
                bodyClassName="p-0 md:p-6" 
                className="md:shadow-md md:border md:bg-white shadow-none border-none bg-transparent overflow-hidden"
            >
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

                {/* Mobile list view: Modern & Clean Style */}
                <div className="sm:hidden space-y-4 pt-2">
                    {items.map((it) => (
                        <div 
                            key={it.id} 
                            className="bg-white rounded-2xl border border-slate-100/50 p-4 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.04)] active:scale-[0.98] transition-all"
                            onClick={() => openDetail(it.id)}
                        >
                            <div className="flex items-start justify-between mb-3">
                                <div className="flex items-center gap-2">
                                    <div className="h-8 w-8 rounded-lg bg-slate-50 flex items-center justify-center text-xs font-bold text-slate-400 border border-slate-100">
                                        #{it.id}
                                    </div>
                                    <span className={`px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider ${it.method === 'POST' ? 'bg-indigo-50 text-indigo-600' : 'bg-sky-50 text-sky-600'}`}>
                                        {it.method}
                                    </span>
                                </div>
                                <div className="text-slate-300">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                            
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <div className="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Thời gian</div>
                                    <div className="text-xs font-semibold text-slate-700">{formatDateTime(it.received_at)}</div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Địa chỉ IP</div>
                                    <div className="text-[11px] font-mono text-slate-500">{it.ip ?? "Unknown"}</div>
                                </div>
                                <div className="mt-3 p-3 rounded-xl bg-slate-50/50 border border-slate-100/50">
                                    <div className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Body Preview</div>
                                    <div className="text-[11px] text-slate-500 font-mono leading-relaxed line-clamp-2">
                                        {it.body_preview ? shortText(it.body_preview, 100) : "No body content"}
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                {items.length === 0 ? (
                    <div className="py-12 text-center text-slate-400 bg-white md:bg-transparent rounded-2xl border border-dashed border-slate-200">
                        <div className="mb-2 flex justify-center text-slate-200">
                            <svg className="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div className="text-sm font-medium italic">Không có dữ liệu log nào được ghi nhận.</div>
                    </div>
                ) : null}

                <div className="mt-4 md:mt-0">
                    <Pagination
                        meta={meta}
                        onChange={(next) => {
                            setMeta((m) => ({ ...m, page: next.page, per_page: next.per_page }));
                            reload(next);
                        }}
                    />
                </div>
            </Card>

            <LogDetailModal 
                open={detailOpen} 
                onClose={() => setDetailOpen(false)} 
                webhookId={webhookId}
                requestId={selectedRequestId}
            />

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
