# Simple-Queue-Management-System (PHP + MySQL)

A lightweight campus Queue Management MVP. Students can register, join queues, and track their position. Admins can create queues and serve next.

## Features
- Registration/Login (session-based)
- Admin creates queues
- Students join/leave queue
- Real-time-ish status via polling (every 5s)
- Estimated wait time using average service seconds

## Requirements
- PHP 8+
- MySQL 5.7+/8+
- A local web server (XAMPP/WAMP) or PHP built-in server

## Setup
1. Create DB and tables:
   - Import `schema.sql` into MySQL.
2. Configure DB credentials:
   - Edit `config/config.php` (DB_HOST, DB_USER, DB_PASS if needed).
3. Run the app:
   - Using PHP built-in server from project root:
     ```bash
     php -S 127.0.0.1:8080 -t public
     ```
   - Or place the `public/` folder under your web root and ensure PHP has access to `config/` and `src/` via `require` paths.
4. Create an admin:
   - Register a normal account via `/public/register.php`.
   - In MySQL, update role:
     ```sql
     UPDATE users SET role = 'admin' WHERE student_id = 'your_admin_student_id';
     ```

## Endpoints
- `public/api/list_queues.php` (GET)
- `public/api/join_queue.php` (POST {queue_id})
- `public/api/leave_queue.php` (POST)
- `public/api/queue_status.php` (GET [queue_id])
- `public/api/create_queue.php` (POST admin)
- `public/api/admin_next.php` (POST admin)

## Notes
- Positions computed by `queue_entries` id order for each queue.
- Modify `BASE_URL` in `config/config.php` if hosted in a subdirectory.
- For production, add CSRF protection and input validation hardening.

##Access the web version through
cqm.page.gd
