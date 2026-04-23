export type WebhookChannel = {
    id: number;
    user_id: number;
    name: string;
    type: string;
    public_id: string;
    is_active: boolean;
    allowed_methods: string[] | null;
    auth_type: "none" | "token" | "hmac";
    has_auth_secret: boolean;
    validation_rules: Record<string, unknown> | null;
    description: string | null;
    last_received_at: string | null;
    created_at: string | null;
    updated_at: string | null;
};

