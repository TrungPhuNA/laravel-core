import React from "react";

export default function Badge(props: { children: React.ReactNode; tone?: "info" | "success" | "warning" | "danger" }) {
    const tone = props.tone ?? "info";
    const cls =
        tone === "success"
            ? "bg-emerald-50 text-emerald-700 ring-emerald-200"
            : tone === "warning"
                ? "bg-amber-50 text-amber-800 ring-amber-200"
                : tone === "danger"
                    ? "bg-rose-50 text-rose-700 ring-rose-200"
                    : "bg-slate-100 text-slate-700 ring-slate-200";

    return (
        <span className={["inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1", cls].join(" ")}>
            {props.children}
        </span>
    );
}

