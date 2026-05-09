# Document Maker (eDokPUPRD)

A web-based document generation system for **Dinas Pekerjaan Umum dan Penataan Ruang Daerah Kota Tomohon**. Staff can generate official documents and letters from pre-defined templates.

---

## Features

- **Template-based generation** — Word (.docx) and Excel (.xlsx) templates with Jinja2 placeholders
- **Autofill** — dropdown slots that auto-fill form fields from staff/official databases
- **Staff & Official Loop** — searchable, draggable checkbox lists for participant lists
- **Repeating Groups** — dynamic row-based data entry
- **Digital Signature (TTD Digital)** — request e-signatures from officials via email; embeds signature image and QR verification code into signed documents
- **Signature verification** — public QR-scannable verification page
- **Auto document numbering** — configurable format with zero-padding and auto-reset (yearly/monthly)
- **PDF Preview** — converts documents to PDF via iLoveAPI for in-browser preview
- **Template scanner** — auto-detect `{{ variable }}` placeholders from uploaded templates
- **Document history & audit log** — per-user document history with download links for original and signed files
- **Role-based access** — Guest, Staff, Admin
- **Auto file purge** — generated files deleted automatically via scheduler

---

## User Roles

| Feature | Guest | Staff | Admin |
|---|---|---|---|
| Access without account | ✅ | ❌ | ❌ |
| Guest-level documents | ✅ | ✅ | ✅ |
| Staff-level documents | ❌ | ✅ | ✅ |
| Request digital signature | ❌ | ✅ | ✅ |
| Admin panel | ❌ | ❌ | ✅ |

---

## Prerequisites

- PHP 8.3+
- Composer
- Node.js 18+
- Python 3.10+ with venv
- LibreOffice (optional, for local PDF preview — replaced by iLoveAPI in production)
- MySQL or SQLite

---

## Installation (Linux/Ubuntu)

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install php8.3 php8.3-fpm php8.3-mysql php8.3-sqlite3 php8.3-xml php8.3-curl php8.3-zip
sudo apt install python3 python3-venv python3-pip
sudo apt install nodejs npm
sudo apt install nginx
```

```bash
git clone https://github.com/JustMarvell/DocumentMaker.git /path/to/project
cd /path/to/project

composer install --no-dev
cp .env.example .env && php artisan key:generate

python3 -m venv venv
venv/bin/pip install docxtpl openpyxl jinja2 pillow qrcode python-docx

npm install && npm run build

touch database/database.sqlite   # if using SQLite
php artisan migrate --seed
```

```bash
sudo chown -R www-data:www-data /path/to/project
sudo chmod -R 775 /path/to/project/storage
mkdir -p storage/app/cached_result storage/app/signatures storage/app/guides storage/app/guide_videos storage/app/document_previews
```

### Crontab (scheduler)

```bash
sudo crontab -u www-data -e
# add:
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### First admin account

```bash
php artisan tinker --execute 'App\Models\User::where("email","your@email.com")->update(["role"=>"admin"]);'
```

---

## Environment Variables (`.env`)

Key variables to configure:

```env
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_DATABASE=edokpuprd
DB_USERNAME=root
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@email.com
```

iLoveAPI keys are configured from the Admin Panel → **Pengaturan PDF**, not in `.env`.

---

## Digital Signature Setup

1. Admin Panel → **Data Pejabat** → upload signature image (PNG transparent recommended) per official
2. Admin Panel → **Jenis Dokumen** → toggle **TTD Digital** on for the document type
3. Optionally toggle **Gambar TTD** (embed signature image) and **QR Code** (embed verification QR)
4. In the `.docx`/`.xlsx` template, place these placeholders where the signature block should appear:
   - Text: `{{ nama_pejabat }}`, `{{ jabatan_pejabat }}`, `{{ tgl_ttd }}`
   - Image placeholders: insert `transparent35mm.png` (for signature) and `dummy_qr.png` (for QR) as inline images — the system swaps them when signing
   - Both dummy images are in `resources/img/`

---

## Auto Document Numbering

Configure per document type at: **Kelola Field → # Nomor Surat**

Format tokens: `{number}`, `{year}`, `{month}`, `{roman_month}`

Example: `{number}/DPUPR/{roman_month}/{year}` → `045/DPUPR/V/2026`

---

## PDF Preview (iLoveAPI)

1. Register at [developer.ilovepdf.com](https://developer.ilovepdf.com)
2. Admin Panel → **Pengaturan PDF** → paste Public Key and Secret Key
3. Enable preview per template via the **Preview** toggle on the Jenis Dokumen page
4. Free tier: 250 conversions/month; counter resets automatically each month

---

## File Purge Schedule

Files in `storage/app/cached_result/` are purged automatically. Default TTL: 300 seconds (5 minutes).

To change TTL, edit `routes/console.php`:
```php
Schedule::command('documents:purge --ttl=600')->everyMinute(); // 10 minutes
```

Manual purge:
```bash
php artisan documents:purge --ttl=60
```

---

## Contributing

Pull requests are welcome. Please open an issue first for major changes.