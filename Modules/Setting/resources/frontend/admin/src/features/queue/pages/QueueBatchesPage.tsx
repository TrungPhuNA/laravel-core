import React from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import Modal from "@shared/ui/Modal";
import Pagination from "@shared/ui/Pagination";
import Badge from "@shared/ui/Badge";
import type { ApiMetaPagination, ApiResponseFail, ApiResponseError } from "@shared/http/types";
import { prettyJson, shortText } from "@shared/lib/format";
import { fetchBatchDetail, fetchBatches } from "../services/queueApi";
import type { QueueBatch } from "../types";

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

export default function QueueBatchesPage() {
  const [loading, setLoading] = React.useState(false);
  const [error, setError] = React.useState<Err>(null);

  const [items, setItems] = React.useState<QueueBatch[]>([]);
  const [meta, setMeta] = React.useState<ApiMetaPagination>({
    page: 1,
    per_page: 20,
    total: 0,
    last_page: 1,
    from: null,
    to: null,
  });

  const [name, setName] = React.useState("");

  const [detailOpen, setDetailOpen] = React.useState(false);
  const [detailLoading, setDetailLoading] = React.useState(false);
  const [detail, setDetail] = React.useState<{ batch: QueueBatch; options: string; failed_job_ids: string } | null>(null);

  async function reload(next?: Partial<{ page: number; per_page: number }>) {
    const page = next?.page ?? meta.page;
    const per_page = next?.per_page ?? meta.per_page;

    setLoading(true);
    setError(null);
    try {
      const res = await fetchBatches({
        page,
        per_page,
        filters: { name },
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

  async function openDetail(id: string) {
    setDetailOpen(true);
    setDetailLoading(true);
    setDetail(null);
    try {
      const d = await fetchBatchDetail(id);
      setDetail(d);
    } catch (e) {
      const batch = items.find((x) => x.id === id);
      setError(e);
      if (batch) {
        setDetail({
          batch,
          options: "",
          failed_job_ids: "",
        });
      } else {
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
          <div className="text-lg font-semibold">Batches</div>
          <div className="text-sm text-slate-600">Bảng `job_batches`.</div>
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
        <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
          <div>
            <div className="text-xs font-medium text-slate-600">Tên batch (LIKE)</div>
            <Input placeholder="import" value={name} onChange={(e) => setName(e.target.value)} />
          </div>
          <div className="text-xs text-slate-500 self-end">
            Tip: batch thường dùng cho các tác vụ background lớn (import/export), có thể xem progress tại đây.
          </div>
        </div>
      </Card>

      <Card title="Danh sách">
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">ID</th>
                <th className="py-2 pr-4">Name</th>
                <th className="py-2 pr-4">Jobs</th>
                <th className="py-2 pr-4">Pending</th>
                <th className="py-2 pr-4">Failed</th>
                <th className="py-2 pr-4">State</th>
                <th className="py-2 pr-2">Created</th>
              </tr>
            </thead>
            <tbody>
              {items.map((it) => {
                const finished = it.finished_at !== null;
                const cancelled = it.cancelled_at !== null;
                const tone = cancelled ? "danger" : finished ? "success" : "warning";
                const state = cancelled ? "cancelled" : finished ? "finished" : "running";

                return (
                  <tr
                    key={it.id}
                    className="border-b last:border-b-0 hover:bg-slate-50 cursor-pointer"
                    onClick={() => openDetail(it.id)}
                  >
                    <td className="py-2 pr-4 font-medium">{shortText(it.id, 14)}</td>
                    <td className="py-2 pr-4">{it.name}</td>
                    <td className="py-2 pr-4">{it.total_jobs}</td>
                    <td className="py-2 pr-4">{it.pending_jobs}</td>
                    <td className="py-2 pr-4">{it.failed_jobs}</td>
                    <td className="py-2 pr-4">
                      <Badge tone={tone as any}>{state}</Badge>
                    </td>
                    <td className="py-2 pr-2 text-slate-600">{it.created_at ?? "-"}</td>
                  </tr>
                );
              })}
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
        title={detail ? `Batch: ${detail.batch.name}` : "Chi tiết batch"}
        onClose={() => setDetailOpen(false)}
      >
        {detailLoading ? <div className="text-sm text-slate-600">Đang tải...</div> : null}
        {detail ? (
          <div className="space-y-3">
            <div className="grid grid-cols-1 gap-2 md:grid-cols-3">
              <Info label="Total jobs" value={String(detail.batch.total_jobs)} />
              <Info label="Pending jobs" value={String(detail.batch.pending_jobs)} />
              <Info label="Failed jobs" value={String(detail.batch.failed_jobs)} />
            </div>
            <div className="grid grid-cols-1 gap-2 md:grid-cols-3">
              <Info label="Created at" value={detail.batch.created_at ?? "-"} />
              <Info label="Finished at" value={detail.batch.finished_at ?? "-"} />
              <Info label="Cancelled at" value={detail.batch.cancelled_at ?? "-"} />
            </div>
            <div>
              <div className="text-xs font-medium text-slate-600">Options</div>
              <pre className="mt-1 overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs">
                {String(detail.options ?? "")}
              </pre>
            </div>
            <div>
              <div className="text-xs font-medium text-slate-600">Failed job IDs</div>
              <pre className="mt-1 overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs">
                {String(detail.failed_job_ids ?? "")}
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
