import React from "react";
import Card from "@shared/ui/Card";
import Badge from "@shared/ui/Badge";
import Alert from "@shared/ui/Alert";
import Button from "@shared/ui/Button";
import { prettyJson } from "@shared/lib/format";
import type { ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { fetchDashboardOverview } from "../services/dashboardApi";

type Err = ApiResponseFail | ApiResponseError | Error | unknown;

function normalizeError(err: Err): { title: string; details?: string } {
  if (err && typeof err === "object" && "status" in err) {
    const anyErr = err as ApiResponseFail | ApiResponseError;
    const code = (anyErr as any).code ? ` (${(anyErr as any).code})` : "";
    const trace = (anyErr as any).trace_id ? `\ntrace_id: ${(anyErr as any).trace_id}` : "";
    return { title: `${anyErr.message}${code}`, details: trace || prettyJson((anyErr as any).data ?? {}) };
  }
  if (err instanceof Error) return { title: err.message };
  return { title: "Có lỗi xảy ra", details: prettyJson(err) };
}

function CardStat({ label, value }: { label: string; value: React.ReactNode }) {
  return (
    <div className="rounded-2xl border border-slate-200 bg-white p-4">
      <div className="text-xs font-semibold text-slate-600">{label}</div>
      <div className="mt-1 text-2xl font-semibold tracking-tight">{value}</div>
    </div>
  );
}

export default function DashboardPage() {
  const [loading, setLoading] = React.useState(false);
  const [data, setData] = React.useState<any>(null);
  const [error, setError] = React.useState<Err>(null);

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const d = await fetchDashboardOverview();
      setData(d);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  React.useEffect(() => {
    reload();
  }, []);

  const errView = error ? normalizeError(error) : null;
  const cards = data?.cards ?? {};
  const byStatus = (data?.orders_by_status ?? []) as Array<{ status: string; total: number }>;

  return (
    <div className="space-y-4">
      <div className="flex items-end justify-between gap-3">
        <div>
          <div className="text-lg font-semibold">Dashboard</div>
          <div className="text-sm text-slate-600">Tổng quan bán hàng theo shop.</div>
        </div>
        <Button variant="ghost" onClick={reload} disabled={loading}>
          Tải lại
        </Button>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <CardStat label="Đơn hôm nay" value={cards.orders_today ?? 0} />
        <CardStat label="Doanh thu hôm nay" value={`${cards.revenue_today ?? 0}`} />
        <CardStat label="Tổng đơn" value={cards.orders_total ?? 0} />
        <CardStat label="Khách hàng" value={cards.customers_total ?? 0} />
        <CardStat label="Sản phẩm" value={cards.products_total ?? 0} />
        <CardStat label="Sắp hết hàng (<=5)" value={cards.low_stock_products ?? 0} />
      </div>

      <Card title="Đơn theo trạng thái">
        <div className="flex flex-wrap gap-2">
          {byStatus.length === 0 ? <div className="text-sm text-slate-500">Chưa có dữ liệu.</div> : null}
          {byStatus.map((s) => (
            <Badge key={s.status} tone="neutral">
              {s.status}: {s.total}
            </Badge>
          ))}
        </div>
      </Card>
    </div>
  );
}

