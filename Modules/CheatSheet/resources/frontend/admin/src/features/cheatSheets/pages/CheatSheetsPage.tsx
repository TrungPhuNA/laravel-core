import React, { useMemo, useState } from "react";
import { useSearchParams } from "react-router-dom";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import Badge from "@shared/ui/Badge";
import Pagination from "@shared/ui/Pagination";
import Modal from "@shared/ui/Modal";
import Select from "@shared/ui/Select";
import { formatDateTime, prettyJson, shortText } from "@shared/lib/format";
import { MarkdownView } from "@shared/lib/markdown";
import type { ApiMetaPagination, ApiResponseError, ApiResponseFail } from "@shared/http/types";
import type { CheatSheetItem, CheatSheetTagItem } from "../types";
import {
  createCheatSheet,
  deleteCheatSheet,
  fetchCheatSheets,
  fetchTagSuggestions,
  updateCheatSheet,
} from "../services/cheatSheetsApi";

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

function parseTagsCsv(csv: string): string[] {
  const parts = csv
    .split(",")
    .map((x) => x.trim())
    .filter(Boolean);
  const uniq: string[] = [];
  for (const t of parts) if (!uniq.includes(t)) uniq.push(t);
  return uniq.slice(0, 50);
}

function tagsToCsv(tags: Array<{ name: string }>): string {
  return (tags ?? []).map((t) => t.name).filter(Boolean).join(", ");
}

function currentTagTerm(csv: string): string {
  const parts = csv.split(",");
  return (parts[parts.length - 1] ?? "").trim();
}

function addTagToCsv(csv: string, tagName: string): string {
  const tag = tagName.trim();
  if (!tag) return csv;

  const tags = parseTagsCsv(csv);
  if (tags.includes(tag)) return tags.join(", ");
  return [...tags, tag].join(", ");
}

