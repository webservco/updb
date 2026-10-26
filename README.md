# UPDb

UPDb, the Underground Press Database: a PHP and MySQL web app to catalogue fanzines and other underground publications
(publications, issues, formats, contents, languages, countries), with user accounts, personal collections, want and trade
lists, an admin area and downloads, in English and Romanian. I started it in 2013 on my WEb framework; in 2024 and 2025 it
moved to DDEV and to the last version of WEb7, updated to run on PHP 5.6 to 8.4.

This repository is the code only, as of 2026-10: one commit without the history. The first version, with its history
from 2013 and 2014, is [updb-2013-2014](https://github.com/webservco-legacy/updb-2013-2014).

## What is in it

- `src/app/`: the app (controllers, modules, the templates, the configs in `etc/`, the translations in `i18n/`);
- `src/core/`: WEb7, the framework, bundled (see also [web7](https://github.com/webservco-legacy/web7));
- `public/`: the web root (`index.php`, the skin, the scripts, the environment files in `etc/`);
- `resources/storage/database/`: the MySQL Workbench model of the schema, the lookup data, the download tables and some
  queries;
- `docs/old/`: my first notes on the idea;
- `.ddev/`: the DDEV set-up (PHP 8.4, Apache, MariaDB).

## Set-up

1. `ddev start`.
2. The database: forward-engineer the schema from `resources/storage/database/c1_updb.mwb` (MySQL Workbench), then run
   `structure_download.sql`, `inserts.sql` and `data/data_download.sql`.
3. The configs: the development values work with DDEV as they are. For production, fill in the `CHANGE_ME` values and the
   example host names in `src/app/etc/lib/database.mysqli.cfg.xml`, `smtp.cfg.xml` and `mail.cfg.xml`, and the paths and
   the address in `public/etc/settings.cfg.xml`; copy the matching `public/etc/.env.<environment>` to `public/etc/.env`.

Left out: the history, the database dumps (other people's accounts), the cover images and the material of the zines
(other people's work), the list of zines and a web font without a licence. The passwords, the hosts and the mailbox are
placeholders.

## Third-party code

Bundled, each under its own licence: jQuery 1.10.2 and jQuery UI 1.10.3 with its UI Lightness theme (MIT), PhotoSwipe
3.0.5 and klass (MIT), html5shiv (MIT or GPL v2), the Bitstream Vera Sans Mono font (Bitstream Vera licence).

## Licence

New BSD (`LICENSE`), as the file headers say.
