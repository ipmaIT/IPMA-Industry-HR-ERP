# IPMA Industry HR ERP

A Laravel-based Human Resources ERP designed for industrial environments. It provides core HR master data, attendance capture (including bulk uploads), and a foundation for leave, approvals, and reporting.

> **Repository:** `kroos/ipma_erp2`

---

## ✨ Key Features

- **Core HR master data**: Employees, departments, designations, and related references.
- **Authentication & authorization**: Laravel-based login and role/permission scaffolding.
- **Attendance management**
  - Daily time records
  - **Bulk upload via Excel** using the provided `Attendance_File_Upload.xls` template for staff attendance report example
- **Reports (foundation)**: Common HR/attendance summaries (extendable).
- **Extensible architecture**: Conventional Laravel app structure with Blade views and Bootstrap CSS.

> *Note:* Feature scope reflects what’s present in the repository structure and seed assets. Build out advanced modules (leave, overtime, payroll) on top of the existing foundations as needed.

---

## 🧱 Tech Stack

- **Backend:** PHP (Laravel)
- **Frontend:** Blade templates, Bootstrap CSS
- **Build tools:** Laravel Mix / Webpack, NPM
- **Database:** MariaDB

Project files include `tailwind.config.js`, `webpack.mix.js`, and a SQL dump `ipmaerp.sql`, indicating Laravel Mix and a MariaDB schema seed. 

---

## 📦 Project Structure (high level)

```
app/                # Application code (Models, Http Controllers, etc.)
bootstrap/
config/
database/           # Migrations/seeders (if present)
public/
resources/          # Blade views, assets
routes/             # Web/API routes
storage/
Attendance_File_Upload.xls  # Excel template for bulk attendance
ipmaerp.sql         # Database schema/data dump
```

---

## 🚀 Quick Start

### 1) Prerequisites
- PHP ≥ 8.2 (with OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo)
- Composer
- MariaDB
- Node.js (LTS) & npm

### 2) Clone & install
```bash
# Clone
git clone https://github.com/kroos/ipma_erp2.git
cd ipma_erp2

# Install PHP deps
composer install --no-dev --prefer-dist

# Copy environment
cp .env.example .env
php artisan key:generate

# Configure DB in .env
# DB_DATABASE=ipmaerp
# DB_USERNAME=...
# DB_PASSWORD=...

# Import provided SQL (fastest way to preview data)
# Using your MariaDB client:
#   CREATE DATABASE ipmaerp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
#   IMPORT ipmaerp.sql into ipmaerp

# Frontend assets
npm install
npx mix   # build assets

# Storage symlink for public uploads
php artisan storage:link
```

### 3) Run the app
```bash
php artisan serve
# App will be available at http://127.0.0.1:8000
```

> **Admin user:** If no admin account is present after import, register via the UI or create one directly in the DB. (Adjust to your organization’s roles/flags.)

---

## 📄 Attendance Upload (Excel)

1. Open `Attendance_File_Upload.xls` (root of the repo).
2. Fill the sheet with the required columns (employee identifier, date, in, out, etc.).
3. Upload via the Attendance module/import screen (ensure date/time formats match your config and timezone).
4. Validate the import summary, fix any rejected rows, and re-upload.

> Tip: Keep a master of the original file, and upload a copy each time. Enable strict input validation in the import logic to prevent partial/bad data.

---

## 🔧 Configuration

Update these in `.env`:

- **App**: `APP_NAME`, `APP_URL`, `APP_TIMEZONE`, `APP_LOCALE`
- **Database**: `DB_*`
- **Cache/Queue**: `CACHE_DRIVER`, `QUEUE_CONNECTION`
- **Mail**: `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- **Filesystem**: `FILESYSTEM_DISK`

If your industry site uses a reverse proxy or load balancer, set `TRUSTED_PROXIES` and `SESSION_SECURE_COOKIE=true` when serving over HTTPS.

---

## 🧪 Testing

```bash
php artisan test
```

Add/extend tests under `tests/` to cover HR flows (attendance import, employee CRUD, role gating).

---

## 🛡️ Security

- Keep dependencies updated (`composer update`, `npm audit fix` when safe).
- Rotate app key and DB credentials in production as per policy.
- Enforce strong passwords and 2FA (if using an external IdP).
- Restrict who can perform bulk imports and critical HR actions.

---

## 🗺️ Roadmap Ideas

- Leave management & approvals workflow
- Shift/roster planning
- Overtime requests & approvals
- Payroll integration (allowances, deductions, OT)
- Self-service portal for employees (leave, claims, payslips)
- Audit trail & activity logs
- API endpoints for integration (attendance devices, payroll, BI)

---

## 🤝 Contributing

1. Fork the repo
2. Create your feature branch: `git checkout -b feature/awesome`
3. Commit changes: `git commit -m "feat: add awesome"`
4. Push the branch: `git push origin feature/awesome`
5. Open a Pull Request

Please include tests for critical HR flows.

---

## 📜 License

No explicit license file is present. Treat the code as **All Rights Reserved** unless the maintainers add an open-source license. Contact the repository owners for usage and distribution permissions.

---

## 🙏 Acknowledgements

- Built on [Laravel](https://laravel.com)
- Bootstrap CSS

---

## 📫 Support & Contact

- Open an Issue on GitHub with reproduction steps and environment details.
- For private/enterprise support, contact the maintainers through your organization’s channel.

