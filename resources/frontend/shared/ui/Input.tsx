import React from "react";

type Props = React.InputHTMLAttributes<HTMLInputElement>;

export default function Input({ className = "", ...props }: Props) {
  return (
    <input
      className={[
        "h-10 rounded-md border border-slate-200 bg-white px-3 text-sm outline-none shadow-sm",
        "placeholder:text-slate-400",
        "focus:border-slate-400 focus:ring-2 focus:ring-slate-200",
        "disabled:bg-slate-50 disabled:text-slate-500",
        className,
      ].join(" ")}
      {...props}
    />
  );
}
