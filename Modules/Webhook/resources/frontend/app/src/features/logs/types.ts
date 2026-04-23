export type WebhookRequestLog = {
    id: number;
    webhook_id: number;
    method: string;
    ip: string | null;
    status: string;
    error_type: string | null;
    error_message: string | null;
    received_at: string | null;
    body_preview: string;
    mapped_payload_preview: string | null;
    created_at: string | null;
};

export type WebhookRequestLogDetail = {
    id: number;
    webhook_id: number;
    method: string;
    ip: string | null;
    status: string;
    error_type: string | null;
    error_message: string | null;
    headers: Record<string, unknown> | null;
    query: Record<string, unknown> | null;
    body: string | null;
    mapped_payload: Record<string, unknown> | null;
    received_at: string | null;
    created_at: string | null;
};

