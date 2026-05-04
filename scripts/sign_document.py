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
    p.add_argument("--use-image", default="1", help="1 to embed signature image, 0 for no/skip")
    p.add_argument("--use-qr", default="1", help="1 to embed signature qr image, 0 for no/skip")
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
    
    use_image = args.use_image == "1"
    use_qr = args.use_qr == "1"
    
    IMAGE_SIZE_MM = 35
    
    if use_image:
        if args.sig_image and os.path.exists(args.sig_image):
            context['ttd_pejabat'] = InlineImage(doc, args.sig_image, width=Mm(IMAGE_SIZE_MM), height=Mm(IMAGE_SIZE_MM))
        else:
            context['ttd_pejabat'] = f"[TTD {args.official_name}]"
    else:
        context['ttd_pejabat'] = '' #empety

    qr_bytes = make_qr_png(args.verify_url)
    with tempfile.NamedTemporaryFile(suffix=".png", delete=False) as tmp:
        tmp.write(qr_bytes)
        qr_tmp_path = tmp.name
        
    try:
        if use_qr:
            context["qr_code"] = InlineImage(doc, qr_tmp_path, width=Mm(IMAGE_SIZE_MM), height=Mm(IMAGE_SIZE_MM))
        else:
            context["qr_code"] = ''
            
        doc.render(context)
        doc.save(output_path)
    finally:
        os.unlink(qr_tmp_path)

# sign this shiiii for excel
def _col_letter(col: int) -> str:
    result = ""
    while col:
        col, rem = divmod(col - 1, 26)
        result = chr(65 + rem) + result
    return result

def _find_placeholder_in_shared_strings(xml_bytes: bytes, placeholder: str):
    """Return (si_index, updated_xml_bytes) — clears the placeholder <t> content."""
    try:
        xml = xml_bytes.decode("utf-8")
    except UnicodeDecodeError:
        return None, xml_bytes

    si_blocks = list(re.finditer(r"<si>(.*?)</si>", xml, re.DOTALL))
    for i, m in enumerate(si_blocks):
        if placeholder in m.group(1):
            new_block = re.sub(r"<t([^>]*)>.*?</t>", r"<t\1></t>", m.group(0), flags=re.DOTALL)
            xml = xml[:m.start()] + new_block + xml[m.end():]
            return i, xml.encode("utf-8")
    return None, xml_bytes

def _find_placeholder_cell_coords(sheet_xml_bytes: bytes, shared_strings_xml: bytes, placeholder: str):
    """Return (col_0based, row_0based) of the cell referencing the placeholder, or None."""
    try:
        ss_xml = shared_strings_xml.decode("utf-8")
    except UnicodeDecodeError:
        return None

    si_blocks = list(re.finditer(r"<si>(.*?)</si>", ss_xml, re.DOTALL))
    target_si = next((i for i, m in enumerate(si_blocks) if placeholder in m.group(1)), None)
    if target_si is None:
        return None

    try:
        sheet_xml = sheet_xml_bytes.decode("utf-8")
    except UnicodeDecodeError:
        return None

    pattern = re.compile(r'<c\s+r="([A-Z]+)(\d+)"[^>]*t="s"[^>]*>.*?<v>(\d+)</v>.*?</c>', re.DOTALL)
    for m in pattern.finditer(sheet_xml):
        if int(m.group(3)) == target_si:
            col = 0
            for ch in m.group(1):
                col = col * 26 + (ord(ch) - ord('A') + 1)
            return col - 1, int(m.group(2)) - 1  # 0-based
    return None

