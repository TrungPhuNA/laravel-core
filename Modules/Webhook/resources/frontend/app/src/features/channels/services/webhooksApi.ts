import type { ApiMetaPagination, ApiResponseSuccess } from "@shared/http/types";
import { buildQuery } from "@shared/http/query";
import { api } from "../../../shared/lib/api";
import type { WebhookChannel } from "../types";

export type ListChannelsParams = {
    page?: number;
    per_page?: number;
    sort?: string;
    filters?: {
        name?: string;
        is_active?: 0 | 1;
        auth_type?: "none" | "token" | "hmac";
        created_at?: string; // from,to
    };
};

export async function listChannels(params: ListChannelsParams): Promise<{ items: WebhookChannel[]; meta: ApiMetaPagination }> {
    const qs = buildQuery(params as any);
    const res = await api.get<ApiResponseSuccess<{ items: WebhookChannel[] }>>(`/webhooks${qs}`);
    if (res.data.status !== "success") throw res.data;
    return {
        items: res.data.data.items ?? [],
        meta: (res.data.meta as ApiMetaPagination) ?? {
            page: 1,
            per_page: params.per_page ?? 20,
            total: 0,
            last_page: 1,
            from: null,
            to: null,
        },
    };
}

export type CreateChannelInput = {
    name: string;
    is_active?: boolean;
    allowed_methods?: Array<"GET" | "POST">;
    auth_type?: "none" | "token" | "hmac";
    validation_rules?: Record<string, string>;
    description?: string | null;
};

export async function createChannel(input: CreateChannelInput): Promise<{ webhook: WebhookChannel; auth_token?: string | null; auth_secret?: string | null; receive_url: string }> {
    const res = await api.post<ApiResponseSuccess<{ webhook: WebhookChannel; auth_token?: string | null; auth_secret?: string | null; receive_url: string }>>(
        `/webhooks`,
        input
    );
    if (res.data.status !== "success") throw res.data;
    return res.data.data;
}

export type UpdateChannelInput = Partial<CreateChannelInput> & {
    rotate_token?: boolean;
    rotate_secret?: boolean;
};

export async function updateChannel(id: number, input: UpdateChannelInput): Promise<{ webhook: WebhookChannel; auth_token?: string | null; auth_secret?: string | null; receive_url: string }> {
    const res = await api.put<ApiResponseSuccess<{ webhook: WebhookChannel; auth_token?: string | null; auth_secret?: string | null; receive_url: string }>>(
        `/webhooks/${id}`,
        input
    );
    if (res.data.status !== "success") throw res.data;
    return res.data.data;
}

export async function deleteChannel(id: number): Promise<void> {
    const res = await api.delete<ApiResponseSuccess<null>>(`/webhooks/${id}`);
    if (res.data.status !== "success") throw res.data;
}

export async function rotateToken(id: number): Promise<{ webhook: WebhookChannel; auth_token: string; receive_url: string }> {
    const res = await api.post<ApiResponseSuccess<{ webhook: WebhookChannel; auth_token: string; receive_url: string }>>(`/webhooks/${id}/rotate-token`, {});
    if (res.data.status !== "success") throw res.data;
    return res.data.data;
}

export async function rotateSecret(id: number): Promise<{ webhook: WebhookChannel; auth_secret: string; receive_url: string }> {
    const res = await api.post<ApiResponseSuccess<{ webhook: WebhookChannel; auth_secret: string; receive_url: string }>>(`/webhooks/${id}/rotate-secret`, {});
    if (res.data.status !== "success") throw res.data;
    return res.data.data;
}

