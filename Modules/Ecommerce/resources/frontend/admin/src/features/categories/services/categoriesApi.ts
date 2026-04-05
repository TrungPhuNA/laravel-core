import { api } from "../../../shared/lib/api";
import type { Category } from "../types";

export async function listCategories() {
  const res = await api.get("/ecm/admin/categories", { params: { per_page: 200, sort: "position,id" } });
  return (res.data?.data?.items ?? []) as Category[];
}

export async function createCategory(input: Partial<Category>) {
  const res = await api.post("/ecm/admin/categories", input);
  return (res.data?.data?.category ?? null) as Category | null;
}

export async function updateCategory(id: number, input: Partial<Category>) {
  const res = await api.put(`/ecm/admin/categories/${id}`, input);
  return (res.data?.data?.category ?? null) as Category | null;
}

export async function deleteCategory(id: number) {
  await api.delete(`/ecm/admin/categories/${id}`);
}

