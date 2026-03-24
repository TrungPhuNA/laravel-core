import React, { useMemo, useState } from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import Badge from "@shared/ui/Badge";
import Pagination from "@shared/ui/Pagination";
import Modal from "@shared/ui/Modal";
import Select from "@shared/ui/Select";
import { prettyJson } from "@shared/lib/format";
import type { ApiMetaPagination, ApiResponseFail, ApiResponseError } from "@shared/http/types";
import type { UserItem } from "../types";
import { fetchUsers } from "../services/usersApi";
import type { PermissionItem, RoleItem, UserRbac } from "../../rbac/types";
import {
  fetchPermissions,
  fetchRoles,
  fetchUserRbac,
  syncUserPermissions,
  syncUserRoles,
} from "../../rbac/services/rbacApi";

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

export default function UsersPage() {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Err>(null);
  const [items, setItems] = useState<UserItem[]>([]);
  const [meta, setMeta] = useState<ApiMetaPagination>(emptyMeta);

  const [q, setQ] = useState("");
  const [userType, setUserType] = useState<string>("all");

  const [rbacOpen, setRbacOpen] = useState(false);
  const [rbacLoading, setRbacLoading] = useState(false);
  const [rbacUser, setRbacUser] = useState<UserItem | null>(null);
  const [rbac, setRbac] = useState<UserRbac | null>(null);
  const [roles, setRoles] = useState<RoleItem[]>([]);
  const [permissions, setPermissions] = useState<PermissionItem[]>([]);
  const [selectedRoles, setSelectedRoles] = useState<string[]>([]);
  const [selectedDirectPermissions, setSelectedDirectPermissions] = useState<string[]>([]);
  const [permFilter, setPermFilter] = useState("");
  const [roleFilter, setRoleFilter] = useState("");

  const filteredRoles = useMemo(() => {
    const term = roleFilter.trim().toLowerCase();
    if (term === "") return roles;
    return roles.filter((r) => r.name.toLowerCase().includes(term));
  }, [roles, roleFilter]);

  const filteredPermissions = useMemo(() => {
    const term = permFilter.trim().toLowerCase();
    if (term === "") return permissions;
    return permissions.filter((p) => p.name.toLowerCase().includes(term));
  }, [permissions, permFilter]);

  async function reload(next?: { page?: number; per_page?: number }) {
    setLoading(true);
    setError(null);
    try {
      const term = q.trim();
      const { items, meta } = await fetchUsers({
        page: next?.page ?? meta.page ?? 1,
        per_page: next?.per_page ?? meta.per_page ?? 20,
        filters: {
          name: term || undefined,
          email: term || undefined,
          user_type: userType === "all" ? undefined : userType,
        },
        sort: "-id",
      });
      setItems(items);
      setMeta(meta);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  React.useEffect(() => {
    reload({ page: 1, per_page: 20 });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  async function openRbac(user: UserItem) {
    setRbacOpen(true);
    setRbacUser(user);
    setRbacLoading(true);
    setError(null);
    try {
      const [u, rs, ps] = await Promise.all([fetchUserRbac(user.id), fetchRoles(), fetchPermissions()]);
      setRbac(u);
      setRoles(rs);
      setPermissions(ps);
      setSelectedRoles(u.roles ?? []);
      setSelectedDirectPermissions(u.direct_permissions ?? []);
    } catch (e) {
      setError(e);
    } finally {
      setRbacLoading(false);
    }
  }

  function toggleSelected(list: string[], name: string): string[] {
    return list.includes(name) ? list.filter((x) => x !== name) : [...list, name];
  }

  async function saveRoles() {
    if (!rbacUser) return;
    setRbacLoading(true);
    setError(null);
    try {
      const u = await syncUserRoles(rbacUser.id, selectedRoles);
      setRbac(u);
    } catch (e) {
      setError(e);
    } finally {
      setRbacLoading(false);
    }
  }

  async function savePermissions() {
    if (!rbacUser) return;
    setRbacLoading(true);
    setError(null);
    try {
      const u = await syncUserPermissions(rbacUser.id, selectedDirectPermissions);
      setRbac(u);
    } catch (e) {
      setError(e);
    } finally {
      setRbacLoading(false);
    }
  }

  const errView = error ? normalizeError(error) : null;

  return (
    <div className="space-y-4">
      <div className="flex flex-wrap items-end justify-between gap-3">
        <div>
          <div className="text-lg font-semibold">Users</div>
          <div className="text-sm text-slate-600">Quản lý tài khoản + phân quyền (roles/permissions).</div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={() => reload()} disabled={loading}>
            Tải lại
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <Card
        title="Danh sách"
        actions={
          <div className="flex flex-wrap items-center gap-2">
            <Input
              placeholder="Tìm theo name/email"
              value={q}
              onChange={(e) => setQ(e.target.value)}
              onKeyDown={(e) => {
                if (e.key === "Enter") reload({ page: 1 });
              }}
            />
            <Select
              value={userType}
              onChange={(e) => {
                setUserType(e.target.value);
                setTimeout(() => reload({ page: 1 }), 0);
              }}
              aria-label="Loại user"
            >
              <option value="all">Tất cả loại</option>
              <option value="USER">USER</option>
              <option value="ADMIN">ADMIN</option>
              <option value="SYSTEM">SYSTEM</option>
            </Select>
            <Button variant="ghost" onClick={() => reload({ page: 1 })} disabled={loading}>
              Lọc
            </Button>
          </div>
        }
      >
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">ID</th>
                <th className="py-2 pr-4">Name</th>
                <th className="py-2 pr-4">Email</th>
                <th className="py-2 pr-4">Type</th>
                <th className="py-2 pr-2">Hành động</th>
              </tr>
            </thead>
            <tbody>
              {items.map((u) => (
                <tr key={u.id} className="border-b last:border-b-0">
                  <td className="py-2 pr-4 font-mono text-xs">{u.id}</td>
                  <td className="py-2 pr-4 font-medium">{u.name ?? "-"}</td>
                  <td className="py-2 pr-4 text-slate-700">{u.email}</td>
                  <td className="py-2 pr-4">
                    {u.user_type === "ADMIN" ? (
                      <Badge tone="success">ADMIN</Badge>
                    ) : u.user_type === "SYSTEM" ? (
                      <Badge tone="warning">SYSTEM</Badge>
                    ) : (
                      <Badge>USER</Badge>
                    )}
                  </td>
                  <td className="py-2 pr-2">
                    <Button variant="ghost" onClick={() => openRbac(u)}>
                      Phân quyền
                    </Button>
                  </td>
                </tr>
              ))}
              {items.length === 0 ? (
                <tr>
                  <td colSpan={5} className="py-6 text-center text-slate-500">
                    Không có dữ liệu.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>

        <Pagination
          meta={meta}
          onChange={(next) => {
            reload(next);
          }}
        />
      </Card>

      <Modal
        open={rbacOpen}
        title={rbacUser ? `Phân quyền: ${rbacUser.email}` : "Phân quyền"}
        onClose={() => setRbacOpen(false)}
      >
        {rbacLoading ? <div className="text-sm text-slate-600">Đang tải...</div> : null}

        {!rbacLoading && rbac ? (
          <div className="space-y-4">
            <div className="text-xs text-slate-600">
              Effective permissions: <span className="font-medium text-slate-900">{rbac.all_permissions.length}</span>
            </div>

            <Card
              title="Roles"
              actions={
                <div className="flex items-center gap-2">
                  <Input placeholder="Lọc role" value={roleFilter} onChange={(e) => setRoleFilter(e.target.value)} />
                  <Button variant="primary" onClick={saveRoles} disabled={rbacLoading}>
                    Lưu roles
                  </Button>
                </div>
              }
            >
              <div className="grid grid-cols-1 md:grid-cols-2 gap-2">
                {filteredRoles.map((r) => (
                  <label key={r.id} className="flex items-center gap-2 text-sm">
                    <input
                      type="checkbox"
                      checked={selectedRoles.includes(r.name)}
                      onChange={() => setSelectedRoles((cur) => toggleSelected(cur, r.name))}
                    />
                    <span className="font-medium">{r.name}</span>
                    <span className="text-xs text-slate-500">({r.permissions.length} perms)</span>
                  </label>
                ))}
                {filteredRoles.length === 0 ? <div className="text-sm text-slate-500">Không có role.</div> : null}
              </div>
            </Card>

            <Card
              title="Direct permissions"
              actions={
                <div className="flex items-center gap-2">
                  <Input
                    placeholder="Lọc permission"
                    value={permFilter}
                    onChange={(e) => setPermFilter(e.target.value)}
                  />
                  <Button variant="primary" onClick={savePermissions} disabled={rbacLoading}>
                    Lưu permissions
                  </Button>
                </div>
              }
            >
              <div className="max-h-[320px] overflow-auto pr-2">
                <div className="space-y-1">
                  {filteredPermissions.map((p) => (
                    <label key={p.id} className="flex items-center gap-2 text-sm">
                      <input
                        type="checkbox"
                        checked={selectedDirectPermissions.includes(p.name)}
                        onChange={() => setSelectedDirectPermissions((cur) => toggleSelected(cur, p.name))}
                      />
                      <span className="font-mono text-xs">{p.name}</span>
                    </label>
                  ))}
                  {filteredPermissions.length === 0 ? (
                    <div className="text-sm text-slate-500">Không có permission.</div>
                  ) : null}
                </div>
              </div>
            </Card>
          </div>
        ) : null}
      </Modal>
    </div>
  );
}

