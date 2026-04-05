import { api } from "../../../shared/lib/api";

export async function fetchDashboardOverview() {
  const res = await api.get("/ecm/admin/dashboard/overview");
  return res.data?.data ?? null;
}