def _ensure_drawing_for_sheet(zip_files: dict, sheet_idx: int):
    """Ensure drawing XML + rels exist and are linked to sheet. Returns (drawing_path, drawing_rels_path)."""
    sheet_path = f"xl/worksheets/sheet{sheet_idx}.xml"
    sheet_rels_path = f"xl/worksheets/_rels/sheet{sheet_idx}.xml.rels"
    drawing_path = f"xl/drawings/drawing{sheet_idx}.xml"
    drawing_rels_path = f"xl/drawings/_rels/drawing{sheet_idx}.xml.rels"

    # Check if drawing already linked
    if sheet_rels_path in zip_files:
        rels_xml = zip_files[sheet_rels_path].decode("utf-8", errors="replace")
        m = re.search(r'Target="\.\./drawings/(drawing\d+\.xml)"', rels_xml)
        if m:
            drawing_path = f"xl/drawings/{m.group(1)}"
            drawing_rels_path = f"xl/drawings/_rels/{m.group(1)}.rels"

    DRAWING_STUB = (
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        '<xdr:wsDr xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing"'
        ' xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"'
        ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        '</xdr:wsDr>'
    ).encode("utf-8")

    RELS_STUB = (
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        '</Relationships>'
    ).encode("utf-8")

    if drawing_path not in zip_files:
        zip_files[drawing_path] = DRAWING_STUB
    if drawing_rels_path not in zip_files:
        zip_files[drawing_rels_path] = RELS_STUB
    if sheet_rels_path not in zip_files:
        zip_files[sheet_rels_path] = RELS_STUB

    rels_xml = zip_files[sheet_rels_path].decode("utf-8", errors="replace")
    drawing_rel_target = f"../drawings/{drawing_path.split('/')[-1]}"
    if drawing_rel_target not in rels_xml:
        rel_id = "rIdDrw1"
        new_rel = (f'<Relationship Id="{rel_id}" '
                   f'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing" '
                   f'Target="{drawing_rel_target}"/>')
        rels_xml = rels_xml.replace("</Relationships>", new_rel + "</Relationships>")
        zip_files[sheet_rels_path] = rels_xml.encode("utf-8")

        if sheet_path in zip_files:
            sheet_xml = zip_files[sheet_path].decode("utf-8", errors="replace")
            if "<drawing " not in sheet_xml:
                sheet_xml = sheet_xml.replace("</sheetData>", f'</sheetData><drawing r:id="{rel_id}"/>')
                zip_files[sheet_path] = sheet_xml.encode("utf-8")

    return drawing_path, drawing_rels_path

def _add_image_rel(drawing_rels_bytes: bytes, img_zip_path: str, drawing_zip_path: str):
    """Append image relationship to drawing rels. Returns (new_bytes, rel_id)."""
    try:
        xml = drawing_rels_bytes.decode("utf-8")
    except UnicodeDecodeError:
        xml = ('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
               '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
               '</Relationships>')

    existing_ids = re.findall(r'Id="rId(\d+)"', xml)
    next_id = max((int(x) for x in existing_ids), default=0) + 1
    rel_id = f"rId{next_id}"

    depth = drawing_zip_path.count("/")  # xl/drawings/drawing1.xml → 2
    rel_target = "../" * (depth - 1) + img_zip_path  # → ../media/img_xxx.png

    new_rel = (f'<Relationship Id="{rel_id}" '
               f'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" '
               f'Target="{rel_target}"/>')
    xml = xml.replace("</Relationships>", new_rel + "</Relationships>")
    return xml.encode("utf-8"), rel_id

def _inject_image_anchor(drawing_xml_bytes: bytes, rel_id: str, col: int, row: int, size_emu: int) -> bytes:
    """Append a oneCellAnchor block to drawing XML."""
    try:
        xml = drawing_xml_bytes.decode("utf-8")
    except UnicodeDecodeError:
        return drawing_xml_bytes

    anchor = (
        f'<xdr:oneCellAnchor>'
        f'<xdr:from><xdr:col>{col}</xdr:col><xdr:colOff>0</xdr:colOff>'
        f'<xdr:row>{row}</xdr:row><xdr:rowOff>0</xdr:rowOff></xdr:from>'
        f'<xdr:ext cx="{size_emu}" cy="{size_emu}"/>'
        f'<xdr:pic>'
        f'<xdr:nvPicPr>'
        f'<xdr:cNvPr id="100" name="img_{rel_id}"/>'
        f'<xdr:cNvPicPr><a:picLocks noChangeAspect="1"/></xdr:cNvPicPr>'
        f'</xdr:nvPicPr>'
        f'<xdr:blipFill><a:blip r:embed="{rel_id}"/><a:stretch><a:fillRect/></a:stretch></xdr:blipFill>'
        f'<xdr:spPr>'
        f'<a:xfrm><a:off x="0" y="0"/><a:ext cx="{size_emu}" cy="{size_emu}"/></a:xfrm>'
        f'<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>'
        f'</xdr:spPr>'
        f'</xdr:pic>'
        f'<xdr:clientData/>'
        f'</xdr:oneCellAnchor>'
    )

    close = "</xdr:wsDr>"
    xml = xml.replace(close, anchor + close) if close in xml else xml + anchor
    return xml.encode("utf-8")

