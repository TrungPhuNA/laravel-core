import React from "react";

type Props = React.InputHTMLAttributes<HTMLInputElement>;

export default function Input({ className = "", ...props }: Props) {
    return (
        <input
            className={[
                "h-10 rounded-md border border-slate-200 bg-white px-3 text-sm outline-none",
                "focus:border-slate-400 focus:ring-2 focus:ring-slate-200",
                className,
            ].join(" ")}
            {...props}
        />
    );
}

