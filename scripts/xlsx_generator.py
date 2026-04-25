"""
xlsx_generator.py
Generic Excel document generator.

Approach: pure zip/XML manipulation — never uses openpyxl to save.

Why: openpyxl.save() converts shared strings to inline strings, which
destroys table structure, removes drawings, and strips text boxes.
Instead we work directly on the xlsx zip contents:
  - sharedStrings.xml  → render {{ }} Jinja2 placeholders (cell text)
  - xl/drawings/*.xml  → render {{ }} Jinja2 placeholders (text boxes)
  - Everything else    → copied verbatim (sheet1.xml, styles, images,
                         relationships, drawings, media all preserved)

For-loop row expansion ({% for item in list %}):
  Uses openpyxl to expand rows in memory, then saves to a temp buffer,
  then the CELL XML from that buffer replaces sheet*.xml — while
  sharedStrings and everything else still come from the template.

Supports:
  - {{ variable }} in cells, text boxes, and drawing shapes
  - {% for item in list %} ... {% endfor %} row expansion in cells
  - All images, drawings, text boxes, merged cells, styles preserved
"""

import argparse
import io
import json
import re
import sys
import zipfile
from copy import copy
from pathlib import Path

from jinja2 import Environment, Undefined
from openpyxl import load_workbook


# ----------------------------------------------------------------
# CLI
# ----------------------------------------------------------------
def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Generic XLSX generator")
    parser.add_argument("--template",        required=True)
    parser.add_argument("--output-filename", required=True)
    parser.add_argument("--context",         required=True)
    return parser.parse_args()


# ----------------------------------------------------------------
# Jinja2 environment
# ----------------------------------------------------------------
def make_env() -> Environment:
    return Environment(undefined=Undefined)


def try_render(env: Environment, text: str, context: dict) -> str:
    """Render a Jinja2 string, returning the original on any error."""
    if "{{" not in text and "{%" not in text:
        return text
    try:
        return env.from_string(text).render(context)
    except Exception:
        return text


# ----------------------------------------------------------------
# XML text rendering
# Renders {{ }} inside <t>...</t> tags in sharedStrings and drawings
# ----------------------------------------------------------------
def render_xml_file(xml_bytes: bytes, context: dict, env: Environment) -> bytes:
    """
    Find all <t ...>content</t> elements and render any Jinja2
    expressions inside them. Preserves all XML attributes.
    """
    try:
        xml = xml_bytes.decode("utf-8")
    except UnicodeDecodeError:
        return xml_bytes  # binary file, skip

    def replace_t(m):
        attrs   = m.group(1)  # e.g. ' xml:space="preserve"'
        content = m.group(2)
        rendered = try_render(env, content, context)
        return f"<t{attrs}>{rendered}</t>"

    xml = re.sub(r"<t([^>]*)>(.*?)</t>", replace_t, xml, flags=re.DOTALL)
    return xml.encode("utf-8")


# ----------------------------------------------------------------
# Loop expansion via openpyxl (only used when template has {% for %})
# ----------------------------------------------------------------
def has_loops(template_path: str) -> bool:
    """Check if the template contains any {% for %} markers."""
    with zipfile.ZipFile(template_path, "r") as z:
        for name in z.namelist():
            if name.startswith("xl/worksheets/") and name.endswith(".xml"):
                try:
                    content = z.read(name).decode("utf-8")
                    if "{%" in content:
                        return True
                except Exception:
                    pass
        # Also check sharedStrings
        if "xl/sharedStrings.xml" in z.namelist():
            try:
                ss = z.read("xl/sharedStrings.xml").decode("utf-8")
                if "{%" in ss:
                    return True
            except Exception:
                pass
    return False


def render_cell_value(env: Environment, value, context: dict):
    """Render a cell value, converting pure-numeric results to numbers."""
    if not isinstance(value, str):
        return value
    if "{{" not in value and "{%" not in value:
        return value
    try:
        rendered = env.from_string(value).render(context)
        stripped = rendered.strip()
        if stripped and stripped.lstrip("-").replace(".", "", 1).isdigit():
            return float(stripped) if "." in stripped else int(stripped)
        return rendered
    except Exception:
        return value


def find_loop_blocks(ws) -> list:
    for_re  = re.compile(r"\{%-?\s*for\s+(\w+)\s+in\s+(\w+)\s*-?%\}")
    end_re  = re.compile(r"\{%-?\s*endfor\s*-?%\}")
    blocks, pending = [], []

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

    blocks.sort(key=lambda b: b["for_row"], reverse=True)
    return blocks


