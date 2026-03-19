import React from "react";
import { Link } from "react-router-dom";
import Card from "@shared/ui/Card";
import Alert from "@shared/ui/Alert";
import Badge from "@shared/ui/Badge";
import type { ApiResponseFail, ApiResponseError } from "@shared/http/types";
import { prettyJson } from "@shared/lib/format";
import { fetchQueueStats } from "../services/queueApi";
import type { QueueStats } from "../types";

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

export default function QueueOverviewPage() {
  const [loading, setLoading] = React.useState(false);
  const [error, setError] = React.useState<Err>(null);
  const [stats, setStats] = React.useState<QueueStats | null>(null);

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const s = await fetchQueueStats();
      setStats(s);
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

  return (
    <div className="space-y-4">
      <div className="flex items-end justify-between gap-3">
        <div>
          <div className="text-lg font-semibold">Hàng đợi</div>
          <div className="text-sm text-slate-600">Thống kê và thao tác debug jobs/failed/batches.</div>
        </div>
        <button
          className="text-sm text-slate-600 hover:text-slate-900"
          onClick={reload}
          disabled={loading}
          type="button"
        >
          Tải lại
        </button>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
        <Card title="Jobs">
          <div className="flex items-center justify-between">
            <div className="text-3xl font-semibold">{stats?.jobs.total ?? "-"}</div>
            <Badge tone="info">database</Badge>
          </div>
          <div className="mt-3 grid grid-cols-3 gap-2 text-sm">
            <Stat label="Pending" value={stats?.jobs.pending} />
            <Stat label="Reserved" value={stats?.jobs.reserved} />
            <Stat label="Delayed" value={stats?.jobs.delayed} />
          </div>
          <div className="mt-4 flex items-center gap-3 text-sm">
            <Link className="text-slate-900 font-medium hover:underline" to="/queue/jobs">
              Xem danh sách jobs
            </Link>
          </div>
        </Card>

        <Card title="Failed Jobs">
          <div className="text-3xl font-semibold">{stats?.failed_jobs.total ?? "-"}</div>
          <div className="mt-4 text-sm">
            <Link className="text-slate-900 font-medium hover:underline" to="/queue/failed-jobs">
              Xem failed jobs
            </Link>
          </div>
        </Card>

        <Card title="Batches">
          <div className="text-3xl font-semibold">{stats?.batches.total ?? "-"}</div>
          <div className="mt-4 text-sm">
            <Link className="text-slate-900 font-medium hover:underline" to="/queue/batches">
              Xem batches
            </Link>
          </div>
        </Card>
      </div>

      <Card title="Gợi ý">
        <div className="text-sm text-slate-700">
          <div>
            Status jobs được suy ra theo các cột: <code className="rounded bg-slate-100 px-1 py-0.5">reserved_at</code>{" "}
            và <code className="rounded bg-slate-100 px-1 py-0.5">available_at</code>.
          </div>
          <div className="mt-2">
            Nếu bạn muốn thao tác sâu hơn (flush, prune, retry all), có thể dùng các command Artisan tương ứng (trên
            server).
          </div>
        </div>
      </Card>
    </div>
  );
}

function Stat(props: { label: string; value?: number }) {
  return (
    <div className="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2">
      <div className="text-xs text-slate-500">{props.label}</div>
      <div className="font-semibold text-slate-900">{props.value ?? "-"}</div>
    </div>
  );
}
