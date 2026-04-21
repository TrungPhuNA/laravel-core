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

const FIELD_LABELS: Record<string, string> = {
    request_name: "Tên yêu cầu",
    problem: "Vấn đề/nhu cầu",
    product_link: "Link giới thiệu sản phẩm",
    current_sales_volume: "Sản lượng bán hiện tại",
    unique_selling_point: "Điểm khác biệt sản phẩm",
    customer_segment: "Phân khúc khách hàng",
    target_market: "Thị trường tập trung",
    promotion_program: "Chương trình khuyến mãi",
    after_sales_policy: "Chính sách hậu mãi",
    sales_channels: "Các kênh bán hàng",
    conversion_rate: "Tỉ lệ chuyển đổi",
    expected_goal: "Mục tiêu kỳ vọng",
    selected_solution: "Giải pháp lựa chọn",
    expected_start_time: "Thời gian bắt đầu",
    expected_end_time: "Thời gian kết thúc",
    budget: "Ngân sách",
    user_flow: "Luồng người dùng",
    contact_name: "Họ và tên",
    contact_email: "Email liên hệ",
    contact_phone: "Số điện thoại",
    company_name: "Tên công ty",
    campaign_source: "Campaign Source",
    campaign_medium: "Campaign Medium",
    campaign_name: "Campaign Name",
    campaign_content: "Campaign Content",
    note: "Ghi chú",
    total_amount: "Tổng tiền đơn hàng",
    payment_account: "Tài khoản thanh toán",
    contract: "Số hợp đồng/Link",
    order_id: "ID đơn hàng",
};

