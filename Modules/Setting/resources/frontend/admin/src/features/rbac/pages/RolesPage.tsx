import React, { useMemo, useState } from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import Modal from "@shared/ui/Modal";
import Badge from "@shared/ui/Badge";
import { prettyJson } from "@shared/lib/format";
import type { ApiResponseFail, ApiResponseError } from "@shared/http/types";
import type { PermissionItem, RoleItem } from "../types";
import { createRole, deleteRole, fetchPermissions, fetchRoles, updateRole } from "../services/rbacApi";
import RbacTabs from "../components/RbacTabs";

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

function toggle(list: string[], name: string) {
  return list.includes(name) ? list.filter((x) => x !== name) : [...list, name];
}

export default function RolesPage() {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Err>(null);
  const [items, setItems] = useState<RoleItem[]>([]);
  const [permissions, setPermissions] = useState<PermissionItem[]>([]);
  const [q, setQ] = useState("");

  const [editorOpen, setEditorOpen] = useState(false);
  const [editor, setEditor] = useState<RoleItem | null>(null);
  const [name, setName] = useState("");
  const [permFilter, setPermFilter] = useState("");
  const [selectedPerms, setSelectedPerms] = useState<string[]>([]);

  const filtered = useMemo(() => {
    const term = q.trim().toLowerCase();
    if (term === "") return items;
    return items.filter((r) => r.name.toLowerCase().includes(term));
  }, [items, q]);

  const filteredPerms = useMemo(() => {
    const term = permFilter.trim().toLowerCase();
    if (term === "") return permissions;
    return permissions.filter((p) => p.name.toLowerCase().includes(term));
  }, [permissions, permFilter]);

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const [rs, ps] = await Promise.all([fetchRoles(), fetchPermissions()]);
      setItems(rs);
      setPermissions(ps);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  React.useEffect(() => {
    reload();
  }, []);

  function openCreate() {
    setEditor(null);
    setName("");
    setSelectedPerms([]);
    setEditorOpen(true);
  }

  function openEdit(role: RoleItem) {
    setEditor(role);
    setName(role.name);
    setSelectedPerms((role.permissions ?? []).map((p) => p.name));
    setEditorOpen(true);
  }

  async function save() {
    setLoading(true);
    setError(null);
    try {
      if (!editor) {
        await createRole({ name, permissions: selectedPerms });
      } else {
        await updateRole(editor.id, { name, permissions: selectedPerms });
      }
      setEditorOpen(false);
      await reload();
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function remove(role: RoleItem) {
    if (!confirm(`Xoá role "${role.name}"?`)) return;
    setLoading(true);
    setError(null);
    try {
      await deleteRole(role.id);
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
          <div className="text-lg font-semibold">Roles</div>
          <div className="text-sm text-slate-600">Tạo/sửa role và gán permissions.</div>
        </div>
        <div className="flex items-center gap-2">
          <RbacTabs />
          <Button variant="primary" onClick={openCreate} disabled={loading}>
            Tạo role
          </Button>
          <Button variant="ghost" onClick={reload} disabled={loading}>
            Tải lại
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <Card
        title="Danh sách"
        actions={<Input placeholder="Lọc theo tên" value={q} onChange={(e) => setQ(e.target.value)} />}
      >
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">Role</th>
                <th className="py-2 pr-4">Permissions</th>
                <th className="py-2 pr-2">Hành động</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((r) => (
                <tr key={r.id} className="border-b last:border-b-0">
                  <td className="py-2 pr-4 font-medium">{r.name}</td>
                  <td className="py-2 pr-4">
                    <div className="flex flex-wrap gap-1">
                      {(r.permissions ?? []).slice(0, 6).map((p) => (
                        <Badge key={p.id} tone="info">
                          {p.name}
                        </Badge>
                      ))}
                      {(r.permissions ?? []).length > 6 ? (
                        <Badge tone="warning">+{(r.permissions ?? []).length - 6}</Badge>
                      ) : null}
                    </div>
                  </td>
                  <td className="py-2 pr-2">
                    <Button variant="ghost" onClick={() => openEdit(r)}>
                      Sửa
                    </Button>
                    <Button variant="ghost" onClick={() => remove(r)}>
                      Xoá
                    </Button>
                  </td>
                </tr>
              ))}
              {filtered.length === 0 ? (
                <tr>
                  <td colSpan={3} className="py-6 text-center text-slate-500">
                    Không có dữ liệu.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </Card>

      <Modal
        open={editorOpen}
        title={editor ? `Sửa role: ${editor.name}` : "Tạo role"}
        onClose={() => setEditorOpen(false)}
      >
        <div className="space-y-3">
          <div className="space-y-1">
            <div className="text-xs text-slate-600">Tên role</div>
            <Input value={name} onChange={(e) => setName(e.target.value)} placeholder="Ví dụ: Admin" />
          </div>

          <div className="space-y-2">
            <div className="flex items-center justify-between gap-2">
              <div className="text-xs text-slate-600">Permissions</div>
              <Input placeholder="Lọc permission" value={permFilter} onChange={(e) => setPermFilter(e.target.value)} />
            </div>
            <div className="max-h-[320px] overflow-auto border rounded-lg bg-white p-3 space-y-1">
              {filteredPerms.map((p) => (
                <label key={p.id} className="flex items-center gap-2 text-sm">
                  <input
                    type="checkbox"
                    checked={selectedPerms.includes(p.name)}
                    onChange={() => setSelectedPerms((cur) => toggle(cur, p.name))}
                  />
                  <span className="font-mono text-xs">{p.name}</span>
                </label>
              ))}
              {filteredPerms.length === 0 ? <div className="text-sm text-slate-500">Không có permission.</div> : null}
            </div>
          </div>

          <div className="flex items-center justify-end gap-2">
            <Button variant="ghost" onClick={() => setEditorOpen(false)}>
              Huỷ
            </Button>
            <Button variant="primary" onClick={save} disabled={loading}>
              Lưu
            </Button>
          </div>
        </div>
      </Modal>
    </div>
  );
}
