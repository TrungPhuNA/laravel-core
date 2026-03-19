import React from "react";
import { Link, Outlet, useLocation } from "react-router-dom";
import { useAuth } from "../../shared/state/auth";
import Button from "@shared/ui/Button";
import Input from "@shared/ui/Input";
import Select from "@shared/ui/Select";

function linkClass(pathname: string, targetPrefix: string) {
  const active = pathname === targetPrefix || pathname.startsWith(targetPrefix + "/");
  return active
    ? "text-slate-900 font-semibold"
    : "text-slate-600 hover:text-slate-900";
}

export default function AdminLayout() {
  const loc = useLocation();
  const auth = useAuth();

  return (
    <div className="min-h-dvh bg-slate-50 text-slate-900">
      <header className="sticky top-0 z-10 border-b bg-white/80 backdrop-blur">
        <div className="mx-auto max-w-6xl px-4 py-3 flex flex-wrap items-center gap-3">
          <div className="font-semibold tracking-tight">
            Setting Admin
            <span className="ml-2 text-xs font-normal text-slate-500">API-first</span>
          </div>

          <nav className="flex items-center gap-4 text-sm">
            <Link className={linkClass(loc.pathname, "/settings")} to="/settings">
              Cấu hình
            </Link>
            <Link className={linkClass(loc.pathname, "/queue")} to="/queue">
              Hàng đợi
            </Link>
          </nav>

          <div className="ml-auto flex flex-wrap items-center gap-2 w-full md:w-auto">
            <Select
              value={auth.locale}
              onChange={(e) => auth.setLocale(e.target.value as "vi" | "en")}
              aria-label="Ngôn ngữ"
            >
              <option value="vi">VI</option>
              <option value="en">EN</option>
            </Select>

            <Input
              className="w-full md:w-[380px]"
              placeholder="Dán token Sanctum (ADMIN/SYSTEM) vào đây"
              value={auth.token}
              onChange={(e) => auth.setToken(e.target.value)}
            />
            <Button onClick={() => auth.persist()} variant="primary">
              Lưu token
            </Button>
            <Button onClick={() => auth.clear()} variant="ghost">
              Xoá
            </Button>
          </div>
        </div>

        {!auth.hasToken ? (
          <div className="border-t bg-amber-50 text-amber-900">
            <div className="mx-auto max-w-6xl px-4 py-2 text-xs">
              Chưa có token. Vui lòng tạo token qua API Auth và dán vào ô ở trên để gọi các API quản trị.
            </div>
          </div>
        ) : null}
      </header>

      <main className="mx-auto max-w-6xl px-4 py-6">
        <Outlet />
      </main>
    </div>
  );
}
