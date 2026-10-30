# Minimal PDF demo

Work only in this demo repository. Do not modify the sibling toolkit via its path dependency; keep `symlink: false`. No remotes, pushes, releases or publication without explicit permission.

This app has one public route: `/invoices/1001/pdf`. No landing page, authentication, database, frontend assets or design/styling. Design remains Claude Code's responsibility.

## Setup and checks

- Follow README fresh-checkout steps; PHP 8.3/8.4, Composer 2, Node **24.21.0**, npm 11.
- `composer install`; `vendor/bin/pdf-testing install` (network needed at setup only).
- `composer test` — full suite, visual intentionally opt-in.
- `composer test:pdf` — portable real PDF text/page assertions through HTTP.
- `composer test:visual` — compare reviewed darwin/arm64 snapshot.
- `composer snapshot:update` — explicit baseline replacement ONLY after intentional changes; inspect PNG before committing. Never update baselines during ordinary tests or CI.
- `composer validate --strict` — check manifest and the pinned lockfile.
- `find bootstrap config public routes tests -name '*.php' -print0 | xargs -0 -n1 php -l`; `php -l artisan`

Use TDD for app behavior; assert through the real HTTP route, not mocked generator calls. Keep dependency licences intact. Commit the lockfile and reviewed baseline, not `.env`, vendor, caches, logs or diff artifacts.
