# TODO

## Done (November 2026 audit)

- [x] Authorization: `ArchivePolicy` / `UserPolicy` on every archive-scoped API route; per-archive permissions (`<permission>.<archive id>`); only Manager/Archivist/Viewer assignable; validated input instead of `$request->all()`
- [x] Image safety: ownership checks on image delete/reorder, no path traversal in moves
- [x] Bug fixes: subscription lookup, address/company policies, upload list keys and sync, dialog/toast races, settings validation, model relations
- [x] Upload limit 32 MB (`config/media.php`, `resources/js/spa/config/upload.js`), larger resize memory, max 12000 px per side
- [x] Laravel 11 → 13, PHP ^8.3, laravel-permission 8, Pest 4; `composer.lock` committed
- [x] Removed packages: ray, breeze, sail, ignition, laravel-lang (malware incident), log-viewer, wiretap
- [x] Images deleted with their record/category/archive; `media:purge-temp` (hourly); `media:prune-orphans` for leftovers
- [x] SPA follows per-archive abilities (`userStore.canArchive`), route guards, lazy-loaded routes
- [x] Performance: record list in a constant 11 queries (was one per record), new indexes, main JS bundle 640 kB → 97 kB
- [x] Slim Laravel 11+ structure (`bootstrap/app.php`, `AppServiceProvider`, `routes/console.php`)
- [x] Favorite toggle is a POST
- [x] Roles per archive: changing a role on one archive keeps the others; `model_has_roles` key includes `archive_id` (migration `2026_11_03_140000`)
- [x] Dead files removed (seo config, `Sidebar-New.vue`, `InviteUser.vue`, Cards/Grid icons)
- [x] Record list paginated on the server (48 per page, search/filters/sort in the API, infinite scroll)
- [x] Test suite green (80 tests, in-memory SQLite)

## Servers (before going live)

- [ ] Work through the server checklist (Claude Doc): Laravel-Lang malware check, PHP 8.3+, migrations, scheduler cron, `media:prune-orphans` dry run, smoke tests

## Next

- [ ] Shared welcome password: per-user invite links instead of one password for all invitations (postponed)
- [ ] Merge duplicate Vue components: Create/UpdateUserPermissions, record Create/Update, Address/BillingAddress, the two delete views
- [ ] Search input in the record sidebar has no accessible label (`aria-label` isn't passed through by `components/forms/Search.vue`)

## Archive cover image (Titelbild)

- [x] if an existing image is replaced, delete the old image
