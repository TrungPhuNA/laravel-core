import { api } from "../../../shared/lib/api";
import type { Order } from "../types";

export async function listOrders() {
  const res = await api.get("/ecm/admin/orders", { params: { per_page: 50, sort: "-id" } });
  return (res.data?.data?.items ?? []) as Order[];
}

export async function getOrder(id: number) {
  const res = await api.get(`/ecm/admin/orders/${id}`);
  return (res.data?.data?.order ?? null) as any;
}

export async function createOrder(input: any) {
  const res = await api.post("/ecm/admin/orders", input);
  return (res.data?.data?.order ?? null) as any;
}

export async function updateOrder(id: number, input: any) {
  const res = await api.put(`/ecm/admin/orders/${id}`, input);
  return (res.data?.data?.order ?? null) as any;
}

export async function deleteOrder(id: number) {
  await api.delete(`/ecm/admin/orders/${id}`);
}

