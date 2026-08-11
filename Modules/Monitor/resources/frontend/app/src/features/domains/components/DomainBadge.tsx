import React from "react";
import Badge from "@shared/ui/Badge";
import type { DomainBadge } from "../types";

const BADGE_LABEL: Record<DomainBadge, string> = {
    ok: "Bình thường",
    soon: "Sắp hết hạn",
    critical: "Gần hết hạn",
    expired: "Đã hết hạn",
    unknown: "Chưa check",
    error: "Lỗi check",
};

const BADGE_CLASS: Record<DomainBadge, string> = {
    ok: "bg-emerald-50 text-emerald-700 ring-emerald-200",
    soon: "bg-amber-50 text-amber-800 ring-amber-200",
    critical: "bg-orange-50 text-orange-700 ring-orange-200",
    expired: "bg-rose-50 text-rose-700 ring-rose-200",
    unknown: "bg-slate-100 text-slate-600 ring-slate-200",
    error: "bg-rose-50 text-rose-700 ring-rose-200",
};

export default function DomainBadge({ badge }: { badge: DomainBadge }) {
    return (
        <span className={["ui-badge", BADGE_CLASS[badge] ?? BADGE_CLASS.unknown].join(" ")}>
            {BADGE_LABEL[badge] ?? badge}
        </span>
    );
}