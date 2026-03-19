import React from "react";

type Props = React.SelectHTMLAttributes<HTMLSelectElement>;

export default function Select({ className = "", ...props }: Props) {
  return (
    <select
      className={[
        "h-10 rounded-md border border-slate-200 bg-white px-3 text-sm outline-none shadow-sm",
        "focus:border-slate-400 focus:ring-2 focus:ring-slate-200",
        "disabled:bg-slate-50 disabled:text-slate-500",
        className,
      ].join(" ")}
      {...props}
    />
  );
}
