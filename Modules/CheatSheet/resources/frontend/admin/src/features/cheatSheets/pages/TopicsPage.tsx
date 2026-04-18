import React from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import Badge from "@shared/ui/Badge";
import { prettyJson } from "@shared/lib/format";
import type { ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { fetchTopics, type CheatSheetTopicItem } from "../services/cheatSheetsApi";
import { useNavigate } from "react-router-dom";

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

export default function TopicsPage() {
  const nav = useNavigate();

  const [loading, setLoading] = React.useState(false);
  const [error, setError] = React.useState<Err>(null);
  const [items, setItems] = React.useState<CheatSheetTopicItem[]>([]);

  const [q, setQ] = React.useState("");

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const res = await fetchTopics({ q: q.trim() || undefined, limit: 100 });
      setItems(res);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  React.useEffect(() => {
    reload();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const errView = error ? normalizeError(error) : null;

  function openTopic(t: CheatSheetTopicItem) {
    nav(`/?tag=${encodeURIComponent(t.name)}`);
  }

  return (
    <div className="space-y-4">
      <div className="flex flex-wrap items-end justify-between gap-3">
        <div>
          <div className="text-lg font-semibold">Chủ đề</div>
          <div className="text-sm text-slate-600">Tổng hợp theo tags (giống “topic / subject”).</div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={reload} disabled={loading}>
            Tải lại
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <Card
        title="Tìm chủ đề"
        actions={
          <div className="flex items-center gap-2">
            <Input
              placeholder="Search tag..."
              value={q}
              onChange={(e) => setQ(e.target.value)}
              onKeyDown={(e) => {
                if (e.key === "Enter") reload();
              }}
            />
            <Button variant="ghost" onClick={reload} disabled={loading}>
              Lọc
            </Button>
          </div>
        }
      >
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          {items.map((t) => (
            <button
              key={t.id}
              type="button"
              onClick={() => openTopic(t)}
              className={[
                "text-left rounded-2xl border border-slate-200 bg-white p-4 shadow-sm",
                "hover:shadow-md hover:border-slate-300 transition-all",
                "focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-sky-200",
              ].join(" ")}
            >
              <div className="flex items-center justify-between gap-3">
                <div className="font-semibold text-slate-900 truncate">{t.name}</div>
                <Badge tone={t.count > 0 ? "success" : "info"}>{t.count}</Badge>
              </div>
              <div className="mt-2 text-xs text-slate-600">
                Click để xem cheat sheets thuộc chủ đề này
              </div>
            </button>
          ))}

          {!loading && items.length === 0 ? (
            <div className="text-sm text-slate-600">Chưa có tag/chủ đề nào.</div>
          ) : null}
        </div>
      </Card>
    </div>
  );
}

