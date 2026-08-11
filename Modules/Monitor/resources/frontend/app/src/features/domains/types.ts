export type DomainBadge = "ok" | "soon" | "critical" | "expired" | "unknown" | "error";

export type Domain = {
    id: number;
    domain: string;
    note: string | null;
    is_active: boolean;
    expires_at: string | null;
    registrar: string | null;
    nameservers: string[];
    check_status: "unknown" | "ok" | "error";
    last_check_at: string | null;
    last_check_error: string | null;
    days_remaining: number | null;
    badge: DomainBadge;
    created_at: string | null;
    updated_at: string | null;
};

export type DomainCheckLog = {
    id: number;
    domain_id: number;
    status: "ok" | "error";
    expires_at_found: string | null;
    registrar: string | null;
    method: string | null;
    error_message: string | null;
    raw_response: string | null;
    checked_at: string | null;
};

export type DomainListParams = {
    page?: number;
    per_page?: number;
    search?: string;
    sort?: string;
};