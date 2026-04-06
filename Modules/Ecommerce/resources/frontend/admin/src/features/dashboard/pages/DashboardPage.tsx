import React from "react";
import Card from "@shared/ui/Card";
import Badge from "@shared/ui/Badge";
import Alert from "@shared/ui/Alert";
import Button from "@shared/ui/Button";
import Select from "@shared/ui/Select";
import { prettyJson } from "@shared/lib/format";
import type { ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { fetchDashboardOverview, fetchDashboardRevenue, fetchDashboardShopsSummary } from "../services/dashboardApi";

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

type Point = { x: string; revenue: number; orders: number };

function clamp(n: number, min: number, max: number) {
  return Math.max(min, Math.min(max, n));
}

function LineChart({ points, height = 180 }: { points: Point[]; height?: number }) {
  const w = 900;
  const h = height;
  const pad = 12;

  const maxY = Math.max(...points.map((p) => p.revenue), 0);
  const minY = 0;

  const usableW = w - pad * 2;
  const usableH = h - pad * 2;

  function xAt(i: number) {
    if (points.length <= 1) return pad;
    return pad + (usableW * i) / (points.length - 1);
  }

  function yAt(v: number) {
    if (maxY <= minY) return pad + usableH;
    const t = (v - minY) / (maxY - minY);
    return pad + usableH - t * usableH;
  }

  const d = points
    .map((p, i) => `${i === 0 ? "M" : "L"} ${xAt(i).toFixed(1)} ${yAt(p.revenue).toFixed(1)}`)
    .join(" ");

  return (
    <div className="w-full overflow-hidden">
      <svg viewBox={`0 0 ${w} ${h}`} className="w-full">
        <defs>
          <linearGradient id="revGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stopColor="rgba(2,132,199,0.35)" />
            <stop offset="100%" stopColor="rgba(2,132,199,0)" />
          </linearGradient>
        </defs>

        <rect x="0" y="0" width={w} height={h} fill="white" rx="16" />
        <path d={`${d} L ${xAt(points.length - 1)} ${yAt(0)} L ${xAt(0)} ${yAt(0)} Z`} fill="url(#revGrad)" />
        <path d={d} fill="none" stroke="rgb(2,132,199)" strokeWidth="3" />
      </svg>
      <div className="mt-2 flex items-center justify-between text-[11px] text-slate-500">
        <div>{points[0]?.x ?? ""}</div>
        <div>{points[points.length - 1]?.x ?? ""}</div>
      </div>
    </div>
  );
}

function GrowthBars({ points }: { points: Point[] }) {
  // % thay đổi revenue so với điểm trước.
  const g = points.map((p, i) => {
    if (i === 0) return 0;
    const prev = points[i - 1]!.revenue;
    if (prev <= 0) return p.revenue > 0 ? 100 : 0;
    return ((p.revenue - prev) / prev) * 100;
  });

  const absMax = Math.max(...g.map((x) => Math.abs(x)), 1);

  return (
    <div className="grid grid-cols-12 gap-1 items-end">
      {g.slice(-60).map((v, idx) => {
        const h = clamp((Math.abs(v) / absMax) * 100, 0, 100);
        const tone = v >= 0 ? "bg-emerald-500/70" : "bg-rose-500/70";
        return (
          <div key={idx} className="h-[72px] rounded-md bg-slate-100 flex items-end overflow-hidden">
            <div className={`w-full ${tone}`} style={{ height: `${h}%` }} />
          </div>
        );
      })}
    </div>
  );
}

export default function DashboardPage() {
  const [loading, setLoading] = React.useState(false);
  const [data, setData] = React.useState<any>(null);
  const [rev, setRev] = React.useState<any>(null);
  const [shopsSummary, setShopsSummary] = React.useState<any>(null);
  const [error, setError] = React.useState<Err>(null);
  const [range, setRange] = React.useState<"7d" | "30d" | "90d" | "12m">("30d");

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const [d, r, s] = await Promise.all([
        fetchDashboardOverview(),
        fetchDashboardRevenue(range),
        fetchDashboardShopsSummary(range),
      ]);
      setData(d);
      setRev(r);
      setShopsSummary(s);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  React.useEffect(() => {
    reload();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [range]);

  const errView = error ? normalizeError(error) : null;
  const cards = data?.cards ?? {};
  const byStatus = (data?.orders_by_status ?? []) as Array<{ status: string; total: number }>;
  const series = (rev?.series ?? []) as Point[];
  const totals = rev?.totals ?? {};
  const growth = totals?.revenue_growth_percent;
  const shop = data?.shop ?? rev?.shop ?? null;
  const byShop = (shopsSummary?.items ?? []) as Array<{ shop: { id: number; code: string; name: string }; revenue: number; orders_paid: number }>;

  return (
    <div className="space-y-4">
      <div className="flex items-end justify-between gap-3">
        <div>
          <div className="text-lg font-semibold">Dashboard</div>
          <div className="text-sm text-slate-600">
            Tổng quan bán hàng theo shop{" "}
            {shop ? (
              <>
                <span className="text-slate-400">•</span>{" "}
                <span className="font-medium text-slate-900">
                  {shop.name} ({shop.code})
                </span>
              </>
            ) : null}
          </div>
        </div>
        <div className="flex items-center gap-2">
          <Select value={range} onChange={(e) => setRange(e.target.value as any)} aria-label="Range" className="w-[140px]">
            <option value="7d">7 ngày</option>
            <option value="30d">30 ngày</option>
            <option value="90d">90 ngày</option>
            <option value="12m">12 tháng</option>
          </Select>
          <Button variant="ghost" onClick={reload} disabled={loading}>
            Tải lại
          </Button>
        </div>
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

      <Card
        title="Biểu đồ doanh thu"
        actions={
          <div className="flex items-center gap-2">
            <Badge tone="neutral">Total: {Number(totals.revenue ?? 0).toFixed(0)}</Badge>
            {growth === null || growth === undefined ? null : growth >= 0 ? (
              <Badge tone="success">Growth: +{Number(growth).toFixed(1)}%</Badge>
            ) : (
              <Badge tone="danger">Growth: {Number(growth).toFixed(1)}%</Badge>
            )}
          </div>
        }
      >
        {series.length > 0 ? <LineChart points={series} /> : <div className="text-sm text-slate-500">Chưa có dữ liệu.</div>}
      </Card>

      <Card title="Biểu đồ tăng trưởng (so với điểm trước)">
        {series.length > 1 ? <GrowthBars points={series} /> : <div className="text-sm text-slate-500">Chưa có dữ liệu.</div>}
      </Card>

      <Card title="Thống kê theo shop (so sánh)">
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">Shop</th>
                <th className="py-2 pr-4">Revenue</th>
                <th className="py-2 pr-4">Orders paid</th>
              </tr>
            </thead>
            <tbody>
              {byShop.slice(0, 10).map((row) => (
                <tr key={row.shop.id} className="border-b last:border-0">
                  <td className="py-2 pr-4">
                    <div className="font-medium">{row.shop.name}</div>
                    <div className="text-xs text-slate-500 font-mono">
                      {row.shop.code} (#{row.shop.id})
                    </div>
                  </td>
                  <td className="py-2 pr-4 text-slate-700">{Number(row.revenue ?? 0).toFixed(0)}</td>
                  <td className="py-2 pr-4 text-slate-700">{row.orders_paid ?? 0}</td>
                </tr>
              ))}
              {byShop.length === 0 ? (
                <tr>
                  <td className="py-6 text-center text-slate-500" colSpan={3}>
                    Chưa có dữ liệu.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </Card>

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
