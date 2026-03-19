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

