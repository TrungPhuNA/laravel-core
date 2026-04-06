import { api } from "../../../shared/lib/api";

export async function fetchDashboardOverview() {
  const res = await api.get("/ecm/admin/dashboard/overview");
  return res.data?.data ?? null;
}

export async function fetchDashboardRevenue(range: "7d" | "30d" | "90d" | "12m") {
  const res = await api.get("/ecm/admin/dashboard/revenue", { params: { range } });
  return res.data?.data ?? null;
}

export async function fetchDashboardShopsSummary(range: "7d" | "30d" | "90d" | "12m") {
  const res = await api.get("/ecm/admin/dashboard/shops-summary", { params: { range } });
  return res.data?.data ?? null;
}
