# IT Inventory Management System

PHP + MySQL (XAMPP-friendly) web app for tracking IT assets across multiple factories and Head Office. Includes dashboards per location, basic role-based access, approval workflows, service history, notifications, exports, and deleted-asset archives.

## Features

- Multi-location dashboards:
  - Head Office: `dashboard.php`
  - Super Admin: `dashboard_admin.php`
  - Factory dashboards: `dashboard_agl.php`, `dashboard_ajl.php`, `dashboard_abm.php`, `dashboard_pwpl.php`
- Asset management
  - Add, update status, send for service, complete service
  - Service history tracking per factory
  - Deleted assets archive per factory
- Approval workflow (requests → approve/reject)
- User management (create/list/delete)
- Notification inbox + unread count
- CSV export
- Client-side pagination on tables

## Tech Stack

- PHP (PDO)
- MySQL / MariaDB
- HTML/CSS + vanilla JavaScript (fetch-based JSON endpoints)
- Designed to run locally on XAMPP

## Quick Start (XAMPP)

1. Start **Apache** and **MySQL** in XAMPP.
2. Place this folder into:
   - `C:\xampp\htdocs\IT-Inventory-Management-System`
3. Create the database:
   - Database name: `it_inventory`
4. Import the schema:
   - Import [it_inventory (3).sql](file:///c:/xampp/htdocs/IT-Inventory-Management-System/it_inventory%20(3).sql) in phpMyAdmin
5. Confirm DB config:
   - Edit [db_connect.php](file:///c:/xampp/htdocs/IT-Inventory-Management-System/db_connect.php) if your MySQL credentials differ.
6. Open the app:
   - `http://localhost/IT-Inventory-Management-System/login.php`

## Configuration

- Database connection: [db_connect.php](file:///c:/xampp/htdocs/IT-Inventory-Management-System/db_connect.php)
  - Default is `localhost`, DB `it_inventory`, user `root`, empty password.

## Roles & Access (high level)

- Super Admin
  - Intended entry: `dashboard_admin.php`
  - Full access across factories (approvals, users, all assets)
- Head Office Admin
  - Entry: `dashboard.php`
  - Approvals, user management, cross-factory filtering (Head Office context)
- Factory Users
  - Entries: `dashboard_agl.php`, `dashboard_ajl.php`, `dashboard_abm.php`, `dashboard_pwpl.php`
  - Factory-scoped assets/actions, plus approval requests

## Important Security Notes

- Do not deploy as-is to production.
- `login.php` contains special-case “superadmin” logic. Remove hardcoded credentials and use DB-backed roles only.
- Change default DB credentials (`root` / empty password) before any real deployment.

## Useful Endpoints (examples)

These pages use fetch calls to PHP endpoints that return JSON:

- Assets:
  - `get_assets.php`, `get_assets_agl.php`, `get_assets_ajl.php`, `get_assets_abm.php`
  - `add_asset*.php`, `delete_asset*.php`, `update_status*.php`
- Service history:
  - `get_service_history*.php`, `complete_service*.php`
- Approvals:
  - `request_approval_*.php`, `get_pending_approvals.php`, `process_approval.php`, `count_pending_approvals.php`
- Notifications:
  - `get_notifications.php`, `get_unread_notifications.php`, `mark_notifications_read.php`
- Users:
  - `create_user.php`, `get_users.php`, `delete_user.php`

## Troubleshooting

- “Trying to access array offset on value of type bool”
  - Usually means the session user no longer exists in the `users` table. Clear cookies/session and log in again.
- Blank page or JSON errors
  - Check your database import and the credentials in `db_connect.php`.
- `php` not recognized in terminal
  - Use XAMPP PHP directly: `C:\xampp\php\php.exe`

## Tests (optional)

There is a `testsprite_tests/` folder containing Python-based test artifacts. Running them is optional and depends on your local Python environment.

## More Documentation

- [SYSTEM_DOCUMENTATION.md](file:///c:/xampp/htdocs/IT-Inventory-Management-System/SYSTEM_DOCUMENTATION.md)

