import { api } from "../../../shared/lib/api";
import type { Customer } from "../types";

export async function listCustomers() {
  const res = await api.get("/ecm/admin/customers", { params: { per_page: 50, sort: "-id" } });
  return (res.data?.data?.items ?? []) as Customer[];
}

export async function createCustomer(input: Partial<Customer>) {
  const res = await api.post("/ecm/admin/customers", input);
  return (res.data?.data?.customer ?? null) as Customer | null;
}

export async function updateCustomer(id: number, input: Partial<Customer>) {
  const res = await api.put(`/ecm/admin/customers/${id}`, input);
  return (res.data?.data?.customer ?? null) as Customer | null;
}

export async function deleteCustomer(id: number) {
  await api.delete(`/ecm/admin/customers/${id}`);
}

