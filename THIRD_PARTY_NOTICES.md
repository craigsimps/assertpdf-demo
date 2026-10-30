# Dependency licences

The MIT licence in this application's root applies to the application, not to its dependencies. Composer/npm install upstream packages with their own copyright and licence files intact. Installed dependency directories are intentionally not committed. Retain their notices and satisfy their terms if redistributing dependencies, modified or unmodified.

| Component | Installed version | Licence / installed notice |
| --- | --- | --- |
| Laravel framework | 12.70.2 | MIT, `vendor/laravel/framework/LICENSE.md` |
| barryvdh/laravel-dompdf | 3.1.2 | MIT, `vendor/barryvdh/laravel-dompdf/LICENSE` |
| Dompdf | 3.1.6 | LGPL-2.1, `vendor/dompdf/dompdf/LICENSE.LGPL` |
| Dompdf PHP font library | 1.0.2 | LGPL-2.1-or-later, `vendor/dompdf/php-font-lib/LICENSE` |
| Dompdf PHP SVG library | 1.0.2 | LGPL-3.0-or-later, `vendor/dompdf/php-svg-lib/LICENSE` |
| Pest | 3.8.6 | MIT, `vendor/pestphp/pest/LICENSE.md` |
| AssertPDF (PDF testing toolkit) | 0.1.0 (local path override of `^0.1`) | MIT, `vendor/craigsimps/assertpdf/LICENSE` |
| pdfjs-dist | 6.4.299 | Apache-2.0, toolkit `node_modules/pdfjs-dist/LICENSE` |
| @napi-rs/canvas | 1.1.0 | MIT, toolkit `node_modules/@napi-rs/canvas/LICENSE` |
| pixelmatch | 8.0.0 | ISC, toolkit `node_modules/pixelmatch/LICENSE` |
| pngjs | 7.0.0 | MIT, toolkit `node_modules/pngjs/LICENSE` |

This is a guide to the main components, not an exhaustive replacement for upstream notices. `composer licenses` lists the complete installed PHP graph; the toolkit's package lock and installed npm package notices cover the renderer graph and native platform packages. Dompdf's bundled font notices also remain in its installed `lib/fonts` directory. No dependency source or licence file has been altered by this demo.
