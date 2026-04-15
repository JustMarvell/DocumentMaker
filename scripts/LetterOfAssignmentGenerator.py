"""
Letter Of Assignent Generator.py
Generates 'Surat Tugas Perjalanan Dinas.docx'

Usage (called by DocumentGenerator.py):
    python3 LetterOfAssignment.py \
        --letter-number \
        --assignment-objective \
        --destination-agency-location \
        --employee-name \
        --employee-position \
        --employee-address \
        --departure-date \
        --return-date \
        --letter-date
"""

import argparse
import sys
from pathlib import Path
from docxtpl import DocxTemplate

def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Generate Letter Of Assignment.docx")
    parser.add_argument("--letter-number", required=True)
    parser.add_argument("--assignment-objective", required=True)
    parser.add_argument("--destination-agency-location", required=True)
    parser.add_argument("--employee-name", required=True)
    parser.add_argument("--employee-position", required=True)
    parser.add_argument("--employee-address", required=True)
    parser.add_argument("--departure-date", required=True)
    parser.add_argument("--return-date", required=True)
    parser.add_argument("--letter-date", required=True)
    parser.add_argument("--output-filename",    required=True)
    return parser.parse_args()

def main() -> None:
    args = parse_args()
    
    base_dir = Path(__file__).resolve().parent.parent
    template_path = base_dir /"document_templates" / "surat-tugas-perjalanan-dinas.docx"
    output_dir = base_dir / "public" / "cached_result"
    output_path = output_dir / args.output_filename
    
    if not template_path.exists():
        print(f"ERROR : Template not found at {template_path}", file=sys.stderr)
        sys.exit(1)

    output_dir.mkdir(parents=True, exist_ok=True)

    context = {
        "letter_number" : args.letter_number,
        "assignment_objective" : args.assignment_objective,
        "destination_agency_location" : args.destination_agency_location,
        "employee_name" : args.employee_name,
        "employee_position" : args.employee_position,
        "employee_address" : args.employee_address,
        "departure_date" : args.departure_date,
        "return_date" : args.return_date,
        "letter_date" : args.letter_date,   
    }
    
    doc = DocxTemplate(str(template_path))
    doc.render(context=context)
    doc.save(str(output_path))
    print(f"OK : Saved to {output_path}")

if __name__ == "__main__" : 
    main()