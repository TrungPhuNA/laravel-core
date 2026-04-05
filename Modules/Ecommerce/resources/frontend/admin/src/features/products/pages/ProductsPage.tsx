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
import type { Category } from "../../categories/types";
import { listCategories } from "../../categories/services/categoriesApi";
import type { Product } from "../types";
import { createProduct, deleteProduct, listProducts, updateProduct } from "../services/productsApi";

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

type Editor = {
  id?: number;
  sku: string;
  name: string;
  slug?: string;
  description?: string | null;
  price: string;
  compare_at_price?: string | null;
  currency: string;
  stock_qty: number;
  is_active: boolean;
  category_ids: number[];
};

function pickIds(selected: HTMLSelectElement) {
  return Array.from(selected.selectedOptions)
    .map((o) => Number(o.value))
    .filter((v) => Number.isFinite(v) && v > 0);
}

export default function ProductsPage() {
  const [loading, setLoading] = React.useState(false);
  const [items, setItems] = React.useState<Product[]>([]);
  const [categories, setCategories] = React.useState<Category[]>([]);
  const [error, setError] = React.useState<Err>(null);

  const [q, setQ] = React.useState("");

  const [editorOpen, setEditorOpen] = React.useState(false);
  const [editor, setEditor] = React.useState<Editor | null>(null);

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const [p, cats] = await Promise.all([listProducts(), listCategories()]);
      setItems(p);
      setCategories(cats);
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
    return items.filter((p) => p.name.toLowerCase().includes(term) || p.sku.toLowerCase().includes(term) || p.slug.toLowerCase().includes(term));
  }, [items, q]);

  function openCreate() {
    setEditor({
      sku: "",
      name: "",
      slug: "",
      description: null,
      price: "0",
      compare_at_price: null,
      currency: "VND",
      stock_qty: 0,
      is_active: true,
      category_ids: [],
    });
    setEditorOpen(true);
  }

  function openEdit(p: Product) {
    setEditor({
      id: p.id,
      sku: p.sku,
      name: p.name,
      slug: p.slug,
      description: p.description ?? null,
      price: String(p.price ?? "0"),
      compare_at_price: p.compare_at_price ?? null,
      currency: p.currency ?? "VND",
      stock_qty: p.stock_qty ?? 0,
      is_active: p.is_active,
      category_ids: (p.categories ?? []).map((c) => c.id),
    });
    setEditorOpen(true);
  }

  async function save() {
    if (!editor) return;
    setLoading(true);
    setError(null);
    try {
      const payload = {
        sku: editor.sku,
        name: editor.name,
        slug: editor.slug?.trim() ? editor.slug : null,
        description: editor.description ?? null,
        price: Number(editor.price || 0),
        compare_at_price: editor.compare_at_price ? Number(editor.compare_at_price) : null,
        currency: editor.currency,
        stock_qty: Number(editor.stock_qty || 0),
        is_active: Boolean(editor.is_active),
        category_ids: editor.category_ids,
      };
      if (editor.id) await updateProduct(editor.id, payload);
      else await createProduct(payload);
      setEditorOpen(false);
      await reload();
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function onDelete(id: number) {
    if (!confirm("Xoá sản phẩm?")) return;
    setLoading(true);
    setError(null);
    try {
      await deleteProduct(id);
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
          <div className="text-lg font-semibold">Sản phẩm</div>
          <div className="text-sm text-slate-600">CRUD sản phẩm + gán danh mục.</div>
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
            <Input placeholder="Tìm theo SKU / tên / slug" value={q} onChange={(e) => setQ(e.target.value)} />
          </div>
        }
      >
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">ID</th>
                <th className="py-2 pr-4">SKU</th>
                <th className="py-2 pr-4">Tên</th>
                <th className="py-2 pr-4">Giá</th>
                <th className="py-2 pr-4">Kho</th>
                <th className="py-2 pr-4">Trạng thái</th>
                <th className="py-2 pr-4">Danh mục</th>
                <th className="py-2 pr-4">Hành động</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((p) => (
                <tr key={p.id} className="border-b last:border-0">
                  <td className="py-2 pr-4 text-slate-500">{p.id}</td>
                  <td className="py-2 pr-4 font-mono text-xs">{p.sku}</td>
                  <td className="py-2 pr-4 font-medium">{p.name}</td>
                  <td className="py-2 pr-4 text-slate-600">
                    {p.price} {p.currency}
                  </td>
                  <td className="py-2 pr-4 text-slate-600">{p.stock_qty}</td>
                  <td className="py-2 pr-4">
                    {p.is_active ? <Badge tone="success">ACTIVE</Badge> : <Badge tone="danger">OFF</Badge>}
                  </td>
                  <td className="py-2 pr-4">
                    <div className="flex flex-wrap gap-1">
                      {(p.categories ?? []).slice(0, 3).map((c) => (
                        <Badge key={c.id} tone="neutral">
                          {c.name}
                        </Badge>
                      ))}
                      {(p.categories ?? []).length > 3 ? <span className="text-xs text-slate-500">…</span> : null}
                    </div>
                  </td>
                  <td className="py-2 pr-4">
                    <div className="flex items-center gap-2">
                      <Button size="sm" variant="ghost" onClick={() => openEdit(p)}>
                        Sửa
                      </Button>
                      <Button size="sm" variant="danger" onClick={() => onDelete(p.id)}>
                        Xoá
                      </Button>
                    </div>
                  </td>
                </tr>
              ))}
              {filtered.length === 0 ? (
                <tr>
                  <td className="py-6 text-center text-slate-500" colSpan={8}>
                    Không có dữ liệu.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </Card>

      <Modal open={editorOpen} onClose={() => setEditorOpen(false)} title={editor?.id ? "Cập nhật sản phẩm" : "Tạo sản phẩm"}>
        {editor ? (
          <div className="space-y-3">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">SKU</div>
                <Input value={editor.sku} onChange={(e) => setEditor({ ...editor, sku: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Tên</div>
                <Input value={editor.name} onChange={(e) => setEditor({ ...editor, name: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Slug (optional)</div>
                <Input value={editor.slug ?? ""} onChange={(e) => setEditor({ ...editor, slug: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Currency</div>
                <Select value={editor.currency} onChange={(e) => setEditor({ ...editor, currency: e.target.value })}>
                  <option value="VND">VND</option>
                  <option value="USD">USD</option>
                </Select>
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Price</div>
                <Input value={editor.price} onChange={(e) => setEditor({ ...editor, price: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Compare at price</div>
                <Input
                  value={editor.compare_at_price ?? ""}
                  onChange={(e) => setEditor({ ...editor, compare_at_price: e.target.value })}
                  placeholder="(optional)"
                />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Stock</div>
                <Input
                  type="number"
                  value={String(editor.stock_qty)}
                  onChange={(e) => setEditor({ ...editor, stock_qty: Number(e.target.value) })}
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
                <div className="text-xs font-semibold text-slate-600">Danh mục</div>
                <Select
                  multiple
                  value={editor.category_ids.map(String)}
                  onChange={(e) => setEditor({ ...editor, category_ids: pickIds(e.target as HTMLSelectElement) })}
                  className="h-[140px]"
                >
                  {categories.map((c) => (
                    <option key={c.id} value={String(c.id)}>
                      {c.name}
                    </option>
                  ))}
                </Select>
                <div className="text-xs text-slate-500">Giữ Ctrl/Cmd để chọn nhiều.</div>
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

