import type { ApiMetaPagination, ApiResponseSuccess } from "@shared/http/types";
import { buildQuery } from "@shared/http/query";
import { api } from "../../../shared/lib/api";
import type { PublicCheatSheet, PublicCheatSheetListItem, PublicTopicItem } from "../types";

export type PublicCheatSheetsParams = {
  page?: number;
  per_page?: number;
  sort?: string;
  filters?: {
    q?: string;
    tag?: string;
  };
};

export async function fetchPublicCheatSheets(
  params: PublicCheatSheetsParams
): Promise<{ items: PublicCheatSheetListItem[]; meta: ApiMetaPagination }> {
  const qs = buildQuery(params as any);
  const res = await api.get<ApiResponseSuccess<{ items: PublicCheatSheetListItem[] }>>(`/public/cheat-sheets${qs}`);
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

export async function fetchPublicCheatSheet(id: number): Promise<PublicCheatSheet> {
  const res = await api.get<ApiResponseSuccess<{ cheat_sheet: PublicCheatSheet }>>(`/public/cheat-sheets/${id}`);
  if (res.data.status !== "success") throw res.data;
  return res.data.data.cheat_sheet;
}

export async function fetchPublicTopics(params: { q?: string; limit?: number }): Promise<PublicTopicItem[]> {
  const qs = buildQuery(params as any);
  const res = await api.get<ApiResponseSuccess<{ topics: PublicTopicItem[] }>>(`/public/cheat-sheets/topics${qs}`);
  if (res.data.status !== "success") throw res.data;
  return res.data.data.topics ?? [];
}

