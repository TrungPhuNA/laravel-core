import React from "react";

type InlineToken =
  | { t: "text"; v: string }
  | { t: "strong"; v: InlineToken[] }
  | { t: "em"; v: InlineToken[] }
  | { t: "code"; v: string }
  | { t: "link"; text: InlineToken[]; href: string };

function splitOnce(s: string, sep: string): [string, string] | null {
  const idx = s.indexOf(sep);
  if (idx < 0) return null;
  return [s.slice(0, idx), s.slice(idx + sep.length)];
}

function isSafeHref(href: string): boolean {
  const h = href.trim();
  if (h === "") return false;
  // allow https/http/mailto only
  return /^https?:\/\//i.test(h) || /^mailto:/i.test(h);
}

function parseInline(text: string): InlineToken[] {
  const out: InlineToken[] = [];

  function pushText(v: string) {
    if (!v) return;
    const last = out[out.length - 1];
    if (last && last.t === "text") last.v += v;
    else out.push({ t: "text", v });
  }

  let s = text ?? "";

  while (s.length) {
    // code `...`
    const codeOpen = s.indexOf("`");
    const strongOpen = s.indexOf("**");
    const emOpen = s.indexOf("*");
    const linkOpen = s.indexOf("[");

    const candidates = [
      { kind: "code" as const, idx: codeOpen },
      { kind: "strong" as const, idx: strongOpen },
      { kind: "em" as const, idx: emOpen },
      { kind: "link" as const, idx: linkOpen },
    ].filter((c) => c.idx >= 0);

    if (candidates.length === 0) {
      pushText(s);
      break;
    }

    candidates.sort((a, b) => a.idx - b.idx);
    const { kind, idx } = candidates[0]!;

    if (idx > 0) {
      pushText(s.slice(0, idx));
      s = s.slice(idx);
    }

    if (kind === "code") {
      const rest = s.slice(1);
      const close = rest.indexOf("`");
      if (close < 0) {
        pushText(s);
        break;
      }
      const code = rest.slice(0, close);
      out.push({ t: "code", v: code });
      s = rest.slice(close + 1);
      continue;
    }

    if (kind === "strong") {
      const rest = s.slice(2);
      const close = rest.indexOf("**");
      if (close < 0) {
        pushText(s);
        break;
      }
      const inner = rest.slice(0, close);
      out.push({ t: "strong", v: parseInline(inner) });
      s = rest.slice(close + 2);
      continue;
    }

    if (kind === "em") {
      // avoid treating "**" as "*"
      if (s.startsWith("**")) {
        pushText("*");
        s = s.slice(1);
        continue;
      }

      const rest = s.slice(1);
      const close = rest.indexOf("*");
      if (close < 0) {
        pushText(s);
        break;
      }
      const inner = rest.slice(0, close);
      out.push({ t: "em", v: parseInline(inner) });
      s = rest.slice(close + 1);
      continue;
    }

    if (kind === "link") {
      // [text](href)
      const afterOpen = s.slice(1);
      const closingBracket = afterOpen.indexOf("]");
      if (closingBracket < 0) {
        pushText(s);
        break;
      }

      const linkText = afterOpen.slice(0, closingBracket);
      const afterBracket = afterOpen.slice(closingBracket + 1);
      if (!afterBracket.startsWith("(")) {
        // not a link, treat as text
        pushText("[");
        s = afterOpen;
        continue;
      }

      const afterParen = afterBracket.slice(1);
      const closingParen = afterParen.indexOf(")");
      if (closingParen < 0) {
        pushText(s);
        break;
      }

      const href = afterParen.slice(0, closingParen).trim();
      const safeHref = isSafeHref(href) ? href : "";

      out.push({
        t: "link",
        text: parseInline(linkText),
        href: safeHref,
      });

      s = afterParen.slice(closingParen + 1);
      continue;
    }
  }

  return out;
}

function renderInline(tokens: InlineToken[], keyPrefix = "i"): React.ReactNode[] {
  return tokens.map((tok, idx) => {
    const key = `${keyPrefix}-${idx}`;
    if (tok.t === "text") return <React.Fragment key={key}>{tok.v}</React.Fragment>;
    if (tok.t === "code") return <code key={key} className="px-1 py-0.5 rounded bg-slate-100 border border-slate-200 font-mono text-[12px]">{tok.v}</code>;
    if (tok.t === "strong") return <strong key={key} className="font-bold text-slate-900">{renderInline(tok.v, key)}</strong>;
    if (tok.t === "em") return <em key={key} className="italic">{renderInline(tok.v, key)}</em>;
    if (tok.t === "link") {
      const content = renderInline(tok.text, key);
      if (!tok.href) return <span key={key} className="underline decoration-slate-300">{content}</span>;
      return (
        <a
          key={key}
          href={tok.href}
          target="_blank"
          rel="noopener noreferrer"
          className="text-sky-700 hover:text-sky-900 underline underline-offset-2"
        >
          {content}
        </a>
      );
    }
    return <React.Fragment key={key} />;
  });
}

type Block =
  | { t: "p"; v: string }
  | { t: "h"; level: 1 | 2 | 3; v: string }
  | { t: "ul"; items: string[] }
  | { t: "ol"; items: string[] }
  | { t: "quote"; lines: string[] }
  | { t: "hr" }
  | { t: "code"; lang: string | null; code: string };

