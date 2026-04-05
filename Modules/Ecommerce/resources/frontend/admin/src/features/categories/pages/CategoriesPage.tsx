import React from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Select from "@shared/ui/Select";
import Badge from "@shared/ui/Badge";
import Alert from "@shared/ui/Alert";
import Modal from "@shared/ui/Modal";
import { prettyJson } from "@shared/lib/format";
import type { ApiResponseError, ApiResponseFail } from "@shared/http/types";
import type { Category } from "../types";
import { createCategory, deleteCategory, listCategories, updateCategory } from "../services/categoriesApi";

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

export default function CategoriesPage() {
  const [loading, setLoading] = React.useState(false);
  const [items, setItems] = React.useState<Category[]>([]);
  const [error, setError] = React.useState<Err>(null);

  const [q, setQ] = React.useState("");

  const [editorOpen, setEditorOpen] = React.useState(false);
  const [editor, setEditor] = React.useState<Partial<Category> | null>(null);

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const list = await listCategories();
      setItems(list);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  React.useEffect(() => {
    reload();
  }, []);

  const filtered = React.useMemo(() => {
    const term = q.trim().toLowerCase();
    if (!term) return items;
    return items.filter((c) => c.name.toLowerCase().includes(term) || c.slug.toLowerCase().includes(term));
  }, [items, q]);

  function openCreate() {
    setEditor({ name: "", slug: "", position: 0, is_active: true, parent_id: null, description: null });
    setEditorOpen(true);
  }

  function openEdit(c: Category) {
    setEditor({ ...c });
    setEditorOpen(true);
  }

  async function save() {
    if (!editor) return;
    setLoading(true);
    setError(null);
    try {
      const payload = {
        parent_id: editor.parent_id ?? null,
        name: editor.name ?? "",
        slug: editor.slug ?? null,
        description: editor.description ?? null,
        position: Number(editor.position ?? 0),
        is_active: Boolean(editor.is_active ?? true),
      };
      if (editor.id) await updateCategory(editor.id, payload);
      else await createCategory(payload);
      setEditorOpen(false);
      await reload();
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function onDelete(id: number) {
    if (!confirm("Xoá danh mục?")) return;
    setLoading(true);
    setError(null);
    try {
      await deleteCategory(id);
      await reload();
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
          <div className="text-lg font-semibold">Danh mục</div>
          <div className="text-sm text-slate-600">CRUD danh mục sản phẩm.</div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={reload} disabled={loading}>
            Tải lại
          </Button>
          <Button variant="primary" onClick={openCreate} disabled={loading}>
            Thêm
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <Card
        title="Danh sách"
        actions={
          <div className="flex items-center gap-2">
            <Input placeholder="Tìm theo tên/slug" value={q} onChange={(e) => setQ(e.target.value)} />
          </div>
        }
      >
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">ID</th>
                <th className="py-2 pr-4">Tên</th>
                <th className="py-2 pr-4">Slug</th>
                <th className="py-2 pr-4">Vị trí</th>
                <th className="py-2 pr-4">Trạng thái</th>
                <th className="py-2 pr-4">Hành động</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((c) => (
                <tr key={c.id} className="border-b last:border-0">
                  <td className="py-2 pr-4 text-slate-500">{c.id}</td>
                  <td className="py-2 pr-4 font-medium">{c.name}</td>
                  <td className="py-2 pr-4 text-slate-600">{c.slug}</td>
                  <td className="py-2 pr-4 text-slate-600">{c.position}</td>
                  <td className="py-2 pr-4">
                    {c.is_active ? <Badge tone="success">ACTIVE</Badge> : <Badge tone="danger">OFF</Badge>}
                  </td>
                  <td className="py-2 pr-4">
                    <div className="flex items-center gap-2">
                      <Button size="sm" variant="ghost" onClick={() => openEdit(c)}>
                        Sửa
                      </Button>
                      <Button size="sm" variant="danger" onClick={() => onDelete(c.id)}>
                        Xoá
                      </Button>
                    </div>
                  </td>
                </tr>
              ))}
              {filtered.length === 0 ? (
                <tr>
                  <td className="py-6 text-center text-slate-500" colSpan={6}>
                    Không có dữ liệu.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </Card>

      <Modal open={editorOpen} onClose={() => setEditorOpen(false)} title={editor?.id ? "Cập nhật danh mục" : "Tạo danh mục"}>
        {editor ? (
          <div className="space-y-3">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Tên</div>
                <Input value={editor.name ?? ""} onChange={(e) => setEditor({ ...editor, name: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Slug (optional)</div>
                <Input value={editor.slug ?? ""} onChange={(e) => setEditor({ ...editor, slug: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Vị trí</div>
                <Input
                  type="number"
                  value={String(editor.position ?? 0)}
                  onChange={(e) => setEditor({ ...editor, position: Number(e.target.value) })}
                />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Trạng thái</div>
                <Select
                  value={editor.is_active ? "1" : "0"}
                  onChange={(e) => setEditor({ ...editor, is_active: e.target.value === "1" })}
                >
                  <option value="1">Active</option>
                  <option value="0">Off</option>
                </Select>
              </div>
              <div className="space-y-1 md:col-span-2">
                <div className="text-xs font-semibold text-slate-600">Parent</div>
                <Select
                  value={editor.parent_id ? String(editor.parent_id) : ""}
                  onChange={(e) => setEditor({ ...editor, parent_id: e.target.value ? Number(e.target.value) : null })}
                >
                  <option value="">(Không)</option>
                  {items
                    .filter((c) => !editor.id || c.id !== editor.id)
                    .map((c) => (
                      <option key={c.id} value={String(c.id)}>
                        {c.name} (#{c.id})
                      </option>
                    ))}
                </Select>
              </div>
              <div className="space-y-1 md:col-span-2">
                <div className="text-xs font-semibold text-slate-600">Mô tả</div>
                <Input
                  value={editor.description ?? ""}
                  onChange={(e) => setEditor({ ...editor, description: e.target.value })}
                  placeholder="(optional)"
                />
              </div>
            </div>

            <div className="flex items-center justify-end gap-2">
              <Button variant="ghost" onClick={() => setEditorOpen(false)} disabled={loading}>
                Huỷ
              </Button>
              <Button variant="primary" onClick={save} disabled={loading}>
                Lưu
              </Button>
            </div>
          </div>
        ) : null}
      </Modal>
    </div>
  );
}

