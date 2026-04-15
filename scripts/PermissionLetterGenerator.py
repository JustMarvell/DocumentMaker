"""
Letter Of Assignent Generator.py
Generates 'Surat Izin.docx'

Usage (called by DocumentGenerator.py):
    python3 PermissionLetterGenerator.py \
        --employee-name \
        --employee-position \
        --employee-address \
        --employee-id-number \
        --letter-address \
        --letter-date \
        --attachment-count \
        --target-name \
        --target-address \
        --total-sick-day \
        --start-date \
        --end-date
"""

import argparse
import sys
from pathlib import Path
from docxtpl import DocxTemplate

def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Generate Permission Letter (.docx)")
    parser.add_argument("--employee-name",      required=True)
    parser.add_argument("--employee-position",  required=True)
    parser.add_argument("--employee-address",   required=True)
    parser.add_argument("--employee-id-number", default="")
    parser.add_argument("--letter-address",     default="")
    parser.add_argument("--letter-date",        required=True)
    parser.add_argument("--attachment-count",   default="0")
    parser.add_argument("--target-name",        required=True)
    parser.add_argument("--target-address",     required=True)
    parser.add_argument("--total-sick-day",     required=True)
    parser.add_argument("--start-date",         required=True)
    parser.add_argument("--end-date",           required=True)
    parser.add_argument("--output-filename",    required=True)
    return parser.parse_args()

def main() -> None:
    args = parse_args()
    
    base_dir = Path(__file__).resolve().parent.parent
    template_path = base_dir / "document_templates" / "surat-izin-sakit.docx"
    output_dir = base_dir / "public" / "cached_result"
    output_path = output_dir / args.output_filename
    
    if not template_path.exists():
        print(f"ERROR : Template not found at {template_path}", file=sys.stderr)
        sys.exit(1)

    output_dir.mkdir(parents=True, exist_ok=True)

    context = {
        "employee_name":      args.employee_name,
        "employee_position":  args.employee_position,
        "employee_address":   args.employee_address,
        "employee_id_number": args.employee_id_number,
        "letter_address":     args.letter_address,
        "letter_date":        args.letter_date,
        "attachment_count":   args.attachment_count,
        "target_name":        args.target_name,
        "target_address":     args.target_address,
        "total_sick_day":     args.total_sick_day,
        "start_date":         args.start_date,
        "end_date":           args.end_date,
    }
    
    doc = DocxTemplate(str(template_path))
    doc.render(context=context)
    doc.save(str(output_path))
    print(f"OK : Saved to {output_path}")

if __name__ == "__main__" : 
    main()