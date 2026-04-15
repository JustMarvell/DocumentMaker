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

> _List your tech stack requirements here, e.g.:_
- Node.js v18+
- A modern web browser
- docxtpl python library `pip install docxtpl`
- xlsxtpl python library `pip install xlsxtpl`
- php, composer, artisan, & laravel

### Installation

```bash
# Clone the repository
git clone https://github.com/your-username/document-maker.git

# Navigate to the project directory
cd document-maker

# Install dependencies
npm install

# Start the development server
npm run dev
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