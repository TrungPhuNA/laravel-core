import React, { useEffect } from "react";

export default function Modal(props: {
  open: boolean;
  title: string;
  children: React.ReactNode;
  onClose: () => void;
  footer?: React.ReactNode;
  className?: string;
}) {
  useEffect(() => {
    if (!props.open) return;

    function onKey(e: KeyboardEvent) {
      if (e.key === "Escape") props.onClose();
    }

    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [props.open, props.onClose]);

  if (!props.open) return null;

  return (
    <div className="fixed inset-0 z-50">
      <div className="absolute inset-0 bg-black/30" onClick={props.onClose} />
      <div className="absolute inset-0 flex items-center justify-center p-4">
        <div className={`w-full flex flex-col rounded-xl border border-slate-200 bg-white shadow-xl ${props.className || "max-w-3xl"}`}>
          <div className="flex items-center justify-between border-b border-slate-100 px-4 py-3 shrink-0">
            <div className="text-sm font-semibold">{props.title}</div>
            <button className="text-sm text-slate-600 hover:text-slate-900" onClick={props.onClose}>
              Đóng
            </button>
          </div>
          <div className="max-h-[75vh] overflow-auto p-4">{props.children}</div>
          {props.footer ? <div className="border-t border-slate-100 px-4 py-3">{props.footer}</div> : null}
        </div>
      </div>
    </div>
  );
}

