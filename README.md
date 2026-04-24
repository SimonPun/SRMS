# SRMS (Service Request Management System)

This project is a Laravel-based Service Request Management System for a masters assignment.

It supports three roles:
- `admin`
- `service_staff`
- `client`

## Core Workflow

1. Client creates a request.
2. Admin assigns one or more staff members.
3. Each assigned staff member updates their own status.
4. Overall request status updates based on staff progress.
5. Admin and client track updates via notifications.

## Main Features

- Role-based access control with middleware.
- Authentication: login, register, logout.
- Password recovery: forgot password and reset password.
- Profile update for all logged-in roles.
- Multi-staff assignment per request.
- Per-staff status tracking (`pending`, `in_progress`, `completed`).
- Admin request filtering and management.
- Staff assigned-request view.
- Client request submission and tracking.
- Notification bell with:
  - dynamic polling updates,
  - unread count,
  - `Read all`,
  - per-notification delete (dismiss per user).

## Tech Stack

- Laravel 11
- PHP
- Blade templates
- MySQL (primary)
- SQLite in-memory (tests)

## Quick Start

1. Install dependencies:

```bash
composer install
```

2. Configure environment:

```bash
cp .env.example .env
php artisan key:generate
```

3. Set DB credentials in `.env`, then run:

```bash
php artisan migrate
php artisan db:seed
```

4. Run app:

```bash
php artisan serve
```

## Demo Accounts

- Admin: `admin@example.com` / `password123`
- Staff: `staff@example.com` / `password123`
- Client: `client@example.com` / `password123`

## Testing

Run:

```bash
php artisan test
```

Current suite verifies:
- auth and registration,
- role-based access control,
- request assignment/status workflow,
- multi-staff status behavior,
- notification read/delete behavior,
- profile update flow.
