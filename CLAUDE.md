# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

BookMatch is a university library management system: a catalog of books with borrowing, ratings (with an approval workflow), bookmarks, and per-user recommendations. It runs a Filament admin panel for staff alongside Breeze (Blade) pages for end users.

## Stack

- PHP 8.5 (8.5.6), Laravel 13, Filament **5** (note: v5 API differs from v3/v4 — `form(Schema $schema)`, actions live under `Filament\Actions\`)
- Spatie Laravel Permission 8 for roles/permissions
- Laravel Breeze (Blade + Alpine + Tailwind 3, Vite 8) — server-rendered, **not** Inertia/Livewire SPA
- **PHPUnit** for tests (not Pest, despite the pest-plugin allow-list entry)
- MySQL in development (`bookmatch` db); SQLite `:memory:` in tests

## Commands

```bash
composer dev                      # server + queue listener + vite, concurrently (primary dev loop)
composer test                     # config:clear then php artisan test
php artisan test --filter=test_name   # run a single test
vendor/bin/pint                   # format / lint (Laravel Pint)
npm run build                     # production asset build
php artisan migrate:fresh --seed  # reset DB and reseed (see seeding order below)
php artisan pail                  # tail application logs
```

`composer setup` does a full from-scratch install (env, key, migrate, npm build).

## Architecture

### Dual role system — the main gotcha
Roles are tracked in **two parallel places that must be kept in sync**:
1. The `users.role` **enum column** (`admin` | `librarian` | `student` | `faculty`), exposed via `User::isAdmin()` / `isLibrarian()`.
2. **Spatie roles + permissions** via the `HasRoles` trait.

`UserSeeder` sets both at once (`'role' => 'admin'` *and* `->assignRole('admin')`). Policies and middleware authorize against **Spatie** (`$user->hasRole(...)` / permissions), not the enum helpers. When creating users or writing authorization, set/check both consistently — relying on only one will silently break access control.

### Authorization
- Policies (`BookPolicy`, `BorrowPolicy`, `RatingPolicy`) are registered **explicitly** in `AppServiceProvider::boot()`, not auto-discovered. A new policy must be added there.
- Permissions are seeded in `RolesAndPermissionsSeeder`: `manage-books`, `manage-borrows`, `approve-ratings`, `rate-books`, `borrow-books`, `manage-own-ratings`. Role→permission mapping lives there too.
- `role` and `permission` middleware aliases are registered in `bootstrap/app.php`.

### Domain models & conventions
- Core entities: `User`, `Book`, `Genre`, `Rating`, `Borrow`, `Bookmark`, `Recommendation`. A book belongs to a genre; users borrow/rate/bookmark books.
- Models declare mass-assignment with the PHP-attribute style `#[Fillable([...])]` / `#[Hidden([...])]` (Laravel 13), **not** `$fillable`/`$hidden` properties — follow this when editing models.
- Role and status columns use PHP backed enums: `App\Enums\UserRole` (for `users.role`) and `App\Enums\BorrowStatus` (for `borrows.status`). Use enum values, never bare strings, when comparing or assigning these fields.
- All PHP files in `app/` carry `declare(strict_types=1)`.
- `Book` auto-generates its `slug` on `creating` and uses `slug` as its route key (`getRouteKeyName`).
- Computed values use `Attribute::make()`: `Book::averageRating` and `approvedRatingsCount` count **only approved ratings**; `Borrow::isOverdue` derives from `status === 'active'` and `due_date`.
- **Ratings have an approval workflow**: `is_approved` gates whether a rating counts toward a book's average. Staff approve via the `approve-ratings` permission.
- `Borrow` is a state machine on `status` (`active` → `returned`) with `borrowed_at` / `due_date` / `returned_at`.

### Filament admin panel
- Single panel `admin` at `/admin` (`AdminPanelProvider`), Amber theme. Resources: Book, Borrow, Rating, Genre, User. Custom dashboard widgets live in `app/Filament/Widgets` (stats, top-books table, bar/line charts).
- Default seeded admin login: `admin@library.edu` / `password`.

### Two frontends
Staff use Filament (`/admin`); end users use Breeze Blade views (`welcome`, `dashboard`, `profile`, auth flows in `routes/auth.php`). Keep admin CRUD in Filament resources and user-facing pages in Blade/controllers — don't conflate them.

### Seeding order
`DatabaseSeeder` runs `RolesAndPermissionsSeeder` **first** (roles/permissions must exist before `UserSeeder` assigns them), then Genre → Book → User → Rating.
