import React from "react";

type Props = React.SelectHTMLAttributes<HTMLSelectElement>;

export default function Select({ className = "", ...props }: Props) {
  return (
    <select
      className={[
        "ui-select",
        className,
      ].join(" ")}
      {...props}
    />
  );
}
