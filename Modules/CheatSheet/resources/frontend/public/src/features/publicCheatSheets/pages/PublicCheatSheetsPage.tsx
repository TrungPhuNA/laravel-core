import React from "react";
import { Link, useParams, useSearchParams } from "react-router-dom";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import Badge from "@shared/ui/Badge";
import Pagination from "@shared/ui/Pagination";
import { formatDateTime, prettyJson } from "@shared/lib/format";
import type { ApiMetaPagination, ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { fetchPublicCheatSheets } from "../services/publicCheatSheetsApi";
import type { PublicCheatSheetListItem } from "../types";

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

const emptyMeta: ApiMetaPagination = {
  page: 1,
  per_page: 20,
  total: 0,
  last_page: 1,
  from: null,
  to: null,
};

export default function PublicCheatSheetsPage() {
  const params = useParams();
  const [searchParams, setSearchParams] = useSearchParams();

  const topicSlug = params.slug ? String(params.slug) : null;

  const [loading, setLoading] = React.useState(false);
  const [error, setError] = React.useState<Err>(null);
  const [items, setItems] = React.useState<PublicCheatSheetListItem[]>([]);
  const [meta, setMeta] = React.useState<ApiMetaPagination>(emptyMeta);

  const [q, setQ] = React.useState(() => searchParams.get("q") ?? "");

  function syncUrl(next?: { page?: number; per_page?: number }) {
    const sp = new URLSearchParams();
    const qv = q.trim();
    if (qv) sp.set("q", qv);
    const page = next?.page ?? meta.page ?? 1;
    const perPage = next?.per_page ?? meta.per_page ?? 20;
    if (page && page !== 1) sp.set("page", String(page));
    if (perPage && perPage !== 20) sp.set("per_page", String(perPage));
    setSearchParams(sp, { replace: true });
  }

  async function reload(next?: { page?: number; per_page?: number }) {
    setLoading(true);
    setError(null);
    try {
      const page = next?.page ?? meta.page ?? 1;
      const perPage = next?.per_page ?? meta.per_page ?? 20;

      const res = await fetchPublicCheatSheets({
        page,
        per_page: perPage,
        filters: {
          q: q.trim() || undefined,
          tag: topicSlug ?? undefined,
        },
        sort: "-published_at,-updated_at,-id",
      });
      setItems(res.items);
      setMeta(res.meta);
      syncUrl({ page, per_page: perPage });
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  React.useEffect(() => {
    const page = Number(searchParams.get("page") ?? "1");
    const perPage = Number(searchParams.get("per_page") ?? "20");
    reload({
      page: Number.isFinite(page) && page > 0 ? Math.floor(page) : 1,
      per_page: Number.isFinite(perPage) && perPage > 0 ? Math.floor(perPage) : 20,
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [topicSlug]);

  const errView = error ? normalizeError(error) : null;

  return (
    <div className="space-y-5">
      <div className="flex flex-wrap items-end justify-between gap-3">
        <div>
          <div className="text-2xl font-extrabold tracking-tight">
            {topicSlug ? `Topic: ${topicSlug}` : "All public cheat sheets"}
          </div>
          <div className="text-slate-300 text-sm">
            {topicSlug ? (
              <Link className="underline underline-offset-2 hover:text-white" to="/">
                ← Back to topics
              </Link>
            ) : (
              <span>Browse everything public.</span>
            )}
          </div>
        </div>
        <div className="flex items-center gap-2">
          <Input
            className="bg-white/90"
            placeholder="Search..."
            value={q}
            onChange={(e) => setQ(e.target.value)}
            onKeyDown={(e) => {
              if (e.key === "Enter") reload({ page: 1 });
            }}
          />
          <Button variant="ghost" onClick={() => reload({ page: 1 })} disabled={loading}>
            Search
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
        {items.map((it) => (
          <Link
            key={it.id}
            to={`/${it.id}`}
            className={[
              "rounded-2xl border border-white/10 bg-slate-950/30 backdrop-blur p-5",
              "hover:bg-slate-950/40 hover:border-white/15 transition-all",
            ].join(" ")}
          >
            <div className="flex items-start justify-between gap-3">
              <div className="min-w-0">
                <div className="text-xs text-slate-400">#{it.id}</div>
                <div className="font-semibold text-white truncate">{it.title}</div>
              </div>
              <div className="text-[11px] text-slate-400 whitespace-nowrap">{formatDateTime(it.updated_at)}</div>
            </div>

            <div className="mt-3 text-sm text-slate-200 whitespace-pre-wrap break-words">{it.excerpt}</div>

            <div className="mt-4 flex flex-wrap gap-1.5">
              {(it.tags ?? []).slice(0, 6).map((t) => (
                <Badge key={t.id} tone="info">
                  {t.name}
                </Badge>
              ))}
              {(it.tags ?? []).length > 6 ? (
                <span className="text-xs text-slate-400">+{(it.tags ?? []).length - 6}</span>
              ) : null}
            </div>
          </Link>
        ))}
      </div>

      <Card
        title=""
        className="bg-transparent border-0 shadow-none"
        bodyClassName="p-0"
      >
        <Pagination meta={meta} onChange={(next) => reload(next)} />
      </Card>
    </div>
  );
}

