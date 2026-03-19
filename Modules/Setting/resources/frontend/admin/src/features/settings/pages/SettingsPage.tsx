import React, { useMemo, useState } from "react";
import Card from "@shared/ui/Card";
import Input from "@shared/ui/Input";
import Button from "@shared/ui/Button";
import Select from "@shared/ui/Select";
import Badge from "@shared/ui/Badge";
import Alert from "@shared/ui/Alert";
import Modal from "@shared/ui/Modal";
import { prettyJson, shortText } from "@shared/lib/format";
import type { ApiResponseFail, ApiResponseError } from "@shared/http/types";
import type { SettingItem } from "../types";
import { fetchAllSettings, upsertSettings } from "../services/settingsApi";

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

export default function SettingsPage() {
  const [loading, setLoading] = useState(false);
  const [items, setItems] = useState<SettingItem[]>([]);
  const [error, setError] = useState<Err>(null);

  const [q, setQ] = useState("");
  const [group, setGroup] = useState<string>("all");
  const [pub, setPub] = useState<string>("all");

  const [editorOpen, setEditorOpen] = useState(false);
  const [editor, setEditor] = useState<SettingItem | null>(null);
  const [editorValue, setEditorValue] = useState<string>("");

  const groups = useMemo(() => {
    const set = new Set<string>();
    for (const it of items) {
      if (it.group) set.add(it.group);
    }
    return Array.from(set).sort((a, b) => a.localeCompare(b));
  }, [items]);

  const filtered = useMemo(() => {
    const term = q.trim().toLowerCase();
    return items.filter((it) => {
      if (group !== "all" && (it.group ?? "") !== group) return false;
      if (pub !== "all") {
        const isPublic = it.is_public ? "public" : "private";
        if (isPublic !== pub) return false;
      }
      if (term === "") return true;
      return (
        it.key.toLowerCase().includes(term) ||
        String(it.value ?? "").toLowerCase().includes(term) ||
        String(it.description ?? "").toLowerCase().includes(term)
      );
    });
  }, [items, q, group, pub]);

  async function reload() {
    setLoading(true);
    setError(null);
    try {
      const list = await fetchAllSettings();
      setItems(list);
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  React.useEffect(() => {
    reload();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  function openEditor(it: SettingItem) {
    setEditor(it);
    setEditorValue(prettyJson(it.value));
    setEditorOpen(true);
  }

  async function saveEditor() {
    if (!editor) return;

    let parsed: unknown = editorValue;
    // Nếu value là JSON hợp lệ thì parse để giữ đúng kiểu.
    try {
      parsed = JSON.parse(editorValue);
    } catch {
      parsed = editorValue;
    }

    setLoading(true);
    setError(null);
    try {
      await upsertSettings([
        {
          key: editor.key,
          value: parsed,
          group: editor.group,
          is_public: editor.is_public,
          description: editor.description,
        },
      ]);
      setEditorOpen(false);
      await reload();
    } catch (e) {
      setError(e);
    } finally {
      setLoading(false);
    }
  }

  async function createNew(input: {
    key: string;
    value: string;
    group: string;
    is_public: boolean;
    description: string;
  }) {
    let parsed: unknown = input.value;
    try {
      parsed = JSON.parse(input.value);
    } catch {
      parsed = input.value;
    }

    setLoading(true);
    setError(null);
    try {
      await upsertSettings([
        {
          key: input.key,
          value: parsed,
          group: input.group.trim() === "" ? null : input.group.trim(),
          is_public: input.is_public,
          description: input.description.trim() === "" ? null : input.description.trim(),
        },
      ]);
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
          <div className="text-lg font-semibold">Cấu hình</div>
          <div className="text-sm text-slate-600">Quản trị settings (bulk upsert).</div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={reload} disabled={loading}>
            Tải lại
          </Button>
        </div>
      </div>

      {errView ? <Alert tone="danger" title={errView.title} details={errView.details} /> : null}

      <NewSettingCard onCreate={createNew} disabled={loading} />

      <Card
        title="Danh sách"
        actions={
          <div className="flex items-center gap-2">
            <Input placeholder="Tìm theo key / value / mô tả" value={q} onChange={(e) => setQ(e.target.value)} />
            <Select value={group} onChange={(e) => setGroup(e.target.value)} aria-label="Nhóm">
              <option value="all">Tất cả nhóm</option>
              {groups.map((g) => (
                <option key={g} value={g}>
                  {g}
                </option>
              ))}
            </Select>
            <Select value={pub} onChange={(e) => setPub(e.target.value)} aria-label="Public">
              <option value="all">Public + Private</option>
              <option value="public">Chỉ public</option>
              <option value="private">Chỉ private</option>
            </Select>
          </div>
        }
      >
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="text-left text-slate-600">
              <tr className="border-b">
                <th className="py-2 pr-4">Key</th>
                <th className="py-2 pr-4">Nhóm</th>
                <th className="py-2 pr-4">Public</th>
                <th className="py-2 pr-4">Value</th>
                <th className="py-2 pr-4">Mô tả</th>
                <th className="py-2 pr-2">Hành động</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((it) => (
                <tr key={it.key} className="border-b last:border-b-0">
                  <td className="py-2 pr-4 font-medium">{it.key}</td>
                  <td className="py-2 pr-4 text-slate-600">{it.group ?? "-"}</td>
                  <td className="py-2 pr-4">
                    {it.is_public ? <Badge tone="success">Public</Badge> : <Badge tone="warning">Private</Badge>}
                  </td>
                  <td className="py-2 pr-4 font-mono text-xs text-slate-700">{shortText(prettyJson(it.value), 110)}</td>
                  <td className="py-2 pr-4 text-slate-600">{it.description ?? "-"}</td>
                  <td className="py-2 pr-2">
                    <Button variant="ghost" onClick={() => openEditor(it)}>
                      Sửa
                    </Button>
                  </td>
                </tr>
              ))}
              {filtered.length === 0 ? (
                <tr>
                  <td colSpan={6} className="py-6 text-center text-slate-500">
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
        title={editor ? `Sửa setting: ${editor.key}` : "Sửa setting"}
        onClose={() => setEditorOpen(false)}
        footer={
          <div className="flex items-center justify-end gap-2">
            <Button variant="ghost" onClick={() => setEditorOpen(false)}>
              Huỷ
            </Button>
            <Button variant="primary" onClick={saveEditor} disabled={loading}>
              Lưu
            </Button>
          </div>
        }
      >
        {editor ? (
          <div className="space-y-3">
            <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
              <div>
                <div className="text-xs font-medium text-slate-600">Nhóm</div>
                <Input
                  value={editor.group ?? ""}
                  onChange={(e) => setEditor({ ...editor, group: e.target.value || null })}
                />
              </div>
              <div>
                <div className="text-xs font-medium text-slate-600">Public</div>
                <Select
                  value={editor.is_public ? "1" : "0"}
                  onChange={(e) => setEditor({ ...editor, is_public: e.target.value === "1" })}
                >
                  <option value="1">Public</option>
                  <option value="0">Private</option>
                </Select>
              </div>
              <div>
                <div className="text-xs font-medium text-slate-600">Mô tả</div>
                <Input
                  value={editor.description ?? ""}
                  onChange={(e) => setEditor({ ...editor, description: e.target.value || null })}
                />
              </div>
            </div>

            <div>
              <div className="text-xs font-medium text-slate-600">Value (JSON hoặc text)</div>
              <textarea
                className={[
                  "mt-1 w-full rounded-md border border-slate-200 bg-white p-3 font-mono text-xs outline-none",
                  "focus:border-slate-400 focus:ring-2 focus:ring-slate-200",
                ].join(" ")}
                rows={12}
                value={editorValue}
                onChange={(e) => setEditorValue(e.target.value)}
              />
              <div className="mt-1 text-xs text-slate-500">
                Tip: nhập JSON để lưu đúng kiểu (object/array/number/bool).
              </div>
            </div>
          </div>
        ) : null}
      </Modal>
    </div>
  );
}

function NewSettingCard(props: { onCreate: (input: any) => Promise<void>; disabled?: boolean }) {
  const [key, setKey] = useState("");
  const [group, setGroup] = useState("");
  const [isPublic, setIsPublic] = useState(true);
  const [description, setDescription] = useState("");
  const [value, setValue] = useState('"Core API"');

  const canSubmit = key.trim() !== "";

  async function submit() {
    if (!canSubmit) return;
    await props.onCreate({
      key: key.trim(),
      value,
      group,
      is_public: isPublic,
      description,
    });
    setKey("");
    setGroup("");
    setDescription("");
  }

  return (
    <Card
      title="Thêm hoặc cập nhật"
      actions={
        <Button variant="primary" onClick={submit} disabled={props.disabled || !canSubmit}>
          Upsert
        </Button>
      }
    >
      <div className="grid grid-cols-1 gap-3 md:grid-cols-4">
        <div>
          <div className="text-xs font-medium text-slate-600">Key</div>
          <Input placeholder="site_name" value={key} onChange={(e) => setKey(e.target.value)} />
        </div>
        <div>
          <div className="text-xs font-medium text-slate-600">Nhóm</div>
          <Input placeholder="general" value={group} onChange={(e) => setGroup(e.target.value)} />
        </div>
        <div>
          <div className="text-xs font-medium text-slate-600">Public</div>
          <Select value={isPublic ? "1" : "0"} onChange={(e) => setIsPublic(e.target.value === "1")}>
            <option value="1">Public</option>
            <option value="0">Private</option>
          </Select>
        </div>
        <div>
          <div className="text-xs font-medium text-slate-600">Mô tả</div>
          <Input placeholder="Tên website" value={description} onChange={(e) => setDescription(e.target.value)} />
        </div>
      </div>

      <div className="mt-3">
        <div className="text-xs font-medium text-slate-600">Value (JSON hoặc text)</div>
        <textarea
          className={[
            "mt-1 w-full rounded-md border border-slate-200 bg-white p-3 font-mono text-xs outline-none",
            "focus:border-slate-400 focus:ring-2 focus:ring-slate-200",
          ].join(" ")}
          rows={4}
          value={value}
          onChange={(e) => setValue(e.target.value)}
        />
      </div>
    </Card>
  );
}
