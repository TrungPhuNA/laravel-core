import React from "react";
import Button from "@shared/ui/Button";
import Input from "@shared/ui/Input";
import Modal from "@shared/ui/Modal";
import Pagination from "@shared/ui/Pagination";
import Card from "@shared/ui/Card";
import Alert from "@shared/ui/Alert";
import type { ApiMetaPagination } from "@shared/http/types";
import { useAuth } from "../../../shared/state/auth";
import {
    checkDomain,
    deleteDomain,
    getDomainLogs,
    importDomains,
    listDomains,
    updateDomain,
} from "../services/domainsApi";
import type { Domain, DomainCheckLog } from "../types";
import DomainBadge from "../components/DomainBadge";

function fmtDate(iso: string | null): string {
    if (!iso) return "-";
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return "-";
    return d.toLocaleDateString("vi-VN", { day: "2-digit", month: "2-digit", year: "numeric" });
}

function fmtDateTime(iso: string | null): string {
    if (!iso) return "-";
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return "-";
    return d.toLocaleString("vi-VN", { day: "2-digit", month: "2-digit", year: "2-digit", hour: "2-digit", minute: "2-digit" });
}

function daysLabel(d: Domain): string {
    if (d.days_remaining === null || d.days_remaining === undefined) return "-";
    if (d.days_remaining < 0) return "Đã hết hạn";
    return `${d.days_remaining} ngày`;
}

