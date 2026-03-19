import React from "react";

export default function Alert(props: { tone?: "info" | "danger" | "warning" | "success"; title: string; details?: string }) {
  const tone = props.tone ?? "info";
  const cls =
    tone === "danger"
      ? "border-rose-200 bg-rose-50 text-rose-900"
    : tone === "warning"
        ? "border-amber-200 bg-amber-50 text-amber-900"
      : tone === "success"
          ? "border-emerald-200 bg-emerald-50 text-emerald-900"
          : "border-slate-200 bg-slate-50 text-slate-900";

  return (
    <div className={["rounded-lg border px-3 py-2 text-sm shadow-sm", cls].join(" ")}>
      <div className="font-semibold">{props.title}</div>
      {props.details ? <div className="mt-1 text-xs opacity-80 whitespace-pre-wrap">{props.details}</div> : null}
    </div>
  );
}