def sign_xlsx(input_path: str, output_path: str, args: argparse.Namespace) -> None:
    require("openpyxl")

    IMAGE_SIZE_EMU = 3_200_000  # ~35mm

    text_ctx = {
        "{{nama_pejabat}}": args.official_name,
        "{{jabatan_pejabat}}": args.official_position,
        "{{tgl_ttd}}": args.approval_date,
    }

    inserts = []  # (img_bytes, placeholder)
    if args.use_image == "1" and args.sig_image and os.path.exists(args.sig_image):
        inserts.append((open(args.sig_image, "rb").read(), "{{ttd_pejabat}}"))
    if args.use_qr == "1" and args.verify_url:
        inserts.append((make_qr_png(args.verify_url), "{{qr_code}}"))

    # Load entire zip into memory
    with zipfile.ZipFile(input_path, "r") as z:
        zip_files = {name: z.read(name) for name in z.namelist()}

    # Render text placeholders in sharedStrings + drawing textboxes
    if "xl/sharedStrings.xml" in zip_files:
        ss_xml = zip_files["xl/sharedStrings.xml"].decode("utf-8", errors="replace")
        for ph, val in text_ctx.items():
            ss_xml = ss_xml.replace(ph, val)
        zip_files["xl/sharedStrings.xml"] = ss_xml.encode("utf-8")

    for name in list(zip_files.keys()):
        if re.match(r"xl/drawings/.*\.xml$", name, re.IGNORECASE) and not name.endswith(".rels"):
            xml = zip_files[name].decode("utf-8", errors="replace")
            for ph, val in text_ctx.items():
                xml = xml.replace(ph, val)
            zip_files[name] = xml.encode("utf-8")

    # Inject images per sheet
    for name in list(zip_files.keys()):
        m = re.match(r"xl/worksheets/sheet(\d+)\.xml$", name)
        if not m:
            continue
        sheet_idx = int(m.group(1))
        ss_bytes = zip_files.get("xl/sharedStrings.xml", b"")

        for img_bytes, placeholder in inserts:
            coords = _find_placeholder_cell_coords(zip_files[name], ss_bytes, placeholder)
            if coords is None:
                continue

            col, row = coords

            _, ss_bytes = _find_placeholder_in_shared_strings(ss_bytes, placeholder)
            zip_files["xl/sharedStrings.xml"] = ss_bytes

            img_zip_path = f"xl/media/img_{placeholder.strip('{}')}.png"
            zip_files[img_zip_path] = img_bytes

            drawing_path, drawing_rels_path = _ensure_drawing_for_sheet(zip_files, sheet_idx)

            new_rels, rel_id = _add_image_rel(zip_files[drawing_rels_path], img_zip_path, drawing_path)
            zip_files[drawing_rels_path] = new_rels

            zip_files[drawing_path] = _inject_image_anchor(zip_files[drawing_path], rel_id, col, row, IMAGE_SIZE_EMU)

    # Write final zip — all original entries preserved
    with zipfile.ZipFile(output_path, "w", zipfile.ZIP_DEFLATED) as out:
        for name, data in zip_files.items():
            out.writestr(name, data)
    
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