function parseBlocks(md: string): Block[] {
  const lines = String(md ?? "").replace(/\r\n/g, "\n").split("\n");
  const blocks: Block[] = [];

  let i = 0;
  while (i < lines.length) {
    let line = lines[i] ?? "";

    // code fence
    if (line.startsWith("```")) {
      const lang = line.slice(3).trim() || null;
      i++;
      const buf: string[] = [];
      while (i < lines.length && !(lines[i] ?? "").startsWith("```")) {
        buf.push(lines[i] ?? "");
        i++;
      }
      // skip closing fence if any
      if (i < lines.length && (lines[i] ?? "").startsWith("```")) i++;
      blocks.push({ t: "code", lang, code: buf.join("\n") });
      continue;
    }

    // empty -> skip (acts as paragraph separator)
    if (line.trim() === "") {
      i++;
      continue;
    }

    // hr
    if (line.trim() === "---" || line.trim() === "***") {
      blocks.push({ t: "hr" });
      i++;
      continue;
    }

    // headings
    const heading = splitOnce(line, " ");
    if (heading) {
      const [prefix, rest] = heading;
      if (prefix === "#") {
        blocks.push({ t: "h", level: 1, v: rest.trim() });
        i++;
        continue;
      }
      if (prefix === "##") {
        blocks.push({ t: "h", level: 2, v: rest.trim() });
        i++;
        continue;
      }
      if (prefix === "###") {
        blocks.push({ t: "h", level: 3, v: rest.trim() });
        i++;
        continue;
      }
    }

    // quote (consecutive)
    if (line.trim().startsWith(">")) {
      const qs: string[] = [];
      while (i < lines.length) {
        const l = lines[i] ?? "";
        if (!l.trim().startsWith(">")) break;
        qs.push(l.replace(/^\s*>\s?/, ""));
        i++;
      }
      blocks.push({ t: "quote", lines: qs });
      continue;
    }

    // unordered list
    if (/^\s*[-*]\s+/.test(line)) {
      const items: string[] = [];
      while (i < lines.length) {
        const l = lines[i] ?? "";
        if (!/^\s*[-*]\s+/.test(l)) break;
        items.push(l.replace(/^\s*[-*]\s+/, ""));
        i++;
      }
      blocks.push({ t: "ul", items });
      continue;
    }

    // ordered list
    if (/^\s*\d+\.\s+/.test(line)) {
      const items: string[] = [];
      while (i < lines.length) {
        const l = lines[i] ?? "";
        if (!/^\s*\d+\.\s+/.test(l)) break;
        items.push(l.replace(/^\s*\d+\.\s+/, ""));
        i++;
      }
      blocks.push({ t: "ol", items });
      continue;
    }

    // paragraph (collect until blank)
    const buf: string[] = [];
    while (i < lines.length) {
      const l = lines[i] ?? "";
      if (l.trim() === "") break;
      if (l.startsWith("```")) break;
      buf.push(l);
      i++;
    }
    blocks.push({ t: "p", v: buf.join("\n") });
  }

  return blocks;
}

export function MarkdownView(props: { markdown: string; className?: string }) {
  const blocks = useMemoBlocks(props.markdown);

  return (
    <div className={["space-y-3 text-sm leading-6 text-slate-800", props.className].filter(Boolean).join(" ")}>
      {blocks.map((b, idx) => {
        if (b.t === "hr") return <hr key={idx} className="border-slate-200" />;

        if (b.t === "h") {
          const inner = renderInline(parseInline(b.v), `h-${idx}`);
          const cls =
            b.level === 1
              ? "text-xl font-bold tracking-tight text-slate-900"
              : b.level === 2
                ? "text-lg font-bold tracking-tight text-slate-900"
                : "text-base font-bold text-slate-900";
          const Tag = (b.level === 1 ? "h1" : b.level === 2 ? "h2" : "h3") as any;
          return (
            <Tag key={idx} className={cls}>
              {inner}
            </Tag>
          );
        }

        if (b.t === "code") {
          return (
            <pre key={idx} className="rounded-xl border border-slate-200 bg-slate-50 p-4 overflow-auto text-[12px] leading-5">
              <code>{b.code}</code>
            </pre>
          );
        }

        if (b.t === "quote") {
          return (
            <blockquote key={idx} className="rounded-xl border border-slate-200 bg-white p-4 border-l-4 border-l-slate-900">
              <div className="space-y-2">
                {b.lines.map((l, j) => (
                  <p key={j} className="text-slate-700">
                    {renderInline(parseInline(l), `q-${idx}-${j}`)}
                  </p>
                ))}
              </div>
            </blockquote>
          );
        }

        if (b.t === "ul") {
          return (
            <ul key={idx} className="list-disc pl-6 space-y-1">
              {b.items.map((it, j) => (
                <li key={j}>{renderInline(parseInline(it), `ul-${idx}-${j}`)}</li>
              ))}
            </ul>
          );
        }

        if (b.t === "ol") {
          return (
            <ol key={idx} className="list-decimal pl-6 space-y-1">
              {b.items.map((it, j) => (
                <li key={j}>{renderInline(parseInline(it), `ol-${idx}-${j}`)}</li>
              ))}
            </ol>
          );
        }

        // paragraph
        if (b.t === "p") {
          // preserve hard line breaks
          const parts = b.v.split("\n");
          return (
            <p key={idx} className="whitespace-pre-wrap break-words">
              {parts.map((p, j) => (
                <React.Fragment key={j}>
                  {renderInline(parseInline(p), `p-${idx}-${j}`)}
                  {j < parts.length - 1 ? <br /> : null}
                </React.Fragment>
              ))}
            </p>
          );
        }

        return null;
      })}
    </div>
  );
}

function useMemoBlocks(markdown: string): Block[] {
  return React.useMemo(() => parseBlocks(markdown), [markdown]);
}

