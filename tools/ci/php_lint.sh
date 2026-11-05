#!/usr/bin/env bash
# Syntax-checks every PHP file the project owns (not system/, third_party/ or backend/ assets).
# Files listed in tools/ci/php-lint-baseline.txt are known legacy failures and are tolerated.
#   PHP=php bash tools/ci/php_lint.sh
set -euo pipefail
PHP=${PHP:-php}
cd "$(dirname "$0")/../.."
baseline=tools/ci/php-lint-baseline.txt
failed=$(mktemp)

git ls-files -z '*.php' \
  | grep -zv -e '^system/' -e '^application/third_party/' -e '^backend/' -e '/vendor/' \
  | xargs -0 -n 50 -P 4 sh -c 'for f do "$0" -l "$f" >/dev/null 2>&1 || printf "%s\n" "$f"; done' "$PHP" \
  | sort > "$failed" || true

known=$(grep -v '^#' "$baseline" | grep -v '^$' | sort)
new=$(comm -23 "$failed" <(printf '%s\n' "$known") || true)
fixed=$(comm -13 "$failed" <(printf '%s\n' "$known") || true)

if [ -n "$fixed" ]; then
  echo "These baseline files compile now; remove them from $baseline:"
  printf '  %s\n' $fixed
fi
if [ -n "$new" ]; then
  echo "PHP syntax errors:"
  while IFS= read -r f; do "$PHP" -l "$f" 2>&1 | grep -v '^No syntax' | sed 's/^/  /'; done <<< "$new"
  exit 1
fi
echo "PHP lint: OK ($(wc -l < "$failed") known legacy failures tolerated)"