def expand_loop(ws, block: dict, context: dict, env: Environment):
    for_row  = block["for_row"]
    end_row  = block["end_row"]
    var      = block["var"]
    list_key = block["list_key"]
    items    = context.get(list_key, [])
    if not isinstance(items, list):
        items = []

    body_rows = list(range(for_row, end_row + 1))
    num_body  = len(body_rows)
    max_col   = ws.max_column

    if not items:
        for _ in range(num_body):
            ws.delete_rows(for_row)
        return

    snapshot = []
    for r in body_rows:
        row_data = []
        for c in range(1, max_col + 1):
            cell = ws.cell(row=r, column=c)
            row_data.append({
                "value":         cell.value,
                "font":          copy(cell.font)      if cell.has_style else None,
                "fill":          copy(cell.fill)      if cell.has_style else None,
                "border":        copy(cell.border)    if cell.has_style else None,
                "alignment":     copy(cell.alignment) if cell.has_style else None,
                "number_format": cell.number_format,
            })
        snapshot.append(row_data)

    extra = (len(items) - 1) * num_body
    if extra > 0:
        ws.insert_rows(end_row + 1, amount=extra)

    for item_idx, item in enumerate(items):
        item_ctx = dict(context)
        item_ctx[var] = item
        for body_idx, snap_row in enumerate(snapshot):
            tgt_row = for_row + item_idx * num_body + body_idx
            for col_idx, cd in enumerate(snap_row):
                tgt = ws.cell(row=tgt_row, column=col_idx + 1)
                tgt.value = render_cell_value(env, cd["value"], item_ctx)
                if cd["font"]:
                    tgt.font          = cd["font"]
                    tgt.fill          = cd["fill"]
                    tgt.border        = cd["border"]
                    tgt.alignment     = cd["alignment"]
                    tgt.number_format = cd["number_format"]


def expand_loops_openpyxl(template_path: str, context: dict, env: Environment) -> dict:
    """
    Use openpyxl to expand for-loops in cells.
    Returns a dict of { "xl/worksheets/sheetN.xml": bytes } with
    the expanded worksheet XML only — we take nothing else from openpyxl.
    """
    wb = load_workbook(template_path)
    for sheet_name in wb.sheetnames:
        ws = wb[sheet_name]
        for block in find_loop_blocks(ws):
            expand_loop(ws, block, context, env)
        # Also render any remaining simple {{ }} in cells
        for row in ws.iter_rows():
            for cell in row:
                if not isinstance(cell.value, str):
                    continue
                val = cell.value.strip()
                if re.match(r"^\{%-?\s*(for|endfor)\b", val):
                    cell.value = None
                    continue
                cell.value = render_cell_value(env, cell.value, context)

    buf = io.BytesIO()
    wb.save(buf)

    result = {}
    with zipfile.ZipFile(io.BytesIO(buf.getvalue()), "r") as z:
        for name in z.namelist():
            if re.match(r"xl/worksheets/sheet\d+\.xml$", name):
                result[name] = z.read(name)
    return result


# ----------------------------------------------------------------
# Main assembly
# ----------------------------------------------------------------
def build_output(template_path: str, context: dict, env: Environment) -> bytes:
    """
    Assemble the final xlsx entirely from the template zip,
    rendering Jinja2 in sharedStrings and drawing XML files.
    If loops are present, worksheet cell XML comes from openpyxl expansion.
    """
    drawing_re = re.compile(r"xl/drawings/.*\.xml$", re.I)

    # Only run openpyxl if the template has for-loops
    expanded_sheets = {}
    if has_loops(template_path):
        expanded_sheets = expand_loops_openpyxl(template_path, context, env)

    output_buf = io.BytesIO()

    with zipfile.ZipFile(template_path, "r") as tz, \
         zipfile.ZipFile(output_buf, "w", zipfile.ZIP_DEFLATED) as out:

        for name in tz.namelist():
            # Worksheet with expanded loops — use openpyxl's version
            if name in expanded_sheets:
                out.writestr(name, expanded_sheets[name])

            # Shared strings — render {{ }} Jinja2 in cell text
            elif name == "xl/sharedStrings.xml":
                data = render_xml_file(tz.read(name), context, env)
                out.writestr(name, data)

            # Drawing XML — render {{ }} Jinja2 in text boxes
            elif drawing_re.match(name):
                data = render_xml_file(tz.read(name), context, env)
                out.writestr(name, data)

            # Everything else — verbatim copy
            # (sheet1.xml when no loops, styles, images, relationships, etc.)
            else:
                out.writestr(name, tz.read(name))

    return output_buf.getvalue()


# ----------------------------------------------------------------
# Entry point
# ----------------------------------------------------------------
def main() -> None:
    args = parse_args()

    try:
        context = json.loads(args.context)
    except json.JSONDecodeError as e:
        print(f"ERROR: Invalid JSON context — {e}", file=sys.stderr)
        sys.exit(1)

    base_dir      = Path(__file__).resolve().parent.parent
    template_path = str(base_dir / "document_templates" / args.template)
    output_dir    = base_dir / "public" / "cached_result"
    output_path   = output_dir / args.output_filename

    if not Path(template_path).exists():
        print(f"ERROR: Template not found at {template_path}", file=sys.stderr)
        sys.exit(1)

    output_dir.mkdir(parents=True, exist_ok=True)

    try:
        env         = make_env()
        final_bytes = build_output(template_path, context, env)

        with open(str(output_path), "wb") as f:
            f.write(final_bytes)

        print(f"OK: Saved to {output_path}")

    except Exception as e:
        print(f"ERROR: Rendering failed — {e}", file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()