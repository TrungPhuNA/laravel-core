import React from "react";
import type { ApiMetaPagination } from "../http/types";
import Button from "./Button";
import Select from "./Select";

export default function Pagination(props: {
  meta: ApiMetaPagination;
  onChange: (next: { page: number; per_page: number }) => void;
}) {
  const { meta } = props;

  return (
    <div className="flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100 pt-3 text-xs sm:text-sm">
      <div className="text-slate-500 order-2 sm:order-1">
        Tổng: <span className="font-bold text-slate-900">{meta.total}</span> | Trang{" "}
        <span className="font-bold text-slate-900">{meta.page}</span> /{" "}
        <span className="font-bold text-slate-900">{meta.last_page}</span>
      </div>

      <div className="flex items-center gap-1.5 sm:gap-2 order-1 sm:order-2">
        <Select
          className="h-8 sm:h-9 text-xs"
          value={String(meta.per_page)}
          onChange={(e) => props.onChange({ page: 1, per_page: Number(e.target.value) })}
          aria-label="Số dòng"
        >
          {[10, 20, 50, 100].map((n) => (
            <option key={n} value={String(n)}>
              {n}/trang
            </option>
          ))}
        </Select>

        <Button
          variant="ghost"
          className="h-8 sm:h-9 px-2 sm:px-3 text-xs font-bold"
          disabled={meta.page <= 1}
          onClick={() => props.onChange({ page: Math.max(1, meta.page - 1), per_page: meta.per_page })}
        >
          Trước
        </Button>
        <Button
          variant="ghost"
          className="h-8 sm:h-9 px-2 sm:px-3 text-xs font-bold"
          disabled={meta.page >= meta.last_page}
          onClick={() => props.onChange({ page: Math.min(meta.last_page, meta.page + 1), per_page: meta.per_page })}
        >
          Sau
        </Button>
      </div>
    </div>
  );
}

