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
    <div className="flex items-center justify-between gap-3 border-t border-slate-100 pt-3 text-sm">
      <div className="text-slate-600">
        Tổng: <span className="font-medium text-slate-900">{meta.total}</span> | Trang{" "}
        <span className="font-medium text-slate-900">{meta.page}</span> /{" "}
        <span className="font-medium text-slate-900">{meta.last_page}</span>
      </div>

      <div className="flex items-center gap-2">
        <Select
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
          disabled={meta.page <= 1}
          onClick={() => props.onChange({ page: Math.max(1, meta.page - 1), per_page: meta.per_page })}
        >
          Trước
        </Button>
        <Button
          variant="ghost"
          disabled={meta.page >= meta.last_page}
          onClick={() => props.onChange({ page: Math.min(meta.last_page, meta.page + 1), per_page: meta.per_page })}
        >
          Sau
        </Button>
      </div>
    </div>
  );
}

