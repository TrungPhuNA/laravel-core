import React from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Select from "@shared/ui/Select";
import Button from "@shared/ui/Button";
import Badge from "@shared/ui/Badge";
import Alert from "@shared/ui/Alert";
import Modal from "@shared/ui/Modal";
import Pagination from "@shared/ui/Pagination";
import type { ApiMetaPagination, ApiResponseFail, ApiResponseError } from "@shared/http/types";
import { prettyJson, shortText } from "@shared/lib/format";
import {
  fetchFailedJobDetail,
  fetchFailedJobs,
  forgetFailedJob,
  retryFailedJob,
} from "../services/queueApi";
import type { QueueFailedJob } from "../types";

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

export default function QueueFailedJobsPage() {
  const [loading, setLoading] = React.useState(false);
  const [error, setError] = React.useState<Err>(null);

  const [items, setItems] = React.useState<QueueFailedJob[]>([]);
  const [meta, setMeta] = React.useState<ApiMetaPagination>({
    page: 1,
    per_page: 20,
    total: 0,
    last_page: 1,
    from: null,
    to: null,
  });

  const [filters, setFilters] = React.useState({
    queue: "",
    connection: "",
    failed_at: "",
  });

  const [detailOpen, setDetailOpen] = React.useState(false);
  const [detailLoading, setDetailLoading] = React.useState(false);
  const [detail, setDetail] = React.useState<{ job: QueueFailedJob; payload: string; exception: string } | null>(null);

  async function reload(next?: Partial<{ page: number; per_page: number }>) {
    const page = next?.page ?? meta.page;
    const per_page = next?.per_page ?? meta.per_page;

    setLoading(true);
    setError(null);
    try {
      const res = await fetchFailedJobs({
        page,
        per_page,
        filters: {
          queue: filters.queue,
          connection: filters.connection,
          failed_at: filters.failed_at,
        },
      });
      setItems(res.items);
      setMeta(res.meta);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  React.useEffect(() => {
    reload({ page: 1 });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  async function openDetail(id: number) {
    setDetailOpen(true);
    setDetailLoading(true);
    setDetail(null);
    try {
      const d = await fetchFailedJobDetail(id);
      setDetail(d);
    } catch (e) {
      const job = items.find((x) => x.id === id);
      if (job) {
        setDetail({
          job,
          payload: prettyJson(e),
          exception: "",
        });
      } else {
        setError(e);
        setDetailOpen(false);
      }
    } finally {
      setDetailLoading(false);
    }
  }

  async function doRetry(id: number) {
    setLoading(true);
    setError(null);
    try {
      await retryFailedJob(id);
      await reload();
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function doForget(id: number) {
    if (!confirm(`Xoá failed job #${id}?`)) return;
    setLoading(true);
    setError(null);
    try {
      await forgetFailedJob(id);
      await reload();
      setDetailOpen(false);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  const errView = error ? normalizeError(error) : null;

  return (
    <div className="space-y-4">
      <div className="flex items-end justify-between gap-3">
        <div>
          <div className="text-lg font-semibold">Failed Jobs</div>
          <div className="text-sm text-slate-600">Bảng `failed_jobs`.</div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={() => reload()} disabled={loading}>
            Tải lại
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <Card
        title="Bộ lọc"
        actions={
          <Button variant="primary" onClick={() => reload({ page: 1 })} disabled={loading}>
            Áp dụng
          </Button>
        }
      >
        <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
          <div>
            <div className="text-xs font-medium text-slate-600">Queue</div>
            <Input placeholder="default" value={filters.queue} onChange={(e) => setFilters({ ...filters, queue: e.target.value })} />
          </div>
          <div>
            <div className="text-xs font-medium text-slate-600">Connection</div>
            <Input
              placeholder="database"
              value={filters.connection}
              onChange={(e) => setFilters({ ...filters, connection: e.target.value })}
            />
          </div>
          <div>
            <div className="text-xs font-medium text-slate-600">Failed at (from,to YYYY-MM-DD)</div>
            <Input
              placeholder="2026-03-01,2026-03-31"
              value={filters.failed_at}
              onChange={(e) => setFilters({ ...filters, failed_at: e.target.value })}
            />
          </div>
        </div>
      </Card>

      <Card title="Danh sách">
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">ID</th>
                <th className="py-2 pr-4">Queue</th>
                <th className="py-2 pr-4">Conn</th>
                <th className="py-2 pr-4">Failed at</th>
                <th className="py-2 pr-4">Exception</th>
                <th className="py-2 pr-2">Hành động</th>
              </tr>
            </thead>
            <tbody>
              {items.map((it) => (
                <tr key={it.id} className="border-b last:border-b-0 hover:bg-slate-50">
                  <td className="py-2 pr-4 font-medium cursor-pointer" onClick={() => openDetail(it.id)}>
                    {it.id}
                  </td>
                  <td className="py-2 pr-4 text-slate-600">{it.queue}</td>
                  <td className="py-2 pr-4">
                    <Badge tone="info">{it.connection}</Badge>
                  </td>
                  <td className="py-2 pr-4 text-slate-600">{it.failed_at ?? "-"}</td>
                  <td className="py-2 pr-4 font-mono text-xs text-slate-700">{shortText(it.exception_preview, 120)}</td>
                  <td className="py-2 pr-2">
                    <div className="flex items-center gap-2">
                      <Button variant="ghost" onClick={() => openDetail(it.id)}>
                        Xem
                      </Button>
                      <Button variant="primary" onClick={() => doRetry(it.id)} disabled={loading}>
                        Retry
                      </Button>
                    </div>
                  </td>
                </tr>
              ))}
              {items.length === 0 ? (
                <tr>
                  <td colSpan={6} className="py-6 text-center text-slate-500">
                    Không có dữ liệu.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>

        <Pagination
          meta={meta}
          onChange={(next) => {
            setMeta((m) => ({ ...m, page: next.page, per_page: next.per_page }));
            reload(next);
          }}
        />
      </Card>

      <Modal
        open={detailOpen}
        title={detail ? `Failed job #${detail.job.id}` : "Chi tiết failed job"}
        onClose={() => setDetailOpen(false)}
        footer={
          detail ? (
            <div className="flex items-center justify-end gap-2">
              <Button variant="ghost" onClick={() => setDetailOpen(false)}>
                Đóng
              </Button>
              <Button variant="primary" onClick={() => doRetry(detail.job.id)} disabled={loading}>
                Retry
              </Button>
              <Button variant="danger" onClick={() => doForget(detail.job.id)} disabled={loading}>
                Xoá
              </Button>
            </div>
          ) : null
        }
      >
        {detailLoading ? <div className="text-sm text-slate-600">Đang tải...</div> : null}
        {detail ? (
          <div className="space-y-3">
            <div className="grid grid-cols-1 gap-2 md:grid-cols-3">
              <Info label="Queue" value={detail.job.queue} />
              <Info label="Connection" value={detail.job.connection} />
              <Info label="Failed at" value={detail.job.failed_at ?? "-"} />
            </div>
            <div>
              <div className="text-xs font-medium text-slate-600">Exception</div>
              <pre className="mt-1 overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs">
                {detail.exception}
              </pre>
            </div>
            <div>
              <div className="text-xs font-medium text-slate-600">Payload</div>
              <pre className="mt-1 overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs">
                {detail.payload}
              </pre>
            </div>
          </div>
        ) : null}
      </Modal>
    </div>
  );
}

function Info(props: { label: string; value: string }) {
  return (
    <div className="rounded-lg border border-slate-100 bg-white px-3 py-2">
      <div className="text-xs text-slate-500">{props.label}</div>
      <div className="text-sm font-medium">{props.value || "-"}</div>
    </div>
  );
}
