import type { ApiMetaPagination, ApiResponseSuccess } from "@shared/http/types";
import { buildQuery } from "@shared/http/query";
import { api } from "../../../shared/lib/api";
import type { CheatSheetItem, CheatSheetTagItem } from "../types";

export type CheatSheetsParams = {
  page?: number;
  per_page?: number;
  sort?: string;
  filters?: {
    q?: string;
    tag?: string;
    visibility?: "private" | "unlisted" | "public";
  };
};

export async function fetchCheatSheets(
  params: CheatSheetsParams
): Promise<{ items: CheatSheetItem[]; meta: ApiMetaPagination }> {
  const qs = buildQuery(params as any);
  const res = await api.get<ApiResponseSuccess<{ items: CheatSheetItem[] }>>(`/cheat-sheets${qs}`);
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

export type CreateCheatSheetInput = {
  title: string;
  body: string;
  visibility?: "private" | "unlisted" | "public";
  tags?: string[];
  published_at?: string | null;
};

export async function createCheatSheet(input: CreateCheatSheetInput): Promise<CheatSheetItem> {
  const res = await api.post<ApiResponseSuccess<{ cheat_sheet: CheatSheetItem }>>(`/cheat-sheets`, input);
  if (res.data.status !== "success") throw res.data;
  return res.data.data.cheat_sheet;
}

export type UpdateCheatSheetInput = Partial<CreateCheatSheetInput>;

export async function updateCheatSheet(id: number, input: UpdateCheatSheetInput): Promise<CheatSheetItem> {
  const res = await api.put<ApiResponseSuccess<{ cheat_sheet: CheatSheetItem }>>(`/cheat-sheets/${id}`, input);
  if (res.data.status !== "success") throw res.data;
  return res.data.data.cheat_sheet;
}

export async function deleteCheatSheet(id: number): Promise<void> {
  const res = await api.delete<ApiResponseSuccess<any>>(`/cheat-sheets/${id}`);
  if (res.data.status !== "success") throw res.data;
}

export async function fetchCheatSheetDetail(id: number): Promise<CheatSheetItem> {
  const res = await api.get<ApiResponseSuccess<{ cheat_sheet: CheatSheetItem }>>(`/cheat-sheets/${id}`);
  if (res.data.status !== "success") throw res.data;
  return res.data.data.cheat_sheet;
}

export async function fetchTagSuggestions(params: { q?: string; limit?: number }): Promise<CheatSheetTagItem[]> {
  const qs = buildQuery(params as any);
  const res = await api.get<ApiResponseSuccess<{ tags: CheatSheetTagItem[] }>>(`/cheat-sheets/tags${qs}`);
  if (res.data.status !== "success") throw res.data;
  return res.data.data.tags ?? [];
}

export type CheatSheetTopicItem = CheatSheetTagItem & { count: number };

export async function fetchTopics(params: { q?: string; limit?: number }): Promise<CheatSheetTopicItem[]> {
  const qs = buildQuery(params as any);
  const res = await api.get<ApiResponseSuccess<{ topics: CheatSheetTopicItem[] }>>(`/cheat-sheets/topics${qs}`);
  if (res.data.status !== "success") throw res.data;
  return res.data.data.topics ?? [];
}
