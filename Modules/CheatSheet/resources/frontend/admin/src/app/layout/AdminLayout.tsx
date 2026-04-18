import React from "react";
import { Link, Outlet, useLocation, useNavigate } from "react-router-dom";
import Button from "@shared/ui/Button";
import Input from "@shared/ui/Input";
import Modal from "@shared/ui/Modal";
import { useAuth } from "../../shared/state/auth";
import { api } from "../../shared/lib/api";

function linkClass(pathname: string, targetPrefix: string) {
  const active = pathname === targetPrefix || pathname.startsWith(targetPrefix + "/");
  return active ? "text-slate-900 font-semibold" : "text-slate-600 hover:text-slate-900";
}

type MeUser = {
  id: number;
  name: string | null;
  email: string | null;
  user_type: string;
  avatar_url?: string | null;
};

function initialsFrom(user: MeUser | null) {
  const base = (user?.name ?? user?.email ?? "U").trim();
  const parts = base.split(/\s+/).filter(Boolean);
  const letters =
    parts.length >= 2
      ? ((parts[0][0] ?? "") + (parts[1][0] ?? "")).toUpperCase()
      : (parts[0]?.slice(0, 2) ?? "U").toUpperCase();
  return letters || "U";
}

export default function AdminLayout() {
  const loc = useLocation();
  const nav = useNavigate();
  const auth = useAuth();

  React.useEffect(() => {
    if (!auth.hasToken) {
      window.location.href = "/auth/login";
    }
  }, [auth.hasToken]);

  const [me, setMe] = React.useState<MeUser | null>(null);
  const [meLoading, setMeLoading] = React.useState(false);

  const [menuOpen, setMenuOpen] = React.useState(false);
  const menuRef = React.useRef<HTMLDivElement | null>(null);

  const [tokenModalOpen, setTokenModalOpen] = React.useState(false);
  const [tokenDraft, setTokenDraft] = React.useState(auth.token);

  React.useEffect(() => setTokenDraft(auth.token), [auth.token]);

  React.useEffect(() => {
    let cancelled = false;

    async function loadMe() {
      if (!auth.hasToken) {
        setMe(null);
        return;
      }

      setMeLoading(true);
      try {
        const res = await api.get("/auth/me");
        const user = (res.data?.data?.user ?? null) as MeUser | null;
        if (!cancelled) setMe(user);
      } catch {
        if (!cancelled) {
          setMe(null);
          auth.clear();
          window.location.href = "/auth/login";
        }
      } finally {
        if (!cancelled) setMeLoading(false);
      }
    }

    loadMe();
    return () => {
      cancelled = true;
    };
  }, [auth.hasToken, auth.token]);

  React.useEffect(() => {
    if (!menuOpen) return;

    function onDocDown(e: MouseEvent) {
      const el = menuRef.current;
      if (!el) return;
      if (e.target instanceof Node && el.contains(e.target)) return;
      setMenuOpen(false);
    }

    document.addEventListener("mousedown", onDocDown);
    return () => document.removeEventListener("mousedown", onDocDown);
  }, [menuOpen]);

  async function logout() {
    try {
      await api.post("/auth/logout", {});
    } catch {
      // ignore
    } finally {
      auth.clear();
      setMe(null);
      nav("/");
      window.location.href = "/auth/login";
    }
  }

  function openProfile() {
    setMenuOpen(false);
    window.location.href = "/auth/profile";
  }

  function openTokenModal() {
    setMenuOpen(false);
    setTokenModalOpen(true);
  }

  function saveToken() {
    auth.setToken(tokenDraft);
    auth.persist();
    setTokenModalOpen(false);
  }

  return (
    <div className="min-h-dvh text-slate-900">
      <header className="sticky top-0 z-10 border-b bg-white/80 backdrop-blur">
        <div className="mx-auto max-w-7xl px-4 py-3 flex flex-col gap-3 md:flex-row md:items-center md:justify-between md:gap-4">
          <div className="flex items-center gap-4 min-w-0">
            <div className="font-semibold tracking-tight shrink-0">
              CheatSheet Admin
              <span className="ml-2 text-xs font-normal text-slate-500">API-first</span>
            </div>

            <nav className="flex items-center gap-4 text-sm min-w-0">
              <Link className={linkClass(loc.pathname, "/")} to="/">
                Cheat sheets
              </Link>
              <Link className={linkClass(loc.pathname, "/topics")} to="/topics">
                Chủ đề
              </Link>
            </nav>
          </div>

          <div className="flex items-center gap-2 w-full md:w-auto justify-end md:flex-nowrap">
            {auth.hasToken && me ? (
              <div className="relative" ref={menuRef}>
                <button
                  type="button"
                  className={[
                    "h-10 inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 text-sm shadow-sm",
                    "hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-300",
                    "whitespace-nowrap",
                  ].join(" ")}
                  onClick={() => setMenuOpen((v) => !v)}
                >
                  {me.avatar_url ? (
                    <img className="h-7 w-7 rounded-full object-cover" src={me.avatar_url} alt="avatar" />
                  ) : (
                    <span className="h-7 w-7 rounded-full bg-slate-900 text-white grid place-items-center text-xs font-semibold">
                      {initialsFrom(me)}
                    </span>
                  )}
                  <span className="max-w-[180px] truncate font-semibold whitespace-nowrap">
                    {me.name ?? me.email ?? "Tài khoản"}
                  </span>
                  <span className="text-slate-500">▾</span>
                </button>

                {menuOpen ? (
                  <div className="absolute right-0 mt-2 w-[260px] rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden">
                    <div className="px-4 py-3 bg-gradient-to-b from-white to-slate-50 border-b border-slate-100">
                      <div className="text-sm font-semibold truncate">{me.name ?? "Tài khoản"}</div>
                      <div className="text-xs text-slate-600 truncate">{me.email ?? "-"}</div>
                      <div className="mt-1 text-[11px] text-slate-500">type: {me.user_type}</div>
                    </div>

                    <button
                      type="button"
                      className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50"
                      onClick={openProfile}
                    >
                      Cập nhật thông tin
                    </button>
                    <button
                      type="button"
                      className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50"
                      onClick={openTokenModal}
                    >
                      Token hệ thống
                    </button>
                    <div className="h-px bg-slate-100" />
                    <button
                      type="button"
                      className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 text-rose-700"
                      onClick={logout}
                    >
                      Đăng xuất
                    </button>
                  </div>
                ) : null}
              </div>
            ) : (
              <>
                <Input
                  className="w-full md:w-[420px] min-w-0"
                  placeholder="Dán token Sanctum hoặc đăng nhập /auth/login"
                  value={auth.token}
                  onChange={(e) => auth.setToken(e.target.value)}
                  disabled={meLoading}
                />
                <Button onClick={() => auth.persist()} variant="primary" disabled={meLoading}>
                  Lưu token
                </Button>
                <Button onClick={() => auth.clear()} variant="ghost" disabled={meLoading}>
                  Xoá
                </Button>
                <a className="text-sm font-semibold text-slate-900 hover:underline" href="/auth/login">
                  Đăng nhập
                </a>
              </>
            )}
          </div>
        </div>
      </header>

      <main className="mx-auto max-w-7xl px-4 py-6">
        <Outlet />
      </main>

      <Modal open={tokenModalOpen} title="Token hệ thống" onClose={() => setTokenModalOpen(false)}>
        <div className="space-y-3">
          <Input
            placeholder="Bearer token (Sanctum)"
            value={tokenDraft}
            onChange={(e) => setTokenDraft(e.target.value)}
            disabled={meLoading}
          />
          <div className="flex items-center justify-end gap-2">
            <Button variant="ghost" onClick={() => setTokenModalOpen(false)}>
              Huỷ
            </Button>
            <Button variant="primary" onClick={saveToken} disabled={meLoading}>
              Lưu
            </Button>
          </div>
        </div>
      </Modal>
    </div>
  );
}
