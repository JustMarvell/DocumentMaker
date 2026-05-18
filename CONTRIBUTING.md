# Contributing to eDokPUPRD

Thank you for your interest in contributing! This document covers the project structure, conventions, and workflow.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11, PHP 8.3+ |
| Frontend | Blade, Alpine.js, Tailwind CSS v4 |
| Document Generation | Python 3.10+ (docxtpl, openpyxl, jinja2) |
| Database | MySQL / SQLite |
| PDF Preview | iLoveAPI |
| Digital Signature | Custom Python script + QR (qrcode, Pillow) |

---

## Project Structure

```
├── app/
│   ├── Console/Commands/     # Artisan commands (e.g. PurgeCachedDocuments)
│   ├── Http/Controllers/     # AdminController, DocumentController, SignatureRequestController, etc.
│   ├── Models/               # Eloquent models
│   ├── Notifications/        # Email notifications (signature flow)
│   └── Services/             # SignatureService, IlovePdfConverter, TemplateScanner
├── document_templates/       # .docx / .xlsx template files
├── resources/
│   ├── css/                  # app.css (design system), home-new.css, signature/*.css
│   ├── js/                   # app.js, video-player.js
│   └── views/
│       ├── admin/            # Admin panel pages
│       ├── partials/         # Reusable form-field partial
│       └── signature/        # Signature review/verify/create pages
├── scripts/                  # Python generation & signing scripts
│   ├── docx_generator.py
│   ├── xlsx_generator.py
│   └── sign_document.py
├── storage/app/
│   ├── cached_result/        # Generated documents (auto-purged)
│   ├── signatures/           # Official signature images
│   └── document_previews/    # Uploaded preview PDFs
└── routes/
    ├── web.php
    └── console.php
```

---

## Setup

```bash
git clone <repo-url> && cd <repo>
composer install
cp .env.example .env && php artisan key:generate
python3 -m venv venv && venv/bin/pip install docxtpl openpyxl jinja2 pillow qrcode
npm install && npm run build
php artisan migrate --seed
```

Set your first admin:
```bash
php artisan tinker --execute 'App\Models\User::where("email","your@email.com")->update(["role"=>"admin"]);'
```

---

## Conventions

### PHP / Laravel
- Follow existing controller patterns — thin controllers, logic in services where appropriate.
- Use `php artisan make:` for all new files.
- Run `vendor/bin/pint --dirty` before committing any PHP changes.
- New models should have a factory and migration.
- Use named routes (`route('name')`) for all URL generation.

### Blade / Frontend
- New styles go in `resources/css/app.css` using the existing CSS variable design system (see `/* SIPADU Design System */` section).
- Use Alpine.js for interactive UI — avoid adding new JS libraries without discussion.
- Reusable form fields go through `partials/form-field.blade.php`.
- AJAX responses from admin pages should return `{ success: true, ... }` JSON.

### Python Scripts
- `docx_generator.py` — handles `.docx` via docxtpl (Jinja2).
- `xlsx_generator.py` — handles `.xlsx` via raw ZIP/XML manipulation to preserve drawings.
- `sign_document.py` — inserts signature image and QR code into signed documents.
- Always accept `--template`, `--output-filename`, `--context` CLI args for generator scripts.
- Print `OK: ...` on success, `ERROR: ...` to stderr on failure, and exit with code 1.

### Database
- All migrations go in `database/migrations/`.
- Soft deletes are not used — `deleted_at` on `document_logs` is a custom timestamp (not Laravel soft delete).
- Foreign keys should use `cascadeOnDelete()` where appropriate.

---

## Adding a New Document Type

1. Create a `.docx` or `.xlsx` template with `{{ variable }}` placeholders.
2. Upload it via Admin Panel → **Jenis Dokumen** → **+ Tambah Template Baru**.
3. Define fields via **Kelola Field** (or use **Scan Template** to auto-detect).
4. Configure autofill slots if the document needs staff/official autofill.
5. Optionally enable **Nomor Surat Otomatis**, **TTD Digital**, and **Preview PDF**.

---

## Adding a New Field Type

1. Add the value to `DocumentField::fieldTypes()` in `app/Models/DocumentField.php`.
2. Add the corresponding render case in `resources/views/partials/form-field.blade.php`.
3. Handle the field value in `DocumentController::generate()` (context building).
4. Update `storeField()` and `updateField()` validation in `AdminController.php`.

---

## Running Tests

```bash
php artisan test --compact
php artisan test --compact --filter=TestName
```

---

## Pull Request Guidelines

- Branch from `main`. Name branches: `feature/short-description` or `fix/short-description`.
- Keep PRs focused — one feature or fix per PR.
- Include a short description of what changed and why.
- Run `vendor/bin/pint --dirty` before pushing PHP changes.
- Test document generation end-to-end before submitting changes to Python scripts or `DocumentController`.

---

## File Purge & Scheduler

Generated files are auto-deleted after 300 seconds (default). During development, run the scheduler manually:

```bash
php artisan schedule:work
# or trigger manually:
php artisan documents:purge --ttl=60
```

---

## Environment Notes

- `APP_URL` must be set correctly — it's used for email links and QR verification URLs.
- iLoveAPI keys are configured from Admin Panel → **Pengaturan PDF**, not `.env`.
- Signature images are stored in `storage/app/signatures/` and served through `OfficialDataController::serveSignature()` (admin-only).
- The Python venv must be at `venv/` in the project root. The scripts are called with `venv/bin/python`.

---

## Reporting Issues

Open a GitHub issue with:
- Steps to reproduce
- Expected vs actual behavior
- Relevant logs (`storage/logs/laravel.log`) or Python stderr output
- PHP version, OS, and whether LibreOffice is installed (if preview-related)