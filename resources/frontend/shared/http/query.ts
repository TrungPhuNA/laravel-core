type QueryValue = string | number | boolean | null | undefined;

function isPlainObject(v: unknown): v is Record<string, unknown> {
  return typeof v === "object" && v !== null && !Array.isArray(v);
}

// Build query string hỗ trợ bracket syntax: filters[queue]=default
export function buildQuery(params: Record<string, unknown>): string {
  const pairs: Array<[string, string]> = [];

  function add(key: string, value: QueryValue) {
    if (value === null || value === undefined) return;
    const s = String(value).trim();
    if (s === "" || s === "null" || s === "undefined") return;
    pairs.push([key, s]);
  }

  function walk(prefix: string, value: unknown) {
    if (isPlainObject(value)) {
      for (const [k, v] of Object.entries(value)) {
        walk(prefix ? `${prefix}[${k}]` : k, v);
      }
      return;
    }

    if (Array.isArray(value)) {
      for (const v of value) {
        walk(`${prefix}[]`, v);
      }
      return;
    }

    add(prefix, value as QueryValue);
  }

  for (const [k, v] of Object.entries(params)) {
    walk(k, v);
  }

  if (pairs.length === 0) return "";

  const usp = new URLSearchParams();
  for (const [k, v] of pairs) usp.append(k, v);
  const qs = usp.toString();
  return qs ? `?${qs}` : "";
}

