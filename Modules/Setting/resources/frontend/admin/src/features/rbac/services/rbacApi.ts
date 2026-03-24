import type { ApiResponseSuccess } from "@shared/http/types";
import { api } from "../../../shared/lib/api";
import type { PermissionItem, RoleItem, UserRbac } from "../types";

export async function fetchRoles(): Promise<RoleItem[]> {
  const res = await api.get<ApiResponseSuccess<{ items: RoleItem[] }>>("/settings/rbac/roles");
  if (res.data.status !== "success") throw res.data;
  return res.data.data.items ?? [];
}

export async function createRole(input: { name: string; permissions: string[] }): Promise<RoleItem> {
  const res = await api.post<ApiResponseSuccess<{ role: RoleItem }>>("/settings/rbac/roles", input);
  if (res.data.status !== "success") throw res.data;
  return res.data.data.role;
}

export async function updateRole(id: number, input: { name?: string; permissions?: string[] }): Promise<RoleItem> {
  const res = await api.put<ApiResponseSuccess<{ role: RoleItem }>>(`/settings/rbac/roles/${id}`, input);
  if (res.data.status !== "success") throw res.data;
  return res.data.data.role;
}

export async function deleteRole(id: number): Promise<void> {
  const res = await api.delete<ApiResponseSuccess<any>>(`/settings/rbac/roles/${id}`);
  if (res.data.status !== "success") throw res.data;
}

export async function fetchPermissions(): Promise<PermissionItem[]> {
  const res = await api.get<ApiResponseSuccess<{ items: PermissionItem[] }>>("/settings/rbac/permissions");
  if (res.data.status !== "success") throw res.data;
  return res.data.data.items ?? [];
}

export async function createPermission(input: { name: string }): Promise<PermissionItem> {
  const res = await api.post<ApiResponseSuccess<{ permission: PermissionItem }>>("/settings/rbac/permissions", input);
  if (res.data.status !== "success") throw res.data;
  return res.data.data.permission;
}

export async function fetchUserRbac(id: number): Promise<UserRbac> {
  const res = await api.get<ApiResponseSuccess<{ user: UserRbac }>>(`/settings/rbac/users/${id}`);
  if (res.data.status !== "success") throw res.data;
  return res.data.data.user;
}

export async function syncUserRoles(id: number, roles: string[]): Promise<UserRbac> {
  const res = await api.put<ApiResponseSuccess<{ user: UserRbac }>>(`/settings/rbac/users/${id}/roles`, { roles });
  if (res.data.status !== "success") throw res.data;
  return res.data.data.user;
}

export async function syncUserPermissions(id: number, permissions: string[]): Promise<UserRbac> {
  const res = await api.put<ApiResponseSuccess<{ user: UserRbac }>>(`/settings/rbac/users/${id}/permissions`, {
    permissions,
  });
  if (res.data.status !== "success") throw res.data;
  return res.data.data.user;
}

