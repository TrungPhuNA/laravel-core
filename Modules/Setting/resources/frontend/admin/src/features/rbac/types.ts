export type PermissionItem = {
  id: number;
  name: string;
  guard_name: string;
};

export type RoleItem = {
  id: number;
  name: string;
  guard_name: string;
  permissions: PermissionItem[];
};

export type UserRbac = {
  id: number;
  email: string;
  name: string | null;
  roles: string[];
  direct_permissions: string[];
  all_permissions: string[];
};

