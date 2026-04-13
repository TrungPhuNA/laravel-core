import React from "react";

export default function Card(props: {
  title?: string;
  children: React.ReactNode;
  actions?: React.ReactNode;
  className?: string;
  bodyClassName?: string;
  noBody?: boolean;
}) {
  return (
    <div className={["ui-card", props.className].filter(Boolean).join(" ")}>
      {props.title ? (
        <div className="ui-card-header">
          <div className="ui-card-title">{props.title}</div>
          {props.actions ? <div className="flex items-center gap-2">{props.actions}</div> : null}
        </div>
      ) : null}
      {props.noBody ? (
        props.children
      ) : (
        <div className={["ui-card-body", props.bodyClassName].filter(Boolean).join(" ")}>
          {props.children}
        </div>
      )}
    </div>
  );
}

