import React from "react";

export default function Card(props: { title?: string; children: React.ReactNode; actions?: React.ReactNode }) {
    return (
        <div className="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            {props.title ? (
                <div className="flex items-center justify-between border-b border-slate-100 px-4 py-3 bg-gradient-to-b from-white to-slate-50">
                    <div className="text-sm font-semibold tracking-tight">{props.title}</div>
                    {props.actions ? <div className="flex items-center gap-2">{props.actions}</div> : null}
                </div>
            ) : null}
            <div className="p-4">{props.children}</div>
        </div>
    );
}
