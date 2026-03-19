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
import { fetchQueueJobDetail, fetchQueueJobs } from "../services/queueApi";
import type { QueueJob } from "../types";

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

export default function QueueJobsPage() {
  const [loading, setLoading] = React.useState(false);
  const [error, setError] = React.useState<Err>(null);

  const [items, setItems] = React.useState<QueueJob[]>([]);
  const [meta, setMeta] = React.useState<ApiMetaPagination>({
    page: 1,
    per_page: 20,
    total: 0,
    last_page: 1,
    from: null,
    to: null,
  });

  const [filters, setFilters] = React.useState({
    status: "pending" as "pending" | "reserved" | "delayed" | "all",
    queue: "",
    created_at: "",
  });

  const [detailOpen, setDetailOpen] = React.useState(false);
  const [detailLoading, setDetailLoading] = React.useState(false);
  const [detail, setDetail] = React.useState<{ job: QueueJob; payload: string } | null>(null);

  async function reload(next?: Partial<{ page: number; per_page: number }>) {
    const page = next?.page ?? meta.page;
    const per_page = next?.per_page ?? meta.per_page;

    setLoading(true);
    setError(null);
    try {
      const res = await fetchQueueJobs({
        page,
        per_page,
        filters: {
          status: filters.status,
          queue: filters.queue,
          created_at: filters.created_at,
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
      const d = await fetchQueueJobDetail(id);
      setDetail(d);
    } catch (e) {
      const job = items.find((x) => x.id === id);
      if (job) {
        setDetail({ job, payload: prettyJson(e) });
      } else {
        setError(e);
        setDetailOpen(false);
      }
    } finally {
      setDetailLoading(false);
    }
  }

  const errView = error ? normalizeError(error) : null;

  return (
    <div className="space-y-4">
      <div className="flex items-end justify-between gap-3">
        <div>
          <div className="text-lg font-semibold">Jobs</div>
          <div className="text-sm text-slate-600">Bảng `jobs` (database driver).</div>
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
          <Button
            variant="primary"
            onClick={() => reload({ page: 1 })}
            disabled={loading}
          >
            Áp dụng
          </Button>
        }
      >
        <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
          <div>
            <div className="text-xs font-medium text-slate-600">Trạng thái</div>
            <Select
              value={filters.status}
              onChange={(e) => setFilters({ ...filters, status: e.target.value as any })}
            >
              <option value="pending">pending</option>
              <option value="reserved">reserved</option>
              <option value="delayed">delayed</option>
              <option value="all">all</option>
            </Select>
          </div>
          <div>
            <div className="text-xs font-medium text-slate-600">Queue</div>
            <Input
              placeholder="default"
              value={filters.queue}
              onChange={(e) => setFilters({ ...filters, queue: e.target.value })}
            />
          </div>
          <div>
            <div className="text-xs font-medium text-slate-600">Created at (from,to unix ts)</div>
            <Input
              placeholder="1700000000,1700100000"
              value={filters.created_at}
              onChange={(e) => setFilters({ ...filters, created_at: e.target.value })}
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
                <th className="py-2 pr-4">Status</th>
                <th className="py-2 pr-4">Attempts</th>
                <th className="py-2 pr-4">Display</th>
                <th className="py-2 pr-4">Created</th>
                <th className="py-2 pr-2">Payload</th>
              </tr>
            </thead>
            <tbody>
              {items.map((it) => (
                <tr
                  key={it.id}
                  className="border-b last:border-b-0 hover:bg-slate-50 cursor-pointer"
                  onClick={() => openDetail(it.id)}
                >
                  <td className="py-2 pr-4 font-medium">{it.id}</td>
                  <td className="py-2 pr-4 text-slate-600">{it.queue}</td>
                  <td className="py-2 pr-4">
                    <Badge tone={it.status === "reserved" ? "warning" : it.status === "delayed" ? "info" : "success"}>
                      {it.status}
                    </Badge>
                  </td>
                  <td className="py-2 pr-4">{it.attempts}</td>
                  <td className="py-2 pr-4 text-slate-700">{it.display_name ?? it.job ?? "-"}</td>
                  <td className="py-2 pr-4 text-slate-600">{it.created_at ?? "-"}</td>
                  <td className="py-2 pr-2 font-mono text-xs text-slate-700">{shortText(it.payload_preview, 120)}</td>
                </tr>
              ))}
              {items.length === 0 ? (
                <tr>
                  <td colSpan={7} className="py-6 text-center text-slate-500">
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
        title={detail ? `Job #${detail.job.id}` : "Chi tiết job"}
        onClose={() => setDetailOpen(false)}
      >
        {detailLoading ? <div className="text-sm text-slate-600">Đang tải...</div> : null}
        {detail ? (
          <div className="space-y-3">
            <div className="grid grid-cols-1 gap-2 md:grid-cols-3">
              <Info label="Queue" value={detail.job.queue} />
              <Info label="Status" value={detail.job.status} />
              <Info label="Attempts" value={String(detail.job.attempts)} />
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
