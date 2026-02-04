# ChunkStack - Agent Guidelines

## Project Overview
Laravel 12 navigation site with PHP 8.2+, Pest testing, Laravel Pint formatting, and Vite/Tailwind frontend.

## Commands

### Setup & Development
```bash
composer install                  # Install PHP dependencies
npm install                       # Install Node dependencies
npm run build                     # Build frontend assets
composer run dev                  # Run server, queue, and Vite concurrently
php artisan serve                 # Start Laravel dev server only
```

### Testing
```bash
composer test                     # Run all tests
php artisan test                 # Run all tests
php artisan test --compact       # Run tests with compact output
php artisan test --filter=Name   # Run single test by name
```

### Code Quality
```bash
vendor/bin/pint --dirty           # Format PHP code (required before commit)
```

### Database
```bash
php artisan migrate               # Run migrations
php artisan migrate --fresh      # Drop and re-run all migrations
php artisan db:seed              # Seed database
```

## Code Style Guidelines

### PHP Formatting
- 4-space indentation (no tabs)
- PSR-4 autoloading with `App\` namespace
- Laravel Pint handles formatting; run `vendor/bin/pint --dirty` before commits
- Always use curly braces for control structures

### Types & Declarations
- Explicit return types on all methods/functions
- Constructor property promotion for dependencies
- Nullable types with `?` syntax (e.g., `?string`)
- PHPDoc blocks for complex types and arrays

### Naming Conventions
- Classes: PascalCase (`UserController`, `LinkRequest`)
- Methods/variables: camelCase (`isActive`, `$sortOrder`)
- Constants: UPPER_SNAKE_CASE
- Database columns: snake_case
- Routes: kebab-case (`admin.links.index`)

### Imports
- Use fully qualified class names outside namespace blocks
- Group imports by type: Laravel, third-party, local
- Alphabetical within groups

### Database & Eloquent
- Use Eloquent query builder over raw SQL (`Link::query()` not `DB::table()`)
- Prevent N+1 with eager loading (`->with('category')`)
- Define relationships with return type hints
- Use `casts()` method for attribute casting

### Controllers & Validation
- Form Request classes for validation (never inline)
- Include `attributes()` method for Chinese error messages
- Use route model binding where possible

### Error Handling
- Use Laravel exceptions and responses
- Return `RedirectResponse` with `->with('success')` or `->withErrors()`
- Let Laravel handle most exceptions automatically

## Testing (Pest)
- Feature tests in `tests/Feature/`, unit tests in `tests/Unit/`
- Use `test()` function, not `it()`
- Create factories for models via `php artisan make:factory`
- Use `$this->faker` for test data

## Laravel 12 Structure
- Middleware in `bootstrap/app.php` (no Kernel.php)
- Routes in `routes/web.php` and `routes/console.php`
- Console commands in `app/Console/Commands/` (auto-registered)
