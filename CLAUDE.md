# Claude Code Configuration

This file contains project-specific configuration for Claude Code.

## Project Info
- **Name**: kartei.site
- **Type**: Web application (archive / card index SaaS)
- **Stack**: Laravel 13 (slim structure, PHP ^8.3), Vue 3 SPA (Pinia, vue-router), Tailwind 4 (theme in `resources/css/app.css`, no JS config), Vite

## Common Commands
```bash
npm run dev          # Vite dev server
npm run build        # Build assets (public/build is committed)
npm run lint         # ESLint (resources/js)
npm run format       # Prettier --write (resources/js); format:check for CI
php artisan test     # Pest, in-memory SQLite (phpunit.xml), never the dev DB
```

## Project Structure
- `app/Actions/` - Action classes (`execute()`), used by the API controllers
- `app/Policies/` - `ArchivePolicy` (per-archive abilities), `UserPolicy`
- `bootstrap/app.php` - Routing, middleware, exceptions; `AppServiceProvider` holds policies, Super Admin gate, api rate limiter
- `routes/console.php` - Schedule (`media:purge-temp` hourly)
- `resources/js/spa/` - Vue SPA (views, components, stores, router)
- `TODO.md` - Progress notes and next steps

## Notes
- Permissions: self-registered users are "Admin" (account owners); other members get per-archive permissions named `<permission>.<archive id>`. The SPA reads abilities per archive from `/api/user/permissions` (`userStore.canArchive`).
- Record display numbers are positional on purpose and shift when records are deleted.
