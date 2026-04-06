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
import {
  createShop,
  deleteShop,
  detachShopUser,
  listShopUsers,
  listShops,
  lookupUsers,
  syncShopUsers,
  updateShop,
  type Shop,
  type ShopUser,
} from "../services/shopsApi";

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

type ShopEditor = {
  id?: number;
  code: string;
  name: string;
  domain: string;
  timezone: string;
  currency: string;
  is_active: boolean;
};

export default function ShopsPage() {
  const [loading, setLoading] = React.useState(false);
  const [items, setItems] = React.useState<Shop[]>([]);
  const [error, setError] = React.useState<Err>(null);

  const [q, setQ] = React.useState("");

  const [editorOpen, setEditorOpen] = React.useState(false);
  const [editor, setEditor] = React.useState<ShopEditor | null>(null);

  const [usersOpen, setUsersOpen] = React.useState(false);
  const [selectedShop, setSelectedShop] = React.useState<Shop | null>(null);
  const [shopUsers, setShopUsers] = React.useState<ShopUser[]>([]);

  const [userLookupQ, setUserLookupQ] = React.useState("");
  const [userLookupItems, setUserLookupItems] = React.useState<Array<{ id: number; name: string | null; email: string | null; user_type: string }>>([]);
  const [addRole, setAddRole] = React.useState("STAFF");

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const list = await listShops();
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
    return items.filter((s) => s.name.toLowerCase().includes(term) || s.code.toLowerCase().includes(term) || (s.domain ?? "").toLowerCase().includes(term));
  }, [items, q]);

  function openCreate() {
    setEditor({
      code: "",
      name: "",
      domain: "",
      timezone: "Asia/Ho_Chi_Minh",
      currency: "VND",
      is_active: true,
    });
    setEditorOpen(true);
  }

  function openEdit(s: Shop) {
    setEditor({
      id: s.id,
      code: s.code,
      name: s.name,
      domain: s.domain ?? "",
      timezone: s.timezone,
      currency: s.currency,
      is_active: s.is_active,
    });
    setEditorOpen(true);
  }

  async function saveShop() {
    if (!editor) return;
    setLoading(true);
    setError(null);
    try {
      const payload = {
        code: editor.code,
        name: editor.name,
        domain: editor.domain.trim() === "" ? null : editor.domain.trim(),
        timezone: editor.timezone,
        currency: editor.currency,
        is_active: editor.is_active,
      };
      if (editor.id) await updateShop(editor.id, payload);
      else await createShop(payload);
      setEditorOpen(false);
      await reload();
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function onDelete(id: number) {
    if (!confirm("Xoá shop?")) return;
    setLoading(true);
    setError(null);
    try {
      await deleteShop(id);
      await reload();
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function openUsers(s: Shop) {
    setSelectedShop(s);
    setUsersOpen(true);
    setUserLookupQ("");
    setUserLookupItems([]);
    setAddRole("STAFF");

    setLoading(true);
    setError(null);
    try {
      const list = await listShopUsers(s.id);
      setShopUsers(list);
    } catch (e) {
      setError(e);
      setShopUsers([]);
    } finally {
      setLoading(false);
    }
  }

  async function reloadUsers() {
    if (!selectedShop) return;
    const list = await listShopUsers(selectedShop.id);
    setShopUsers(list);
  }

  async function doLookup() {
    setLoading(true);
    setError(null);
    try {
      const list = await lookupUsers(userLookupQ);
      setUserLookupItems(list);
    } catch (e) {
      setError(e);
      setUserLookupItems([]);
    } finally {
      setLoading(false);
    }
  }

  async function addUser(userId: number) {
    if (!selectedShop) return;
    setLoading(true);
    setError(null);
    try {
      const next = [...shopUsers];
      const existing = next.find((u) => u.id === userId);
      if (!existing) {
        next.push({ id: userId, name: null, email: null, user_type: "-", shop_role: addRole });
      } else {
        existing.shop_role = addRole;
      }
      await syncShopUsers(
        selectedShop.id,
        next.map((u) => ({ user_id: u.id, role: u.shop_role || "STAFF" }))
      );
      await reloadUsers();
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function removeUser(userId: number) {
    if (!selectedShop) return;
    if (!confirm("Gỡ user khỏi shop?")) return;
    setLoading(true);
    setError(null);
    try {
      await detachShopUser(selectedShop.id, userId);
      await reloadUsers();
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
          <div className="text-lg font-semibold">Shops</div>
          <div className="text-sm text-slate-600">Quản lý shop + gán tài khoản theo shop.</div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={reload} disabled={loading}>
            Tải lại
          </Button>
          <Button variant="primary" onClick={openCreate} disabled={loading}>
            Thêm shop
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <Card title="Danh sách" actions={<Input placeholder="Tìm theo code/name/domain" value={q} onChange={(e) => setQ(e.target.value)} />}>
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">ID</th>
                <th className="py-2 pr-4">Code</th>
                <th className="py-2 pr-4">Name</th>
                <th className="py-2 pr-4">Domain</th>
                <th className="py-2 pr-4">Currency</th>
                <th className="py-2 pr-4">Status</th>
                <th className="py-2 pr-4">Hành động</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((s) => (
                <tr key={s.id} className="border-b last:border-0">
                  <td className="py-2 pr-4 text-slate-500">{s.id}</td>
                  <td className="py-2 pr-4 font-mono text-xs">{s.code}</td>
                  <td className="py-2 pr-4 font-medium">{s.name}</td>
                  <td className="py-2 pr-4 text-slate-600">{s.domain ?? "-"}</td>
                  <td className="py-2 pr-4 text-slate-600">{s.currency}</td>
                  <td className="py-2 pr-4">{s.is_active ? <Badge tone="success">ACTIVE</Badge> : <Badge tone="danger">OFF</Badge>}</td>
                  <td className="py-2 pr-4">
                    <div className="flex items-center gap-2">
                      <Button size="sm" variant="ghost" onClick={() => openUsers(s)}>
                        Users
                      </Button>
                      <Button size="sm" variant="ghost" onClick={() => openEdit(s)}>
                        Sửa
                      </Button>
                      <Button size="sm" variant="danger" onClick={() => onDelete(s.id)}>
                        Xoá
                      </Button>
                    </div>
                  </td>
                </tr>
              ))}
              {filtered.length === 0 ? (
                <tr>
                  <td className="py-6 text-center text-slate-500" colSpan={7}>
                    Không có dữ liệu.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </Card>

      <Modal open={editorOpen} onClose={() => setEditorOpen(false)} title={editor?.id ? "Cập nhật shop" : "Tạo shop"}>
        {editor ? (
          <div className="space-y-3">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Code</div>
                <Input value={editor.code} onChange={(e) => setEditor({ ...editor, code: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Name</div>
                <Input value={editor.name} onChange={(e) => setEditor({ ...editor, name: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Domain</div>
                <Input value={editor.domain} onChange={(e) => setEditor({ ...editor, domain: e.target.value })} placeholder="(optional)" />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Timezone</div>
                <Input value={editor.timezone} onChange={(e) => setEditor({ ...editor, timezone: e.target.value })} />
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Currency</div>
                <Select value={editor.currency} onChange={(e) => setEditor({ ...editor, currency: e.target.value })}>
                  <option value="VND">VND</option>
                  <option value="USD">USD</option>
                </Select>
              </div>
              <div className="space-y-1">
                <div className="text-xs font-semibold text-slate-600">Status</div>
                <Select value={editor.is_active ? "1" : "0"} onChange={(e) => setEditor({ ...editor, is_active: e.target.value === "1" })}>
                  <option value="1">Active</option>
                  <option value="0">Off</option>
                </Select>
              </div>
            </div>

            <div className="flex items-center justify-end gap-2">
              <Button variant="ghost" onClick={() => setEditorOpen(false)} disabled={loading}>
                Huỷ
              </Button>
              <Button variant="primary" onClick={saveShop} disabled={loading}>
                Lưu
              </Button>
            </div>
          </div>
        ) : null}
      </Modal>

      <Modal open={usersOpen} onClose={() => setUsersOpen(false)} title={`Shop users${selectedShop ? `: ${selectedShop.name}` : ""}`}>
        <div className="space-y-3">
          <div className="rounded-xl border border-slate-200 bg-white p-3">
            <div className="text-sm font-semibold">Thêm user vào shop</div>
            <div className="mt-2 flex flex-col gap-2 md:flex-row md:items-center">
              <Input className="w-full" placeholder="Tìm user theo email/name/phone" value={userLookupQ} onChange={(e) => setUserLookupQ(e.target.value)} />
              <Select value={addRole} onChange={(e) => setAddRole(e.target.value)} className="w-[140px]">
                <option value="STAFF">STAFF</option>
                <option value="ADMIN">ADMIN</option>
                <option value="MANAGER">MANAGER</option>
              </Select>
              <Button variant="primary" onClick={doLookup} disabled={loading}>
                Tìm
              </Button>
            </div>
            {userLookupItems.length > 0 ? (
              <div className="mt-3 overflow-auto">
                <table className="min-w-full text-sm">
                  <thead className="text-left text-slate-600">
                    <tr className="border-b">
                      <th className="py-2 pr-4">ID</th>
                      <th className="py-2 pr-4">Email</th>
                      <th className="py-2 pr-4">Name</th>
                      <th className="py-2 pr-4">Type</th>
                      <th className="py-2 pr-4"></th>
                    </tr>
                  </thead>
                  <tbody>
                    {userLookupItems.map((u) => (
                      <tr key={u.id} className="border-b last:border-0">
                        <td className="py-2 pr-4 text-slate-500">{u.id}</td>
                        <td className="py-2 pr-4">{u.email ?? "-"}</td>
                        <td className="py-2 pr-4">{u.name ?? "-"}</td>
                        <td className="py-2 pr-4">
                          <Badge tone="neutral">{u.user_type}</Badge>
                        </td>
                        <td className="py-2 pr-4">
                          <Button size="sm" variant="primary" onClick={() => addUser(u.id)} disabled={loading}>
                            Add
                          </Button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : null}
          </div>

          <Card title="Danh sách user theo shop">
            <div className="overflow-auto">
              <table className="min-w-full text-sm">
                <thead className="text-left text-slate-600">
                  <tr className="border-b">
                    <th className="py-2 pr-4">ID</th>
                    <th className="py-2 pr-4">Email</th>
                    <th className="py-2 pr-4">Name</th>
                    <th className="py-2 pr-4">Type</th>
                    <th className="py-2 pr-4">Shop role</th>
                    <th className="py-2 pr-4"></th>
                  </tr>
                </thead>
                <tbody>
                  {shopUsers.map((u) => (
                    <tr key={u.id} className="border-b last:border-0">
                      <td className="py-2 pr-4 text-slate-500">{u.id}</td>
                      <td className="py-2 pr-4">{u.email ?? "-"}</td>
                      <td className="py-2 pr-4">{u.name ?? "-"}</td>
                      <td className="py-2 pr-4">
                        <Badge tone="neutral">{u.user_type}</Badge>
                      </td>
                      <td className="py-2 pr-4">
                        <Badge tone="success">{u.shop_role}</Badge>
                      </td>
                      <td className="py-2 pr-4">
                        <Button size="sm" variant="danger" onClick={() => removeUser(u.id)} disabled={loading}>
                          Remove
                        </Button>
                      </td>
                    </tr>
                  ))}
                  {shopUsers.length === 0 ? (
                    <tr>
                      <td className="py-6 text-center text-slate-500" colSpan={6}>
                        Chưa có user.
                      </td>
                    </tr>
                  ) : null}
                </tbody>
              </table>
            </div>
          </Card>

          <div className="flex items-center justify-end">
            <Button variant="ghost" onClick={() => setUsersOpen(false)}>
              Đóng
            </Button>
          </div>
        </div>
      </Modal>
    </div>
  );
}

