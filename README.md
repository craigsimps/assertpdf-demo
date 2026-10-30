# Laravel PDF testing demo

A deliberately minimal Laravel 12 app for [toolkit milestone 3, part 1](https://github.com/craigsimps/laravel-pdf-testing/issues/3). `GET /invoices/1001/pdf` renders an actual one-page invoice with `barryvdh/laravel-dompdf`. Tests go through Laravel's HTTP test client and inspect the resulting PDF using the toolkit's existing `PdfTesting` expectations, not HTML or a PDF fake.

There is no landing page (`/` returns 404), authentication, database, frontend build, CSS, browser or external invoice service. The invoice is fixed sample data: 2 × GBP 50.00 + 20% VAT = **GBP 120.00**. All design/styling is intentionally deferred to Claude Code. This is a local development example, not a production invoice service.

## Fresh local checkout setup

Prerequisites: PHP 8.3 or 8.4 with Laravel/Dompdf extensions (including DOM, mbstring and XML), Composer 2, **Node 24.21.0** and npm 11. Verified here with PHP 8.4.19, Composer 2.8.9, npm 11.19.0, macOS 25.6.0 / arm64. There is no public demo remote yet; these steps apply to a local clone/copy of this repository.

1. Place the two repositories side by side:

   ```text
   parent/
     laravel-pdf-testing/       # toolkit, milestone-3-release-readiness branch
     laravel-pdf-testing-demo/  # this app
   ```

   Check out `milestone-3-release-readiness` in the toolkit. The path repository also declares a Composer version override for `dev-milestone-3-release-readiness`; this supports an equivalent detached checkout but does not verify its contents. Use a trusted copy of that branch. The toolkit is not on Packagist yet.

2. From the demo directory, create runtime directories and environment before installation:

   ```sh
   mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/fonts
   cp .env.example .env
   composer install
   php artisan key:generate
   node --version # must print v24.21.0
   vendor/bin/pdf-testing install
   ```

   `composer.lock` pins all 124 PHP packages. Framework/generator constraints stay within their supported major versions; the lockfile preserves the exact tested versions. `composer validate --strict` checks the manifest and lockfile. Do not run `composer update` for routine setup.

   The path repository uses **`symlink: false`**: Composer mirrors the toolkit into this app's vendor directory. The supported installer runs the toolkit's pinned npm install inside that copy, never in the sibling toolkit checkout. There is no application `package.json`, npm build or frontend asset pipeline. Do not run npm in the app root. Installation needs network access; assertions run locally without fetching resources.

   To pick up intentional changes from the sibling toolkit, run `composer reinstall craigsimps/laravel-pdf-testing`, then `vendor/bin/pdf-testing install` again. A path dependency is a local working-copy input, not a reproducible remote release, even with a lockfile. Record/review the toolkit revision separately when updating it.

3. Run the portable text/count suite, then optionally the pinned-machine visual comparison:

   ```sh
   composer test        # 3 passed, 1 deliberately skipped visual test
   composer test:pdf    # text, page count and absent routes only
   composer test:visual # compare committed macOS/arm64 baseline; NEVER updates it
   ```

   No database creation/migration, login, Chrome or server process is needed for these HTTP tests. The testing key in `phpunit.xml` is public test-only material; `.env` uses your independently generated local key and is never committed.

4. Inspect the endpoint yourself if desired:

   ```sh
   php artisan serve --host=127.0.0.1 --port=8000
   # In another terminal:
   curl -f http://127.0.0.1:8000/invoices/1001/pdf -o /tmp/invoice-1001.pdf
   ```

   Open that PDF with your local viewer. Stop the development server with Ctrl-C.

## Explicit visual baseline workflow

The committed `tests/pdf-snapshots/invoice-1001/manifest.json` and `page-1.png` were generated and inspected on **darwin/arm64, Node v24.21.0**, using PHP 8.4.19 and the locked Composer dependencies. The manifest pins pdfjs-dist **6.4.299**, @napi-rs/canvas **1.1.0**, pixelmatch **8.0.0**, pngjs **7.0.0**, scale 1 (72 DPI). It is plain Dompdf default typography, not approved UI design.

Cross-OS pixel identity is not promised; the toolkit rejects incompatible renderer environments. Run `composer test:pdf` anywhere supported. Only run the committed visual baseline on its matching environment. For Linux or another OS, intentionally generate and review a baseline on that environment rather than bypassing the environment check. Do not replace the macOS baseline casually or expect both environments to share it.

To intentionally create/replace a baseline after reviewing an invoice change:

```sh
composer snapshot:update
# Equivalent explicit flags:
# PDF_TESTING_UPDATE_SNAPSHOTS=1 PDF_DEMO_VISUAL=1 vendor/bin/pest --group=visual
composer test:visual
git diff -- tests/pdf-snapshots
```

Open and review `page-1.png` before committing the update. Normal tests never set `PDF_TESTING_UPDATE_SNAPSHOTS`; leave it unset in your shell and CI. Missing baselines fail when opted in, rather than being silently created. Comparison images/diffs under `tests/pdf-snapshots/*/artifacts/` are ignored; baseline PNGs and manifests are tracked. Do not regenerate baselines automatically in CI.

## Pinned dependencies and future release

Current direct dependencies: Laravel **12.70.2**, barryvdh/laravel-dompdf **3.1.2**, Pest **3.8.6**, local toolkit **dev-milestone-3-release-readiness**. Locked Dompdf is **3.1.6** and PHPUnit is **11.5.50**. See `composer.lock` for the complete exact transitive graph and upstream source references.

**Future Packagist installation — NOT available yet.** After an authorised `0.1` release exists, replace the local path repository/development constraint, deliberately refresh and review the lockfile, then reinstall the renderer:

```sh
# Future only; do not run against today's unpublished package:
composer config --unset repositories.0
composer require --dev 'craigsimps/laravel-pdf-testing:^0.1' -W
vendor/bin/pdf-testing install
```

## Licensing

Application source is MIT (see `LICENSE`). Dependencies retain their own licences and notices; the application's MIT licence does **not** relicense dependencies. In particular Dompdf and its font library are LGPL-2.1; the wrapper and Laravel are MIT. Composer and the renderer installer retain upstream licence files in `vendor/` and the toolkit's `node_modules/`. Do not remove those notices when redistributing installed dependencies. `composer licenses` lists the PHP dependency licences; see `THIRD_PARTY_NOTICES.md` for the main components.
