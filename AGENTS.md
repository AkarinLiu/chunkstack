# ChunkStack

Laravel 13 navigation/bookmark site. Links organized by categories and tags, admin panel for CRUD, public frontend via Vue SPA.

## Commands

```bash
composer run dev                  # Server + queue + Vite concurrently
composer test                     # Full suite (runs config:clear first)
php artisan test --compact --filter=TestName   # Single test
vendor/bin/pint --dirty           # Format PHP (required before commit)
npm run build                     # Build frontend for production
npm run dev                       # Vite dev server only
```

## Architecture

### Models
- **Category** — hasMany `Link`; SoftDeletes; sorted by `sort_order`
- **Link** — belongsTo `Category`, belongsToMany `Tag`; SoftDeletes; slug auto-generated from title on create/update (see `boot()`); fields include `click_count`, `page_view_count`, `icon_type` (emoji/font-awesome/image)
- **Tag** — belongsToMany `Link`; has `color` field; **no SoftDeletes**
- **CustomHeader** — name/content for head injections; SoftDeletes
- **SiteSetting** — key/value with static `getValue()`/`setValue()` helpers
- **User** — role-based (admin/user); uses `casts()` **method** (only model that does); 180-day email reconfirmation logic

### Routes (`routes/web.php`)
- `/` — `HomeController@index` (public listing, search)
- `/link/{slug}` — `HomeController@show`
- `/sitemap.xml` — `SitemapController@index`
- `/api/links`, `/api/links/{slug}`, `/api/link-views/{slug}`, `/api/link-clicks/{slug}`
- `/admin/*` — auth-protected; `admin` middleware on CRUD; `email.confirmation` middleware global on all web routes

### Middleware (`bootstrap/app.php`)
- `admin` → `IsAdmin`, `email.confirmation` → `CheckEmailConfirmation` (both in web group)

### Frontend
- **Vue 3 SPA** — `resources/js/app.js` calls `mountApp()` which mounts on `<div id="app">` with router (`resources/js/router.js`); data injected via `window.__INITIAL_DATA__`
- `resources/js/vue.js` also exports `mount()`/`mountComponent()` for island-style use
- Tailwind CSS v4 via `@tailwindcss/vite`; FontAwesome Free v7

## Conventions
- Form Request classes for validation (`app/Http/Requests/`)
- Models use `$casts` property — **except User** which uses `casts()` method
- Success messages in Chinese (e.g., `'链接创建成功'`)
- `icon_type` values: `emoji` (uses `icon`), `font-awesome` (uses `icon`), `image` (uses `icon_url`)
- `DB::` raw queries only in `AuthController` (password reset tokens)

## Testing (Pest v4)
- `test()` function only, **not** `it()`
- Uses `RefreshDatabase` trait (configured in `tests/Pest.php`)
- SQLite in-memory (`:memory:`) — `phpunit.xml`
- Factories in `database/factories/`

## Gotchas
- `apps/` and `packages/chunkstack-core/` are empty/unused scaffolding — ignore them
- Email confirmation middleware runs on ALL web routes (including `/`); account for this in tests
- `composer test` runs `config:clear` first — after config changes use `composer test` not `php artisan test`
- Laravel Herd serves at `chunkstack.test`; don't run `php artisan serve`
- `laravel-boost` MCP configured in `opencode.json`
