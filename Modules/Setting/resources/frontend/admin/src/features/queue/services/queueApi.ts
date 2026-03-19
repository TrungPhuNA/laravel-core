import type { ApiMetaPagination, ApiResponseSuccess } from "@shared/http/types";
import { buildQuery } from "@shared/http/query";
import { api } from "../../../shared/lib/api";
import type { QueueBatch, QueueFailedJob, QueueJob, QueueStats } from "../types";

export async function fetchQueueStats(): Promise<QueueStats> {
  const res = await api.get<ApiResponseSuccess<QueueStats>>("/settings/queue/stats");
  if (res.data.status !== "success") throw res.data;
  return res.data.data;
}

export type QueueJobsParams = {
  page?: number;
  per_page?: number;
  sort?: string;
  filters?: {
    queue?: string;
    status?: "pending" | "reserved" | "delayed" | "all";
    created_at?: string; // "from,to" (unix ts)
  };
};

export async function fetchQueueJobs(params: QueueJobsParams): Promise<{ items: QueueJob[]; meta: ApiMetaPagination }> {
  const qs = buildQuery(params as any);
  const res = await api.get<ApiResponseSuccess<{ items: QueueJob[] }>>(`/settings/queue/jobs${qs}`);
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

export async function fetchQueueJobDetail(id: number): Promise<{ job: QueueJob; payload: string }> {
  const res = await api.get<ApiResponseSuccess<{ job: QueueJob; payload: string }>>(`/settings/queue/jobs/${id}`);
  if (res.data.status !== "success") throw res.data;
  return res.data.data;
}

export type FailedJobsParams = {
  page?: number;
  per_page?: number;
  sort?: string;
  filters?: {
    queue?: string;
    connection?: string;
    failed_at?: string; // "from,to" (date)
  };
};

export async function fetchFailedJobs(params: FailedJobsParams): Promise<{ items: QueueFailedJob[]; meta: ApiMetaPagination }> {
  const qs = buildQuery(params as any);
  const res = await api.get<ApiResponseSuccess<{ items: QueueFailedJob[] }>>(`/settings/queue/failed-jobs${qs}`);
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

export async function fetchFailedJobDetail(id: number): Promise<{ job: QueueFailedJob; payload: string; exception: string }> {
  const res = await api.get<ApiResponseSuccess<{ job: QueueFailedJob; payload: string; exception: string }>>(
    `/settings/queue/failed-jobs/${id}`
  );
  if (res.data.status !== "success") throw res.data;
  return res.data.data;
}

export async function retryFailedJob(id: number): Promise<void> {
  const res = await api.post<ApiResponseSuccess<any>>(`/settings/queue/failed-jobs/${id}/retry`, {});
  if (res.data.status !== "success") throw res.data;
}

export async function forgetFailedJob(id: number): Promise<void> {
  const res = await api.delete<ApiResponseSuccess<any>>(`/settings/queue/failed-jobs/${id}`);
  if (res.data.status !== "success") throw res.data;
}

export type BatchesParams = {
  page?: number;
  per_page?: number;
  sort?: string;
  filters?: {
    name?: string;
  };
};

export async function fetchBatches(params: BatchesParams): Promise<{ items: QueueBatch[]; meta: ApiMetaPagination }> {
  const qs = buildQuery(params as any);
  const res = await api.get<ApiResponseSuccess<{ items: QueueBatch[] }>>(`/settings/queue/batches${qs}`);
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

export async function fetchBatchDetail(id: string): Promise<{ batch: QueueBatch; options: string; failed_job_ids: string }> {
  const res = await api.get<ApiResponseSuccess<{ batch: QueueBatch; options: string; failed_job_ids: string }>>(
    `/settings/queue/batches/${encodeURIComponent(id)}`
  );
  if (res.data.status !== "success") throw res.data;
  return res.data.data;
}
