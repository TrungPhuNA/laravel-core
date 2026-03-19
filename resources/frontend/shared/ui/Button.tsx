import React from "react";

type Props = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: "primary" | "ghost" | "danger";
};

export default function Button({ className = "", variant = "primary", ...props }: Props) {
  const base =
    "inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-semibold transition " +
    "disabled:opacity-50 disabled:cursor-not-allowed " +
    "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-300 focus-visible:ring-offset-2 focus-visible:ring-offset-white";

  const styles =
    variant === "primary"
      ? "bg-slate-900 text-white shadow-sm hover:bg-slate-800 active:bg-slate-900"
      : variant === "danger"
        ? "bg-rose-600 text-white shadow-sm hover:bg-rose-500 active:bg-rose-600 focus-visible:ring-rose-300"
        : "bg-transparent text-slate-700 hover:bg-slate-100 active:bg-slate-200";

  return <button className={[base, styles, className].join(" ")} {...props} />;
}
