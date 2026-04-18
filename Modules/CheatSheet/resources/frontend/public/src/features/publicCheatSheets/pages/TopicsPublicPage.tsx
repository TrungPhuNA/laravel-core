import React from "react";
import { Link } from "react-router-dom";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import Badge from "@shared/ui/Badge";
import { prettyJson } from "@shared/lib/format";
import type { ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { fetchPublicTopics } from "../services/publicCheatSheetsApi";
import type { PublicTopicItem } from "../types";

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

export default function TopicsPublicPage() {
  const [loading, setLoading] = React.useState(false);
  const [error, setError] = React.useState<Err>(null);
  const [items, setItems] = React.useState<PublicTopicItem[]>([]);
  const [q, setQ] = React.useState("");

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const res = await fetchPublicTopics({ q: q.trim() || undefined, limit: 120 });
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

  return (
    <div className="space-y-5">
      <div className="space-y-2">
        <div className="text-3xl font-extrabold tracking-tight">Cheat sheets</div>
        <div className="text-slate-300 max-w-2xl">
          Browse theo chủ đề. Click vào 1 topic để xem cheat sheets tương ứng.
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <Card
        title="Topics"
        actions={
          <div className="flex items-center gap-2">
            <Input
              className="bg-white/90"
              placeholder="Search topic..."
              value={q}
              onChange={(e) => setQ(e.target.value)}
              onKeyDown={(e) => {
                if (e.key === "Enter") reload();
              }}
            />
            <Button variant="ghost" onClick={reload} disabled={loading}>
              Search
            </Button>
          </div>
        }
      >
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          {items.map((t) => (
            <Link
              key={t.slug}
              to={`/topic/${encodeURIComponent(t.slug)}`}
              className={[
                "rounded-2xl border border-slate-200/60 bg-white p-4 text-slate-900 shadow-sm",
                "hover:shadow-md hover:border-slate-300 transition-all",
                "focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-sky-200",
              ].join(" ")}
            >
              <div className="flex items-center justify-between gap-3">
                <div className="font-semibold truncate">{t.name}</div>
                <Badge tone={t.count > 0 ? "success" : "info"}>{t.count}</Badge>
              </div>
              <div className="mt-2 text-xs text-slate-600">View sheets in this topic</div>
            </Link>
          ))}

          {!loading && items.length === 0 ? (
            <div className="text-sm text-slate-300">Chưa có topic nào.</div>
          ) : null}
        </div>
      </Card>
    </div>
  );
}