export default function CheatSheetsPage() {
  const [searchParams, setSearchParams] = useSearchParams();

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Err>(null);
  const [items, setItems] = useState<CheatSheetItem[]>([]);
  const [meta, setMeta] = useState<ApiMetaPagination>(emptyMeta);

  const [q, setQ] = useState(() => searchParams.get("q") ?? "");
  const [tag, setTag] = useState(() => searchParams.get("tag") ?? "");
  const [visibility, setVisibility] = useState<string>(() => searchParams.get("visibility") ?? "all");

  const [viewOpen, setViewOpen] = useState(false);
  const [viewItem, setViewItem] = useState<CheatSheetItem | null>(null);

  const [editOpen, setEditOpen] = useState(false);
  const [editMode, setEditMode] = useState<"create" | "edit">("create");
  const [editing, setEditing] = useState<CheatSheetItem | null>(null);

  const [titleDraft, setTitleDraft] = useState("");
  const [bodyDraft, setBodyDraft] = useState("");
  const [visibilityDraft, setVisibilityDraft] = useState<"private" | "unlisted" | "public">("private");
  const [tagsDraft, setTagsDraft] = useState("");

  const [tagSuggestLoading, setTagSuggestLoading] = useState(false);
  const [tagSuggestItems, setTagSuggestItems] = useState<CheatSheetTagItem[]>([]);

  const [bodyTab, setBodyTab] = useState<"write" | "preview" | "help">("write");
  const [listView, setListView] = useState<"grid" | "table">("grid");

  const errView = error ? normalizeError(error) : null;

  function syncUrl(next?: { page?: number; per_page?: number }) {
    const sp = new URLSearchParams();

    const qv = q.trim();
    const tv = tag.trim();
    const vv = visibility.trim();

    if (qv) sp.set("q", qv);
    if (tv) sp.set("tag", tv);
    if (vv && vv !== "all") sp.set("visibility", vv);

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
      const term = q.trim();
      const tagTerm = tag.trim();
      const reqPage = next?.page ?? meta.page ?? 1;
      const reqPerPage = next?.per_page ?? meta.per_page ?? 20;
      const { items, meta } = await fetchCheatSheets({
        page: reqPage,
        per_page: reqPerPage,
        filters: {
          q: term || undefined,
          tag: tagTerm || undefined,
          visibility: visibility === "all" ? undefined : (visibility as any),
        },
        sort: "-updated_at,-id",
      });
      setItems(items);
      setMeta(meta);
      syncUrl({ page: reqPage, per_page: reqPerPage });
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
  }, []);

  function openCreate() {
    setEditMode("create");
    setEditing(null);
    setTitleDraft("");
    setBodyDraft("");
    setBodyTab("write");
    setVisibilityDraft("private");
    setTagsDraft("");
    setTagSuggestItems([]);
    setEditOpen(true);
  }

  function openEdit(item: CheatSheetItem) {
    setEditMode("edit");
    setEditing(item);
    setTitleDraft(item.title ?? "");
    setBodyDraft(item.body ?? "");
    setBodyTab("write");
    setVisibilityDraft(item.visibility ?? "private");
    setTagsDraft(tagsToCsv(item.tags ?? []));
    setTagSuggestItems([]);
    setEditOpen(true);
  }

  function openView(item: CheatSheetItem) {
    setViewItem(item);
    setViewOpen(true);
  }

  async function save() {
    setLoading(true);
    setError(null);
    try {
      const input = {
        title: titleDraft.trim(),
        body: bodyDraft,
        visibility: visibilityDraft,
        tags: parseTagsCsv(tagsDraft),
      };

      if (editMode === "create") {
        await createCheatSheet(input);
      } else if (editing) {
        await updateCheatSheet(editing.id, input);
      }

      setEditOpen(false);
      await reload({ page: 1, per_page: meta.per_page ?? 20 });
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function remove(item: CheatSheetItem) {
    if (!window.confirm(`Xoá cheat sheet #${item.id}?`)) return;

    setLoading(true);
    setError(null);
    try {
      await deleteCheatSheet(item.id);
      await reload({ page: 1, per_page: meta.per_page ?? 20 });
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  const canSave = titleDraft.trim() !== "" && bodyDraft.trim() !== "";

  const tagTerm = useMemo(() => currentTagTerm(tagsDraft), [tagsDraft]);

  React.useEffect(() => {
    let cancelled = false;
    const term = tagTerm.trim();

    if (term === "") {
      setTagSuggestItems([]);
      return;
    }

    const t = window.setTimeout(async () => {
      setTagSuggestLoading(true);
      try {
        const tags = await fetchTagSuggestions({ q: term, limit: 12 });
        if (!cancelled) setTagSuggestItems(tags);
      } catch {
        if (!cancelled) setTagSuggestItems([]);
      } finally {
        if (!cancelled) setTagSuggestLoading(false);
      }
    }, 250);

    return () => {
      cancelled = true;
      window.clearTimeout(t);
    };
  }, [tagTerm]);

  function pickSuggest(t: CheatSheetTagItem) {
    setTagsDraft((csv) => addTagToCsv(csv, t.name));
  }

  return (
    <div className="space-y-4">
      <div className="flex flex-wrap items-end justify-between gap-3">
        <div>
          <div className="text-lg font-semibold">Cheat sheets</div>
          <div className="text-sm text-slate-600">Ghi chú cá nhân (sẵn sàng cho public sau).</div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={() => reload()} disabled={loading}>
            Tải lại
          </Button>
          <Button variant="primary" onClick={openCreate} disabled={loading}>
            Tạo mới
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <Card
        title="Danh sách"
        actions={
          <div className="flex flex-wrap items-center gap-2">
            <Button
              variant={listView === "grid" ? "primary" : "ghost"}
              className="h-9 px-3 text-xs"
              onClick={() => setListView("grid")}
              disabled={loading}
            >
              Grid
            </Button>
            <Button
              variant={listView === "table" ? "primary" : "ghost"}
              className="h-9 px-3 text-xs"
              onClick={() => setListView("table")}
              disabled={loading}
            >
              Table
            </Button>
            <Input
              placeholder="Search (title/body)"
              value={q}
              onChange={(e) => setQ(e.target.value)}
              onKeyDown={(e) => {
                if (e.key === "Enter") reload({ page: 1 });
              }}
            />
            <Input
              placeholder="Tag (csv)"
              value={tag}
              onChange={(e) => setTag(e.target.value)}
              onKeyDown={(e) => {
                if (e.key === "Enter") reload({ page: 1 });
              }}
            />
            <Select
              value={visibility}
              onChange={(e) => {
                setVisibility(e.target.value);
                setTimeout(() => reload({ page: 1 }), 0);
              }}
              aria-label="Visibility"
            >
              <option value="all">Tất cả</option>
              <option value="private">private</option>
              <option value="unlisted">unlisted</option>
              <option value="public">public</option>
            </Select>
            <Button variant="ghost" onClick={() => reload({ page: 1 })} disabled={loading}>
              Lọc
            </Button>
          </div>
        }
      >
        {listView === "grid" ? (
          <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            {items.map((it) => (
              <div
                key={it.id}
                className={[
                  "rounded-2xl border border-slate-200 bg-white p-4 shadow-sm",
                  "hover:shadow-md hover:border-slate-300 transition-all",
                ].join(" ")}
              >
                <div className="flex items-start justify-between gap-3">
                  <div className="min-w-0">
                    <div className="text-xs text-slate-500">#{it.id}</div>
                    <div className="font-semibold text-slate-900 truncate">{it.title}</div>
                  </div>
                  <Badge tone={it.visibility === "public" ? "success" : it.visibility === "unlisted" ? "warning" : "info"}>
                    {it.visibility}
                  </Badge>
                </div>

                <div className="mt-2 text-xs text-slate-600 whitespace-pre-wrap break-words">
                  {shortText(it.body, 160)}
                </div>

                <div className="mt-3 flex flex-wrap gap-1.5">
                  {(it.tags ?? []).length ? (
                    it.tags.slice(0, 6).map((t) => (
                      <Badge key={t.id} tone="info">
                        {t.name}
                      </Badge>
                    ))
                  ) : (
                    <span className="text-xs text-slate-500">—</span>
                  )}
                  {(it.tags ?? []).length > 6 ? <span className="text-xs text-slate-500">+{(it.tags ?? []).length - 6}</span> : null}
                </div>

                <div className="mt-3 flex items-center justify-between gap-2">
                  <div className="text-[11px] text-slate-500">updated: {formatDateTime(it.updated_at)}</div>
                  <div className="flex items-center gap-2">
                    <Button variant="ghost" className="h-9 px-3 text-xs" onClick={() => openView(it)} disabled={loading}>
                      Xem
                    </Button>
                    <Button variant="ghost" className="h-9 px-3 text-xs" onClick={() => openEdit(it)} disabled={loading}>
                      Sửa
                    </Button>
                    <Button variant="danger" className="h-9 px-3 text-xs" onClick={() => remove(it)} disabled={loading}>
                      Xoá
                    </Button>
                  </div>
                </div>
              </div>
            ))}

            {!loading && items.length === 0 ? (
              <div className="text-sm text-slate-600">Chưa có cheat sheet nào.</div>
            ) : null}
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="ui-table">
              <thead className="ui-thead">
                <tr>
                  <th className="ui-th w-[90px]">ID</th>
                  <th className="ui-th min-w-[240px]">Tiêu đề</th>
                  <th className="ui-th w-[120px]">Visibility</th>
                  <th className="ui-th min-w-[220px]">Tags</th>
                  <th className="ui-th w-[190px]">Updated</th>
                  <th className="ui-th w-[200px] text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                {items.map((it) => (
                  <tr key={it.id} className="ui-tr">
                    <td className="ui-td font-semibold text-slate-900">#{it.id}</td>
                    <td className="ui-td">
                      <div className="font-semibold text-slate-900">{it.title}</div>
                      <div className="text-xs text-slate-600 mt-1">{shortText(it.body, 120)}</div>
                    </td>
                    <td className="ui-td">
                      <Badge tone={it.visibility === "public" ? "success" : it.visibility === "unlisted" ? "warning" : "info"}>
                        {it.visibility}
                      </Badge>
                    </td>
                    <td className="ui-td">
                      <div className="flex flex-wrap gap-1.5">
                        {(it.tags ?? []).length ? (
                          it.tags.map((t) => (
                            <Badge key={t.id} tone="info">
                              {t.name}
                            </Badge>
                          ))
                        ) : (
                          <span className="text-xs text-slate-500">—</span>
                        )}
                      </div>
                    </td>
                    <td className="ui-td text-slate-700">{formatDateTime(it.updated_at)}</td>
                    <td className="ui-td">
                      <div className="flex items-center justify-end gap-2">
                        <Button variant="ghost" className="h-9 px-3 text-xs" onClick={() => openView(it)} disabled={loading}>
                          Xem
                        </Button>
                        <Button variant="ghost" className="h-9 px-3 text-xs" onClick={() => openEdit(it)} disabled={loading}>
                          Sửa
                        </Button>
                        <Button variant="danger" className="h-9 px-3 text-xs" onClick={() => remove(it)} disabled={loading}>
                          Xoá
                        </Button>
                      </div>
                    </td>
                  </tr>
                ))}

                {!loading && items.length === 0 ? (
                  <tr className="ui-tr">
                    <td className="ui-td text-slate-600" colSpan={6}>
                      Chưa có cheat sheet nào.
                    </td>
                  </tr>
                ) : null}
              </tbody>
            </table>
          </div>
        )}

        <Pagination
          meta={meta}
          onChange={(next) => {
            reload(next);
          }}
        />
      </Card>

      <Modal open={viewOpen} title={viewItem ? `#${viewItem.id} - ${viewItem.title}` : "Cheat sheet"} onClose={() => setViewOpen(false)}>
        {viewItem ? (
          <div className="space-y-3">
            <div className="flex flex-wrap items-center gap-2">
              <Badge tone={viewItem.visibility === "public" ? "success" : viewItem.visibility === "unlisted" ? "warning" : "info"}>
                {viewItem.visibility}
              </Badge>
              <div className="text-xs text-slate-500">updated: {formatDateTime(viewItem.updated_at)}</div>
            </div>

            <div className="flex flex-wrap gap-1.5">
              {(viewItem.tags ?? []).map((t) => (
                <Badge key={t.id} tone="info">
                  {t.name}
                </Badge>
              ))}
            </div>

            <div className="rounded-xl border border-slate-200 bg-white p-4">
              <MarkdownView markdown={viewItem.body ?? ""} />
            </div>
          </div>
        ) : null}
      </Modal>

      <Modal
        open={editOpen}
        title={editMode === "create" ? "Tạo cheat sheet" : editing ? `Sửa cheat sheet #${editing.id}` : "Sửa cheat sheet"}
        onClose={() => setEditOpen(false)}
      >
        <div className="space-y-4">
          <div className="space-y-1.5">
            <label className="ui-label">Title</label>
            <Input value={titleDraft} onChange={(e) => setTitleDraft(e.target.value)} placeholder="Tiêu đề..." />
          </div>

          <div className="space-y-1.5">
            <label className="ui-label">Visibility</label>
            <Select value={visibilityDraft} onChange={(e) => setVisibilityDraft(e.target.value as any)} aria-label="visibility">
              <option value="private">private</option>
              <option value="unlisted">unlisted</option>
              <option value="public">public</option>
            </Select>
          </div>

          <div className="space-y-1.5">
            <label className="ui-label">Tags (csv)</label>
            <Input
              value={tagsDraft}
              onChange={(e) => setTagsDraft(e.target.value)}
              placeholder="php, laravel, sanctum"
            />
            {tagSuggestLoading ? (
              <div className="text-xs text-slate-500">Đang gợi ý tags...</div>
            ) : tagSuggestItems.length ? (
              <div className="flex flex-wrap gap-1.5">
                {tagSuggestItems.map((t) => (
                  <button
                    key={t.id}
                    type="button"
                    className="text-xs px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50"
                    onClick={() => pickSuggest(t)}
                    title={t.slug}
                  >
                    + {t.name}
                  </button>
                ))}
              </div>
            ) : null}
          </div>

          <div className="space-y-1.5">
            <label className="ui-label">Body</label>
            <div className="flex items-center gap-2">
              <Button
                variant={bodyTab === "write" ? "primary" : "ghost"}
                className="h-9 px-3 text-xs"
                onClick={() => setBodyTab("write")}
                disabled={loading}
              >
                Write
              </Button>
              <Button
                variant={bodyTab === "preview" ? "primary" : "ghost"}
                className="h-9 px-3 text-xs"
                onClick={() => setBodyTab("preview")}
                disabled={loading}
              >
                Preview
              </Button>
              <Button
                variant={bodyTab === "help" ? "primary" : "ghost"}
                className="h-9 px-3 text-xs"
                onClick={() => setBodyTab("help")}
                disabled={loading}
              >
                Help
              </Button>
              <div className="text-xs text-slate-500">Markdown</div>
            </div>

            {bodyTab === "write" ? (
              <textarea
                className="ui-input h-auto min-h-[200px] py-3 font-mono text-[12px]"
                value={bodyDraft}
                onChange={(e) => setBodyDraft(e.target.value)}
                placeholder={"Ví dụ:\n# Title\n- Item\n```php\nCache::remember(...)\n```"}
              />
            ) : (
              <div className="rounded-xl border border-slate-200 bg-white p-4 min-h-[200px]">
                {bodyTab === "preview" ? (
                  <MarkdownView markdown={bodyDraft || "*(empty)*"} />
                ) : (
                  <MarkdownView
                    markdown={[
                      "# Markdown quick guide",
                      "",
                      "## Headings",
                      "- `# H1`",
                      "- `## H2`",
                      "- `### H3`",
                      "",
                      "## Emphasis",
                      "- `**bold**`, `*italic*`, `` `inline code` ``",
                      "",
                      "## Lists",
                      "- `- item`",
                      "- `1. item`",
                      "",
                      "## Links",
                      "- `[Laravel](https://laravel.com)`",
                      "",
                      "## Code block",
                      "```php",
                      "Cache::remember('key', 60, fn () => 'value');",
                      "```",
                      "",
                      "## Quote",
                      "> Note / tip",
                    ].join("\n")}
                  />
                )}
              </div>
            )}
          </div>

          <div className="flex items-center justify-end gap-2">
            <Button variant="ghost" onClick={() => setEditOpen(false)} disabled={loading}>
              Huỷ
            </Button>
            <Button variant="primary" onClick={save} disabled={loading || !canSave}>
              Lưu
            </Button>
          </div>
        </div>
      </Modal>
    </div>
  );
}
