import { api } from "../../../shared/lib/api";
import type { ApiResponseSuccess } from "@shared/http/types";
import type { SettingItem } from "../types";

export async function fetchAllSettings(): Promise<SettingItem[]> {
  const res = await api.get<ApiResponseSuccess<{ items: SettingItem[] }>>("/settings");
  if (res.data.status !== "success") throw res.data;
  return res.data.data.items ?? [];
}

export type UpsertSettingInput = {
  key: string;
  value: unknown;
  group?: string | null;
  is_public?: boolean | null;
  description?: string | null;
};

export async function upsertSettings(items: UpsertSettingInput[]): Promise<void> {
  const res = await api.put<ApiResponseSuccess<null>>("/settings", { items });
  if (res.data.status !== "success") throw res.data;
}
