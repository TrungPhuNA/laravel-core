import type { ApiMetaPagination, ApiResponseSuccess } from "@shared/http/types";
import { buildQuery } from "@shared/http/query";
import { api } from "../../../shared/lib/api";
import type { UserItem } from "../types";

export type UsersParams = {
  page?: number;
  per_page?: number;
  sort?: string;
  filters?: {
    name?: string;
    email?: string;
    user_type?: string;
  };
};

export async function fetchUsers(params: UsersParams): Promise<{ items: UserItem[]; meta: ApiMetaPagination }> {
  const qs = buildQuery(params as any);
  const res = await api.get<ApiResponseSuccess<{ items: UserItem[] }>>(`/users${qs}`);
  if (res.data.status !== "success") throw res.data;
  return {
    items: res.data.data.items ?? [],
    meta: (res.data.meta as ApiMetaPagination) ?? {
      page: params.page ?? 1,
      per_page: params.per_page ?? 20,
      total: 0,
      last_page: 1,
      from: null,
      to: null,
    },
  };
}

