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
import { fetchUsers, createUser, deleteUser } from "../services/usersApi";
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

  const [createOpen, setCreateOpen] = useState(false);
  const [createData, setCreateData] = useState({ name: "", email: "", password: "", user_type: "USER" });

  const [deleteOpen, setDeleteOpen] = useState(false);
  const [userToDelete, setUserToDelete] = useState<UserItem | null>(null);

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
      // Rename destructured meta to responseMeta to avoid shadowing and initialization error
      const { items: fetchedItems, meta: responseMeta } = await fetchUsers({
        page: next?.page ?? meta.page ?? 1,
        per_page: next?.per_page ?? meta.per_page ?? 20,
        filters: {
          name: term || undefined,
          email: term || undefined,
          user_type: userType === "all" ? undefined : userType,
        },
        sort: "-id",
      });
      setItems(fetchedItems);
      setMeta(responseMeta);
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

  async function handleCreate() {
    if (!createData.name || !createData.email || !createData.password) {
      alert("Vui lòng nhập đầy đủ thông tin");
      return;
    }
    setLoading(true);
    setError(null);
    try {
      await createUser(createData);
      setCreateOpen(false);
      setCreateData({ name: "", email: "", password: "", user_type: "USER" });
      reload({ page: 1 });
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function confirmDelete() {
    if (!userToDelete) return;
    setLoading(true);
    setError(null);
    try {
      await deleteUser(userToDelete.id);
      setDeleteOpen(false);
      setUserToDelete(null);
      reload();
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

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
    <div className="space-y-6">
      {/* Header Section */}
      <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div className="flex-1">
          <div className="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">
            Thiết lập hệ thống
          </div>
          <h1 className="text-2xl font-black text-slate-900 tracking-tight">Quản lý Tài khoản</h1>
          <p className="text-sm text-slate-500 mt-1">Danh sách người dùng và cấu hình phân quyền truy cập (RBAC).</p>
        </div>
        <div className="flex items-center gap-2">
          <Button onClick={() => reload()} disabled={loading} variant="ghost">Tải lại</Button>
          <Button onClick={() => setCreateOpen(true)} disabled={loading} variant="primary">
            + Tạo tài khoản
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      {/* Filter Section */}
      <div className="bg-slate-50 p-4 rounded-3xl border border-slate-200/50 flex flex-wrap items-center gap-4">
        <div className="flex-1 min-w-[200px]">
          <Input
            placeholder="Tìm theo tên hoặc email..."
            value={q}
            onChange={(e) => setQ(e.target.value)}
            onKeyDown={(e) => {
              if (e.key === "Enter") reload({ page: 1 });
            }}
            className="w-full"
          />
        </div>
        <div className="w-[180px]">
          <Select
            value={userType}
            onChange={(e) => {
              setUserType(e.target.value);
              setTimeout(() => reload({ page: 1 }), 0);
            }}
            aria-label="Loại user"
          >
            <option value="all">Tất cả loại</option>
            <option value="USER">Người dùng (USER)</option>
            <option value="ADMIN">Quản trị (ADMIN)</option>
            <option value="SYSTEM">Hệ thống (SYSTEM)</option>
          </Select>
        </div>
        <Button variant="primary" onClick={() => reload({ page: 1 })} disabled={loading} className="px-8 min-w-[100px]">
          Lọc
        </Button>
      </div>

      <Card
        title={`Danh sách (${meta.total ?? 0})`}
        className="rounded-3xl overflow-hidden border-slate-100 shadow-sm"
      >
        <div className={`overflow-x-auto transition-opacity duration-200 ${loading ? 'opacity-50 pointer-events-none' : 'opacity-100'}`}>
          <table className="min-w-full text-sm">
            <thead className="bg-slate-50 text-slate-500">
              <tr>
                <th className="text-left px-4 py-3 font-bold">ID</th>
                <th className="text-left px-4 py-3 font-bold">Thông tin tài khoản</th>
                <th className="text-left px-4 py-3 font-bold">Loại</th>
                <th className="text-left px-4 py-3 font-bold">Ngày tạo</th>
                <th className="text-right px-4 py-3 font-bold">Hành động</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {items.map((u) => (
                <tr key={u.id} className="hover:bg-slate-50/50 transition-colors group">
                  <td className="px-4 py-3 font-mono text-xs text-slate-400">#{u.id}</td>
                  <td className="px-4 py-3">
                    <div className="font-bold text-slate-900">{u.name || "N/A"}</div>
                    <div className="text-xs text-slate-500">{u.email}</div>
                  </td>
                  <td className="px-4 py-3">
                    {u.user_type === "ADMIN" ? (
                      <Badge tone="success">ADMIN</Badge>
                    ) : u.user_type === "SYSTEM" ? (
                      <Badge tone="warning">SYSTEM</Badge>
                    ) : (
                      <Badge>USER</Badge>
                    )}
                  </td>
                  <td className="px-4 py-3 text-slate-500 text-xs italic">
                    {new Date(u.created_at).toLocaleDateString("vi-VN")}
                  </td>
                  <td className="px-4 py-3 text-right space-x-1">
                    <Button variant="ghost" className="h-8 text-xs font-bold" onClick={() => openRbac(u)}>
                      Phân quyền
                    </Button>
                    <Button variant="ghost" className="h-8 text-xs font-bold text-rose-500 hover:bg-rose-50" onClick={() => {
                      setUserToDelete(u);
                      setDeleteOpen(true);
                    }}>
                      Xoá
                    </Button>
                  </td>
                </tr>
              ))}
              {items.length === 0 && !loading ? (
                <tr>
                  <td colSpan={5} className="py-12 text-center text-slate-500 italic">
                    Không tìm thấy tài khoản nào phù hợp.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>

        <div className="p-4 border-t border-slate-50 bg-slate-50/30">
          <Pagination
            meta={meta}
            onChange={(next) => {
              reload(next);
            }}
          />
        </div>
      </Card>

      {/* Modal Create User */}
      <Modal
        open={createOpen}
        title="Tạo tài khoản mới"
        onClose={() => setCreateOpen(false)}
        footer={
          <div className="flex items-center justify-end gap-2 px-6 py-4 bg-slate-50/50">
            <Button variant="ghost" onClick={() => setCreateOpen(false)}>Huỷ bỏ</Button>
            <Button variant="primary" onClick={handleCreate} disabled={loading}>
              {loading ? "Đang lưu..." : "Xác nhận tạo"}
            </Button>
          </div>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="ui-label">Họ và tên</label>
            <Input
              value={createData.name}
              onChange={(e) => setCreateData({ ...createData, name: e.target.value })}
              placeholder="Nhập tên người dùng..."
            />
          </div>
          <div>
            <label className="ui-label">Địa chỉ Email</label>
            <Input
              type="email"
              value={createData.email}
              onChange={(e) => setCreateData({ ...createData, email: e.target.value })}
              placeholder="email@example.com"
            />
          </div>
          <div>
            <label className="ui-label">Mật khẩu</label>
            <Input
              type="password"
              value={createData.password}
              onChange={(e) => setCreateData({ ...createData, password: e.target.value })}
              placeholder="••••••••"
            />
          </div>
          <div>
            <label className="ui-label">Loại tài khoản</label>
            <Select
              value={createData.user_type}
              onChange={(e) => setCreateData({ ...createData, user_type: e.target.value })}
            >
              <option value="USER">USER (Người dùng thông thường)</option>
              <option value="ADMIN">ADMIN (Quản trị viên)</option>
              <option value="SYSTEM">SYSTEM (Tài khoản hệ thống)</option>
            </Select>
          </div>
        </div>
      </Modal>

      {/* Modal Delete Confirm */}
      <Modal
        open={deleteOpen}
        title="Xác nhận xoá tài khoản"
        onClose={() => setDeleteOpen(false)}
        footer={
          <div className="flex items-center justify-end gap-2 px-6 py-4 bg-slate-50/50">
            <Button variant="ghost" onClick={() => setDeleteOpen(false)}>Đóng</Button>
            <Button variant="danger" onClick={confirmDelete} disabled={loading}>
              {loading ? "Đang xử lý..." : "Đồng ý xoá"}
            </Button>
          </div>
        }
      >
        <div className="p-2 text-center space-y-4">
          <div className="mx-auto w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center">
            <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
          </div>
          <div>
            <p className="text-slate-600">Bạn có chắc chắn muốn xoá tài khoản này không?</p>
            <p className="text-sm font-bold text-slate-900 mt-1">{userToDelete?.email}</p>
            <p className="text-xs text-slate-400 mt-2">Hành động này không thể hoàn tác trong một số trường hợp.</p>
          </div>
        </div>
      </Modal>

      {/* Modal RBAC */}
      <Modal
        open={rbacOpen}
        title={rbacUser ? `Phân quyền: ${rbacUser.email}` : "Phân quyền"}
        onClose={() => setRbacOpen(false)}
        className="max-w-4xl"
      >
        {rbacLoading ? (
          <div className="py-12 flex flex-col items-center justify-center space-y-4">
            <div className="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
            <div className="text-sm text-slate-500 font-medium">Đang tải dữ liệu quyền hạn...</div>
          </div>
        ) : null}

        {!rbacLoading && rbac ? (
          <div className="space-y-6">
            <div className="bg-sky-50 p-4 rounded-2xl border border-sky-100 flex items-center justify-between">
              <div className="text-sm text-sky-800 font-medium">
                Quyền hạn thực tế (Effective permissions)
              </div>
              <Badge tone="success" className="text-sm px-4 py-1">
                {rbac.all_permissions.length} Quyền
              </Badge>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card
                title="Vai trò (Roles)"
                className="shadow-none border-slate-200"
                actions={
                  <div className="flex items-center gap-2">
                    <Input 
                      placeholder="Lọc vai trò..." 
                      value={roleFilter} 
                      onChange={(e) => setRoleFilter(e.target.value)} 
                      className="h-8 text-xs"
                    />
                    <Button variant="primary" onClick={saveRoles} disabled={rbacLoading} className="h-8 text-xs whitespace-nowrap">
                      Lưu Roles
                    </Button>
                  </div>
                }
              >
                <div className="grid grid-cols-1 gap-2 max-h-[400px] overflow-auto pr-2">
                  {filteredRoles.map((r) => (
                    <label key={r.id} className="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors cursor-pointer group">
                      <input
                        type="checkbox"
                        className="w-4 h-4 rounded text-sky-600 focus:ring-sky-500"
                        checked={selectedRoles.includes(r.name)}
                        onChange={() => setSelectedRoles((cur) => toggleSelected(cur, r.name))}
                      />
                      <div className="flex-1">
                        <div className="text-sm font-bold text-slate-700 group-hover:text-slate-900 transition-colors">{r.name}</div>
                        <div className="text-[10px] text-slate-400 font-medium uppercase tracking-wider">{r.permissions.length} permissions</div>
                      </div>
                    </label>
                  ))}
                  {filteredRoles.length === 0 ? <div className="text-sm text-slate-400 italic py-4 text-center">Không có vai trò nào.</div> : null}
                </div>
              </Card>

              <Card
                title="Quyền hạn trực tiếp"
                className="shadow-none border-slate-200"
                actions={
                  <div className="flex items-center gap-2">
                    <Input
                      placeholder="Lọc quyền..."
                      value={permFilter}
                      onChange={(e) => setPermFilter(e.target.value)}
                      className="h-8 text-xs"
                    />
                    <Button variant="primary" onClick={savePermissions} disabled={rbacLoading} className="h-8 text-xs whitespace-nowrap">
                      Lưu Direct Perms
                    </Button>
                  </div>
                }
              >
                <div className="max-h-[400px] overflow-auto pr-2 space-y-1">
                  {filteredPermissions.map((p) => (
                    <label key={p.id} className="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer">
                      <input
                        type="checkbox"
                        className="w-3.5 h-3.5 rounded text-sky-600 focus:ring-sky-500"
                        checked={selectedDirectPermissions.includes(p.name)}
                        onChange={() => setSelectedDirectPermissions((cur) => toggleSelected(cur, p.name))}
                      />
                      <span className="font-mono text-xs text-slate-600">{p.name}</span>
                    </label>
                  ))}
                  {filteredPermissions.length === 0 ? (
                    <div className="text-sm text-slate-400 italic py-4 text-center">Không tìm thấy quyền hạn.</div>
                  ) : null}
                </div>
              </Card>
            </div>
          </div>
        ) : null}
      </Modal>
    </div>
  );
}
