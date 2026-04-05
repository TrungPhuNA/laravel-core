import { api } from "../../../shared/lib/api";
import type { Product } from "../types";

export async function listProducts() {
  const res = await api.get("/ecm/admin/products", { params: { per_page: 50, include: "categories", sort: "-id" } });
  return (res.data?.data?.items ?? []) as Product[];
}

export async function createProduct(input: any) {
  const res = await api.post("/ecm/admin/products", input);
  return (res.data?.data?.product ?? null) as Product | null;
}

export async function updateProduct(id: number, input: any) {
  const res = await api.put(`/ecm/admin/products/${id}`, input);
  return (res.data?.data?.product ?? null) as Product | null;
}

export async function deleteProduct(id: number) {
  await api.delete(`/ecm/admin/products/${id}`);
}

