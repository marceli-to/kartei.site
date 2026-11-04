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
- [x] User permission forms merged into `UserPermissions.vue` (one `PUT /user/permissions/{user}`); fixed buttons that re-saved, false "invitation sent" message, discard dialog without changes
- [x] Settings page: unique field ids, Abbrechen in user/company forms no longer submits, taken email on user update is a 422
- [x] Info (lightbulb) icon: views register while visible (`useInfoBox`), no state carried over between slides/pages, auto-opened boxes can be closed
- [x] Demo data: `php artisan db:seed --class=DemoArchiveSeeder` (10 archives with categories, registers, records, tags, images; re-runnable)
- [x] Payment interval validated (`monthly`/`yearly`, was a DB error); option lists moved from `data/` to `config/` (`countries.js`, `paymentIntervals.js`)
- [x] Dev-only UI demo pages (`views/ui`) and the `Users.vue` stub removed
- [x] Settings slider rewritten as one component (`components/slider/Slider.vue`, header `slider/Header.vue`): slides keyed by id, active slide in `?section=<id>`, `inert` on inactive slides, disabled menu buttons; pages keyed by `route.path` (query changes no longer remount); permissions refetched after creating an archive
- [x] Test suite green (83 tests, in-memory SQLite)

## Servers (before going live)

- [ ] Work through the server checklist (Claude Doc): Laravel-Lang malware check, PHP 8.3+, migrations, scheduler cron, `media:prune-orphans` dry run, smoke tests

## Next

- [ ] Slider: check in the browser (deep link `?section=`, disabled entries on a new archive, Tab focus stays in the active slide, Admin creating an archive lands on its settings, not 403)
- [ ] Slider layout uses hard-coded offsets that offset each other (`mt-48`/`mt-58`/`-top-60`/`pt-38`, `Slide` `pull` = `-top-24`, `-top-4` in the delete views); clean up together
- [ ] Shared welcome password: per-user invite links instead of one password for all invitations (postponed)
- [ ] Merge duplicate Vue components: record Create/Update, Address/BillingAddress, the two delete views
- [ ] `App\Http\Requests\User\StoreRequest` is imported in `UserController` but unused
- [ ] Search input in the record sidebar has no accessible label (`aria-label` isn't passed through by `components/forms/Search.vue`)
- [ ] Countries are stored as German names; switch to ISO codes (with a data migration) once billing/invoices need it

## Archive cover image (Titelbild)

- [x] if an existing image is replaced, delete the old image
