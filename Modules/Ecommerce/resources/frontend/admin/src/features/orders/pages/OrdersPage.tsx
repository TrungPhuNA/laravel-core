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
import type { Order, OrderItemInput } from "../types";
import { createOrder, deleteOrder, getOrder, listOrders, updateOrder } from "../services/ordersApi";

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

function toneForStatus(s: string) {
  const v = (s || "").toUpperCase();
  if (v.includes("CANCEL")) return "danger";
  if (v.includes("PAID") || v.includes("FULFILL") || v.includes("DONE")) return "success";
  return "neutral";
}

type Editor = {
  id?: number;
  code?: string;
  status: string;
  payment_status: string;
  fulfillment_status: string;
  currency: string;
  customer_email: string;
  customer_phone: string;
  note: string;
  itemsJson: string;
};

function defaultItemsJson() {
  const sample: OrderItemInput[] = [{ product_id: null, sku: null, name: "Sample item", quantity: 1, unit_price: 10000 }];
  return prettyJson(sample);
}

export default function OrdersPage() {
  const [loading, setLoading] = React.useState(false);
  const [items, setItems] = React.useState<Order[]>([]);
  const [error, setError] = React.useState<Err>(null);

  const [q, setQ] = React.useState("");

  const [viewerOpen, setViewerOpen] = React.useState(false);
  const [viewerOrder, setViewerOrder] = React.useState<any>(null);

  const [editorOpen, setEditorOpen] = React.useState(false);
  const [editor, setEditor] = React.useState<Editor | null>(null);

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const list = await listOrders();
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
    return items.filter((o) => {
      return (
        String(o.id).includes(term) ||
        o.code.toLowerCase().includes(term) ||
        (o.customer_email ?? "").toLowerCase().includes(term) ||
        (o.customer_phone ?? "").toLowerCase().includes(term)
      );
    });
  }, [items, q]);

  async function openView(id: number) {
    setLoading(true);
    setError(null);
    try {
      const o = await getOrder(id);
      setViewerOrder(o);
      setViewerOpen(true);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function openEdit(id: number) {
    setLoading(true);
    setError(null);
    try {
      const o = await getOrder(id);
      setEditor({
        id: o.id,
        code: o.code,
        status: o.status ?? "NEW",
        payment_status: o.payment_status ?? "UNPAID",
        fulfillment_status: o.fulfillment_status ?? "UNFULFILLED",
        currency: o.currency ?? "VND",
        customer_email: o.customer_email ?? "",
        customer_phone: o.customer_phone ?? "",
        note: o.note ?? "",
        itemsJson: prettyJson(o.items ?? []),
      });
      setEditorOpen(true);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  function openCreate() {
    setEditor({
      status: "NEW",
      payment_status: "UNPAID",
      fulfillment_status: "UNFULFILLED",
      currency: "VND",
      customer_email: "",
      customer_phone: "",
      note: "",
      itemsJson: defaultItemsJson(),
    });
    setEditorOpen(true);
  }

  async function save() {
    if (!editor) return;
    setLoading(true);
    setError(null);
    try {
      let items: any[] = [];
      try {
        items = JSON.parse(editor.itemsJson);
        if (!Array.isArray(items)) items = [];
      } catch {
        items = [];
      }

      const payload: any = {
        status: editor.status,
        payment_status: editor.payment_status,
        fulfillment_status: editor.fulfillment_status,
        currency: editor.currency,
        customer_email: editor.customer_email.trim() === "" ? null : editor.customer_email,
        customer_phone: editor.customer_phone.trim() === "" ? null : editor.customer_phone,
        note: editor.note.trim() === "" ? null : editor.note,
        items,
      };

      if (editor.id) await updateOrder(editor.id, payload);
      else await createOrder(payload);

      setEditorOpen(false);
      await reload();
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function onDelete(id: number) {
    if (!confirm("Xoá đơn hàng (soft delete)?")) return;
    setLoading(true);
    setError(null);
    try {
      await deleteOrder(id);
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
          <div className="text-lg font-semibold">Đơn hàng</div>
          <div className="text-sm text-slate-600">Xem / cập nhật trạng thái, tạo đơn (MVP).</div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={reload} disabled={loading}>
            Tải lại
          </Button>
          <Button variant="primary" onClick={openCreate} disabled={loading}>
            Tạo đơn
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <Card title="Danh sách" actions={<Input placeholder="Tìm theo code/email/phone" value={q} onChange={(e) => setQ(e.target.value)} />}>
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">ID</th>
                <th className="py-2 pr-4">Code</th>
                <th className="py-2 pr-4">Status</th>
                <th className="py-2 pr-4">Payment</th>
                <th className="py-2 pr-4">Fulfillment</th>
                <th className="py-2 pr-4">Total</th>
                <th className="py-2 pr-4">Customer</th>
                <th className="py-2 pr-4">Hành động</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((o) => (
                <tr key={o.id} className="border-b last:border-0">
                  <td className="py-2 pr-4 text-slate-500">{o.id}</td>
                  <td className="py-2 pr-4 font-mono text-xs">{o.code}</td>
                  <td className="py-2 pr-4">
                    <Badge tone={toneForStatus(o.status) as any}>{o.status}</Badge>
                  </td>
                  <td className="py-2 pr-4">
                    <Badge tone={toneForStatus(o.payment_status) as any}>{o.payment_status}</Badge>
                  </td>
                  <td className="py-2 pr-4">
                    <Badge tone={toneForStatus(o.fulfillment_status) as any}>{o.fulfillment_status}</Badge>
                  </td>
                  <td className="py-2 pr-4 text-slate-600">
                    {o.total} {o.currency}
                  </td>
                  <td className="py-2 pr-4 text-slate-600">
                    <div className="truncate max-w-[240px]">{o.customer_email ?? o.customer_phone ?? "-"}</div>
                  </td>
                  <td className="py-2 pr-4">
                    <div className="flex items-center gap-2">
                      <Button size="sm" variant="ghost" onClick={() => openView(o.id)}>
                        Xem
                      </Button>
                      <Button size="sm" variant="ghost" onClick={() => openEdit(o.id)}>
                        Sửa
                      </Button>
                      <Button size="sm" variant="danger" onClick={() => onDelete(o.id)}>
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

      <Modal open={viewerOpen} onClose={() => setViewerOpen(false)} title="Chi tiết đơn hàng">
        <div className="space-y-3">
          <pre className="max-h-[420px] overflow-auto rounded-lg border border-slate-200 bg-white p-3 text-xs">
            {prettyJson(viewerOrder ?? {})}
          </pre>
          <div className="flex justify-end">
            <Button variant="ghost" onClick={() => setViewerOpen(false)}>
              Đóng
            </Button>
          </div>
        </div>
      </Modal>

      <Modal open={editorOpen} onClose={() => setEditorOpen(false)} title={editor?.id ? "Cập nhật đơn hàng" : "Tạo đơn hàng"}>
        {editor ? (
          <div className="space-y-3">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Status</div>
                <Input value={editor.status} onChange={(e) => setEditor({ ...editor, status: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Payment status</div>
                <Input value={editor.payment_status} onChange={(e) => setEditor({ ...editor, payment_status: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Fulfillment status</div>
                <Input value={editor.fulfillment_status} onChange={(e) => setEditor({ ...editor, fulfillment_status: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Currency</div>
                <Select value={editor.currency} onChange={(e) => setEditor({ ...editor, currency: e.target.value })}>
                  <option value="VND">VND</option>
                  <option value="USD">USD</option>
                </Select>
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Customer email</div>
                <Input value={editor.customer_email} onChange={(e) => setEditor({ ...editor, customer_email: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Customer phone</div>
                <Input value={editor.customer_phone} onChange={(e) => setEditor({ ...editor, customer_phone: e.target.value })} />
              </div>
              <div className="space-y-1 md:col-span-2">
                <div className="text-xs font-semibold text-slate-600">Note</div>
                <Input value={editor.note} onChange={(e) => setEditor({ ...editor, note: e.target.value })} placeholder="(optional)" />
              </div>
              <div className="space-y-1 md:col-span-2">
                <div className="text-xs font-semibold text-slate-600">Items (JSON array)</div>
                <textarea
                  className="w-full min-h-[140px] rounded-md border border-slate-200 bg-white p-3 text-xs font-mono outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-200"
                  value={editor.itemsJson}
                  onChange={(e) => setEditor({ ...editor, itemsJson: e.target.value })}
                />
                <div className="text-xs text-slate-500">
                  Dạng: <code className="font-mono">[&#123;quantity, unit_price, product_id?, sku?, name?&#125;]</code>
                </div>
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

