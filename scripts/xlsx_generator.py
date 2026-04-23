"""
xlsx_generator.py
Generic Excel document generator using openpyxl + Jinja2.

Supports:
  - Simple placeholders:     {{ variable_name }}
  - Jinja2 expressions:      {{ value | upper }}, {{ items | length }}
  - For loops across rows:   {% for item in items %} ... {% endfor %}
    Place {% for item in items %} in a cell on one row and
    {% endfor %} in a cell on a later row. All rows between
    (inclusive of the for/endfor rows) are treated as the loop body
    and are repeated once per item in the list.

Usage:
    python3 xlsx_generator.py \
        --template "template.xlsx" \
        --output-filename "output_<uuid>.xlsx" \
        --context '{"employee_name": "John", "items": [{"name": "A"}, {"name": "B"}]}'
"""

import argparse
import json
import re
import sys
from copy import copy
from pathlib import Path

from jinja2 import Environment, Undefined
from openpyxl import load_workbook
from openpyxl.utils import get_column_letter


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Generic XLSX generator")
    parser.add_argument("--template",        required=True)
    parser.add_argument("--output-filename", required=True)
    parser.add_argument("--context",         required=True)
    return parser.parse_args()


def make_env() -> Environment:
    return Environment(undefined=Undefined)


def render_value(env: Environment, value, context: dict):
    """Render a cell value through Jinja2. Returns original if not a string."""
    if not isinstance(value, str):
        return value
    if "{{" not in value and "{%" not in value:
        return value
    try:
        rendered = env.from_string(value).render(context)
        # Try to convert purely numeric results back to numbers
        stripped = rendered.strip()
        if stripped.lstrip("-").replace(".", "", 1).isdigit():
            return float(stripped) if "." in stripped else int(stripped)
        return rendered
    except Exception:
        return value


def copy_cell_style(src_cell, dst_cell):
    """Copy font, fill, border, alignment, number_format from src to dst."""
    if src_cell.has_style:
        dst_cell.font         = copy(src_cell.font)
        dst_cell.fill         = copy(src_cell.fill)
        dst_cell.border       = copy(src_cell.border)
        dst_cell.alignment    = copy(src_cell.alignment)
        dst_cell.number_format = src_cell.number_format


def find_loop_blocks(ws):
    """
    Scan the worksheet for {% for ... in ... %} / {% endfor %} pairs.
    Returns a list of dicts:
      { 'for_row': int, 'end_row': int, 'var': str, 'list_key': str }
    Rows are 1-indexed.
    """
    for_re  = re.compile(r"\{%-?\s*for\s+(\w+)\s+in\s+(\w+)\s*-?%\}")
    end_re  = re.compile(r"\{%-?\s*endfor\s*-?%\}")
    blocks  = []
    pending = []  # stack of (row, var, list_key)

    for row in ws.iter_rows():
        for cell in row:
            if not isinstance(cell.value, str):
                continue
            fm = for_re.search(cell.value)
            if fm:
                pending.append((cell.row, fm.group(1), fm.group(2)))
            em = end_re.search(cell.value)
            if em and pending:
                for_row, var, list_key = pending.pop()
                blocks.append({
                    "for_row":  for_row,
                    "end_row":  cell.row,
                    "var":      var,
                    "list_key": list_key,
                })

    # Sort by for_row descending so we expand from bottom to top
    # (avoids row index shifting when inserting rows)
    blocks.sort(key=lambda b: b["for_row"], reverse=True)
    return blocks


def expand_loop(ws, block: dict, context: dict, env: Environment):
    """
    Expand a single {% for %} / {% endfor %} block in the worksheet.
    Replaces the template rows with one set of rows per list item.
    """
    for_row  = block["for_row"]
    end_row  = block["end_row"]
    var      = block["var"]
    list_key = block["list_key"]

    items = context.get(list_key, [])
    if not isinstance(items, list):
        items = []

    body_rows = list(range(for_row, end_row + 1))  # inclusive
    num_body  = len(body_rows)
    num_items = len(items)
    max_col   = ws.max_column

    if num_items == 0:
        # Delete the template rows entirely
        for _ in range(num_body):
            ws.delete_rows(for_row)
        return

    # Snapshot the template row data and styles before modifying
    template_snapshot = []
    for r in body_rows:
        row_data = []
        for c in range(1, max_col + 1):
            cell = ws.cell(row=r, column=c)
            row_data.append({
                "value":         cell.value,
                "font":          copy(cell.font)          if cell.has_style else None,
                "fill":          copy(cell.fill)          if cell.has_style else None,
                "border":        copy(cell.border)        if cell.has_style else None,
                "alignment":     copy(cell.alignment)     if cell.has_style else None,
                "number_format": cell.number_format,
            })
        template_snapshot.append(row_data)

    # Insert (num_items - 1) * num_body new blank rows after the template block
    # (we'll overwrite the original rows for the first item)
    extra_rows = (num_items - 1) * num_body
    if extra_rows > 0:
        ws.insert_rows(end_row + 1, amount=extra_rows)

    # Write expanded rows
    for item_idx, item in enumerate(items):
        # Build per-item context: global context + loop variable
        item_context = dict(context)
        item_context[var] = item

        for body_idx, snapshot_row in enumerate(template_snapshot):
            target_row = for_row + item_idx * num_body + body_idx
            for col_idx, cell_data in enumerate(snapshot_row):
                target_cell = ws.cell(row=target_row, column=col_idx + 1)
                target_cell.value = render_value(env, cell_data["value"], item_context)
                if cell_data["font"]:
                    target_cell.font          = cell_data["font"]
                    target_cell.fill          = cell_data["fill"]
                    target_cell.border        = cell_data["border"]
                    target_cell.alignment     = cell_data["alignment"]
                    target_cell.number_format = cell_data["number_format"]


def fill_simple_cells(ws, context: dict, env: Environment):
    """
    Replace {{ }} and {% %} expressions in cells that are NOT part of
    any for-loop block (those are handled by expand_loop).
    """
    for row in ws.iter_rows():
        for cell in row:
            if not isinstance(cell.value, str):
                continue
            # Skip cells that are pure for/endfor markers
            # (they were already consumed by expand_loop)
            val = cell.value.strip()
            if re.match(r"^\{%-?\s*(for|endfor)\b", val):
                cell.value = None
                continue
            cell.value = render_value(env, cell.value, context)


def process_worksheet(ws, context: dict):
    env = make_env()

    # First pass: expand all loop blocks (bottom-to-top to preserve row indices)
    blocks = find_loop_blocks(ws)
    for block in blocks:
        expand_loop(ws, block, context, env)

    # Second pass: render remaining simple expressions
    fill_simple_cells(ws, context, env)


def main() -> None:
    args = parse_args()

    try:
        context = json.loads(args.context)
    except json.JSONDecodeError as e:
        print(f"ERROR: Invalid JSON context — {e}", file=sys.stderr)
        sys.exit(1)

    base_dir      = Path(__file__).resolve().parent.parent
    template_path = base_dir / "document_templates" / args.template
    output_dir    = base_dir / "public" / "cached_result"
    output_path   = output_dir / args.output_filename

    if not template_path.exists():
        print(f"ERROR: Template not found at {template_path}", file=sys.stderr)
        sys.exit(1)

    output_dir.mkdir(parents=True, exist_ok=True)

    try:
        wb = load_workbook(str(template_path))
        for sheet_name in wb.sheetnames:
            process_worksheet(wb[sheet_name], context)
        wb.save(str(output_path))
        print(f"OK: Saved to {output_path}")
    except Exception as e:
        print(f"ERROR: Rendering failed — {e}", file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()