function RenderStructuredData({ data }: { data: any }) {
    if (!data || typeof data !== "object" || Array.isArray(data)) {
        return <div className="text-slate-400 italic text-xs">Không có dữ liệu hoặc định dạng không hỗ trợ hiển thị bảng.</div>;
    }

    return (
        <div className="border border-slate-100 rounded-xl overflow-hidden shadow-sm">
            <table className="min-w-full divide-y divide-slate-50">
                <tbody className="bg-white divide-y divide-slate-50">
                    {Object.entries(data).map(([key, value]) => (
                        <tr key={key} className="hover:bg-slate-50/50 transition-colors">
                            <td className="px-4 py-2.5 text-xs bg-slate-50/30 w-1/3 border-r border-slate-50">
                                <div className="font-bold text-slate-600">{FIELD_LABELS[key] || key}</div>
                                <div className="text-[10px] text-slate-400 font-mono mt-0.5">{key}</div>
                            </td>
                            <td className="px-4 py-2.5 text-xs text-slate-700 break-all font-medium">
                                {typeof value === "object" ? (
                                    <pre className="text-[10px] bg-slate-50 p-2 rounded">{JSON.stringify(value, null, 2)}</pre>
                                ) : (
                                    String(value ?? "—")
                                )}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
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
    const [viewMode, setViewMode] = React.useState<"table" | "json">("table");

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

            <Modal open={detailOpen} onClose={() => setDetailOpen(false)} title={detail ? `Chi tiết Dispatch #${detail.id}` : "Đang tải..."} size="xxl">
                {detailLoading && (
                    <div className="py-20 flex flex-col items-center justify-center space-y-3">
                        <div className="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
                        <div className="text-sm text-slate-500 font-medium">Đang lấy dữ liệu log...</div>
                    </div>
                )}
                {!detailLoading && detail && (
                    <div className="space-y-6">
                        {/* Header Info Cards */}
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div className="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <div className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">TRẠNG THÁI</div>
                                <div className="flex items-center gap-2">
                                    <Badge tone={badgeTone(detail.status)}>{detail.status.toUpperCase()}</Badge>
                                    <span className="text-xs font-mono text-slate-500">HTTP {detail.response_status ?? "—"}</span>
                                </div>
                            </div>
                            <div className="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <div className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">ĐIỂM ĐẾN (DEST)</div>
                                <div className="text-sm font-bold text-slate-700">ID #{detail.destination_id}</div>
                                <div className="text-[10px] text-slate-500 font-mono mt-0.5 truncate">{detail.webhook_request_id ? `request #${detail.webhook_request_id}` : ""}</div>
                            </div>
                            <div className="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <div className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">THỜI GIAN / XỬ LÝ</div>
                                <div className="text-sm font-bold text-slate-700">{formatDateTime(detail.created_at ?? null)}</div>
                                <div className="text-[10px] text-sky-600 font-bold mt-0.5 uppercase">{detail.duration_ms != null ? `${detail.duration_ms}ms` : "—"}</div>
                            </div>
                        </div>

                        {detail.error_message && (
                            <div className="bg-rose-50 border border-rose-100 p-4 rounded-2xl">
                                <div className="flex items-center gap-2 text-rose-600 font-bold text-xs mb-1 uppercase tracking-tight">
                                    <span className="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                    {detail.error_type || "Error Exception"}
                                </div>
                                <div className="text-xs text-rose-700 font-mono break-words leading-relaxed">{detail.error_message}</div>
                            </div>
                        )}

                        <div className="space-y-3">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <span className="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                    <h3 className="text-sm font-black text-slate-800 tracking-tight">Dữ liệu Request Body</h3>
                                </div>
                                <div className="flex bg-slate-100 p-0.5 rounded-lg border border-slate-200">
                                    <button
                                        className={`px-3 py-1 text-[10px] font-bold rounded-md transition-all ${viewMode === "table" ? "bg-white text-sky-600 shadow-sm" : "text-slate-500 hover:text-slate-700"}`}
                                        onClick={() => setViewMode("table")}
                                    >
                                        DẠNG BẢNG
                                    </button>
                                    <button
                                        className={`px-3 py-1 text-[10px] font-bold rounded-md transition-all ${viewMode === "json" ? "bg-white text-sky-600 shadow-sm" : "text-slate-500 hover:text-slate-700"}`}
                                        onClick={() => setViewMode("json")}
                                    >
                                        JSON THÔ
                                    </button>
                                </div>
                            </div>

                            {viewMode === "table" ? (
                                <RenderStructuredData data={typeof detail.request_body === "string" ? (function() {
                                    try { return JSON.parse(detail.request_body); } catch(e) { return { _raw: detail.request_body }; }
                                })() : detail.request_body} />
                            ) : (
                                <div className="relative group">
                                    <textarea
                                        className="ui-input w-full font-mono text-[11px] min-h-[160px] bg-slate-900 text-slate-100 border-none rounded-2xl p-4 shadow-inner leading-relaxed focus:ring-2 focus:ring-sky-500/50"
                                        readOnly
                                        value={detail.request_body ? (function() {
                                            try { return JSON.stringify(JSON.parse(detail.request_body), null, 2); } catch(e) { return detail.request_body; }
                                        })() : ""}
                                    />
                                    <div className="absolute top-3 right-3 text-[10px] text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity uppercase font-bold">READ ONLY</div>
                                </div>
                            )}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div className="space-y-2">
                                <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                                    <span className="w-1 h-1 rounded-full bg-slate-400"></span>
                                    Response Headers
                                </div>
                                <textarea
                                    className="ui-input w-full font-mono text-[10px] min-h-[110px] bg-slate-50 border-slate-200 rounded-2xl p-3 leading-tight"
                                    readOnly
                                    value={prettyJson(detail.response_headers ?? {})}
                                />
                            </div>
                            <div className="space-y-2">
                                <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                                    <span className="w-1 h-1 rounded-full bg-slate-400"></span>
                                    Response Body
                                </div>
                                <textarea
                                    className="ui-input w-full font-mono text-[10px] min-h-[110px] bg-slate-50 border-slate-200 rounded-2xl p-3 leading-tight"
                                    readOnly
                                    value={detail.response_body ? (function() {
                                        try { return JSON.stringify(JSON.parse(detail.response_body), null, 2); } catch(e) { return detail.response_body; }
                                    })() : "—"}
                                />
                            </div>
                        </div>

                        <div className="flex justify-end pt-2">
                            <Button onClick={() => setDetailOpen(false)} size="sm">Đóng</Button>
                        </div>
                    </div>
                )}
            </Modal>
        </div>
    );
}

