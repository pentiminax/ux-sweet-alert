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
- Static analysis: `./vendor/bin/phpstan analyse src tests --memory-limit=1G` (the default memory limit crashes it).
- Coding style: `vendor/bin/php-cs-fixer fix --dry-run --diff` must be clean before pushing.
- If you modify the Stimulus controller source, rebuild `assets/dist/controller.js` with your bundler of choice before committing (expected output: ESM).

## Coding Style & Naming Conventions
- Target PHP 8.3+, strict typing, PSR-4 namespaces, and 4-space indentation; keep services constructor-injected and classes small.
- Keep interfaces beside implementations (`*ManagerInterface.php`) for services consumers may replace; internal single-implementation classes (marked `@internal`) get no interface. Prefer value objects/enums from `Enum/`.
- Twig helper IDs and Stimulus controller IDs follow `ux_sweet_alert_*` and `sweetalert`.
- Composer packages are sorted via `"sort-packages": true`; avoid trailing commas in JSON.

## Code Quality
Reference example: `src/Delivery/AlertDelivery.php`.
- **Small public surface, private cases.** One public entry point hides the decision logic; each case lives in its own private method. Classes are `final` by default.
- **Guards first, then dispatch.** Early-return guard clauses at the top, then `match (true)` when the branches are ordered and mutually exclusive, one private method call per arm. No long `if/elseif` chains.
- **Name conditions.** Extract compound boolean checks into `is*` / `has*` / `can*` private predicates.
- **No duplication.** A repeated expression (serialisation, a guard) becomes one private method.
- **No magic strings.** Header names, template paths, event and JSON keys are class constants (`private const` unless consumers or tests need them).
- **Imports, not FQCNs.** Import classes with `use`; `Foo::class` does not trigger autoloading, so it is safe in `class_exists()` for optional packages.
- **Types everywhere.** Typed closures and return types; `@param Foo[]` / `@return` array shapes on arrays for phpstan.
- **JSON.** Use `JSON_THROW_ON_ERROR` with `try/catch`, never `json_last_error()`. Decode as objects (`json_decode($s, false)`) when re-encoding someone else's data so `{}` stays `{}`; add `JSON_INVALID_UTF8_SUBSTITUTE` when encoding alert content.
- **Check before side effects.** Decide whether an operation applies before consuming state (draining the alert queue, reading the session); never start a session just to look for alerts.
- **No speculative abstraction.** No strategy interfaces or class hierarchies for a single implementation; add a seam only when something actually varies across it.
- **Comments say why.** One line, only for what the code cannot show.

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

## Additional Information
- Always use context7 when I need code generation, setup or configuration steps, or library/API documentation. This means you should automatically use the Context7 MCP tools to resolve library id and get library docs without me having to explicitly ask.
