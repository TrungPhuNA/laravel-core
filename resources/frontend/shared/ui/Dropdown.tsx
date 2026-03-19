import React from "react";
import { createPortal } from "react-dom";

type Props = {
    trigger: React.ReactNode;
    children: React.ReactNode | ((ctx: { close: () => void }) => React.ReactNode);
    align?: "left" | "right";
};

export default function Dropdown(props: Props) {
    const [open, setOpen] = React.useState(false);
    const ref = React.useRef<HTMLDivElement | null>(null);
    const btnRef = React.useRef<HTMLButtonElement | null>(null);
    const align = props.align ?? "right";
    const close = React.useCallback(() => setOpen(false), []);
    const [pos, setPos] = React.useState<{ top: number; left: number } | null>(null);

    React.useEffect(() => {
        if (!open) return;

        function onDocDown(e: MouseEvent) {
            const el = ref.current;
            if (!el) return;
            if (e.target instanceof Node && el.contains(e.target)) return;
            setOpen(false);
        }

        document.addEventListener("mousedown", onDocDown);
        return () => document.removeEventListener("mousedown", onDocDown);
    }, [open]);

    React.useEffect(() => {
        if (!open) return;
        const btn = btnRef.current;
        if (!btn) return;

        const r = btn.getBoundingClientRect();
        const top = Math.round(r.bottom + 8);
        const left = align === "right" ? Math.round(r.right) : Math.round(r.left);
        setPos({ top, left });
    }, [open, align]);

    React.useEffect(() => {
        if (!open) return;

        function onKeyDown(e: KeyboardEvent) {
            if (e.key === "Escape") close();
        }

        // Đóng menu khi scroll/resize để tránh lệch vị trí.
        function onWinScroll() {
            close();
        }

        window.addEventListener("keydown", onKeyDown);
        window.addEventListener("scroll", onWinScroll, true);
        window.addEventListener("resize", onWinScroll);
        return () => {
            window.removeEventListener("keydown", onKeyDown);
            window.removeEventListener("scroll", onWinScroll, true);
            window.removeEventListener("resize", onWinScroll);
        };
    }, [open, close]);

    return (
        <div className="relative inline-flex" ref={ref}>
            <button
                type="button"
                className="cursor-pointer"
                aria-haspopup="menu"
                aria-expanded={open}
                ref={btnRef}
                onClick={() => setOpen((v) => !v)}
            >
                {props.trigger}
            </button>

            {open && pos
                ? createPortal(
                      <div
                          className={[
                              "fixed min-w-[200px] rounded-xl border border-slate-200 bg-white shadow-xl overflow-hidden z-[9999]",
                              "backdrop-blur supports-[backdrop-filter]:bg-white/95",
                          ].join(" ")}
                          style={{
                              top: pos.top,
                              left: pos.left,
                              transform: align === "right" ? "translateX(-100%)" : undefined,
                          }}
                          role="menu"
                      >
                          {typeof props.children === "function" ? props.children({ close }) : props.children}
                      </div>,
                      document.body
                  )
                : null}
        </div>
    );
}
