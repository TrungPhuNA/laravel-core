import { api } from "../../../shared/lib/api";
import type { Domain, DomainCheckLog, DomainListParams } from "../types";

export async function listDomains(params: DomainListParams = {}) {
    const res = await api.get("/monitor/domains", {
        params: {
            page: params.page ?? 1,
            per_page: params.per_page ?? 20,
            filters: params.search ? { domain: params.search } : undefined,
            sort: params.sort,
        },
    });
    return res.data;
}

export async function createDomain(domain: string) {
    const res = await api.post("/monitor/domains", { domain });
    return res.data;
}

export async function importDomains(domains: string[]) {
    const res = await api.post("/monitor/domains/bulk", { domains });
    return res.data;
}

export async function checkDomain(id: number): Promise<Domain> {
    const res = await api.post(`/monitor/domains/${id}/check`);
    return res.data.data?.domain;
}

export async function getDomainLogs(id: number, limit = 20): Promise<DomainCheckLog[]> {
    const res = await api.get(`/monitor/domains/${id}/logs`, { params: { limit } });
    return res.data.data?.logs ?? [];
}

export async function updateDomain(id: number, data: { note?: string | null; is_active?: boolean }) {
    const res = await api.put(`/monitor/domains/${id}`, data);
    return res.data;
}

export async function deleteDomain(id: number) {
    const res = await api.delete(`/monitor/domains/${id}`);
    return res.data;
}