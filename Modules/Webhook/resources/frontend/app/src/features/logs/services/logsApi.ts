import type { ApiMetaPagination, ApiResponseSuccess } from "@shared/http/types";
import { buildQuery } from "@shared/http/query";
import { api } from "../../../shared/lib/api";
import type { WebhookRequestLog, WebhookRequestLogDetail } from "../types";

export type ListLogsParams = {
    page?: number;
    per_page?: number;
    sort?: string;
    filters?: {
        method?: "GET" | "POST";
        ip?: string;
        received_at?: string; // from,to
    };
};

export async function listLogs(webhookId: number, params: ListLogsParams): Promise<{ items: WebhookRequestLog[]; meta: ApiMetaPagination }> {
    const qs = buildQuery(params as any);
    const res = await api.get<ApiResponseSuccess<{ items: WebhookRequestLog[] }>>(`/webhooks/${webhookId}/requests${qs}`);
    if (res.data.status !== "success") throw res.data;

    // Backend trả về meta: { pagination: { ... } } hoặc meta phẳng
    const rawMeta = res.data.meta as any;
    const pagination = rawMeta?.pagination || rawMeta;

    return {
        items: res.data.data.items ?? [],
        meta: (pagination as ApiMetaPagination) ?? {
            page: 1,
            per_page: params.per_page ?? 20,
            total: 0,
            last_page: 1,
            from: null,
            to: null,
        },
    };
}

export async function getLog(webhookId: number, requestId: number): Promise<WebhookRequestLogDetail> {
    const res = await api.get<ApiResponseSuccess<{ request: WebhookRequestLogDetail }>>(`/webhooks/${webhookId}/requests/${requestId}`);
    if (res.data.status !== "success") throw res.data;
    return res.data.data.request;
}

export async function pruneLogs(webhookId: number, input: { days?: number; before?: string }): Promise<{ deleted: number; before: string }> {
    const res = await api.post<ApiResponseSuccess<{ deleted: number; before: string }>>(`/webhooks/${webhookId}/requests/prune`, input);
    if (res.data.status !== "success") throw res.data;
    return res.data.data;
}

export type WebhookStats = {
    date: string;
    success_count: number;
    failed_count: number;
}

export async function getStats(webhookId: number, days: number = 30): Promise<WebhookStats[]> {
    const res = await api.get<ApiResponseSuccess<{ stats: WebhookStats[] }>>(`/webhooks/${webhookId}/stats?days=${days}`);
    if (res.data.status !== "success") throw res.data;
    return res.data.data.stats;
}

