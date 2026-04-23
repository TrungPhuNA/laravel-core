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
import Dropdown from "@shared/ui/Dropdown";
import type { ApiMetaPagination, ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { formatDateTime, prettyJson, shortText } from "@shared/lib/format";
import { copyToClipboard } from "@shared/lib/clipboard";
import type { WebhookChannel } from "../types";
import { createChannel, deleteChannel, listChannels, rotateSecret, rotateToken, updateChannel } from "../services/webhooksApi";

// --- Icons ---
const IconPlus = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="M5 12h14" /><path d="M12 5v14" /></svg>
);
const IconRefresh = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8" /><path d="M21 3v5h-5" /></svg>
);
const IconCopy = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2" /><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" /></svg>
);
const IconExternal = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="M15 3h6v6" /><path d="M10 14 21 3" /><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" /></svg>
);
const IconTerminal = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><polyline points="4 17 10 11 4 5" /><line x1="12" x2="20" y1="19" y2="19" /></svg>
);
const IconFilter = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" /></svg>
);
const IconChart = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="20" x2="18" y2="10" /><line x1="12" y1="20" x2="12" y2="4" /><line x1="6" y1="20" x2="6" y2="14" /></svg>
);
const IconInfo = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10" /><path d="M12 16v-4" /><path d="M12 8h.01" /></svg>
);


type Err = ApiResponseFail | ApiResponseError | Error | unknown;

type ValidationField = {
    id: string;
    field: string;
    tokens: Array<{ id: string; token: string }>;
};

const VALIDATION_RULE_OPTIONS: Array<{ value: string; label: string; hint?: string }> = [
    { value: "required", label: "required" },
    { value: "nullable", label: "nullable" },
    { value: "sometimes", label: "sometimes" },
    { value: "email", label: "email" },
    { value: "string", label: "string" },
    { value: "numeric", label: "numeric" },
    { value: "integer", label: "integer" },
    { value: "boolean", label: "boolean" },
    { value: "array", label: "array" },
    { value: "date", label: "date" },
    { value: "url", label: "url" },
    { value: "uuid", label: "uuid" },
    { value: "ip", label: "ip" },
    { value: "json", label: "json", hint: "Validate chuỗi JSON hợp lệ" },
];

function newId() {
    return `${Date.now()}-${Math.random().toString(16).slice(2)}`;
}

function parseValidationRulesToFields(rules: unknown): ValidationField[] {
    if (!rules || typeof rules !== "object") return [];
    const anyRules = rules as Record<string, unknown>;

    const fields: ValidationField[] = [];
    for (const [field, ruleVal] of Object.entries(anyRules)) {
        if (typeof field !== "string" || field.trim() === "") continue;
        const ruleStr = typeof ruleVal === "string" ? ruleVal : "";
        const parts = ruleStr
            .split("|")
            .map((s) => s.trim())
            .filter(Boolean);

        fields.push({
            id: newId(),
            field,
            tokens: parts.map((token) => ({ id: newId(), token })),
        });
    }
    return fields;
}

function buildValidationRulesRecord(fields: ValidationField[]): Record<string, string> | undefined {
    const out: Record<string, string> = {};

    for (const f of fields) {
        const key = f.field.trim();
        if (key === "") continue;

        const tokenStr = f.tokens
            .map((t) => t.token.trim())
            .filter(Boolean)
            .join("|");

        if (tokenStr === "") continue;
        out[key] = tokenStr;
    }

    return Object.keys(out).length ? out : undefined;
}

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

function receiveUrlFor(publicId: string) {
    return `${window.location.origin}/api/v1/webhooks/receive/${publicId}`;
}

