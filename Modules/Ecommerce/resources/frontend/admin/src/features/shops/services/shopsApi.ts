import { api } from "../../../shared/lib/api";

export type Shop = {
  id: number;
  code: string;
  name: string;
  domain: string | null;
  timezone: string;
  currency: string;
  is_active: boolean;
};

export type ShopUser = {
  id: number;
  name: string | null;
  email: string | null;
  user_type: string;
  shop_role: string;
};

export async function listShops() {
  const res = await api.get("/ecm/admin/shops");
  return (res.data?.data?.items ?? []) as Shop[];
}

export async function createShop(input: Partial<Shop>) {
  const res = await api.post("/ecm/admin/shops", input);
  return (res.data?.data?.shop ?? null) as Shop | null;
}

export async function updateShop(id: number, input: Partial<Shop>) {
  const res = await api.put(`/ecm/admin/shops/${id}`, input);
  return (res.data?.data?.shop ?? null) as Shop | null;
}

export async function deleteShop(id: number) {
  await api.delete(`/ecm/admin/shops/${id}`);
}

export async function listShopUsers(shopId: number) {
  const res = await api.get(`/ecm/admin/shops/${shopId}/users`);
  return (res.data?.data?.items ?? []) as ShopUser[];
}

export async function syncShopUsers(shopId: number, members: Array<{ user_id: number; role: string }>) {
  await api.put(`/ecm/admin/shops/${shopId}/users`, { members });
}

export async function detachShopUser(shopId: number, userId: number) {
  await api.delete(`/ecm/admin/shops/${shopId}/users/${userId}`);
}

export async function lookupUsers(q: string) {
  const res = await api.get("/ecm/admin/users", { params: { q, limit: 20 } });
  return (res.data?.data?.items ?? []) as Array<{ id: number; name: string | null; email: string | null; user_type: string }>;
}

