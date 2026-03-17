#!/usr/bin/env bash
set -euo pipefail

# Regenerate docs/README.md as an index.
# Keep it simple and deterministic (sorted).

root_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$root_dir"

out="docs/README.md"

{
  echo "# Docs Index"
  echo
  for section in dev api architecture requests testing; do
    title="$(echo "$section" | awk '{print toupper(substr($0,1,1)) substr($0,2)}')"
    echo "## ${title}"
    echo
    if [ -d "docs/${section}" ]; then
      # List only markdown files, stable ordering.
      while IFS= read -r f; do
        echo "- \`${f}\`"
      done < <(find "docs/${section}" -maxdepth 1 -type f -name "*.md" | sort)
    else
      echo "- (missing: docs/${section})"
    fi
    echo
  done

  echo "## Tooling"
  echo
  if [ -f "scripts/index-docs.sh" ]; then
    echo "- \`scripts/index-docs.sh\`"
  fi
  echo
} > "$out"

echo "Wrote ${out}"

