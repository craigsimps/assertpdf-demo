# AssertPDF demo

A small Laravel 12 app demonstrating [AssertPDF](https://github.com/craigsimps/assertpdf), a PDF testing toolkit for Laravel. `GET /invoices/1001/pdf` renders an actual one-page invoice with `barryvdh/laravel-dompdf`. Tests call the route through Laravel's HTTP test client and inspect the PDF with AssertPDF's expectations. They check the PDF bytes rather than HTML or a PDF fake.

There is no landing page (`/` returns 404), authentication, database, frontend build, CSS, browser or external invoice service. The invoice is fixed sample data: 2 × GBP 50.00 + 20% VAT = **GBP 120.00**. This is a local development example, not a production invoice service.

## Setup

Prerequisites: PHP 8.3 or 8.4 with Laravel/Dompdf extensions (including DOM, mbstring and XML), Composer 2, **Node 24.21.0** and npm 11. Verified here with PHP 8.4.19, Composer 2.8.9, npm 11.19.0, macOS 25.6.0 / arm64.

1. Place the two repositories side by side:

   ```text
   parent/
     assertpdf/       # the toolkit, https://github.com/craigsimps/assertpdf
     assertpdf-demo/  # this app
   ```

   `composer.json` requires `craigsimps/assertpdf:^0.1`. A local path repository points at `../assertpdf` and gives that checkout a Composer version of `0.1.0`. This lets the demo install from the sibling checkout before the package is on Packagist. If your toolkit clone has another name, change the repository `url` in `composer.json`.

2. From the demo directory, create runtime directories and environment before installation:

   ```sh
   mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/fonts
   cp .env.example .env
   composer install
   php artisan key:generate
   node --version # must print v24.21.0
   vendor/bin/assertpdf install
   ```

   `composer.lock` pins all 124 PHP packages. Framework/generator constraints stay within their supported major versions; the lockfile preserves the exact tested versions. `composer validate --strict` checks the manifest and lockfile. Do not run `composer update` for routine setup.

   The path repository uses **`symlink: false`**: Composer mirrors the toolkit into this app's vendor directory. The supported installer runs the toolkit's pinned npm install inside that copy, never in the sibling toolkit checkout. There is no application `package.json`, npm build or frontend asset pipeline. Do not run npm in the app root. Installation needs network access; assertions run locally without fetching resources.

   To pick up intentional changes from the sibling toolkit, run `composer reinstall craigsimps/assertpdf`, then `vendor/bin/assertpdf install` again. A path dependency is a local working-copy input, not a reproducible remote release, even with a lockfile. Record/review the toolkit revision separately when updating it.

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
# ASSERTPDF_UPDATE_SNAPSHOTS=1 PDF_DEMO_VISUAL=1 vendor/bin/pest --group=visual
composer test:visual
git diff -- tests/pdf-snapshots
```

Open and review `page-1.png` before committing the update. Normal tests never set `ASSERTPDF_UPDATE_SNAPSHOTS`; leave it unset in your shell and CI. Missing baselines fail when opted in, rather than being silently created. Comparison images/diffs under `tests/pdf-snapshots/*/artifacts/` are ignored; baseline PNGs and manifests are tracked. Do not regenerate baselines automatically in CI.

## Pinned dependencies and installing from Packagist

Current direct dependencies: Laravel **12.70.2**, barryvdh/laravel-dompdf **3.1.2**, Pest **3.8.6**, AssertPDF **^0.1** (resolved as 0.1.0 from the local path override). Locked Dompdf is **3.1.6** and PHPUnit is **11.5.50**. See `composer.lock` for the complete exact transitive graph and upstream source references.

**Installing from Packagist:** once `craigsimps/assertpdf` 0.1 is published, remove the local path override, refresh the lock entry and reinstall the renderer:

```sh
composer config --unset repositories.0
composer update craigsimps/assertpdf
vendor/bin/assertpdf install
```

## Licensing

Application source is MIT (see `LICENSE`). Dependencies retain their own licences and notices; the application's MIT licence does **not** relicense dependencies. In particular Dompdf and its font library are LGPL-2.1; the wrapper and Laravel are MIT. Composer and the renderer installer retain upstream licence files in `vendor/` and the toolkit's `node_modules/`. Do not remove those notices when redistributing installed dependencies. `composer licenses` lists the PHP dependency licences; see `THIRD_PARTY_NOTICES.md` for the main components.
