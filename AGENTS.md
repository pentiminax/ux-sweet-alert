# Repository Guidelines

Repository specifics for contributors and automation. Keep this doc close when adding features or triaging issues.

## Project Structure & Module Organization
- Core PHP bundle lives in `src/` (managers, enums, Twig extension, event listeners) and autoloads under `Pentiminax\UX\SweetAlert`.
- Frontend Stimulus controller is prebuilt at `assets/dist/controller.js`; metadata for the package sits in `assets/package.json`.
- Tests stay in `tests/` with a custom kernel bootstrap in `tests/bootstrap.php`; fixtures/config are colocated there.
- Docs for installation and usage are in `docs/`; example Twig templates are in `templates/`; default Symfony config lives in `config/`.
- Build artifacts and caches reside in `var/` (ignored); Composer dependencies in `vendor/`.

## Build, Test, and Development Commands
- Install PHP dependencies: `composer install`.
- Run the test suite: `./vendor/bin/phpunit` (uses `phpunit.xml.dist` and `.phpunit.cache`).
- Optional static analysis: `./vendor/bin/phpstan analyse src tests` (bring your own level/flags).
- If you modify the Stimulus controller source, rebuild `assets/dist/controller.js` with your bundler of choice before committing (expected output: ESM).

## Coding Style & Naming Conventions
- Target PHP 8.2+, strict typing, PSR-4 namespaces, and 4-space indentation; keep services constructor-injected and classes small.
- Keep interfaces beside implementations (`*ManagerInterface.php`); prefer value objects/enums from `Enum/`.
- Twig helper IDs and Stimulus controller IDs follow `ux_sweet_alert_*` and `sweetalert`.
- Composer packages are sorted via `"sort-packages": true`; avoid trailing commas in JSON.

## Testing Guidelines
- Mirror source namespaces under `Pentiminax\UX\SweetAlert\Tests\...`; name classes with a `Test` suffix and group related scenarios per method.
- For Live/Turbo interactions, assert emitted events and callbacks; favor deterministic checks over timing-based waits.
- Run `./vendor/bin/phpunit` before pushing; note coverage exclusions if you skip files.

## Commit & Pull Request Guidelines
- Use conventional-style prefixes (`feat:`, `fix:`, `chore:`, `docs:`) with an imperative summary, e.g., `feat: add turbo alert action`.
- PRs should state the change, rationale, and testing performed; link issues when relevant and include screenshots/GIFs for UI or behavior changes.
- Call out consumer-facing config changes and document any rebuild steps.

## Security & Configuration Tips
- Do not commit secrets; rely on local `.env` for sensitive values.
- Keep `var/` and other cache/output directories ignored; clear them if tests behave unexpectedly after dependency bumps.
