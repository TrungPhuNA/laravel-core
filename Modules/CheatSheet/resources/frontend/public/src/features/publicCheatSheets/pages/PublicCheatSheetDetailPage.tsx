import React from "react";
import { Link, useParams } from "react-router-dom";
import Alert from "@shared/ui/Alert";
import Badge from "@shared/ui/Badge";
import Button from "@shared/ui/Button";
import { MarkdownView } from "@shared/lib/markdown";
import { formatDateTime, prettyJson } from "@shared/lib/format";
import type { ApiResponseError, ApiResponseFail } from "@shared/http/types";
import { fetchPublicCheatSheet } from "../services/publicCheatSheetsApi";
import type { PublicCheatSheet } from "../types";

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

export default function PublicCheatSheetDetailPage() {
  const { id } = useParams();
  const sheetId = Number(id);

  const [loading, setLoading] = React.useState(false);
  const [error, setError] = React.useState<Err>(null);
  const [item, setItem] = React.useState<PublicCheatSheet | null>(null);

  async function load() {
    if (!Number.isFinite(sheetId) || sheetId <= 0) return;
    setLoading(true);
    setError(null);
    try {
      const res = await fetchPublicCheatSheet(sheetId);
      setItem(res);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  React.useEffect(() => {
    load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [sheetId]);

  const errView = error ? normalizeError(error) : null;

  return (
    <div className="space-y-5">
      <div className="flex items-center justify-between gap-3">
        <Link className="text-sm text-slate-300 hover:text-white underline underline-offset-2" to="/all">
          ← Back
        </Link>
        <a className="text-sm text-slate-300 hover:text-white underline underline-offset-2" href="/admin/cheat-sheets">
          Edit in admin
        </a>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      {item ? (
        <div className="rounded-3xl border border-white/10 bg-slate-950/35 backdrop-blur p-6">
          <div className="text-2xl font-extrabold tracking-tight">{item.title}</div>
          <div className="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-400">
            <span>updated: {formatDateTime(item.updated_at)}</span>
            {item.author?.name ? <span>• by {item.author.name}</span> : null}
          </div>

          <div className="mt-4 flex flex-wrap gap-1.5">
            {(item.tags ?? []).map((t) => (
              <Badge key={t.id} tone="info">
                {t.name}
              </Badge>
            ))}
          </div>

          <div className="mt-6 rounded-2xl border border-white/10 bg-white p-5 text-slate-900">
            <MarkdownView markdown={item.body ?? ""} />
          </div>
        </div>
      ) : loading ? (
        <div className="text-sm text-slate-300">Loading...</div>
      ) : null}
    </div>
  );
}

