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
- [x] Accessibility: real buttons instead of `javascript:;`/`href=""` anchors (Password toggle, Search clear, Collapsible with `aria-expanded`, InfoBox close), `aria-label` on all icon-only buttons/links; `Password`/`Search`/`Select` pass attributes to the input (`inheritAttrs: false`); radio groups get a `legend` and a visible keyboard focus; dialog moves focus in, traps Tab, restores focus, no close after a drag out of the panel
- [x] All failed requests reach the user: `notifyError()` in every catch block, no `console.error` left in the SPA (favorites load stays silent on purpose)
- [x] Dead code and dependencies: removed `views/Archives.vue`, `ContentExample.vue`, the old `users/partials/SubscriptionInfo.vue`, `ChevronUp.vue`, the commented `App.vue` block and the title wiring, unused upload helpers; `Sort` uses `isAscending` for its label; `package.json` without the mix scripts, `vue-axios` and `uuid`, Alpine once, `axios` in `dependencies`; the SPA shell loads only `spa.js` (Alpine only on Blade pages); Tailwind spacing generated (identical CSS)
- [x] Structure (small items): `utils/numbering.js` (alpha numbering continues with AA, AB, … after Z), `dialogStore.confirmDelete()` for the confirm → delete → toast pattern; stores in `stores/`, shared composables in `composables/`, view parts in `partials/`; `Subscriptioninfo.vue` was tracked with a lower-case "i" (would break a case-sensitive build), now `users/partials/SubscriptionInfo.vue`, the plan list is `settings/partials/PlanInfo.vue`; API: company/subscription/permission calls in their own modules, own account delete is `deleteAccount()`
- [x] Merged: `AddressForm.vue` (address + billing slide, via slide `props`), `CompanyForm.vue`, `UserForm.vue`, shared `AddressFields.vue`, `DeleteSlide.vue` layout, one `ErrorPage.vue` for 401/403/404; native `required` dropped in company/user forms (errors show in the label like everywhere else)
- [x] Record Create/Update merged into `views/records/RecordForm.vue` (both routes, mode from the route name); `useArchiveMeta()` loads and normalizes categories/registers/tags/settings (also used by the record list), replaces `useNormalizeData`
- [x] `archive/Category.vue` split (455 -> 264 lines): `composables/useStructureForm.js` (load, numbering, auto IDs, save), `partials/StructureRow.vue`, `partials/StructurePreview.vue` (example column of the structure dialog, no `v-html`)
- [x] Icons: one `components/icons/Icon.vue` (`<Icon name="cross" variant="small" class="…" />`, single `<svg>` root, `aria-hidden`) with the SVG data in `icons/registry.js` (generated from the 21 old files, which are gone; `Logo.vue` stays); `Action.vue` template no longer duplicated; screenshots of 16 screens pixel-identical before/after
- [x] Buttons: `Primary` + `Auth` -> `components/buttons/Button.vue` (imported as `AppButton`, variants `primary`/`danger`/`secondary`, `loading`); `Action` (list row: link or button with icon) and `Sort` (text toggle) stay separate on purpose, they are different controls
- [x] `ImageUpload`: one `v-model` (`defineModel`), no `existingImages`; `useFileUpload` hands uploads to an `onUploaded` callback, `List.vue` drags the parent's list directly and saves the order in the setter (no copies left)
- [x] Bug found on the way: an address without house number failed with a 500 (`addresses.street_number` was NOT NULL); migration makes it nullable, test added
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

### Structure and duplication

### Tooling
- [ ] Prettier/ESLint config and one run (semicolons, quotes, attribute order)

## Archive cover image (Titelbild)

- [x] if an existing image is replaced, delete the old image
