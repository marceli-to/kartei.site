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

## Done (Vue review, 4 November 2026)

- [x] Review of all Vue parts (components, views, stores, services); open points below under "Vue review"
- [x] Bugs: record edit fields matched by uuid in settings order (text landed in the wrong field), sidebar gets the archive uuid, Category register numeral type + auto IDs on Enter, Card/Tags fresh rows + stable keys + failed deletes keep the row + focus on new input, no double archive delete, company/user delete awaited, Address typo, loading counter, user store retries init, load-more retry button
- [x] Dialog awaits async `onConfirm` (busy state, returning `false` keeps it open), German default labels, title/description ids
- [x] Logout is a POST; profile icon links to settings; Stripe note dev-only; "Sie" everywhere; `loesungen@kartei.site`; German upload texts; archive list search
- [x] Account deletion deletes the archives the user owns (Admin without pivot role, no other Admin); invited members keep their accounts; confirm dialog lists the archives (`GET /api/user/owned-archives`)
- [x] Found in the browser test: Super Admin not recognised in the SPA (`RoleResource` now `name` + translated `label`), user list blocked at the seat limit (only "Benutzer/in" is disabled now), "Abbrechen" in permissions asks before discarding
- [x] German validation attribute names (Kürzel, Hausnummer, …; `city` is "Ort"); toast layer lets header clicks through, sits above dialogs, aria-live
- [x] Form errors shown in the label (`InputLabel` `error` prop), placeholders keep their text, errors clear on first change, `aria-invalid`; `composables/useFormErrors.js` used by all 10 forms
- [x] Test suite green (89 tests)
- [x] Review bugs: `handleApiError` keeps its message on the error (`error.userMessage`), `notifyError(error, fallback)` toasts it unless the user was redirected or the request was aborted; 401 -> "Sitzung abgelaufen" page -> login (419 page merged into it); 419 refreshes the CSRF cookie and retries once; 403/404 only go to the error page for GET requests
- [x] Theme saves queued (latest click wins) and reverted on failure; upload errors toasted (422 reason), no `NaN%`, reorder only on a real move (`@update`), no drag flicker, uploads through the `api` instance (no timeout, own progress bar); `normalizeImageEntry` naming documented (not swapped: follows the media columns)
- [x] Favorite in-flight guard + error toast + `aria-pressed`; record list aborts stale requests; company search with empty street/city; BasicInformation: dead watcher removed, create success no longer reported as failure; `isLoading` starts `true` (Theme, BasicInformation, users subscription note); "Meine Karteien" shows a load error with retry; `ButtonPrimary` `loading` prop; Slideshow keys, German alt and button labels; no `width="auto"`

## Servers (before going live)

- [ ] Work through the server checklist (Claude Doc): Laravel-Lang malware check, PHP 8.3+, migrations, scheduler cron, `media:prune-orphans` dry run, smoke tests

## Next

- [ ] Slider: check Tab focus stays in the active slide and disabled entries on a new archive (deep links and "Admin creating an archive lands on its settings" verified 2026-11-04)
- [ ] Slider layout uses hard-coded offsets that offset each other (`mt-48`/`mt-58`/`-top-60`/`pt-38`, `Slide` `pull` = `-top-24`, `-top-4` in the delete views); clean up together
- [ ] Shared welcome password: per-user invite links instead of one password for all invitations (postponed)
- [ ] `App\Http\Requests\User\StoreRequest` is imported in `UserController` but unused
- [ ] Countries are stored as German names; switch to ISO codes (with a data migration) once billing/invoices need it

## Vue review: open points

Placeholders kept on purpose until implemented: Kontakt/Impressum/Datenschutz/Vertragsbedingungen links (`href=""`), record list actions (Ordnen, Kacheln, Liste, Bilder, Export, Teilen), "Meine Karteien" sort/layout selects and "Duplizieren".

### Cross-cutting (accessibility, consistency)
- [ ] `javascript:;` / `href=""` anchors used as buttons: Password toggle, Search clear, Collapsible, InfoBox close; icon-only buttons without `aria-label` (remove buttons in Category/Card/Tags, slider close, upload delete, burger menu, error page back links)
- [ ] Wrapper-root inputs swallow attributes: `Password`, `Search`, `Select` need `inheritAttrs: false` + `v-bind="$attrs"` on the input (search fields have no accessible name)
- [ ] Load/save errors that only reach the console: show a toast with `notifyError()` (done in Theme, upload, Favorite, records list, Company, users Index, BasicInformation; ~15 left: `grep -rn console.error resources/js/spa`)
- [ ] Focus: no focus ring on `sr-only` radios (`RadioGroup`, `SelectButtons`), no `<legend>`; dialog has no focus trap/restore, closes on mouseup after a drag from inside

### Structure and duplication
- [ ] Merge: record Create/Update (`RecordForm.vue` + `useArchiveMeta()`), Address/BillingAddress (`AddressForm.vue`), Create/UpdateCompany, Create/UpdateUser, the two delete views, 401/403/404 pages
- [ ] `formatNumber`/`toRoman` duplicated in `Category.vue` and `StructureDialog.vue` -> `utils/numbering.js`; confirm-then-delete helper
- [ ] Split `archive/Category.vue` (~540 lines): `useStructureForm`, `StructureRow`, `StructurePreview`
- [ ] Icons: 23 files with mixed `class`/`classes` props (`Tiles`, `List` ignore it) -> one `<Icon name variant>`; buttons: Primary/Auth/Action/Sort -> one `Button` with variants (`Action.vue` template duplicated)
- [ ] `ImageUpload`: one `v-model` instead of `modelValue` + `existingImages` (three copies of the list)
- [ ] Folders: `partials/` vs `components/` in views, stores/composables inside component folders, `media/upload/composable` (singular); two `SubscriptionInfo.vue`
- [ ] API layer: `deleteUser`/`updateUser` in two modules, `user/index.js` mixes company/theme/address/subscription

### Dead code and dependencies
- [ ] Delete `views/Archives.vue`, `dialog/ContentExample.vue`, `users/partials/SubscriptionInfo.vue`, `icons/ChevronUp.vue`, the commented block in `App.vue` and its title wiring (`pageComponent`, Header `title` prop)
- [ ] Unused props: `Primary.submitting`, `Sort.isAscending`, `Search.id`/`error`; unused upload helpers (`retryFailed`, `reset`, `formatFileSize`, …)
- [ ] `package.json`: drop the `mix` scripts and `vue-axios`, `alpinejs` listed twice, `uuid` -> `crypto.randomUUID()`, `axios` to `dependencies`
- [ ] Upload files use plain axios (no loading/error handling); Alpine loaded on the SPA shell (`footer.blade.php`)
- [ ] `tailwind.config.js`: 250 hand-written spacing entries (generate with a loop)
- [ ] Prettier/ESLint config and one run (semicolons, quotes, attribute order)

## Archive cover image (Titelbild)

- [x] if an existing image is replaced, delete the old image
