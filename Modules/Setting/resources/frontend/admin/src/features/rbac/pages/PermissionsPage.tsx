import React, { useMemo, useState } from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import Badge from "@shared/ui/Badge";
import { prettyJson } from "@shared/lib/format";
import type { ApiResponseFail, ApiResponseError } from "@shared/http/types";
import type { PermissionItem } from "../types";
import { createPermission, fetchPermissions } from "../services/rbacApi";
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

export default function PermissionsPage() {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Err>(null);
  const [items, setItems] = useState<PermissionItem[]>([]);
  const [q, setQ] = useState("");
  const [name, setName] = useState("");

  const filtered = useMemo(() => {
    const term = q.trim().toLowerCase();
    if (term === "") return items;
    return items.filter((p) => p.name.toLowerCase().includes(term));
  }, [items, q]);

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const ps = await fetchPermissions();
      setItems(ps);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  React.useEffect(() => {
    reload();
  }, []);

  async function create() {
    const n = name.trim();
    if (n === "") return;
    setLoading(true);
    setError(null);
    try {
      await createPermission({ name: n });
      setName("");
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
          <div className="text-lg font-semibold">Permissions</div>
          <div className="text-sm text-slate-600">Danh sách permissions (tạo thêm khi cần).</div>
        </div>
        <div className="flex items-center gap-2">
          <RbacTabs />
          <Button variant="ghost" onClick={reload} disabled={loading}>
            Tải lại
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <Card title="Tạo permission">
        <div className="flex flex-wrap items-center gap-2">
          <Input
            className="w-full md:w-[420px]"
            placeholder="Ví dụ: setting.users.read"
            value={name}
            onChange={(e) => setName(e.target.value)}
            onKeyDown={(e) => {
              if (e.key === "Enter") create();
            }}
          />
          <Button variant="primary" onClick={create} disabled={loading}>
            Tạo
          </Button>
        </div>
        <div className="mt-2 text-xs text-slate-500">
          Convention: <Badge tone="info">setting.*</Badge> để tránh trùng với module khác.
        </div>
      </Card>

      <Card title="Danh sách" actions={<Input placeholder="Lọc theo tên" value={q} onChange={(e) => setQ(e.target.value)} />}>
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">Permission</th>
                <th className="py-2 pr-2">Guard</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((p) => (
                <tr key={p.id} className="border-b last:border-b-0">
                  <td className="py-2 pr-4 font-mono text-xs">{p.name}</td>
                  <td className="py-2 pr-2 text-slate-600">{p.guard_name}</td>
                </tr>
              ))}
              {filtered.length === 0 ? (
                <tr>
                  <td colSpan={2} className="py-6 text-center text-slate-500">
                    Không có dữ liệu.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </Card>
    </div>
  );
}
