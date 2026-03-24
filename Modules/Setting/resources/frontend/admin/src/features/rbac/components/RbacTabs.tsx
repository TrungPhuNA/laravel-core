import React from "react";
import { Link, useLocation } from "react-router-dom";

function tabClass(active: boolean) {
  return active
    ? "px-3 py-1.5 rounded-md bg-slate-900 text-white text-sm font-semibold"
    : "px-3 py-1.5 rounded-md bg-white text-slate-700 text-sm hover:bg-slate-50 border border-slate-200";
}

export default function RbacTabs() {
  const loc = useLocation();
  const isRoles = loc.pathname === "/rbac/roles" || loc.pathname.startsWith("/rbac/roles/");
  const isPerms = loc.pathname === "/rbac/permissions" || loc.pathname.startsWith("/rbac/permissions/");

  return (
    <div className="flex items-center gap-2">
      <Link to="/rbac/roles" className={tabClass(isRoles)}>
        Roles
      </Link>
      <Link to="/rbac/permissions" className={tabClass(isPerms)}>
        Permissions
      </Link>
    </div>
  );
}

