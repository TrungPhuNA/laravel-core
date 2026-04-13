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
import { prettyJson, shortText } from "@shared/lib/format";
import { copyToClipboard } from "@shared/lib/clipboard";
import type { WebhookChannel } from "../types";
import { createChannel, deleteChannel, listChannels, rotateSecret, rotateToken, updateChannel } from "../services/webhooksApi";

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
            validation_fields: [
                { id: newId(), field: "email", tokens: [{ id: newId(), token: "required" }, { id: newId(), token: "email" }] },
            ],
        });
        setRulesJsonOpen(false);
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
            validation_fields: ch.validation_rules ? parseValidationRulesToFields(ch.validation_rules) : [],
        });
        setRulesJsonOpen(false);
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
            {toast ? <Alert tone="success" title={toast} /> : null}

            <Card title="Hướng dẫn nhanh">
                <div className="text-sm text-slate-700 space-y-2">
                    <div>
                        Link nhận postback (receiver) của mỗi kênh nằm ở cột <b>Receive URL</b>.
                        Dạng chung:
                        <code className="ml-2 rounded bg-slate-100 px-1 py-0.5 font-mono">
                            /api/v1/webhooks/receive/&lt;public_id&gt;
                        </code>
                    </div>
                    <div>
                        Auth:
                        <span className="ml-2 font-semibold">token</span> (đơn giản) hoặc{" "}
                        <span className="font-semibold">hmac</span> (an toàn hơn). Token/secret chỉ trả 1 lần khi tạo hoặc rotate,
                        nhớ lưu lại.
                    </div>
                    <div className="text-xs text-slate-500">
                        Tip: bấm vào Receive URL hoặc nút Copy để lấy full URL.
                    </div>
                </div>
            </Card>

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
                        <Input className="mt-1" value={filters.name} onChange={(e) => setFilters({ ...filters, name: e.target.value })} placeholder="payment" />
                    </div>
                    <div>
                        <div className="text-xs font-medium text-slate-600">Auth</div>
                        <Select className="mt-1" value={filters.auth_type} onChange={(e) => setFilters({ ...filters, auth_type: e.target.value as any })}>
                            <option value="all">Tất cả</option>
                            <option value="none">none</option>
                            <option value="token">token</option>
                            <option value="hmac">hmac</option>
                        </Select>
                    </div>
                    <div>
                        <div className="text-xs font-medium text-slate-600">Trạng thái</div>
                        <Select className="mt-1" value={filters.is_active} onChange={(e) => setFilters({ ...filters, is_active: e.target.value as any })}>
                            <option value="all">Tất cả</option>
                            <option value="1">Đang bật</option>
                            <option value="0">Đang tắt</option>
                        </Select>
                    </div>
                </div>
            </Card>

            <Card title="Danh sách">
                <div className="md:hidden space-y-2">
                    {items.map((it) => {
                        const methods = it.allowed_methods?.length ? it.allowed_methods.join(",") : "GET,POST";
                        const url = receiveUrlFor(it.public_id);
                        return (
                            <div key={it.id} className="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                                <div className="flex items-start justify-between gap-3">
                                    <div className="min-w-0">
                                        <div className="font-semibold text-slate-900 truncate" title={it.name}>
                                            {it.name}
                                        </div>
                                        <div className="mt-0.5 text-xs text-slate-500 truncate" title={it.description ?? ""}>
                                            {it.description ?? "-"}
                                        </div>
                                        <div className="mt-2 flex flex-wrap items-center gap-2">
                                            <Badge tone={it.is_active ? "success" : "danger"}>{it.is_active ? "active" : "inactive"}</Badge>
                                            <Badge tone={it.auth_type === "hmac" ? "info" : it.auth_type === "token" ? "warning" : "success"}>
                                                {it.auth_type}
                                            </Badge>
                                            <span className="text-xs text-slate-600 font-mono">#{it.id}</span>
                                        </div>
                                    </div>
                                    <Dropdown
                                        align="right"
                                        trigger={<span className="ui-btn ui-btn-ghost h-9 w-9 px-0 py-0 grid place-items-center">⋯</span>}
                                    >
                                        {({ close }) => (
                                            <>
                                                <button
                                                    type="button"
                                                    className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50"
                                                    onClick={() => {
                                                        close();
                                                        openEdit(it);
                                                    }}
                                                >
                                                    Sửa
                                                </button>
                                                <Link
                                                    className="block px-4 py-2 text-sm hover:bg-slate-50"
                                                    to={`/channels/${it.id}/logs`}
                                                    onClick={() => close()}
                                                >
                                                    Logs
                                                </Link>
                                                {it.auth_type === "token" ? (
                                                    <button
                                                        type="button"
                                                        className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50"
                                                        onClick={() => {
                                                            close();
                                                            doRotateToken(it);
                                                        }}
                                                        disabled={loading}
                                                    >
                                                        Rotate token
                                                    </button>
                                                ) : null}
                                                {it.auth_type === "hmac" ? (
                                                    <button
                                                        type="button"
                                                        className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50"
                                                        onClick={() => {
                                                            close();
                                                            doRotateSecret(it);
                                                        }}
                                                        disabled={loading}
                                                    >
                                                        Rotate secret
                                                    </button>
                                                ) : null}
                                                <div className="h-px bg-slate-100" />
                                                <button
                                                    type="button"
                                                    className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 text-rose-700"
                                                    onClick={() => {
                                                        close();
                                                        doDelete(it.id);
                                                    }}
                                                    disabled={loading}
                                                >
                                                    Xoá
                                                </button>
                                            </>
                                        )}
                                    </Dropdown>
                                </div>

                                <div className="mt-3 overflow-hidden rounded-lg border border-slate-100 bg-slate-50 divide-y divide-slate-100 text-xs text-slate-700">
                                    <div className="flex items-center justify-between gap-3 px-3 py-2">
                                        <div className="text-slate-500 shrink-0">Public ID</div>
                                        <div className="font-mono truncate" title={it.public_id}>
                                            {shortText(it.public_id, 28)}
                                        </div>
                                    </div>
                                    <div className="flex items-center justify-between gap-3 px-3 py-2">
                                        <div className="text-slate-500 shrink-0">Methods</div>
                                        <div className="font-mono">{methods}</div>
                                    </div>
                                    <div className="flex items-center justify-between gap-3 px-3 py-2">
                                        <div className="text-slate-500 shrink-0">Last</div>
                                        <div className="text-slate-600 truncate">{it.last_received_at ?? "-"}</div>
                                    </div>
                                    <div className="px-3 py-2">
                                        <div className="text-slate-500">Receive URL</div>
                                        <button
                                            type="button"
                                            className="mt-1 w-full rounded-md border border-slate-200 bg-white p-2 text-left font-mono text-[11px] text-slate-700 hover:bg-slate-50"
                                            title={url}
                                            onClick={() => openReceiveUrl(url)}
                                        >
                                            {shortText(url, 52)}
                                        </button>
                                        <div className="mt-2 flex items-center gap-2">
                                            <Button variant="ghost" className="h-8 px-2 py-1 text-xs" onClick={() => openReceiveUrl(url)}>
                                                Xem
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                className="h-8 px-2 py-1 text-xs"
                                                onClick={async () => {
                                                    const ok = await copyToClipboard(url);
                                                    toastOnce(ok ? "Đã copy Receive URL" : "Không copy được. Tip: mở Receive URL rồi bấm Cmd/Ctrl+C.");
                                                }}
                                            >
                                                Copy
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                    {items.length === 0 ? (
                        <div className="py-10 text-center text-slate-500">
                            Không có dữ liệu.
                        </div>
                    ) : null}
                </div>

                <div className="hidden md:block overflow-auto">
                    <table className="ui-table min-w-[1100px] w-full table-fixed">
                        <thead className="ui-thead">
                            <tr>
                                <th className="ui-th w-[64px]">ID</th>
                                <th className="ui-th w-[260px]">Kênh</th>
                                <th className="ui-th w-[180px]">Public ID</th>
                                <th className="ui-th w-[110px]">Auth</th>
                                <th className="ui-th w-[110px]">Methods</th>
                                <th className="ui-th w-[360px]">Receive URL</th>
                                <th className="ui-th w-[220px]">Last</th>
                                <th className="ui-th w-[86px] text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {items.map((it) => {
                                const methods = it.allowed_methods?.length ? it.allowed_methods.join(",") : "GET,POST";
                                const url = receiveUrlFor(it.public_id);
                                return (
                                    <tr key={it.id} className="ui-tr align-top">
                                        <td className="ui-td font-medium text-slate-900 whitespace-nowrap">{it.id}</td>
                                        <td className="ui-td">
                                            <div className="font-semibold text-slate-900 leading-5 truncate" title={it.name}>
                                                {it.name}
                                            </div>
                                            <div
                                                className="text-xs text-slate-500 leading-4 truncate"
                                                title={it.description ?? ""}
                                            >
                                                {it.description ?? "-"}
                                            </div>
                                        </td>
                                        <td className="ui-td font-mono text-xs text-slate-700 whitespace-nowrap">
                                            <span className="truncate block" title={it.public_id}>
                                                {shortText(it.public_id, 18)}
                                            </span>
                                        </td>
                                        <td className="ui-td">
                                            <Badge tone={it.auth_type === "hmac" ? "info" : it.auth_type === "token" ? "warning" : "success"}>
                                                {it.auth_type}
                                            </Badge>
                                        </td>
                                        <td className="ui-td text-slate-700 whitespace-nowrap">{methods}</td>
                                        <td className="ui-td">
                                            <div className="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    className="font-mono text-xs text-slate-700 hover:underline truncate text-left cursor-pointer"
                                                    title={url}
                                                    onClick={() => openReceiveUrl(url)}
                                                >
                                                    {url}
                                                </button>
                                                <Button
                                                    variant="ghost"
                                                    className="h-8 px-2 py-1 text-xs"
                                                    onClick={async () => {
                                                        const ok = await copyToClipboard(url);
                                                        toastOnce(
                                                            ok
                                                                ? "Đã copy Receive URL"
                                                                : "Không copy được. Tip: mở Receive URL rồi bấm Cmd/Ctrl+C."
                                                        );
                                                    }}
                                                >
                                                    Copy
                                                </Button>
                                            </div>
                                        </td>
                                        <td className="ui-td text-slate-600 whitespace-nowrap">{it.last_received_at ?? "-"}</td>
                                        <td className="ui-td text-right whitespace-nowrap">
                                            <Dropdown
                                                align="right"
                                                trigger={
                                                    <span className="ui-btn ui-btn-ghost h-9 w-9 px-0 py-0 grid place-items-center">
                                                        ⋯
                                                    </span>
                                                }
                                            >
                                                {({ close }) => (
                                                    <>
                                                        <button
                                                            type="button"
                                                            className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50"
                                                            onClick={() => {
                                                                close();
                                                                openEdit(it);
                                                            }}
                                                        >
                                                            Sửa
                                                        </button>
                                                        <Link
                                                            className="block px-4 py-2 text-sm hover:bg-slate-50"
                                                            to={`/channels/${it.id}/logs`}
                                                            onClick={() => close()}
                                                        >
                                                            Logs
                                                        </Link>
                                                        {it.auth_type === "token" ? (
                                                            <button
                                                                type="button"
                                                                className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50"
                                                                onClick={() => {
                                                                    close();
                                                                    doRotateToken(it);
                                                                }}
                                                                disabled={loading}
                                                            >
                                                                Rotate token
                                                            </button>
                                                        ) : null}
                                                        {it.auth_type === "hmac" ? (
                                                            <button
                                                                type="button"
                                                                className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50"
                                                                onClick={() => {
                                                                    close();
                                                                    doRotateSecret(it);
                                                                }}
                                                                disabled={loading}
                                                            >
                                                                Rotate secret
                                                            </button>
                                                        ) : null}
                                                        <div className="h-px bg-slate-100" />
                                                        <button
                                                            type="button"
                                                            className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 text-rose-700"
                                                            onClick={() => {
                                                                close();
                                                                doDelete(it.id);
                                                            }}
                                                            disabled={loading}
                                                        >
                                                            Xoá
                                                        </button>
                                                    </>
                                                )}
                                            </Dropdown>
                                        </td>
                                    </tr>
                                );
                            })}
                            {items.length === 0 ? (
                                <tr>
                                    <td colSpan={8} className="py-10 text-center text-slate-500 border-b-0">
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
                            <Input className="mt-1" value={editor.name} onChange={(e) => setEditor({ ...editor, name: e.target.value })} placeholder="Payment callback" />
                        </div>
                        <div>
                            <div className="text-xs font-medium text-slate-600">Auth</div>
                            <Select className="mt-1" value={editor.auth_type} onChange={(e) => setEditor({ ...editor, auth_type: e.target.value as any })}>
                                <option value="none">none</option>
                                <option value="token">token</option>
                                <option value="hmac">hmac</option>
                            </Select>
                            <div className="mt-2 text-xs text-slate-600">
                                {editor.auth_type === "none" ? (
                                    <span>Không yêu cầu auth. Bên thứ 3 gọi receiver trực tiếp.</span>
                                ) : editor.auth_type === "token" ? (
                                    <span>
                                        Token sẽ được trả về 1 lần khi tạo/rotate. Bên thứ 3 gửi{" "}
                                        <code className="rounded bg-slate-100 px-1 py-0.5">X-Webhook-Token</code> hoặc query{" "}
                                        <code className="rounded bg-slate-100 px-1 py-0.5">?token=</code>.
                                    </span>
                                ) : (
                                    <span>
                                        Secret HMAC sẽ được trả về 1 lần khi tạo/rotate. Bên thứ 3 ký HMAC SHA-256 với canonical string
                                        và gửi{" "}
                                        <code className="rounded bg-slate-100 px-1 py-0.5">X-Webhook-Timestamp</code> +{" "}
                                        <code className="rounded bg-slate-100 px-1 py-0.5">X-Webhook-Signature</code>.
                                    </span>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <div className="text-xs font-medium text-slate-600">Trạng thái</div>
                            <Select
                                className="mt-1"
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
                                className="mt-1"
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
                            <Input className="mt-1" value={editor.description} onChange={(e) => setEditor({ ...editor, description: e.target.value })} placeholder="Nhận callback..." />
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
                        <div className="flex items-center justify-between gap-3">
                            <div className="text-xs font-medium text-slate-600">Validation rules</div>
                            <div className="flex items-center gap-2">
                                <button
                                    type="button"
                                    className="text-xs text-slate-600 hover:underline"
                                    onClick={() => setRulesJsonOpen((v) => !v)}
                                >
                                    {rulesJsonOpen ? "Ẩn JSON" : "Xem JSON"}
                                </button>
                                <Button
                                    variant="ghost"
                                    className="h-8 px-2 py-1 text-xs"
                                    onClick={() =>
                                        setEditor((cur) => ({
                                            ...cur,
                                            validation_fields: [...cur.validation_fields, { id: newId(), field: "", tokens: [] }],
                                        }))
                                    }
                                >
                                    Thêm field
                                </Button>
                            </div>
                        </div>

                        <div className="mt-2 space-y-2">
                            {editor.validation_fields.length === 0 ? (
                                <div className="rounded-md border border-dashed border-slate-200 bg-white p-3 text-xs text-slate-500">
                                    Chưa có rule. Bấm <b>Thêm field</b> để bắt đầu.
                                </div>
                            ) : null}

                            {editor.validation_fields.map((f) => (
                                <div key={f.id} className="rounded-md border border-slate-200 bg-white p-3 shadow-sm">
                                    <div className="flex items-start gap-2">
                                        <div className="flex-1">
                                            <div className="text-[11px] font-medium text-slate-600">Field</div>
                                            <Input
                                                className="mt-1"
                                                value={f.field}
                                                onChange={(e) =>
                                                    setEditor((cur) => ({
                                                        ...cur,
                                                        validation_fields: cur.validation_fields.map((it) =>
                                                            it.id === f.id ? { ...it, field: e.target.value } : it
                                                        ),
                                                    }))
                                                }
                                                placeholder="email"
                                            />
                                        </div>

                                        <div className="w-[200px]">
                                            <div className="text-[11px] font-medium text-slate-600">Thêm rule</div>
                                            <Select
                                                className="mt-1"
                                                defaultValue=""
                                                onChange={(e) => {
                                                    const token = e.target.value;
                                                    (e.target as HTMLSelectElement).value = "";
                                                    if (!token) return;

                                                    setEditor((cur) => ({
                                                        ...cur,
                                                        validation_fields: cur.validation_fields.map((it) => {
                                                            if (it.id !== f.id) return it;
                                                            if (it.tokens.some((t) => t.token === token)) return it;
                                                            return { ...it, tokens: [...it.tokens, { id: newId(), token }] };
                                                        }),
                                                    }));
                                                }}
                                            >
                                                <option value="">Chọn rule...</option>
                                                {VALIDATION_RULE_OPTIONS.map((opt) => (
                                                    <option key={opt.value} value={opt.value}>
                                                        {opt.label}
                                                    </option>
                                                ))}
                                            </Select>
                                        </div>

                                        <Button
                                            variant="ghost"
                                            className="mt-5 h-8 px-2 py-1 text-xs text-rose-700"
                                            onClick={() =>
                                                setEditor((cur) => ({
                                                    ...cur,
                                                    validation_fields: cur.validation_fields.filter((it) => it.id !== f.id),
                                                }))
                                            }
                                        >
                                            Xoá
                                        </Button>
                                    </div>

                                    <div className="mt-2 flex flex-wrap gap-2">
                                        {f.tokens.length === 0 ? (
                                            <span className="text-xs text-slate-500">Chưa có rule.</span>
                                        ) : (
                                            f.tokens.map((t) => (
                                                <span key={t.id} className="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-700 ring-1 ring-slate-200">
                                                    <span className="font-mono">{t.token}</span>
                                                    <button
                                                        type="button"
                                                        className="ml-1 rounded px-1 text-slate-500 hover:bg-slate-200 hover:text-slate-700"
                                                        onClick={() =>
                                                            setEditor((cur) => ({
                                                                ...cur,
                                                                validation_fields: cur.validation_fields.map((it) =>
                                                                    it.id === f.id ? { ...it, tokens: it.tokens.filter((x) => x.id !== t.id) } : it
                                                                ),
                                                            }))
                                                        }
                                                        aria-label={`Remove ${t.token}`}
                                                    >
                                                        ×
                                                    </button>
                                                </span>
                                            ))
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="mt-2 text-xs text-slate-500">
                            Không còn phải nhập JSON thủ công. Nếu muốn bỏ validate payload thì xoá hết fields hoặc xoá hết rules trong mỗi field.
                        </div>

                        {rulesJsonOpen ? (
                            <div className="mt-2">
                                <div className="text-[11px] font-medium text-slate-600">JSON preview</div>
                                <textarea
                                    className={[
                                        "mt-1 w-full rounded-md border border-slate-200 bg-slate-50 p-3 font-mono text-xs outline-none shadow-sm",
                                        "focus:border-slate-400 focus:ring-2 focus:ring-slate-200",
                                    ].join(" ")}
                                    rows={8}
                                    readOnly
                                    value={JSON.stringify(buildValidationRulesRecord(editor.validation_fields) ?? {}, null, 2)}
                                />
                            </div>
                        ) : null}
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
                {receiveHelp ? (
                    <div className="mt-2 text-xs text-slate-700">
                        <b>Cách dùng:</b> {receiveHelp}
                    </div>
                ) : null}
                <pre className="mt-3 overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs">{secretValue}</pre>
                <div className="mt-2 flex items-center gap-2">
                    <Button
                        variant="primary"
                        className="h-8 px-2 py-1 text-xs"
                        onClick={async () => {
                            const ok = await copyToClipboard(secretValue);
                            toastOnce(ok ? "Đã copy" : "Không copy được. Tip: chọn text rồi Cmd/Ctrl+C.");
                        }}
                    >
                        Copy
                    </Button>
                    <Button
                        variant="ghost"
                        className="h-8 px-2 py-1 text-xs"
                        onClick={() => openReceiveUrl(receiveUrl)}
                    >
                        Xem Receive URL
                    </Button>
                </div>
            </Modal>

            <Modal
                open={urlOpen}
                title="Receive URL"
                onClose={() => setUrlOpen(false)}
                footer={
                    <div className="flex items-center justify-end gap-2">
                        <Button variant="ghost" onClick={() => setUrlOpen(false)}>
                            Đóng
                        </Button>
                        <Button
                            variant="primary"
                            onClick={async () => {
                                const ok = await copyToClipboard(urlValue);
                                toastOnce(ok ? "Đã copy Receive URL" : "Không copy được. Tip: chọn text rồi Cmd/Ctrl+C.");
                            }}
                        >
                            Copy URL
                        </Button>
                    </div>
                }
            >
                <div className="text-sm text-slate-700">
                    Đây là link bên thứ 3 sẽ gọi để gửi postback.
                </div>
                <pre className="mt-3 overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs font-mono">{urlValue}</pre>
            </Modal>
        </div>
    );
}
