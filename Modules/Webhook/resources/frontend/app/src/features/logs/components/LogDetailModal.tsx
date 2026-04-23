import React from "react";
import Modal from "@shared/ui/Modal";
import Button from "@shared/ui/Button";
import { formatDateTime } from "@shared/lib/format";
import { getLog } from "../services/logsApi";
import type { WebhookRequestLogDetail } from "../types";

interface Props {
    open: boolean;
    onClose: () => void;
    webhookId: number;
    requestId: number | null;
}

export default function LogDetailModal({ open, onClose, webhookId, requestId }: Props) {
    const [loading, setLoading] = React.useState(false);
    const [detail, setDetail] = React.useState<WebhookRequestLogDetail | null>(null);
    const [viewMode, setViewMode] = React.useState<"fancy" | "raw">("fancy");

    React.useEffect(() => {
        if (open && requestId && webhookId) {
            fetchDetail();
        } else {
            setDetail(null);
        }
    }, [open, requestId, webhookId]);

    const fetchDetail = async () => {
        if (!requestId || !webhookId) return;
        setLoading(true);
        try {
            const res = await getLog(webhookId, requestId);
            setDetail(res);
        } catch (e) {
            console.error(e);
        } finally {
            setLoading(false);
        }
    };

    return (
        <Modal
            open={open}
            onClose={onClose}
            title={detail ? `Chi tiết Log #${detail.id}` : "Đang tải..."}
            className="max-w-7xl"
            footer={
                <div className="flex items-center justify-between w-full">
                    <div className="flex gap-2">
                        <Button 
                            variant={viewMode === "fancy" ? "primary" : "ghost"} 
                            className="h-8 text-[11px] font-bold"
                            onClick={() => setViewMode("fancy")}
                        >
                            Dạng bảng
                        </Button>
                        <Button 
                            variant={viewMode === "raw" ? "primary" : "ghost"} 
                            className="h-8 text-[11px] font-bold"
                            onClick={() => setViewMode("raw")}
                        >
                            JSON Thô
                        </Button>
                    </div>
                    <Button variant="ghost" onClick={onClose} className="h-8 text-xs font-bold">
                        Đóng
                    </Button>
                </div>
            }
        >
            {loading && (
                <div className="py-20 flex flex-col items-center justify-center text-slate-400">
                    <div className="w-8 h-8 border-4 border-slate-200 border-t-sky-500 rounded-full animate-spin mb-4"></div>
                    <div className="text-sm font-medium animate-pulse">Đang truy xuất dữ liệu chi tiết...</div>
                </div>
            )}

            {!loading && detail && (
                <div className="space-y-6">
                    {/* Header Info */}
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <StatItem label="Trạng thái" value={detail.status === 'success' ? 'Success' : 'Failed'} color={detail.status === 'success' ? 'emerald' : 'rose'} />
                        <StatItem label="Phương thức" value={detail.method} color="indigo" />
                        <StatItem label="Địa chỉ IP" value={detail.ip ?? "-"} color="slate" fontMono />
                        <StatStatStat statLabel="Thời gian" value={formatDateTime(detail.received_at)} color="slate" />
                    </div>
                    {detail.error_message && (
                        <div className="p-3 rounded-2xl border bg-rose-50 border-rose-100 text-rose-700 mt-3">
                            <div className="text-[10px] font-bold uppercase tracking-widest mb-1 opacity-80">
                                Lỗi hệ thống {detail.error_type ? `(${detail.error_type})` : ''}
                            </div>
                            <div className="text-sm font-medium">{detail.error_message}</div>
                        </div>
                    )}

                    {/* Meta Data (Headers & Query) */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <CollapsibleSection title="Headers" count={Object.keys(detail.headers ?? {}).length}>
                            <div className="text-[11px] font-mono bg-slate-900 text-slate-300 p-4 rounded-xl overflow-auto max-h-60">
                                {JSON.stringify(detail.headers, null, 2)}
                            </div>
                        </CollapsibleSection>
                        <CollapsibleSection title="Query Parameters" count={Object.keys(detail.query ?? {}).length}>
                            <div className="text-[11px] font-mono bg-slate-50 text-slate-600 p-4 rounded-xl overflow-auto border border-slate-100">
                                {JSON.stringify(detail.query, null, 2)}
                            </div>
                        </CollapsibleSection>
                    </div>

                    {/* Main Body Content */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div className="flex items-center justify-between mb-3">
                                <h3 className="text-sm font-bold text-slate-800 flex items-center gap-2">
                                    <span className="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                    Request Body (Gốc)
                                </h3>
                            </div>

                            {viewMode === "raw" ? (
                                <pre className="p-4 bg-slate-900 text-sky-400 rounded-2xl font-mono text-xs overflow-auto max-h-[500px] border border-slate-800 shadow-inner">
                                    {detail.body}
                                </pre>
                            ) : (
                                <div className="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
                                    <RecursiveDataRenderer data={tryParse(detail.body)} />
                                </div>
                            )}
                        </div>

                        <div>
                            <div className="flex items-center justify-between mb-3">
                                <h3 className="text-sm font-bold text-slate-800 flex items-center gap-2">
                                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Mapped Payload (Đã xử lý)
                                </h3>
                                {viewMode === "fancy" && (
                                    <span className="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md uppercase tracking-wider">
                                        Dữ liệu Cấu trúc
                                    </span>
                                )}
                            </div>

                            {detail.mapped_payload ? (
                                viewMode === "raw" ? (
                                    <pre className="p-4 bg-slate-900 text-emerald-400 rounded-2xl font-mono text-xs overflow-auto max-h-[500px] border border-slate-800 shadow-inner">
                                        {JSON.stringify(detail.mapped_payload, null, 2)}
                                    </pre>
                                ) : (
                                    <div className="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
                                        <RecursiveDataRenderer data={detail.mapped_payload} />
                                    </div>
                                )
                            ) : (
                                <div className="p-6 bg-slate-50 border border-slate-100 rounded-2xl text-center text-slate-400 text-sm italic flex flex-col items-center justify-center min-h-[120px]">
                                    <svg className="w-8 h-8 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    Không có dữ liệu mapped
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </Modal>
    );
}

// --- Sub-components ---

function StatItem({ label, value, color, fontMono }: { label: string, value: string, color: string, fontMono?: boolean }) {
    const colorClasses: Record<string, string> = {
        indigo: "bg-indigo-50 text-indigo-700 border-indigo-100",
        slate: "bg-slate-50 text-slate-700 border-slate-100",
        emerald: "bg-emerald-50 text-emerald-700 border-emerald-100",
        rose: "bg-rose-50 text-rose-700 border-rose-100",
    };
    return (
        <div className={`p-3 rounded-2xl border ${colorClasses[color]}`}>
            <div className="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-1">{label}</div>
            <div className={`text-sm font-bold ${fontMono ? 'font-mono' : ''}`}>{value}</div>
        </div>
    );
}

function StatStatStat({ statLabel, value, color }: { statLabel: string, value: string, color: string }) {
    return <StatItem label={statLabel} value={value} color={color} />;
}

function CollapsibleSection({ title, children, count }: { title: string, children: React.ReactNode, count: number }) {
    const [isOpen, setIsOpen] = React.useState(false);
    return (
        <div className="border border-slate-100 rounded-2xl overflow-hidden bg-white">
            <button 
                onClick={() => setIsOpen(!isOpen)}
                className="w-full flex items-center justify-between p-4 text-left hover:bg-slate-50 transition-colors"
            >
                <div className="flex items-center gap-2">
                    <span className="text-xs font-bold text-slate-700">{title}</span>
                    <span className="px-1.5 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-500">{count}</span>
                </div>
                <svg className={`w-4 h-4 text-slate-400 transition-transform ${isOpen ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            {isOpen && <div className="p-4 pt-0">{children}</div>}
        </div>
    );
}

function RecursiveDataRenderer({ data, depth = 0 }: { data: any, depth?: number }) {
    if (data === null || data === undefined) return <span className="text-slate-400 italic">null</span>;

    // Handle Objects (Hash Maps)
    if (typeof data === 'object' && !Array.isArray(data)) {
        return (
            <table className="w-full text-xs border-collapse">
                <tbody>
                    {Object.entries(data).map(([key, value], idx) => (
                        <tr key={key} className={idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/30'}>
                            <td className="py-2.5 px-4 font-mono font-bold text-slate-500 border-r border-slate-50 w-1/3 align-top">
                                {key}
                            </td>
                            <td className="py-2.5 px-4 text-slate-700 align-top">
                                <RecursiveDataRenderer data={value} depth={depth + 1} />
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        );
    }

    // Handle Arrays
    if (Array.isArray(data)) {
        if (data.length === 0) return <span className="text-slate-400 italic">Mảng rỗng []</span>;
        
        return (
            <div className="space-y-3 p-3 bg-slate-50/50 rounded-xl m-1">
                {data.map((item, idx) => (
                    <div key={idx} className="bg-white border border-slate-100 rounded-xl overflow-hidden shadow-sm">
                        <div className="px-3 py-1 bg-slate-50 border-b border-slate-100 text-[9px] font-bold text-slate-400 uppercase tracking-tighter">
                            Item #{idx + 1}
                        </div>
                        <div className="p-0">
                            <RecursiveDataRenderer data={item} depth={depth + 1} />
                        </div>
                    </div>
                ))}
            </div>
        );
    }

    // Handle Primitives (String, Number, Boolean)
    if (typeof data === 'boolean') {
        return (
            <span className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase ${data ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'}`}>
                {data ? 'true' : 'false'}
            </span>
        );
    }

    if (typeof data === 'number') {
        return <span className="font-mono font-bold text-indigo-600">{data.toLocaleString()}</span>;
    }

    const str = String(data);
    if (str.startsWith('http')) {
        return <a href={str} target="_blank" rel="noreferrer" className="text-sky-600 hover:underline break-all">{str}</a>;
    }

    return <span className="whitespace-pre-wrap break-all leading-relaxed">{str}</span>;
}

function tryParse(body: string | null | undefined): any {
    if (!body) return null;
    try {
        return JSON.parse(body);
    } catch (e) {
        return body;
    }
}
