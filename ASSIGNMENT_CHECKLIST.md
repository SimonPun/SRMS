# Assignment Checklist (Simple + Complete)

Use this list before final demo/submission.

## A. Functional Coverage

- [ ] Client can register and login.
- [ ] Client can submit a request (title, category, location, priority, description).
- [ ] Admin can view all requests.
- [ ] Admin can assign multiple staff to one request.
- [ ] Staff can view assigned requests only.
- [ ] Each staff can update their own status.
- [ ] Overall request status updates correctly:
  - [ ] `pending` when no progress,
  - [ ] `in_progress` when at least one staff started/completed,
  - [ ] `completed` only when all assigned staff are completed.
- [ ] Client can view request progress and assigned staff states.

## B. Security + Access Control

- [ ] Guest cannot access protected routes.
- [ ] Client cannot access admin routes.
- [ ] Staff cannot access admin user-management routes.
- [ ] Unassigned staff cannot update request status.
- [ ] Admin role cannot be changed in user management.

## C. Notifications

- [ ] Notification bell updates dynamically.
- [ ] Admin receives request update notifications.
- [ ] Staff receives notifications for assigned requests.
- [ ] Client receives notifications for own requests.
- [ ] `Read all` sets unread count to 0.
- [ ] Delete notification dismisses it for current user only.

## D. Account Management

- [ ] Profile update works (name/email/password).
- [ ] Forgot password form works.
- [ ] Password reset link/token flow works.
- [ ] Reset password form updates password successfully.

## E. Data + Migrations

- [ ] `php artisan migrate` runs cleanly.
- [ ] Seed data available for demo accounts.
- [ ] New tables exist:
  - [ ] `service_request_staff`
  - [ ] `request_updates`
  - [ ] `request_update_user_dismissals`
  - [ ] `password_reset_tokens`

## F. Testing Evidence

- [ ] `php artisan test` passes.
- [ ] Screenshot or terminal output saved for report appendix.

## G. Report/Presentation Evidence

- [ ] Include role-permission matrix.
- [ ] Include request lifecycle diagram.
- [ ] Include database schema/ERD snapshot.
- [ ] Include test summary and key scenarios.
- [ ] Include assumptions and known limitations.

## H. Final Demo Flow (Recommended)

1. Login as client and create a request.
2. Login as admin and assign two staff.
3. Login as staff #1 and complete own status.
4. Show overall status is still in progress.
5. Login as staff #2 and complete own status.
6. Show overall status becomes completed.
7. Show notifications (dynamic count, read all, delete).
