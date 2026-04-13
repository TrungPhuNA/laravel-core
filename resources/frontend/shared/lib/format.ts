export function prettyJson(value: unknown): string {
  try {
    return JSON.stringify(value, null, 2);
  } catch {
    return String(value);
  }
}

export function shortText(v: unknown, max = 140): string {
  const s = String(v ?? "");
  if (s.length <= max) return s;
  return s.slice(0, max) + "...";
}

/**
 * Format datetime string/Date to "HH:mm DD/MM/YYYY" (Vietnamese style)
 */
export function formatDateTime(value: string | Date | null | undefined): string {
  if (!value) return "—";

  const d = typeof value === "string" ? new Date(value) : value;
  if (isNaN(d.getTime())) return "—";

  return new Intl.DateTimeFormat("vi-VN", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: false,
  }).format(d);
}

