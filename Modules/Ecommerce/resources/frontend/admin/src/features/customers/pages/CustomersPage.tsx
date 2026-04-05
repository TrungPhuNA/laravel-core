import React from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import Modal from "@shared/ui/Modal";
import { prettyJson } from "@shared/lib/format";
import type { ApiResponseError, ApiResponseFail } from "@shared/http/types";
import type { Customer } from "../types";
import { createCustomer, deleteCustomer, listCustomers, updateCustomer } from "../services/customersApi";

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
  name: string;
  email: string;
  phone: string;
  note: string;
};

export default function CustomersPage() {
  const [loading, setLoading] = React.useState(false);
  const [items, setItems] = React.useState<Customer[]>([]);
  const [error, setError] = React.useState<Err>(null);

  const [q, setQ] = React.useState("");

  const [editorOpen, setEditorOpen] = React.useState(false);
  const [editor, setEditor] = React.useState<Editor | null>(null);

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const list = await listCustomers();
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
    return items.filter((c) => {
      return (
        String(c.id).includes(term) ||
        (c.name ?? "").toLowerCase().includes(term) ||
        (c.email ?? "").toLowerCase().includes(term) ||
        (c.phone ?? "").toLowerCase().includes(term)
      );
    });
  }, [items, q]);

  function openCreate() {
    setEditor({ name: "", email: "", phone: "", note: "" });
    setEditorOpen(true);
  }

  function openEdit(c: Customer) {
    setEditor({
      id: c.id,
      name: c.name ?? "",
      email: c.email ?? "",
      phone: c.phone ?? "",
      note: c.note ?? "",
    });
    setEditorOpen(true);
  }

  async function save() {
    if (!editor) return;
    setLoading(true);
    setError(null);
    try {
      const payload = {
        name: editor.name.trim() === "" ? null : editor.name,
        email: editor.email.trim() === "" ? null : editor.email,
        phone: editor.phone.trim() === "" ? null : editor.phone,
        note: editor.note.trim() === "" ? null : editor.note,
      };
      if (editor.id) await updateCustomer(editor.id, payload);
      else await createCustomer(payload);
      setEditorOpen(false);
      await reload();
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function onDelete(id: number) {
    if (!confirm("Xoá khách hàng?")) return;
    setLoading(true);
    setError(null);
    try {
      await deleteCustomer(id);
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
          <div className="text-lg font-semibold">Khách hàng</div>
          <div className="text-sm text-slate-600">CRUD khách hàng (MVP).</div>
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
        actions={<Input placeholder="Tìm theo tên/email/phone" value={q} onChange={(e) => setQ(e.target.value)} />}
      >
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">ID</th>
                <th className="py-2 pr-4">Tên</th>
                <th className="py-2 pr-4">Email</th>
                <th className="py-2 pr-4">Phone</th>
                <th className="py-2 pr-4">Hành động</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((c) => (
                <tr key={c.id} className="border-b last:border-0">
                  <td className="py-2 pr-4 text-slate-500">{c.id}</td>
                  <td className="py-2 pr-4 font-medium">{c.name ?? "-"}</td>
                  <td className="py-2 pr-4 text-slate-600">{c.email ?? "-"}</td>
                  <td className="py-2 pr-4 text-slate-600">{c.phone ?? "-"}</td>
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
                  <td className="py-6 text-center text-slate-500" colSpan={5}>
                    Không có dữ liệu.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </Card>

      <Modal open={editorOpen} onClose={() => setEditorOpen(false)} title={editor?.id ? "Cập nhật khách hàng" : "Tạo khách hàng"}>
        {editor ? (
          <div className="space-y-3">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Tên</div>
                <Input value={editor.name} onChange={(e) => setEditor({ ...editor, name: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Email</div>
                <Input value={editor.email} onChange={(e) => setEditor({ ...editor, email: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Phone</div>
                <Input value={editor.phone} onChange={(e) => setEditor({ ...editor, phone: e.target.value })} />
              </div>
              <div className="space-y-1 md:col-span-2">
                <div className="text-xs font-semibold text-slate-600">Ghi chú</div>
                <Input value={editor.note} onChange={(e) => setEditor({ ...editor, note: e.target.value })} placeholder="(optional)" />
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

