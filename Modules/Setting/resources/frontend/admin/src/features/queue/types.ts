import type { ApiMetaPagination } from "@shared/http/types";

export type QueueStats = {
  jobs: {
    pending: number;
    reserved: number;
    delayed: number;
    total: number;
  };
  failed_jobs: {
    total: number;
  };
  batches: {
    total: number;
  };
};

export type QueueJob = {
  id: number;
  queue: string;
  attempts: number;
  status: "pending" | "reserved" | "delayed";
  reserved_at: string | null;
  available_at: string | null;
  created_at: string | null;
  display_name: string | null;
  job: string | null;
  payload_preview: string;
};

export type QueueFailedJob = {
  id: number;
  uuid: string;
  connection: string;
  queue: string;
  failed_at: string | null;
  exception_preview: string;
  payload_preview: string;
};

export type QueueBatch = {
  id: string;
  name: string;
  total_jobs: number;
  pending_jobs: number;
  failed_jobs: number;
  cancelled_at: string | null;
  created_at: string | null;
  finished_at: string | null;
};

export type ApiPaginated<T> = {
  items: T[];
  meta: ApiMetaPagination;
};
