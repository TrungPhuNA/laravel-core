import React from "react";
import Button from "@shared/ui/Button";
import Input from "@shared/ui/Input";
import Card from "@shared/ui/Card";
import Alert from "@shared/ui/Alert";
import { useAuth } from "../../../shared/state/auth";
import { getSettings, saveSettings } from "../services/settingsApi";
import type { MonitorSettings } from "../types";

const DEFAULT_SETTINGS: MonitorSettings = {
    check: { rdap: { enabled: true }, whois: { enabled: true }, third_party: { enabled: false, api_key: "" } },
    warning: { normal_days: 60, soon_days: 30, critical_days: 7 },
};

export default function SettingsPage() {
    const auth = useAuth();

    const [settings, setSettings] = React.useState<MonitorSettings>(DEFAULT_SETTINGS);
    const [loading, setLoading] = React.useState(false);
    const [saving, setSaving] = React.useState(false);
    const [error, setError] = React.useState<string | null>(null);
    const [saved, setSaved] = React.useState(false);

    React.useEffect(() => {
        if (!auth.hasToken) return;
        let cancelled = false;
        (async () => {
            setLoading(true);
            try {
                const s = await getSettings();
                if (!cancelled) setSettings(s);
            } catch (e: unknown) {
                if (!cancelled) setError((e as { message?: string })?.message ?? "Không tải được cấu hình");
            } finally {
                if (!cancelled) setLoading(false);
            }
        })();
        return () => {
            cancelled = true;
        };
    }, [auth.hasToken]);

    function patch(p: (prev: MonitorSettings) => MonitorSettings) {
        setSettings((prev) => p(prev));
        setSaved(false);
    }

    async function handleSave() {
        setSaving(true);
        setError(null);
        setSaved(false);
        try {
            const s = await saveSettings(settings);
            setSettings(s);
            setSaved(true);
        } catch (e: unknown) {
            setError((e as { message?: string })?.message ?? "Lưu cấu hình thất bại");
        } finally {
            setSaving(false);
        }
    }

    return (
        <div className="mx-auto max-w-3xl space-y-4">
            <div>
                <h1 className="text-lg font-semibold">Cấu hình monitor domain</h1>
                <p className="text-sm text-slate-500">Cách tra cứu hạn domain và ngưỡng cảnh báo màu.</p>
            </div>

            {error ? <Alert tone="danger" title={error} /> : null}
            {saved ? <Alert tone="success" title="Đã lưu cấu hình." /> : null}

            {loading ? (
                <div className="py-8 text-center text-sm text-slate-500">Đang tải...</div>
            ) : (
                <>
                    <Card title="Phương thức tra cứu">
                        <div className="space-y-4">
                            <label className="flex items-start justify-between gap-3 rounded-lg border border-slate-100 p-3">
                                <div>
                                    <div className="font-medium">RDAP (HTTP)</div>
                                    <div className="text-xs text-slate-500">
                                        Tra cứu qua HTTP JSON. Hoạt động tốt cho .com/.org/.net... Không hỗ trợ .vn.
                                    </div>
                                </div>
                                <input
                                    type="checkbox"
                                    checked={settings.check.rdap.enabled}
                                    onChange={(e) =>
                                        patch((p) => ({ ...p, check: { ...p.check, rdap: { enabled: e.target.checked } } }))
                                    }
                                />
                            </label>

                            <label className="flex items-start justify-between gap-3 rounded-lg border border-slate-100 p-3">
                                <div>
                                    <div className="font-medium">WHOIS (port 43)</div>
                                    <div className="text-xs text-slate-500">
                                        Fallback cho .vn và các TLD không hỗ trợ RDAP. Cần server mở được port 43 tới whois
                                        server (whois.vnnic.vn cho .vn).
                                    </div>
                                </div>
                                <input
                                    type="checkbox"
                                    checked={settings.check.whois.enabled}
                                    onChange={(e) =>
                                        patch((p) => ({ ...p, check: { ...p.check, whois: { enabled: e.target.checked } } }))
                                    }
                                />
                            </label>

                            <div className="rounded-lg border border-slate-100 p-3">
                                <label className="flex items-start justify-between gap-3">
                                    <div>
                                        <div className="font-medium">API tra cứu bên thứ ba (tùy chọn)</div>
                                        <div className="text-xs text-slate-500">
                                            Dùng khi RDAP/WHOIS đều fail (VD: .vn bị chặn port 43). Cần API key.
                                        </div>
                                    </div>
                                    <input
                                        type="checkbox"
                                        checked={settings.check.third_party.enabled}
                                        onChange={(e) =>
                                            patch((p) => ({
                                                ...p,
                                                check: { ...p.check, third_party: { ...p.check.third_party, enabled: e.target.checked } },
                                            }))
                                        }
                                    />
                                </label>
                                {settings.check.third_party.enabled ? (
                                    <div className="mt-3">
                                        <div className="text-xs font-medium text-slate-600">API key</div>
                                        <Input
                                            className="mt-1 w-full"
                                            placeholder="Nhập API key..."
                                            value={settings.check.third_party.api_key}
                                            onChange={(e) =>
                                                patch((p) => ({
                                                    ...p,
                                                    check: { ...p.check, third_party: { ...p.check.third_party, api_key: e.target.value } },
                                                }))
                                            }
                                        />
                                    </div>
                                ) : null}
                            </div>
                        </div>
                    </Card>

                    <Card title="Ngưỡng cảnh báo màu">
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <div className="text-xs font-medium text-slate-600">Bình thường (xanh) — trên N ngày</div>
                                <Input
                                    className="mt-1 w-full"
                                    type="number"
                                    min={1}
                                    value={String(settings.warning.normal_days)}
                                    onChange={(e) =>
                                        patch((p) => ({ ...p, warning: { ...p.warning, normal_days: Number(e.target.value) } }))
                                    }
                                />
                            </div>
                            <div>
                                <div className="text-xs font-medium text-slate-600">Sắp hết hạn (vàng) — dưới N ngày</div>
                                <Input
                                    className="mt-1 w-full"
                                    type="number"
                                    min={1}
                                    value={String(settings.warning.soon_days)}
                                    onChange={(e) =>
                                        patch((p) => ({ ...p, warning: { ...p.warning, soon_days: Number(e.target.value) } }))
                                    }
                                />
                            </div>
                            <div>
                                <div className="text-xs font-medium text-slate-600">Gần hết hạn (cam) — dưới N ngày</div>
                                <Input
                                    className="mt-1 w-full"
                                    type="number"
                                    min={1}
                                    value={String(settings.warning.critical_days)}
                                    onChange={(e) =>
                                        patch((p) => ({ ...p, warning: { ...p.warning, critical_days: Number(e.target.value) } }))
                                    }
                                />
                            </div>
                        </div>
                    </Card>

                    <div className="flex justify-end">
                        <Button onClick={handleSave} disabled={saving}>
                            {saving ? "Đang lưu..." : "Lưu cấu hình"}
                        </Button>
                    </div>
                </>
            )}
        </div>
    );
}