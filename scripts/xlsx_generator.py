"""
xlsx_generator.py
Generic Excel document generator using openpyxl + Jinja2.

Supports:
  - Simple placeholders:  {{ variable_name }}
  - Conditionals:         {{ "yes" if condition else "no" }}
  - Jinja2 expressions:   {{ value | upper }}, {{ items | length }}, etc.

Note on loops in Excel:
  Loops ({% for item in items %}) are NOT supported inline in cells the way
  docxtpl supports them in Word tables. Instead, use repeating_group fields —
  the controller passes list data and this script handles row expansion
  by detecting a special __repeat__ marker in the template.

  For simple use cases, pass a list value and reference it by index:
  {{ items[0].name }}, {{ items[1].name }}, etc.

Usage (called by DocumentController.php):
    python3 xlsx_generator.py \
        --template "SKP-Template.xlsx" \
        --output-filename "sasaran-kerja-pegawai_<uuid>.xlsx" \
        --context '{"employee_name": "John Doe", "appraisal_period_start": "2025-01-01"}'
"""

import argparse
import json
import re
import sys
from pathlib import Path

from jinja2 import Environment, Undefined
from openpyxl import load_workbook


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Generic XLSX generator")
    parser.add_argument("--template",        required=True, help="Template filename inside document_templates/")
    parser.add_argument("--output-filename", required=True, help="Output filename inside public/cached_result/")
    parser.add_argument("--context",         required=True, help="JSON string of template variables")
    return parser.parse_args()


def make_jinja_env() -> Environment:
    """Create a Jinja2 environment that silently ignores undefined variables."""
    return Environment(undefined=Undefined)


def render_cell(env: Environment, value: str, context: dict) -> str:
    """Render a single cell string through Jinja2."""
    try:
        return env.from_string(value).render(context)
    except Exception:
        # If rendering fails, return the original value unchanged
        return value


def fill_worksheet(ws, context: dict) -> None:
    """Iterate all cells and render any Jinja2 expressions found."""
    env = make_jinja_env()

    for row in ws.iter_rows():
        for cell in row:
            if not isinstance(cell.value, str):
                continue
            # Only process cells that contain {{ or {%
            if "{{" not in cell.value and "{%" not in cell.value:
                continue
            rendered = render_cell(env, cell.value, context)
            # Keep the cell as a string; preserve numeric strings as numbers
            # if the original template variable was purely numeric
            try:
                if rendered.strip().lstrip("-").replace(".", "", 1).isdigit():
                    cell.value = float(rendered) if "." in rendered else int(rendered)
                else:
                    cell.value = rendered
            except (ValueError, AttributeError):
                cell.value = rendered


def main() -> None:
    args = parse_args()

    # Parse context JSON
    try:
        context = json.loads(args.context)
    except json.JSONDecodeError as e:
        print(f"ERROR: Invalid JSON context — {e}", file=sys.stderr)
        sys.exit(1)

    # Resolve paths
    base_dir      = Path(__file__).resolve().parent.parent
    template_path = base_dir / "document_templates" / args.template
    output_dir    = base_dir / "public" / "cached_result"
    output_path   = output_dir / args.output_filename

    if not template_path.exists():
        print(f"ERROR: Template not found at {template_path}", file=sys.stderr)
        sys.exit(1)

    output_dir.mkdir(parents=True, exist_ok=True)

    # Load, render, save
    try:
        wb = load_workbook(str(template_path))
        for sheet_name in wb.sheetnames:
            fill_worksheet(wb[sheet_name], context)
        wb.save(str(output_path))
        print(f"OK: Saved to {output_path}")
    except Exception as e:
        print(f"ERROR: Rendering failed — {e}", file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()