import type { ApiMetaPagination, ApiResponseSuccess } from "@shared/http/types";
import { buildQuery } from "@shared/http/query";
import { api } from "../../../shared/lib/api";
import type { WebhookDestination, WebhookDispatchLog, WebhookDispatchLogDetail } from "../types";

export type ListDestinationsParams = {
    page?: number;
    per_page?: number;
    sort?: string;
    filters?: {
        name?: string;
        is_active?: 0 | 1;
        http_method?: string;
        send_mode?: "merge" | "mapped_only";
        created_at?: string; // from,to
    };
};

export async function listDestinations(webhookId: number, params: ListDestinationsParams): Promise<{ items: WebhookDestination[]; meta: ApiMetaPagination }> {
    const qs = buildQuery(params as any);
    const res = await api.get<ApiResponseSuccess<{ items: WebhookDestination[] }>>(`/webhooks/${webhookId}/destinations${qs}`);
    if (res.data.status !== "success") throw res.data;
    return {
        items: res.data.data.items ?? [],
        meta: (res.data.meta as ApiMetaPagination) ?? { page: 1, per_page: params.per_page ?? 20, total: 0, last_page: 1, from: null, to: null },
    };
}

export type DestinationInput = {
    name: string;
    url: string;
    http_method?: "GET" | "POST" | "PUT" | "PATCH" | "DELETE";
    is_active?: boolean;
    headers?: Record<string, unknown> | null;
    send_mode?: "merge" | "mapped_only";
    field_mappings?: Array<{ from: string; to: string }> | null;
    drop_mapped_sources?: boolean;
    timeout_seconds?: number;
};

export async function createDestination(webhookId: number, input: DestinationInput): Promise<{ destination: WebhookDestination }> {
    const res = await api.post<ApiResponseSuccess<{ destination: WebhookDestination }>>(`/webhooks/${webhookId}/destinations`, input);
    if (res.data.status !== "success") throw res.data;
    return res.data.data;
}

export async function updateDestination(webhookId: number, destinationId: number, input: Partial<DestinationInput>): Promise<{ destination: WebhookDestination }> {
    const res = await api.put<ApiResponseSuccess<{ destination: WebhookDestination }>>(`/webhooks/${webhookId}/destinations/${destinationId}`, input);
    if (res.data.status !== "success") throw res.data;
    return res.data.data;
}

export async function deleteDestination(webhookId: number, destinationId: number): Promise<void> {
    const res = await api.delete<ApiResponseSuccess<null>>(`/webhooks/${webhookId}/destinations/${destinationId}`);
    if (res.data.status !== "success") throw res.data;
}

export type ListDispatchesParams = {
    page?: number;
    per_page?: number;
    sort?: string;
    filters?: {
        status?: "pending" | "success" | "failed";
        destination_id?: number;
        response_status?: number;
        created_at?: string; // from,to
    };
};

export async function listDispatches(webhookId: number, params: ListDispatchesParams): Promise<{ items: WebhookDispatchLog[]; meta: ApiMetaPagination }> {
    const qs = buildQuery(params as any);
    const res = await api.get<ApiResponseSuccess<{ items: WebhookDispatchLog[] }>>(`/webhooks/${webhookId}/dispatches${qs}`);
    if (res.data.status !== "success") throw res.data;
    return {
        items: res.data.data.items ?? [],
        meta: (res.data.meta as ApiMetaPagination) ?? { page: 1, per_page: params.per_page ?? 20, total: 0, last_page: 1, from: null, to: null },
    };
}

export async function getDispatch(webhookId: number, dispatchId: number): Promise<{ dispatch: WebhookDispatchLogDetail }> {
    const res = await api.get<ApiResponseSuccess<{ dispatch: WebhookDispatchLogDetail }>>(`/webhooks/${webhookId}/dispatches/${dispatchId}`);
    if (res.data.status !== "success") throw res.data;
    return res.data.data;
}

export async function getDispatchStats(webhookId: number, days: number = 30): Promise<any[]> {
    const res = await api.get<ApiResponseSuccess<{ stats: any[] }>>(`/webhooks/${webhookId}/dispatch-stats?days=${days}`);
    if (res.data.status !== "success") throw res.data;
    return res.data.data.stats;
}

