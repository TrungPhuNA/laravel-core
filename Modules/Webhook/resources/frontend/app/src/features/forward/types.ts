export type WebhookFieldMapping = {
    from: string;
    to: string;
};

export type WebhookDestination = {
    id: number;
    webhook_id: number;
    name: string;
    url: string;
    http_method: string;
    is_active: boolean;
    type: string;
    headers?: Record<string, unknown> | null;
    send_mode: "merge" | "mapped_only";
    field_mappings?: WebhookFieldMapping[] | null;
    drop_mapped_sources: boolean;
    timeout_seconds: number;
    created_at?: string | null;
    updated_at?: string | null;
};

export type WebhookDispatchLog = {
    id: number;
    webhook_id: number;
    webhook_request_id: number;
    destination_id: number;
    status: "pending" | "success" | "failed";
    dispatched_at?: string | null;
    duration_ms?: number | null;
    request_body_preview?: string | null;
    response_status?: number | null;
    response_body_preview?: string | null;
    error_type?: string | null;
    error_message?: string | null;
    created_at?: string | null;
};

export type WebhookDispatchLogDetail = {
    id: number;
    webhook_id: number;
    webhook_request_id: number;
    destination_id: number;
    status: "pending" | "success" | "failed";
    dispatched_at?: string | null;
    duration_ms?: number | null;
    request_body?: string | null;
    response_status?: number | null;
    response_headers?: Record<string, unknown> | null;
    response_body?: string | null;
    error_type?: string | null;
    error_message?: string | null;
    created_at?: string | null;
    updated_at?: string | null;
};

