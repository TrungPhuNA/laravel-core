import React from "react";
import { Link, Outlet, useLocation } from "react-router-dom";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";

function linkClass(pathname: string, targetPrefix: string) {
  const active = pathname === targetPrefix || pathname.startsWith(targetPrefix + "/");
  return active
    ? "text-white font-semibold"
    : "text-slate-300 hover:text-white";
}

export default function PublicLayout() {
  const loc = useLocation();

  return (
    <div className="min-h-dvh text-white">
      <header className="sticky top-0 z-10 border-b border-white/10 bg-slate-950/40 backdrop-blur">
        <div className="mx-auto max-w-7xl px-4 py-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <div className="flex items-center gap-4 min-w-0">
            <Link to="/" className="font-bold tracking-tight text-white shrink-0">
              CheatSheets
            </Link>
            <nav className="flex items-center gap-4 text-sm min-w-0">
              <Link className={linkClass(loc.pathname, "/")} to="/">
                Topics
              </Link>
              <Link className={linkClass(loc.pathname, "/all")} to="/all">
                All
              </Link>
            </nav>
          </div>

          <div className="flex items-center gap-2">
            <a className="text-sm text-slate-300 hover:text-white" href="/admin/cheat-sheets">
              Admin
            </a>
            <a className="text-sm text-slate-300 hover:text-white" href="/auth/login">
              Login
            </a>
          </div>
        </div>
      </header>

      <main className="mx-auto max-w-7xl px-4 py-8">
        <Outlet />
      </main>

      <footer className="border-t border-white/10 py-8">
        <div className="mx-auto max-w-7xl px-4 text-xs text-slate-400">
          Powered by Laravel Core Template
        </div>
      </footer>
    </div>
  );
}

