# ChunkStack - Agent Guidelines

## Project Overview
Laravel 13 navigation/bookmark site. Links organized by categories and tags, with an admin panel for CRUD and a public frontend.

## Key Commands

```bash
composer run dev                  # Server + queue + Vite concurrently
composer test                     # Full test suite (clears config first)
php artisan test --compact        # Compact test output (preferred)
php artisan test --compact --filter=TestName   # Single test
vendor/bin/pint --dirty           # Format PHP (required before commit)
npm run build                     # Build frontend for production
```

## Architecture

### Models (all use SoftDeletes)
- `Category` — hasMany `Link`; ordered by `sort_order`
- `Link` — belongsTo `Category`, belongsToMany `Tag`; fields: title, url, description, icon, icon_type, icon_url, sort_order, page_view_count, is_active
- `Tag` — belongsToMany `Link` via `link_tag` pivot
- `User` — role-based (admin/user), auto-registered via auth system
- `SiteSetting` — key-value settings store

### Routes
- `/` → `Frontend\HomeController@index` (public listing with search)
- `/link/{slug}` → `Frontend\HomeController@show` (link detail page)
- `/admin/*` → auth-protected admin panel
  - `admin` middleware gate limits category/link/tag CRUD to admin users
  - `email.confirmation` middleware is global on all web routes

### Middleware (`bootstrap/app.php`)
- `admin` alias → `IsAdmin` (restricts CRUD routes)
- `email.confirmation` → `CheckEmailConfirmation` (added to web group globally)

### Frontend
- Tailwind CSS v4 via `@tailwindcss/vite` plugin
- FontAwesome Free v7 for icons
- **Vue.js 3** — component framework via `@vitejs/plugin-vue` v6
  - Vue entry: `resources/js/vue.js` (exports `mount`/`mountComponent` for island-style mounting)
  - Vue components: `resources/js/components/`
  - `.vue` single-file components fully supported in Vite build
  - **Architecture: islands pattern** — Vue mounts on specific DOM selectors, never wraps server-rendered Blade content with a full-page app. Do NOT auto-mount or wrap `{{ $slot }}` in a Vue root.
- Vite entry: `resources/css/app.css` + `resources/js/app.js`

### Views
- `resources/views/frontend/home.blade.php` — public page
- `resources/views/admin/` — admin CRUD views (links, categories, tags, settings, auth, email)
- `resources/views/components/` — shared Blade components

## Conventions

### PHP
- Models use `$casts` property (not `casts()` method) — follow existing pattern
- Always use Form Request classes for validation (never inline in controllers)
- `$fillable` arrays on models, explicit return types on all methods
- Success/error messages are in Chinese (e.g., `'链接创建成功'`)
- Use `Model::query()` chain, never `DB::` raw queries
- Icons use `icon_type` field: `"fontawesome"` uses `icon` column, `"url"` uses `icon_url`

### Testing (Pest v4)
- Tests extend `Tests\TestCase` (configured in `tests/Pest.php`)
- SQLite in-memory database (`:memory:`) for testing
- Use `test()` function, **not** `it()`
- DB tables are migrated fresh per test run (no `RefreshDatabase` trait needed for in-memory)
- Factories located in `database/factories/`

### Database
- Default connection is SQLite (`database/database.sqlite` committed to repo)
- Migrations are numbered chronologically; latest ones handle categories, links, tags, site_settings

## Gotchas
- `apps/` and `packages/chunkstack-core/` directories exist but are empty/unused scaffolding — don't treat them as active code
- Email confirmation middleware runs on ALL web routes (including public homepage); test accordingly
- `composer test` runs `config:clear` before tests — if tests fail after changing config, run `composer test` not `php artisan test`
- Laravel Herd serves this at `chunkstack.test`; never run commands to make the site available
