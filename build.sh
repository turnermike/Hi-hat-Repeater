#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd -P)"
PLUGIN_SLUG="hi-hat-repeater"
DIST_DIR="${SCRIPT_DIR}/dist"
ZIP_FILE="${DIST_DIR}/${PLUGIN_SLUG}.zip"

for command in rsync zip; do
	if ! command -v "$command" >/dev/null 2>&1; then
		printf 'Error: required command "%s" was not found.\n' "$command" >&2
		exit 1
	fi
done

for file in hi-hat-repeater.php readme.txt LICENSE .distignore; do
	if [[ ! -f "${SCRIPT_DIR}/${file}" ]]; then
		printf 'Error: required distribution file "%s" is missing.\n' "$file" >&2
		exit 1
	fi
done

BUILD_DIR="$(mktemp -d "${TMPDIR:-/tmp}/${PLUGIN_SLUG}.XXXXXX")"
trap 'rm -rf "$BUILD_DIR"' EXIT

if [[ -L "$DIST_DIR" ]]; then
	printf 'Error: refusing to clear symlinked distribution directory: %s\n' "$DIST_DIR" >&2
	exit 1
fi

mkdir -p -- "$DIST_DIR"
DIST_REAL="$(cd "$DIST_DIR" && pwd -P)"
EXPECTED_DIST="${SCRIPT_DIR}/dist"
if [[ "$DIST_REAL" != "$EXPECTED_DIST" ]]; then
	printf 'Error: distribution path resolved outside the expected directory: %s\n' "$DIST_REAL" >&2
	exit 1
fi

shopt -s dotglob nullglob
for entry in "${DIST_REAL}"/*; do
	if [[ -e "$entry" || -L "$entry" ]]; then
		rm -rf -- "$entry"
	fi
done

rsync -a --exclude-from="${SCRIPT_DIR}/.distignore" \
	"${SCRIPT_DIR}/" "${BUILD_DIR}/${PLUGIN_SLUG}/"

(
	cd "$BUILD_DIR"
	zip -qr "$ZIP_FILE" "$PLUGIN_SLUG"
)

printf 'Built distribution: %s\n' "$ZIP_FILE"
