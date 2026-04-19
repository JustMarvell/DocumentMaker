"""
docx_generator.py
Generic Word document generator using docxtpl (Jinja2).

Supports:
  - Simple placeholders:      {{ variable_name }}
  - Conditionals:             {% if condition %}...{% endif %}
  - Loops:                    {% for item in items %}...{% endfor %}
  - All other Jinja2 syntax supported by docxtpl

Usage (called by DocumentController.php):
    python3 docx_generator.py \
        --template "surat-izin-sakit.docx" \
        --output-filename "surat-izin-sakit_<uuid>.docx" \
        --context '{"employee_name": "John Doe", "letter_date": "2025-04-01"}'
"""

import argparse
import json
import sys
from pathlib import Path
from docxtpl import DocxTemplate


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Generic DOCX generator")
    parser.add_argument("--template",        required=True, help="Template filename inside document_templates/")
    parser.add_argument("--output-filename", required=True, help="Output filename inside public/cached_result/")
    parser.add_argument("--context",         required=True, help="JSON string of template variables")
    return parser.parse_args()


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

    # Render and save
    try:
        doc = DocxTemplate(str(template_path))
        doc.render(context)
        doc.save(str(output_path))
        print(f"OK: Saved to {output_path}")
    except Exception as e:
        print(f"ERROR: Rendering failed — {e}", file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()