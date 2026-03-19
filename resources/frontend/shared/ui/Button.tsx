import React from "react";

type Props = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: "primary" | "ghost" | "danger";
};

export default function Button({ className = "", variant = "primary", ...props }: Props) {
  const base = "ui-btn";

  const styles = variant === "primary" ? "ui-btn-primary" : variant === "danger" ? "ui-btn-danger" : "ui-btn-ghost";

  return <button className={[base, styles, className].join(" ")} {...props} />;
}
