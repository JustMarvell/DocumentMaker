"""
sign_document.py
Inserts a signature image and a QR code into an already-generated
.docx or .xlsx document when an official approves a signature request.

What it does:
  - Replaces the placeholder {{ ttd_pejabat }} with the official's
    signature image (docx via docxtpl InlineImage, xlsx via openpyxl image)
  - Inserts a QR code image that links to the public verification URL
  - Saves the result as a new "signed" file, leaving the original untouched

Template requirements:
  DOCX — add these Jinja2 placeholders anywhere in the document:
    {{ ttd_pejabat }}          ← replaced with signature image
    {{ qr_code }}              ← replaced with QR code image
    {{ nama_pejabat }}         ← official's name (text)
    {{ jabatan_pejabat }}      ← official's position (text)
    {{ tgl_ttd }}              ← approval date (text)

  XLSX — add these text placeholders in cells:
    {{ttd_pejabat}}            ← cell will be replaced by an image anchored there
    {{qr_code}}                ← cell will be replaced by QR code image

Usage (called by SignatureService.php):
    python3 sign_document.py \
        --input  "original_uuid.docx" \
        --output "signed_uuid.docx" \
        --sig-image  "/abs/path/to/signature.png" \
        --verify-url "https://yourdomain.com/verify/TOKEN" \
        --official-name "Ir. John Doe, M.T." \
        --official-position "Kepala Dinas PUPRD" \
        --approval-date "30 April 2026"
"""

import argparse, io, os, re, sys, tempfile, zipfile
from pathlib import Path

# sediah hujan sebelum payung. 
def require(pkg, install_name=None):
    import importlib
    try:
        return importlib.import_module(pkg)
    except ImportError:
        name = install_name or pkg
        print(f"ERROR: Python package '{name}' is not installed.\nRun: pip install {name}", file=sys.stderr)
        sys.exit(1)

# argume puisi
def parse_args() -> argparse.Namespace:
    p = argparse.ArgumentParser(description="Insert signature + QR code into a document")
    p.add_argument("--input", required=True, help="Input filename (path public/cached_result/)")
    p.add_argument("--output", required=True, help="Output filename (path public/cached_result/)")
    p.add_argument("--sig-image", required=False, default="", help="Absolute path to signature PNG/JPG")
    p.add_argument("--verify-url", required=False, default="", help="URL for QR code verification page")
    p.add_argument("--official-name", required=True)
    p.add_argument("--official-position", required=False, default="")
    p.add_argument("--approval-date", required=True)
    return p.parse_args()

def make_qr_png(url: str) -> bytes:
    """Generate qr code in memory, return in bytes."""
    qrcode = require("qrcode")
    PIL = require("PIL", "Pillow")
    from PIL import Image as PILImage
    
    qr = qrcode.QRCode(
        version=None,
        error_correction=qrcode.constants.ERROR_CORRECT_M,
        box_size=6,
        border=2,
    )
    
    qr.add_data(url)
    qr.make(fit=True)
    img = qr.make_image(fill_color="black", back_color="white").convert("RGB")

    buf = io.BytesIO()
    img.save(buf, format="PNG")
    return buf.getvalue()

# sign this doc shii
def sign_docx(input_path: str, output_path: str, args: argparse.Namespace) -> None:
    docxtpl = require("docxtpl")
    from docxtpl import DocxTemplate, InlineImage
    from docx.shared import Mm
    
    doc = DocxTemplate(input_path)
    
    context: dict = {
        "nama_pejabat" : args.official_name,
        "jabatan_pejabat" : args.official_position,
        "tgl_ttd" : args.approval_date,
    }
    
    if args.sig_image and os.path.exists(args.sig_image):
        context['ttd_pejabat'] = InlineImage(doc, args.sig_image, width=Mm(40))
    else:
        context['ttd_pejabat'] = f"[TTD {args.official_name}]"

    qr_bytes = make_qr_png(args.verify_url)
    with tempfile.NamedTemporaryFile(suffix=".png", delete=False) as tmp:
        tmp.write(qr_bytes)
        qr_tmp_path = tmp.name
        
    try:
        context["qr_code"] = InlineImage(doc, qr_tmp_path, width=Mm(28))
        doc.render(context)
        doc.save(output_path)
    finally:
        os.unlink(qr_tmp_path)

# sign this shiiii for excel
def _find_placeholder_cell(ws, placehoder: str):
    """Find the first cell containing the given placeholder text."""
    for row in ws.iter_rows():
        for cell in row:
            if isinstance(cell.value, str) and placehoder in cell.value:
                return cell
    return None

def _col_letter(col: int) -> str:
    """COnvert -1 based column index to excel column letters"""
    result = ""
    while col:
        col, rem = divmod(col - 1, 26)
        result = chr(65 + rem) + result
    return result

def sign_xlsx(input_path: str, output_path: str, args: argparse.Namespace) -> None:
    openpyxl = require("openpyxl")
    from openpyxl import load_workbook
    from openpyxl.drawing.image import Image as XLImage
    
    wb = load_workbook(input_path)

    inserts = []
    
    if args.sig_image and os.path.exists(args.sig_image):
        inserts.append((open(args.sig_image, "rb").read(), "{{ttd_pejabat}}", 120, 60))
    
    qr_bytes = make_qr_png(args.verify_url)
    inserts.append((qr_bytes, "{{qr_code}}", 80, 80))

    for sheet_name in wb.sheetnames:
        ws = wb[sheet_name]

        text_ctx = {
            "{{nama_pejabat}}" : args.official_name,
            "{{jabatan_pejabat}}" : args. official_position,
            "{{tgl_ttd}}" : args.approval_date
        }
        
        for row in ws.iter_rows():
            for cell in row: 
                if isinstance(cell.value, str):
                    for ph, val in text_ctx.items():
                        if ph in cell.value:
                            cell.value = cell.value.replace(ph, val)
        
        for img_bytes, placeholder, w_px, h_px in inserts:
            cell = _find_placeholder_cell(ws, placeholder)
            if cell is None:
                continue
            
            cell.value = None
            
            anchor = f"{_col_letter(cell.column)}{cell.row}"
            with tempfile.NamedTemporaryFile(suffix=".png", delete=False) as tmp:
                tmp.write(img_bytes)
                tmp_path = tmp.name
            try:
                xl_img = XLImage(tmp_path)
                xl_img.width = w_px
                xl_img.height = h_px
                xl_img.anchor = anchor
                ws.add_image(xl_img)
            finally:
                os.unlink(tmp_path)
        
    wb.save(output_path)
    
# you shall enter here
def main() -> None:
    args = parse_args()
    
    base_dir = Path(__file__).resolve().parent.parent
    result_dir = base_dir / "public" / "cached_result"
    input_path = str(result_dir / args.input)
    output_path = str(result_dir / args.output)

    if not os.path.exists(input_path):
        print(f"ERROR: Input file not found: {input_path}", file=sys.stderr)
        sys.exit(1)
    
    result_dir.mkdir(parents=True, exist_ok=True)

    ext = Path(args.input).suffix.lower()
    
    try:
        if ext == ".docx":
            sign_docx(input_path, output_path, args)
        elif ext == ".xlsx":
            sign_xlsx(input_path, output_path, args)
        else:
            print(f"ERROR: Unsupported file type '{ext}'. Only .docx and .xlsx are supported.", file=sys.stderr)
            sys.exit(1)
        
        print(f"OK: Signed document saved to {output_path}")

    except Exception as e:
        print(f"ERROR: Signing failed - {e}", file=sys.stderr)
        import traceback
        traceback.print_exc(file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    main()