export default function ChannelsPage() {
    const [loading, setLoading] = React.useState(false);
    const [error, setError] = React.useState<Err>(null);
    const [rulesJsonOpen, setRulesJsonOpen] = React.useState(false);
    const [jsonRaw, setJsonRaw] = React.useState("");
    const [jsonError, setJsonError] = React.useState<string | null>(null);

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
        type: "default",
        is_active: true,
        allowed_methods: ["POST"] as Array<"GET" | "POST">,
        auth_type: "none" as "none" | "token" | "hmac",
        rotate_token: false,
        rotate_secret: false,
        description: "",
        validation_fields: [
            { id: newId(), field: "email", tokens: [{ id: newId(), token: "required" }, { id: newId(), token: "email" }] },
        ] as ValidationField[],
    });

    const [secretOpen, setSecretOpen] = React.useState(false);
    const [secretTitle, setSecretTitle] = React.useState("");
    const [secretValue, setSecretValue] = React.useState("");
    const [receiveUrl, setReceiveUrl] = React.useState("");
    const [receiveHelp, setReceiveHelp] = React.useState<string>("");

    const [urlOpen, setUrlOpen] = React.useState(false);
    const [urlValue, setUrlValue] = React.useState("");
    const [toast, setToast] = React.useState<string>("");
    const [mobileFiltersOpen, setMobileFiltersOpen] = React.useState(false);
    const [guideOpen, setGuideOpen] = React.useState(false);

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
            type: "default",
            is_active: true,
            allowed_methods: ["POST"],
            auth_type: "none",
            rotate_token: false,
            rotate_secret: false,
            description: "",
            validation_fields: [
                { id: newId(), field: "email", tokens: [{ id: newId(), token: "required" }, { id: newId(), token: "email" }] },
            ],
        });
        setRulesJsonOpen(false);
        setJsonRaw(prettyJson({ email: "required|email" }));
        setJsonError(null);
        setEditorOpen(true);
    }

    function openEdit(ch: WebhookChannel) {
        setEditorMode("edit");
        setEditor({
            id: ch.id,
            name: ch.name,
            type: ch.type ?? "default",
            is_active: ch.is_active,
            allowed_methods: (ch.allowed_methods && ch.allowed_methods.length ? (ch.allowed_methods as any) : ["GET", "POST"]),
            auth_type: ch.auth_type,
            rotate_token: false,
            rotate_secret: false,
            description: ch.description ?? "",
            validation_fields: ch.validation_rules ? parseValidationRulesToFields(ch.validation_rules) : [],
        });
        setRulesJsonOpen(false);
        setJsonRaw(prettyJson(ch.validation_rules || {}));
        setJsonError(null);
        setEditorOpen(true);
    }

    function showSecret(title: string, value: string, url: string) {
        setSecretTitle(title);
        setSecretValue(value);
        setReceiveUrl(url);
        setReceiveHelp("");
        setSecretOpen(true);
    }

    function openReceiveUrl(url: string) {
        setUrlValue(url);
        setUrlOpen(true);
    }

    function toastOnce(message: string) {
        setToast(message);
        window.setTimeout(() => setToast(""), 2500);
    }

    async function submitEditor() {
        setLoading(true);
        setError(null);
        try {
            const rules = buildValidationRulesRecord(editor.validation_fields);

            if (editorMode === "create") {
                const res = await createChannel({
                    name: editor.name,
                    type: editor.type,
                    is_active: editor.is_active,
                    allowed_methods: editor.allowed_methods,
                    auth_type: editor.auth_type,
                    description: editor.description.trim() === "" ? null : editor.description,
                    validation_rules: rules,
                });
                setEditorOpen(false);
                await reload({ page: 1 });

                if (res.auth_token) {
                    setReceiveHelp(
                        "Dùng header `X-Webhook-Token: <token>` hoặc query `?token=<token>` khi gọi receiver."
                    );
                    showSecret("Token Webhook (chỉ hiển thị 1 lần)", res.auth_token, res.receive_url);
                }
                if (res.auth_secret) {
                    setReceiveHelp(
                        "Dùng header `X-Webhook-Timestamp` (unix seconds) và `X-Webhook-Signature` để ký HMAC SHA-256."
                    );
                    showSecret("Secret HMAC (chỉ hiển thị 1 lần)", res.auth_secret, res.receive_url);
                }
            } else {
                const res = await updateChannel(editor.id, {
                    name: editor.name,
                    type: editor.type,
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

                if (res.auth_token) {
                    setReceiveHelp(
                        "Dùng header `X-Webhook-Token: <token>` hoặc query `?token=<token>` khi gọi receiver."
                    );
                    showSecret("Token mới (chỉ hiển thị 1 lần)", res.auth_token, res.receive_url);
                }
                if (res.auth_secret) {
                    setReceiveHelp(
                        "Dùng header `X-Webhook-Timestamp` (unix seconds) và `X-Webhook-Signature` để ký HMAC SHA-256."
                    );
                    showSecret("Secret mới (chỉ hiển thị 1 lần)", res.auth_secret, res.receive_url);
                }
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
        <div className="space-y-4 md:space-y-6 pb-12">
            {/* Header section */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-black tracking-tight text-slate-900 flex items-center gap-3">
                        Kênh Webhook
                    </h1>
                    <p className="mt-1 text-sm font-medium text-slate-500">
                        Quản lý các điểm tiếp nhận dữ liệu từ bên thứ 3 (postbacks) với bảo mật cao.
                    </p>
                </div>
                <div className="flex items-center gap-2">
                    <button
                        className={`ui-btn ui-btn-ghost gap-2 border border-slate-200 lg:hidden ${guideOpen ? 'bg-sky-50 border-sky-200 text-sky-700' : 'bg-white'}`}
                        onClick={() => setGuideOpen(!guideOpen)}
                    >
                        <IconInfo />
                        <span className="hidden sm:inline">Hướng dẫn</span>
                    </button>
                    <button
                        className={`ui-btn ui-btn-ghost gap-2 border border-slate-200 lg:hidden ${mobileFiltersOpen ? 'bg-slate-100 border-slate-300' : 'bg-white'}`}
                        onClick={() => setMobileFiltersOpen(!mobileFiltersOpen)}
                    >
                        <IconFilter />
                        Lọc
                    </button>
                    <button
                        className="ui-btn ui-btn-ghost gap-2 border border-slate-200 bg-white hidden md:flex"
                        onClick={() => reload()}
                        disabled={loading}
                    >
                        <IconRefresh />
                        Tải lại
                    </button>
                    <button
                        className="ui-btn ui-btn-primary gap-2"
                        onClick={openCreate}
                        disabled={loading}
                    >
                        <IconPlus />
                        <span className="hidden sm:inline">Tạo kênh mới</span>
                        <span className="sm:hidden">Tạo</span>
                    </button>
                </div>
            </div>

            {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}
            {toast ? <Alert tone="success" title={toast} /> : null}

            {/* Quick guide section */}
            <Card className={`border-none bg-sky-50 shadow-none ${guideOpen ? 'block' : 'hidden lg:block'}`}>
                <div className="flex gap-4">
                    <div className="shrink-0 text-sky-600 mt-0.5">
                        <IconInfo />
                    </div>
                    <div className="text-sm text-sky-900 leading-relaxed font-medium">
                        <p>
                            Mỗi kênh sẽ nhận dữ liệu (POST/GET) thông qua <span className="font-bold underline decoration-sky-300">Receive URL</span> tương ứng.
                            Bạn có thể cấu hình <span className="text-sky-700">Token</span> hoặc <span className="text-sky-700">HMAC</span> để xác thực người gọi.
                        </p>
                        <div className="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-sky-700/80">
                            <span className="flex items-center gap-1.5">
                                <span className="h-1.5 w-1.5 rounded-full bg-sky-400" />
                                Token: header X-Webhook-Token
                            </span>
                            <span className="flex items-center gap-1.5">
                                <span className="h-1.5 w-1.5 rounded-full bg-sky-400" />
                                HMAC: header X-Webhook-Signature
                            </span>
                        </div>
                    </div>
                </div>
            </Card>

            <div className="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
                {/* Filters sidebar/top */}
                <div className={`${mobileFiltersOpen ? "block" : "hidden"} lg:block lg:col-span-1 space-y-4 md:space-y-6`}>
                    <Card title="Bộ lọc" className="shadow-sm" bodyClassName="p-4 md:p-6">
                        <div className="space-y-4">
                            <div>
                                <label className="ui-label">Tìm theo tên</label>
                                <Input
                                    value={filters.name}
                                    onChange={(e) => setFilters({ ...filters, name: e.target.value })}
                                    placeholder="Ví dụ: Payment, SMS..."
                                />
                            </div>
                            <div>
                                <label className="ui-label">Kiểu Auth</label>
                                <Select
                                    value={filters.auth_type}
                                    onChange={(e) => setFilters({ ...filters, auth_type: e.target.value as any })}
                                >
                                    <option value="all">Tất cả</option>
                                    <option value="none">none</option>
                                    <option value="token">token</option>
                                    <option value="hmac">hmac</option>
                                </Select>
                            </div>
                            <div>
                                <label className="ui-label">Trạng thái</label>
                                <Select
                                    value={filters.is_active}
                                    onChange={(e) => setFilters({ ...filters, is_active: e.target.value as any })}
                                >
                                    <option value="all">Tất cả</option>
                                    <option value="1">Đang bật</option>
                                    <option value="0">Đang tắt</option>
                                </Select>
                            </div>
                            <Button
                                className="w-full gap-2"
                                variant="primary"
                                onClick={() => reload({ page: 1 })}
                                disabled={loading}
                            >
                                <IconFilter />
                                Áp dụng bộ lọc
                            </Button>
                        </div>
                    </Card>
                </div>

                {/* Main list section */}
                <div className="lg:col-span-3 space-y-4 md:space-y-6 min-w-0">
                    <Card
                        className="md:shadow-md md:border md:bg-white shadow-none border-none bg-transparent overflow-hidden"
                        title="Danh sách các kênh"
                        bodyClassName="p-0 md:p-6"
                    >
                        <div className="md:hidden space-y-4">
                            {items.map((it) => {
                                const url = receiveUrlFor(it.public_id);
                                return (
                                    <div key={it.id} className="rounded-2xl border border-slate-100/50 bg-white p-4 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.04)]">
                                        <div className="flex items-start justify-between">
                                            <div className="min-w-0">
                                                <div className="font-bold text-slate-900 truncate pr-2 capitalize">
                                                    {it.name}
                                                </div>
                                                <div className="mt-1 flex flex-wrap items-center gap-2">
                                                    <Badge tone={it.is_active ? "success" : "danger"}>
                                                        {it.is_active ? "Active" : "Disabled"}
                                                    </Badge>
                                                    <Badge tone={it.auth_type === "hmac" ? "info" : it.auth_type === "token" ? "warning" : "success"}>
                                                        {it.auth_type}
                                                    </Badge>
                                                    <span className="text-[11px] font-bold text-slate-400 font-mono">#{it.id}</span>
                                                </div>
                                            </div>
                                            <Dropdown
                                                align="right"
                                                trigger={
                                                    <div className="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                                                        <span className="text-xl leading-none -mt-2">...</span>
                                                    </div>
                                                }
                                            >
                                                {({ close }) => (
                                                    <div className="py-1 min-w-[140px]">
                                                        <button
                                                            type="button"
                                                            className="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 font-medium"
                                                            onClick={() => { close(); openEdit(it); }}
                                                        >
                                                            Chỉnh sửa
                                                        </button>
                                                        <Link
                                                            className="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-slate-50 font-medium"
                                                            to={`/channels/${it.id}/logs`}
                                                            onClick={() => close()}
                                                        >
                                                            <IconTerminal /> Logs
                                                        </Link>
                                                        <Link
                                                            className="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-slate-50 font-medium"
                                                            to={`/channels/${it.id}/destinations`}
                                                            onClick={() => close()}
                                                        >
                                                            Điểm nhận (Forward)
                                                        </Link>
                                                        <Link
                                                            className="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-slate-50 font-medium"
                                                            to={`/channels/${it.id}/dispatches`}
                                                            onClick={() => close()}
                                                        >
                                                            Log bắn (Dispatch)
                                                        </Link>
                                                        <Link
                                                            className="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-slate-50 font-medium"
                                                            to={`/channels/${it.id}/stats`}
                                                            onClick={() => close()}
                                                        >
                                                            <IconChart /> Thống kê
                                                        </Link>
                                                        <div className="h-px bg-slate-100 my-1" />
                                                        <button
                                                            type="button"
                                                            className="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 text-rose-600 font-bold"
                                                            onClick={() => { close(); doDelete(it.id); }}
                                                            disabled={loading}
                                                        >
                                                            Xoá kênh
                                                        </button>
                                                    </div>
                                                )}
                                            </Dropdown>
                                        </div>

                                        <div className="mt-4 p-4 rounded-xl bg-slate-50/50 space-y-3">
                                            <div>
                                                <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                                    <span className="h-1 w-1 rounded-full bg-slate-300"></span>
                                                    Receive URL
                                                </div>
                                                <div className="flex items-center gap-2">
                                                    <div className="flex-1 font-mono text-[11px] text-slate-500 truncate bg-white px-3 py-2.5 rounded-lg border border-slate-100/60 shadow-sm">
                                                        {url}
                                                    </div>
                                                    <button
                                                        onClick={async () => {
                                                            const ok = await copyToClipboard(url);
                                                            toastOnce(ok ? "Đã sao chép URL" : "Lỗi sao chép");
                                                        }}
                                                        className="p-2 text-slate-500 hover:text-sky-600 transition-colors"
                                                    >
                                                        <IconCopy />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                            {items.length === 0 && <div className="py-12 text-center text-slate-400 font-medium">Chưa có kênh nào được tạo.</div>}
                        </div>

                        <div className="hidden md:block overflow-x-auto">
                            <table className="ui-table w-full table-fixed">
                                <thead className="ui-thead">
                                    <tr>
                                        <th className="ui-th w-16">ID</th>
                                        <th className="ui-th w-1/4">Kênh & Mô tả</th>
                                        <th className="ui-th w-28 whitespace-nowrap">Trạng thái</th>
                                        <th className="ui-th w-28">Auth</th>
                                        <th className="ui-th w-1/3">Receive URL</th>
                                        <th className="ui-th w-40">Hoạt động cuối</th>
                                        <th className="ui-th w-16 text-right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {items.map((it) => {
                                        const url = receiveUrlFor(it.public_id);
                                        return (
                                            <tr key={it.id} className="ui-tr group">
                                                <td className="ui-td font-mono font-bold text-slate-400">#{it.id}</td>
                                                <td className="ui-td overflow-hidden">
                                                    <div className="flex items-center gap-2">
                                                        <div className="font-bold text-slate-900 truncate" title={it.name}>{it.name}</div>
                                                        {it.type !== 'default' && (
                                                            <Badge tone="info" className="scale-90 origin-left">
                                                                {it.type}
                                                            </Badge>
                                                        )}
                                                    </div>
                                                    <div className="text-xs text-slate-500 mt-0.5 truncate" title={it.description ?? ""}>
                                                        {it.description || "Không có mô tả"}
                                                    </div>
                                                </td>
                                                <td className="ui-td">
                                                    <Badge tone={it.is_active ? "success" : "danger"}>
                                                        {it.is_active ? "Active" : "Disabled"}
                                                    </Badge>
                                                </td>
                                                <td className="ui-td">
                                                    <Badge tone={it.auth_type === "hmac" ? "info" : it.auth_type === "token" ? "warning" : "success"}>
                                                        {it.auth_type}
                                                    </Badge>
                                                </td>
                                                <td className="ui-td">
                                                    <div className="flex items-center gap-2 group/url overflow-hidden">
                                                        <div
                                                            className="flex-1 font-mono text-[10px] text-slate-500 bg-slate-50 px-2 py-1.5 rounded-lg border border-slate-100 truncate cursor-pointer hover:border-sky-300 hover:text-sky-600 transition-all"
                                                            title={url}
                                                            onClick={() => openReceiveUrl(url)}
                                                        >
                                                            {url}
                                                        </div>
                                                        <button
                                                            className="p-1.5 text-slate-400 hover:text-sky-600 transition-colors opacity-0 group-hover:opacity-100 shrink-0"
                                                            onClick={async () => {
                                                                const ok = await copyToClipboard(url);
                                                                toastOnce(ok ? "Copied URL!" : "Failed to copy");
                                                            }}
                                                            title="Copy URL"
                                                        >
                                                            <IconCopy />
                                                        </button>
                                                    </div>
                                                </td>
                                                <td className="ui-td text-xs font-medium text-slate-500 whitespace-nowrap">
                                                    {formatDateTime(it.last_received_at)}
                                                </td>
                                                <td className="ui-td text-right">
                                                    <Dropdown
                                                        align="right"
                                                        trigger={
                                                            <div className="ui-btn ui-btn-ghost h-8 w-8 p-0 flex items-center justify-center border border-transparent hover:border-slate-200 bg-transparent text-slate-400 hover:text-slate-900 transition-all cursor-pointer">
                                                                <span className="text-xl leading-none -mt-2">...</span>
                                                            </div>
                                                        }
                                                    >
                                                        {({ close }) => (
                                                            <div className="py-1 min-w-[160px]">
                                                                <button
                                                                    type="button"
                                                                    className="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold"
                                                                    onClick={() => { close(); openEdit(it); }}
                                                                >
                                                                    Thiết lập
                                                                </button>
                                                                <Link
                                                                    className="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold text-sky-600"
                                                                    to={`/channels/${it.id}/logs`}
                                                                    onClick={() => close()}
                                                                >
                                                                    <IconTerminal /> Xem Logs
                                                                </Link>
                                                                <Link
                                                                    className="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold"
                                                                    to={`/channels/${it.id}/destinations`}
                                                                    onClick={() => close()}
                                                                >
                                                                    Điểm nhận (Forward)
                                                                </Link>
                                                                <Link
                                                                    className="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold"
                                                                    to={`/channels/${it.id}/dispatches`}
                                                                    onClick={() => close()}
                                                                >
                                                                    Log bắn (Dispatch)
                                                                </Link>
                                                                <Link
                                                                    className="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-slate-50 font-semibold text-emerald-600"
                                                                    to={`/channels/${it.id}/stats`}
                                                                    onClick={() => close()}
                                                                >
                                                                    <IconChart /> Xem Thống kê
                                                                </Link>
                                                                {it.auth_type === "token" && (
                                                                    <button
                                                                        className="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50"
                                                                        onClick={() => { close(); doRotateToken(it); }}
                                                                    >
                                                                        Làm mới token
                                                                    </button>
                                                                )}
                                                                {it.auth_type === "hmac" && (
                                                                    <button
                                                                        className="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50"
                                                                        onClick={() => { close(); doRotateSecret(it); }}
                                                                    >
                                                                        Làm mới secret
                                                                    </button>
                                                                )}
                                                                <div className="h-px bg-slate-100 my-1" />
                                                                <button
                                                                    type="button"
                                                                    className="w-full text-left px-4 py-2.5 text-sm hover:bg-rose-50 text-rose-600 font-black"
                                                                    onClick={() => { close(); doDelete(it.id); }}
                                                                    disabled={loading}
                                                                >
                                                                    Xoá kênh
                                                                </button>
                                                            </div>
                                                        )}
                                                    </Dropdown>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                    {items.length === 0 && (
                                        <tr>
                                            <td colSpan={6} className="py-20 text-center text-slate-400 font-medium">
                                                Không có dữ liệu kênh webhook.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <div className="mt-6">
                            <Pagination
                                meta={meta}
                                onChange={(next) => {
                                    setMeta((m) => ({ ...m, page: next.page, per_page: next.per_page }));
                                    reload(next);
                                }}
                            />
                        </div>
                    </Card>
                </div>
            </div>

            {/* Modals remain mostly same logic but with refined UI using shared components */}
            <Modal
                open={editorOpen}
                title={editorMode === "create" ? "Tạo kênh Webhook" : `Thiết lập kênh #${editor.id}`}
                className="max-w-2xl"
                onClose={() => setEditorOpen(false)}
                footer={
                    <div className="flex items-center justify-end gap-3 px-6 py-4 bg-slate-50/50 backdrop-blur">
                        <Button variant="ghost" onClick={() => setEditorOpen(false)}>Huỷ bỏ</Button>
                        <Button variant="primary" onClick={submitEditor} disabled={loading} className="min-w-[100px]">
                            {loading ? "Đang lưu..." : "Lưu cấu hình"}
                        </Button>
                    </div>
                }
            >
                <div className="space-y-6">
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label className="ui-label">Tên kênh gợi nhớ</label>
                            <Input
                                value={editor.name}
                                onChange={(e) => setEditor({ ...editor, name: e.target.value })}
                                placeholder="Ví dụ: Callback thanh toán Visa"
                            />
                        </div>
                        <div>
                            <label className="ui-label">Loại kênh (Type)</label>
                            <Select
                                value={editor.type === 'default' ? 'default' : 'custom'}
                                onChange={(e) => setEditor({ ...editor, type: e.target.value === 'default' ? 'default' : '' })}
                            >
                                <option value="default">Mặc định (Dùng Validation UI)</option>
                                <option value="custom">Khác (Tuỳ chỉnh theo Code)</option>
                            </Select>
                            {editor.type !== 'default' && (
                                <div className="mt-3">
                                    <Input
                                        placeholder="Nhập mã loại (vd: woocommerce, kiotviet)"
                                        value={editor.type}
                                        onChange={(e) => setEditor({ ...editor, type: e.target.value })}
                                    />
                                    <div className="mt-1.5 text-[11px] text-slate-500 italic">
                                        Mã loại này sẽ được dùng trong code để gọi class xử lý logic tương ứng.
                                    </div>
                                </div>
                            )}
                        </div>
                        <div>
                            <label className="ui-label">Phương thức xác thực</label>
                            <Select
                                value={editor.auth_type}
                                onChange={(e) => setEditor({ ...editor, auth_type: e.target.value as any })}
                            >
                                <option value="none">none (Không bảo vệ)</option>
                                <option value="token">token (Header X-Webhook-Token)</option>
                                <option value="hmac">hmac (HMAC SHA-256)</option>
                            </Select>
                            <div className="mt-2 text-[11px] text-slate-500 italic bg-amber-50 p-2 rounded-lg border border-amber-100">
                                {editor.auth_type === "none" ? "Cẩn thận: Bất kỳ ai biết URL đều có thể gửi dữ liệu." :
                                    editor.auth_type === "token" ? "Sử dụng token tĩnh gửi kèm trong Header." :
                                        "Bảo mật nhất: Dùng secret key để ký và kiểm tra chữ ký."}
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label className="ui-label">Trạng thái kênh</label>
                            <Select
                                value={editor.is_active ? "1" : "0"}
                                onChange={(e) => setEditor({ ...editor, is_active: e.target.value === "1" })}
                            >
                                <option value="1">Đang bật (Active)</option>
                                <option value="0">Tạm tắt (Disabled)</option>
                            </Select>
                        </div>
                        <div>
                            <label className="ui-label">Method cho phép</label>
                            <Select
                                value={editor.allowed_methods.join(",")}
                                onChange={(e) => {
                                    const v = e.target.value;
                                    setEditor({ ...editor, allowed_methods: v === "GET" ? ["GET"] : v === "POST" ? ["POST"] : ["GET", "POST"] });
                                }}
                            >
                                <option value="GET,POST">GET + POST</option>
                                <option value="GET">Chỉ GET</option>
                                <option value="POST">Chỉ POST (Khuyên dùng)</option>
                            </Select>
                        </div>
                        <div className="md:col-span-1">
                            {/* Optional for space */}
                        </div>
                    </div>

                    <div>
                        <label className="ui-label">Mô tả chi tiết</label>
                        <Input
                            value={editor.description}
                            onChange={(e) => setEditor({ ...editor, description: e.target.value })}
                            placeholder="Nhận thông tin thanh toán từ PayOS hoặc ZaloPay..."
                        />
                    </div>

                    <div>
                        <div className="flex items-center justify-between mb-2">
                            <label className="ui-label !mb-0">Validation Rules (Rules cho body JSON)</label>
                            <button
                                type="button"
                                className="text-xs font-bold text-sky-600 hover:text-sky-700 underline"
                                onClick={() => {
                                    if (!rulesJsonOpen) {
                                        // Khi chuyển sang JSON mode, lấy data từ fields hiện tại
                                        setJsonRaw(prettyJson(buildValidationRulesRecord(editor.validation_fields) || {}));
                                        setJsonError(null);
                                    }
                                    setRulesJsonOpen(!rulesJsonOpen);
                                }}
                            >
                                {rulesJsonOpen ? "Dùng Mode UI" : "Sửa JSON trực tiếp"}
                            </button>
                        </div>

                        {rulesJsonOpen ? (
                            <div className="space-y-2">
                                <textarea
                                    className={`ui-input h-48 font-mono text-xs py-2 ${jsonError ? 'border-rose-500 ring-rose-50' : ''}`}
                                    value={jsonRaw}
                                    onChange={(e) => {
                                        const val = e.target.value;
                                        setJsonRaw(val);
                                        try {
                                            const parsed = JSON.parse(val);
                                            setJsonError(null);
                                            // Parse thành fields để đồng bộ
                                            setEditor(prev => ({
                                                ...prev,
                                                validation_fields: parseValidationRulesToFields(parsed)
                                            }));
                                        } catch (err: any) {
                                            setJsonError(err.message);
                                        }
                                    }}
                                    placeholder='{ "field_name": "required|string" }'
                                />
                                {jsonError && (
                                    <div className="text-[10px] font-bold text-rose-500 bg-rose-50 p-2 rounded-lg border border-rose-100">
                                        JSON không hợp lệ: {jsonError}
                                    </div>
                                )}
                            </div>
                        ) : (
                            <div className="border border-slate-200 rounded-xl overflow-hidden bg-slate-50/50">
                                <table className="w-full text-xs">
                                    <thead className="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                                        <tr>
                                            <th className="px-4 py-2 text-left w-1/3">Field Name</th>
                                            <th className="px-4 py-2 text-left">Rules (Laravel Style)</th>
                                            <th className="px-4 py-2 w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100">
                                        {editor.validation_fields.map((f, fIdx) => (
                                            <tr key={f.id}>
                                                <td className="p-2">
                                                    <input
                                                        className="w-full h-8 px-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                                        value={f.field}
                                                        onChange={(e) => {
                                                            const newFields = [...editor.validation_fields];
                                                            newFields[fIdx].field = e.target.value;
                                                            setEditor({ ...editor, validation_fields: newFields });
                                                        }}
                                                        placeholder="order_id"
                                                    />
                                                </td>
                                                <td className="p-2">
                                                    <div className="flex flex-wrap gap-1.5 items-center">
                                                        {f.tokens.map((t, tIdx) => (
                                                            <div key={t.id} className="flex items-center bg-white border border-slate-200 rounded-md py-0.5 px-1.5 group/token">
                                                                <select
                                                                    className="bg-transparent font-medium focus:outline-none cursor-pointer"
                                                                    value={t.token}
                                                                    onChange={(e) => {
                                                                        const newFields = [...editor.validation_fields];
                                                                        newFields[fIdx].tokens[tIdx].token = e.target.value;
                                                                        setEditor({ ...editor, validation_fields: newFields });
                                                                    }}
                                                                >
                                                                    {VALIDATION_RULE_OPTIONS.map(opt => (
                                                                        <option key={opt.value} value={opt.value}>{opt.label}</option>
                                                                    ))}
                                                                    {!VALIDATION_RULE_OPTIONS.find(o => o.value === t.token) && (
                                                                        <option value={t.token}>{t.token}</option>
                                                                    )}
                                                                </select>
                                                                <button
                                                                    type="button"
                                                                    className="ml-1 text-slate-400 hover:text-rose-500"
                                                                    onClick={() => {
                                                                        const newFields = [...editor.validation_fields];
                                                                        newFields[fIdx].tokens.splice(tIdx, 1);
                                                                        setEditor({ ...editor, validation_fields: newFields });
                                                                    }}
                                                                >
                                                                    &times;
                                                                </button>
                                                            </div>
                                                        ))}
                                                        <button
                                                            type="button"
                                                            className="h-6 w-6 rounded-md bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-sky-600 hover:border-sky-300"
                                                            onClick={() => {
                                                                const newFields = [...editor.validation_fields];
                                                                newFields[fIdx].tokens.push({ id: newId(), token: "required" });
                                                                setEditor({ ...editor, validation_fields: newFields });
                                                            }}
                                                        >
                                                            +
                                                        </button>
                                                    </div>
                                                </td>
                                                <td className="p-2 text-center">
                                                    <button
                                                        type="button"
                                                        className="text-slate-300 hover:text-rose-500 transition-colors"
                                                        onClick={() => {
                                                            const newFields = [...editor.validation_fields];
                                                            newFields.splice(fIdx, 1);
                                                            setEditor({ ...editor, validation_fields: newFields });
                                                        }}
                                                    >
                                                        &times;
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                                <button
                                    type="button"
                                    className="w-full py-2 bg-slate-50 hover:bg-slate-100 text-[11px] font-bold text-slate-500 transition-colors"
                                    onClick={() => {
                                        setEditor({
                                            ...editor,
                                            validation_fields: [...editor.validation_fields, { id: newId(), field: "", tokens: [{ id: newId(), token: "required" }] }]
                                        });
                                    }}
                                >
                                    + THÊM TRƯỜNG DỮ LIỆU
                                </button>
                            </div>
                        )}
                        <p className="ui-help">Định nghĩa các validation rule cho body JSON của request. Dữ liệu sẽ được lọc trước khi đưa vào module xử lý.</p>
                    </div>

                    {editorMode === "edit" && (
                        <div className="p-4 rounded-xl border border-amber-200 bg-amber-50 space-y-3">
                            <div className="text-sm font-bold text-amber-800 flex items-center gap-2">
                                <IconInfo /> Zone Nguy Hiểm
                            </div>
                            <div className="flex flex-wrap gap-4">
                                {editor.auth_type === "token" && (
                                    <label className="flex items-center gap-2 cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            className="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                                            checked={editor.rotate_token}
                                            onChange={(e) => setEditor({ ...editor, rotate_token: e.target.checked })}
                                        />
                                        <span className="text-xs font-bold text-slate-700">Tạo lại Auth Token mới</span>
                                    </label>
                                )}
                                {editor.auth_type === "hmac" && (
                                    <label className="flex items-center gap-2 cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            className="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                                            checked={editor.rotate_secret}
                                            onChange={(e) => setEditor({ ...editor, rotate_secret: e.target.checked })}
                                        />
                                        <span className="text-xs font-bold text-slate-700">Tạo lại HMAC Secret mới</span>
                                    </label>
                                )}
                            </div>
                            <p className="text-[10px] text-amber-700 font-medium leading-relaxed">
                                Lưu ý: Khi tạo mới token/secret, giá trị cũ sẽ mất hiệu lực ngay lập tức. Hãy cập nhật ở phía bên gọi (Sender) sau khi lưu.
                            </p>
                        </div>
                    )}
                </div>
            </Modal>

            {/* Secret Result Modal */}
            <Modal
                open={secretOpen}
                title={secretTitle}
                onClose={() => setSecretOpen(false)}
            >
                <div className="space-y-4">
                    <Alert tone="warning" title="Lưu ý quan trọng" details="Giá trị này chỉ hiển thị duy nhất một lần này. Vui lòng sao lưu và cất giữ cẩn thận." />

                    <div className="space-y-3">
                        <div>
                            <label className="ui-label">Giá trị Key/Secret</label>
                            <div className="flex items-center gap-2">
                                <div className="flex-1 font-mono text-sm bg-slate-900 text-sky-400 p-3 rounded-xl break-all">
                                    {secretValue}
                                </div>
                                <button
                                    onClick={async () => {
                                        const ok = await copyToClipboard(secretValue);
                                        toastOnce(ok ? "Copied!" : "Error");
                                    }}
                                    className="ui-btn ui-btn-ghost h-12 w-12 border border-slate-200"
                                >
                                    <IconCopy />
                                </button>
                            </div>
                        </div>

                        <div>
                            <label className="ui-label">Receive URL</label>
                            <div className="flex items-center gap-2">
                                <div className="flex-1 font-mono text-xs bg-slate-100 p-3 rounded-xl text-slate-600 truncate">
                                    {receiveUrl}
                                </div>
                                <button
                                    onClick={async () => {
                                        const ok = await copyToClipboard(receiveUrl);
                                        toastOnce(ok ? "Copied!" : "Error");
                                    }}
                                    className="ui-btn ui-btn-ghost h-10 w-10 border border-slate-200"
                                >
                                    <IconCopy />
                                </button>
                            </div>
                        </div>

                        {receiveHelp && (
                            <div className="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 italic">
                                {receiveHelp}
                            </div>
                        )}
                    </div>

                    <div className="pt-2">
                        <Button className="w-full" variant="primary" onClick={() => setSecretOpen(false)}>Tôi đã lưu, đóng cửa sổ</Button>
                    </div>
                </div>
            </Modal>

            {/* Simple View URL Modal */}
            <Modal open={urlOpen} title="Địa chỉ Receive URL" onClose={() => setUrlOpen(false)}>
                <div className="space-y-4">
                    <p className="text-sm text-slate-600">Đây là địa chỉ duy nhất để bên thứ 3 gửi dữ liệu tới hệ thống cho kênh này.</p>
                    <div className="p-4 bg-slate-100 rounded-xl font-mono text-xs break-all border border-slate-200 select-all">
                        {urlValue}
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="ghost" onClick={() => setUrlOpen(false)}>Đóng</Button>
                        <Button
                            variant="primary"
                            onClick={async () => {
                                const ok = await copyToClipboard(urlValue);
                                toastOnce(ok ? "Đã copy!" : "Lỗi");
                                setUrlOpen(false);
                            }}
                        >
                            Sao chép URL
                        </Button>
                    </div>
                </div>
            </Modal>
        </div>
    );
}
