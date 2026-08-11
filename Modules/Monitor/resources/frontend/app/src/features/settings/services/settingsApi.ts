import { api } from "../../../shared/lib/api";
import type { MonitorSettings } from "../types";

export async function getSettings(): Promise<MonitorSettings> {
    const res = await api.get("/monitor/settings");
    return res.data.data?.settings;
}

export async function saveSettings(settings: MonitorSettings): Promise<MonitorSettings> {
    const res = await api.put("/monitor/settings", settings);
    return res.data.data?.settings;
}