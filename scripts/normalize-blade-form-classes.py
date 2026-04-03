#!/usr/bin/env python3
"""Normalize legacy Tailwind form classes to v-toolbar-label, v-input, v-select."""
from __future__ import annotations

import re
from pathlib import Path

VIEWS = Path(__file__).resolve().parent.parent / "resources" / "views"

CORE = (
    "rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm "
    "focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 "
    "dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600"
)

OLD1 = f"mt-1 block w-full {CORE}"
OLD2 = f"mt-1 w-full {CORE}"
OLD_SEARCH = f"block w-full px-3 py-2 {CORE}"
OLD_CEP = f"cep mt-1 block w-full {CORE}"

LEGEND_OLD = 'class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"'
LEGEND_NEW = 'class="v-section-title mb-2"'

SELECT_PH = "__SEL_{}__"
TEXTAREA_PH = "__TA_{}__"


def process(content: str) -> str:
    s = content

    s = s.replace(
        'class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"',
        'class="v-toolbar-label mb-1"',
    )
    s = s.replace(
        'class="block text-sm font-medium text-gray-700 dark:text-gray-300"',
        'class="v-toolbar-label"',
    )
    s = s.replace(LEGEND_OLD, LEGEND_NEW)

    selects: list[str] = []

    def stash_select(m: re.Match[str]) -> str:
        block = m.group(0)
        block = block.replace(OLD1, "v-select mt-1").replace(OLD2, "v-select mt-1")
        i = len(selects)
        selects.append(block)
        return SELECT_PH.format(i)

    s = re.sub(r"<select\b[\s\S]*?</select>", stash_select, s, flags=re.IGNORECASE)

    textareas: list[str] = []

    def stash_ta(m: re.Match[str]) -> str:
        block = m.group(0)
        block = block.replace(OLD1, "v-input mt-1").replace(OLD2, "v-input mt-1")
        i = len(textareas)
        textareas.append(block)
        return TEXTAREA_PH.format(i)

    s = re.sub(r"<textarea\b[\s\S]*?</textarea>", stash_ta, s, flags=re.IGNORECASE)

    s = s.replace(OLD1, "v-input mt-1")
    s = s.replace(OLD2, "v-input mt-1")
    s = s.replace(OLD_SEARCH, "v-input")
    s = s.replace(OLD_CEP, "cep v-input mt-1")

    for i, block in enumerate(textareas):
        s = s.replace(TEXTAREA_PH.format(i), block)
    for i, block in enumerate(selects):
        s = s.replace(SELECT_PH.format(i), block)

    return s


def main() -> None:
    n = 0
    for path in sorted(VIEWS.rglob("*.blade.php")):
        raw = path.read_text(encoding="utf-8")
        new = process(raw)
        if new != raw:
            path.write_text(new, encoding="utf-8")
            n += 1
            print(path.relative_to(VIEWS.parent.parent))
    print(f"Updated {n} files.")


if __name__ == "__main__":
    main()