export default function DomainsPage() {
    const auth = useAuth();

    const [items, setItems] = React.useState<Domain[]>([]);
    const [meta, setMeta] = React.useState<ApiMetaPagination | null>(null);
    const [loading, setLoading] = React.useState(false);
    const [search, setSearch] = React.useState("");
    const [searchDraft, setSearchDraft] = React.useState("");
    const [page, setPage] = React.useState(1);
    const [perPage, setPerPage] = React.useState(20);

    const [addOpen, setAddOpen] = React.useState(false);
    const [addText, setAddText] = React.useState("");
    const [addLoading, setAddLoading] = React.useState(false);

    const [checkingIds, setCheckingIds] = React.useState<Set<number>>(new Set());
    const [checkingAll, setCheckingAll] = React.useState(false);

    const [logsDomain, setLogsDomain] = React.useState<Domain | null>(null);
    const [logs, setLogs] = React.useState<DomainCheckLog[]>([]);
    const [logsLoading, setLogsLoading] = React.useState(false);

    const [editDomain, setEditDomain] = React.useState<Domain | null>(null);
    const [editNote, setEditNote] = React.useState("");
    const [editActive, setEditActive] = React.useState(true);

    const [error, setError] = React.useState<string | null>(null);

    const baseSort = "expires_at";

    const load = React.useCallback(
        async (nextPage?: number) => {
            if (!auth.hasToken) return;
            setLoading(true);
            setError(null);
            try {
                const res = await listDomains({
                    page: nextPage ?? page,
                    per_page: perPage,
                    search: searchDraft || undefined,
                    sort: baseSort,
                });
                setItems(res.data?.items ?? []);
                setMeta(res.data?.meta?.pagination ?? null);
            } catch (e: unknown) {
                setError((e as { message?: string })?.message ?? "Không tải được danh sách domain");
            } finally {
                setLoading(false);
            }
        },
        [auth.hasToken, page, perPage, searchDraft]
    );

    React.useEffect(() => {
        load();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [auth.hasToken, page, perPage, searchDraft]);

    async function handleAdd() {
        setAddLoading(true);
        setError(null);
        try {
            const lines = addText
                .split(/\r?\n|,/)
                .map((s) => s.trim())
                .filter(Boolean);
            if (lines.length === 0) {
                setError("Vui lòng nhập ít nhất 1 domain.");
                return;
            }
            await importDomains(lines);
            setAddText("");
            setAddOpen(false);
            setPage(1);
            load(1);
        } catch (e: unknown) {
            setError((e as { message?: string })?.message ?? "Thêm domain thất bại");
        } finally {
            setAddLoading(false);
        }
    }

    async function handleCheck(id: number) {
        setCheckingIds((prev) => new Set(prev).add(id));
        setError(null);
        try {
            await checkDomain(id);
            load();
        } catch (e: unknown) {
            setError((e as { message?: string })?.message ?? "Check domain thất bại");
            load();
        } finally {
            setCheckingIds((prev) => {
                const next = new Set(prev);
                next.delete(id);
                return next;
            });
        }
    }

    async function handleCheckAll() {
        setCheckingAll(true);
        setError(null);
        const ids = items.map((d) => d.id);
        for (const id of ids) {
            try {
                await checkDomain(id);
            } catch {
                // tiếp tục các domain còn lại
            }
        }
        setCheckingAll(false);
        load();
    }

    async function openLogs(domain: Domain) {
        setLogsDomain(domain);
        setLogs([]);
        setLogsLoading(true);
        try {
            setLogs(await getDomainLogs(domain.id, 20));
        } catch {
            setLogs([]);
        } finally {
            setLogsLoading(false);
        }
    }

    function openEdit(domain: Domain) {
        setEditDomain(domain);
        setEditNote(domain.note ?? "");
        setEditActive(domain.is_active);
    }

    async function saveEdit() {
        if (!editDomain) return;
        setError(null);
        try {
            await updateDomain(editDomain.id, { note: editNote, is_active: editActive });
            setEditDomain(null);
            load();
        } catch (e: unknown) {
            setError((e as { message?: string })?.message ?? "Lưu thất bại");
        }
    }

    async function handleDelete(domain: Domain) {
        if (!window.confirm(`Xoá domain "${domain.domain}" khỏi danh sách theo dõi?`)) return;
        setError(null);
        try {
            await deleteDomain(domain.id);
            load();
        } catch (e: unknown) {
            setError((e as { message?: string })?.message ?? "Xoá thất bại");
        }
    }

    return (
        <div className="space-y-4">
            <div className="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 className="text-lg font-semibold">Danh sách domain</h1>
                    <p className="text-sm text-slate-500">Theo dõi thời gian hết hạn domain (tra cứu RDAP/WHOIS).</p>
                </div>
                <div className="flex flex-wrap items-center gap-2">
                    <Input
                        className="w-56"
                        placeholder="Tìm domain..."
                        value={searchDraft}
                        onChange={(e) => setSearchDraft(e.target.value)}
                        onKeyDown={(e) => {
                            if (e.key === "Enter") {
                                setSearch(searchDraft);
                                setPage(1);
                            }
                        }}
                    />
                    <Button variant="ghost" onClick={handleCheckAll} disabled={checkingAll || items.length === 0}>
                        {checkingAll ? "Đang check..." : "Check tất cả"}
                    </Button>
                    <Button onClick={() => setAddOpen(true)}>+ Thêm domain</Button>
                </div>
            </div>

            {error ? <Alert tone="danger" title={error} /> : null}

            <Card className="overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-500">
                                <th className="px-4 py-3">Domain</th>
                                <th className="px-4 py-3">Ngày hết hạn</th>
                                <th className="px-4 py-3">Còn lại</th>
                                <th className="px-4 py-3">Registrar</th>
                                <th className="px-4 py-3">Trạng thái</th>
                                <th className="px-4 py-3 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            {loading && items.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="px-4 py-8 text-center text-slate-500">
                                        Đang tải...
                                    </td>
                                </tr>
                            ) : items.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="px-4 py-8 text-center text-slate-500">
                                        Chưa có domain nào.{auth.hasToken ? "" : " Vui lòng nhập token ở góc phải."}
                                    </td>
                                </tr>
                            ) : (
                                items.map((d) => (
                                    <tr key={d.id} className="border-b border-slate-50 hover:bg-slate-50/60">
                                        <td className="px-4 py-3">
                                            <div className="font-semibold">{d.domain}</div>
                                            {d.note ? <div className="text-xs text-slate-500">{d.note}</div> : null}
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap">{fmtDate(d.expires_at)}</td>
                                        <td className="px-4 py-3 whitespace-nowrap">{daysLabel(d)}</td>
                                        <td className="px-4 py-3 text-slate-600">{d.registrar ?? "-"}</td>
                                        <td className="px-4 py-3">
                                            <DomainBadge badge={d.badge} />
                                            {d.check_status === "error" && d.last_check_error ? (
                                                <div className="mt-1 max-w-[220px] truncate text-xs text-rose-600" title={d.last_check_error}>
                                                    {d.last_check_error}
                                                </div>
                                            ) : null}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex items-center justify-end gap-2">
                                                <Button
                                                    variant="ghost"
                                                    className="h-8 px-2 text-xs"
                                                    disabled={checkingIds.has(d.id) || checkingAll}
                                                    onClick={() => handleCheck(d.id)}
                                                >
                                                    {checkingIds.has(d.id) ? "Đang check..." : "Check now"}
                                                </Button>
                                                <Button variant="ghost" className="h-8 px-2 text-xs" onClick={() => openLogs(d)}>
                                                    Log
                                                </Button>
                                                <Button variant="ghost" className="h-8 px-2 text-xs" onClick={() => openEdit(d)}>
                                                    Sửa
                                                </Button>
                                                <Button variant="danger" className="h-8 px-2 text-xs" onClick={() => handleDelete(d)}>
                                                    Xoá
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                {meta ? (
                    <div className="px-4 py-3">
                        <Pagination meta={meta} onChange={(n) => {
                            setPage(n.page);
                            setPerPage(n.per_page);
                        }} />
                    </div>
                ) : null}
            </Card>

            {/* Modal thêm domain */}
            <Modal
                open={addOpen}
                title="Thêm domain"
                onClose={() => setAddOpen(false)}
                footer={
                    <div className="flex items-center justify-end gap-2">
                        <Button variant="ghost" onClick={() => setAddOpen(false)}>
                            Huỷ
                        </Button>
                        <Button variant="primary" onClick={handleAdd} disabled={addLoading}>
                            {addLoading ? "Đang thêm..." : "Thêm"}
                        </Button>
                    </div>
                }
            >
                <div className="text-sm text-slate-600">
                    Nhập 1 domain hoặc dán nhiều domain (mỗi dòng 1 domain). Có thể dán kèm <code>https://</code> và đường
                    dẫn, hệ thống sẽ tự chuẩn hoá.
                </div>
                <textarea
                    className="ui-input mt-3 min-h-[140px] w-full font-mono text-sm"
                    placeholder={"ví dụ: https://example.com/ \nexample.net\ntilo.vn"}
                    value={addText}
                    onChange={(e) => setAddText(e.target.value)}
                />
            </Modal>

            {/* Modal sửa domain */}
            <Modal
                open={editDomain !== null}
                title={editDomain ? `Sửa ${editDomain.domain}` : ""}
                onClose={() => setEditDomain(null)}
                footer={
                    <div className="flex items-center justify-end gap-2">
                        <Button variant="ghost" onClick={() => setEditDomain(null)}>
                            Huỷ
                        </Button>
                        <Button variant="primary" onClick={saveEdit}>
                            Lưu
                        </Button>
                    </div>
                }
            >
                <div className="space-y-3">
                    <div>
                        <div className="text-xs font-medium text-slate-600">Ghi chú</div>
                        <textarea
                            className="ui-input mt-1 w-full min-h-[80px] text-sm"
                            placeholder="Ghi chú về domain..."
                            value={editNote}
                            onChange={(e) => setEditNote(e.target.value)}
                        />
                    </div>
                    <label className="flex items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            checked={editActive}
                            onChange={(e) => setEditActive(e.target.checked)}
                        />
                        Theo dõi domain này
                    </label>
                </div>
            </Modal>

            {/* Modal lịch sử check */}
            <Modal
                open={logsDomain !== null}
                title={logsDomain ? `Lịch sử check: ${logsDomain.domain}` : ""}
                onClose={() => setLogsDomain(null)}
                className="max-w-4xl"
            >
                {logsLoading ? (
                    <div className="py-8 text-center text-sm text-slate-500">Đang tải...</div>
                ) : logs.length === 0 ? (
                    <div className="py-8 text-center text-sm text-slate-500">Chưa có lần check nào.</div>
                ) : (
                    <div className="space-y-3">
                        {logs.map((l) => (
                            <div key={l.id} className="rounded-lg border border-slate-100 p-3 text-sm">
                                <div className="flex flex-wrap items-center gap-2">
                                    <span
                                        className={
                                            l.status === "ok"
                                                ? "rounded bg-emerald-50 px-1.5 py-0.5 text-xs font-semibold text-emerald-700"
                                                : "rounded bg-rose-50 px-1.5 py-0.5 text-xs font-semibold text-rose-700"
                                        }
                                    >
                                        {l.status === "ok" ? "OK" : "LỖI"}
                                    </span>
                                    <span className="text-xs text-slate-500">{fmtDateTime(l.checked_at)}</span>
                                    {l.method ? <span className="text-xs text-slate-500">method: {l.method}</span> : null}
                                    {l.expires_at_found ? (
                                        <span className="text-xs text-slate-600">hết hạn: {fmtDate(l.expires_at_found)}</span>
                                    ) : null}
                                    {l.registrar ? <span className="text-xs text-slate-600">{l.registrar}</span> : null}
                                </div>
                                {l.error_message ? (
                                    <div className="mt-2 rounded bg-rose-50 px-2 py-1 text-xs text-rose-700">{l.error_message}</div>
                                ) : null}
                            </div>
                        ))}
                    </div>
                )}
            </Modal>
        </div>
    );
}