# Document Maker

A simple web-based application for creating documents and letters from pre-defined templates. Users can access the platform as a **Guest** (limited access) or as a **Staff** member (full access).

---

## Overview

Document Maker is a template-based document generation website that streamlines the creation of official documents and letters. It supports two user roles with different levels of access, making it suitable for both internal staff and external/public users.

---

## Features

- **Template Selection** — Choose from a variety of document and letter templates
- **Guest Access** — Login-free entry with access to a limited set of templates
- **Staff Access** — Full access to all available documents and letters
- **Document Generation** — Fill in form fields and generate ready-to-use documents

---

## User Roles

| Feature | Guest | Staff |
|---|---|---|
| Access without account | ✅ | ❌ |
| Limited template access | ✅ | ✅ |
| Full template access | ❌ | ✅ |
| Create all document types | ❌ | ✅ |

---

## Getting Started

### Prerequisites

- Node.js v18+
- A modern web browser
- docxtpl python library `pip install docxtpl`
- xlsxtpl python library `pip install xlsxtpl`
- php, composer, artisan, & laravel

### Installation On Linux

```bash
# On Linux (Ubuntu)
sudo apt update && sudo apt upgrade -y
sudo apt install php8.3 php8.3-fpm php8.3-sqlite3 php8.3-xml php8.3-curl
sudo apt install python3 python3-venv python3-pip
sudo apt install nodejs npm
sudo apt install libreoffice
sudo apt install nginx

# Clone and setup project
git clone https://github.com/JustMarvell/DocumentMaker.git /path/to/project
cd /path/to/project
composer install --no-dev
cp .env.example .env && php artisan key:generate
python3 -m venv venv && venv/bin/pip install docxtpl openpyxl jinja2
npm install && npm run build
touch database/database.sqlite
php artisan migrate --seed

# Setup permision
sudo chown -R www-data:www-data /path/to/project
sudo chmod -R 775 /path/to/project/storage
sudo chmod -R 775 /path/to/project/public/cached_result
```

### Setup Crontab for Scheduler
```bash
sudo crontab -u www-data -e
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### Usage

1. Open the app in your browser (e.g., `http://localhost:8000`)
2. Choose to continue as a **Guest** or log in as a **Staff** member
3. Browse available templates
4. Fill in the required fields
5. Generate and download your document

---

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any improvements or bug fixes.