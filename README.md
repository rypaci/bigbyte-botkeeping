# Bigbyte Aqua Ventures

A Laravel-based business management system (billing & invoicing, accounts receivable,
payroll, DTR/attendance, inventory, and water-refilling management).

- **Live site:** <https://botkeeping.bigbyteaquaventures.com/>
- **Framework:** Laravel 5.2
- **Runtime in production:** PHP 8.0.30 on the web sapi (via the `php8-compat-patch.php`
  shim — see [The two PHP versions](#the-two-php-versions))
- **Deploys:** manually, from the Actions tab — see [Deployment](#deployment)

> ⚠️ That URL is the **live site**, and the deploy target. There is no separate staging
> environment — a deploy goes straight there. Test destructive changes locally first.

### Test access

Credentials for the demo login are **not stored in this repo**. Ask the team for them, or
take them from the shared password manager.

They are deliberately kept out of git: anything committed stays in history forever, and the
demo account has full admin rights (Company, Users, Chart of Accounts, Journal, Ledger,
Reports) on a site that is reachable from the public internet.

---

## Requirements

| Tool | Version |
|------|---------|
| PHP | **8.0** — match the server's web runtime, see [The two PHP versions](#the-two-php-versions) |
| Composer | 2.x |
| MySQL | 5.7+ / MariaDB |
| Node + npm | only if rebuilding front-end assets (legacy Laravel Elixir / Gulp — optional) |

---

## Getting started (fresh clone)

```bash
# 1. Clone
git clone git@github.com:rypaci/bigbyte-botkeeping.git
cd bigbyte-botkeeping

# 2. Install PHP dependencies, then apply the PHP 8 shim. The shim is NOT
#    optional and must be re-run after every composer install/update - see
#    "Dependencies" below.
composer install
php php8-compat-patch.php

# 3. Create your environment file
cp .env.example .env

# 4. Generate the app key (fills the empty APP_KEY= line in .env)
php artisan key:generate

# 5. Edit .env with your local DB credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 6. Set up the database — either:
php artisan migrate          # run migrations, OR
#   import a database dump provided by the team

# 7. Make storage writable (Laravel)
chmod -R 775 storage bootstrap/cache

# 8. Run it locally
php artisan serve
# → http://localhost:8000
```

> **Note:** `.env` is gitignored and must **never** be committed. Get production/staging
> values from the team.

### Dependencies

`vendor/` is **not** committed. `composer install` builds it, and the deploy builds it on
the server. Three pieces of `composer.json` config make that possible on PHP 8 — do not
remove them:

| Config | Why |
|---|---|
| `config.platform.php: 8.0.30` | Pins resolution to the server's **web** PHP (see below) so everyone resolves the same tree regardless of local PHP |
| `config.audit.block-insecure: false` | Laravel 5.2 and its tree carry 12 published advisories. Composer 2.x refuses to install them otherwise, so nothing resolves at all |
| `config.allow-plugins.kylekatarnls/update-helper` | Composer 2.2+ blocks unlisted plugins; without this `composer install` aborts |

> ⚠️ `block-insecure: false` means **known vulnerabilities are being installed knowingly**.
> That is inherent to running Laravel 5.2 in 2026, not something this config introduced —
> but it is the reason upgrading off 5.2 matters.

The lock file was regenerated against PHP 8.0.30; the previous one pinned `doctrine/*` at
versions requiring PHP `^5.6 || ^7.0` and could not resolve at all.

### The two PHP versions

The server runs **two different PHP builds**, and this has bitten us:

| | Version | Used for |
|---|---|---|
| **Web (LiteSpeed)** | **8.0.30** | Every HTTP request — `ea-php80`, set by `AddHandler` in `.htaccess` |
| CLI (`php`) | 8.2.32 | `artisan`, cron, anything you run over SSH |

**The web version is the one that matters.** Pinning composer to the CLI's 8.2.32 resolved
packages requiring PHP ≥ 8.1 and generated `vendor/composer/platform_check.php`, which
aborted every request with *"Your Composer dependencies require a PHP version >= 8.1.0"* —
while `php artisan --version` still passed happily under the CLI. A green deploy, a dead site.

So: the deploy runs composer, the shim and the boot check through
`/opt/cpanel/ea-php80/root/usr/bin/php`, and CI builds on PHP 8.0. If you ever check
something by hand over SSH, use that binary, not plain `php`.

`ea-php81`, `ea-php82` and `ea-php83` are installed on the box, so moving the site to a
newer PHP is a `.htaccess` handler change plus a re-resolve — but test it, don't assume.

Laravel 5.2's `HandleExceptions` bootstrapper calls `error_reporting(-1)`
and rethrows **every** reported error as an `ErrorException`. On PHP 8 the 5.2 / Symfony 2.8
tree emits dozens of deprecations, so each one becomes fatal and the framework cannot boot —
every request returns HTTP 500 with nothing in `storage/logs/laravel.log`, because the
failure happens before the logger exists.

`php8-compat-patch.php` keeps `E_DEPRECATED` out of both `error_reporting()` and the rethrow
path. It is idempotent, and `php php8-compat-patch.php --check` exits non-zero if the patch
is missing — the deploy runs that check so an unpatched `vendor/` fails the run instead of
taking the site down.

**Every `composer install` or `composer update` overwrites `vendor/` and undoes the shim.**
Always follow one with:

```bash
php php8-compat-patch.php
php php8-compat-patch.php --check    # confirms it took
```

### Front-end assets (optional / legacy)

The project uses the old Laravel Elixir + Gulp 3 pipeline (`gulpfile.js`). You only need
this if you're changing LESS/CSS:

```bash
npm install
node_modules/.bin/gulp        # or `gulp` if installed globally
```

---

## Git workflow

We keep `main` as a **clean, linear history** — no merge commits. Every feature gets its
own branch off `main`, and changes land via a Pull Request that is **squashed or rebased**
(the "Create a merge commit" button is disabled on the repo).

### Day-to-day

```bash
# 1. Always branch off the latest main
git checkout main
git pull --ff-only origin main
git checkout -b feature/short-description     # naming: feature/… , fix/… , chore/…

# 2. Work and commit normally (see "Commit messages" below for the format)
git add -p
git commit -m "feat: describe the change"

# 3. Push and open a Pull Request on GitHub
git push -u origin feature/short-description

# 4. If main has moved on, REBASE your branch onto it (do NOT merge main in)
git fetch origin
git rebase origin/main
#    On conflict: fix files → `git add <files>` → `git rebase --continue`
#    Bail out with: `git rebase --abort`

# 5. Re-push the rebased branch (history was rewritten)
git push --force-with-lease

# 6. Once approved, merge the PR with "Squash and merge" (preferred) or "Rebase and merge".
#    The branch auto-deletes after merge.
```

### Commit messages

Use [Conventional Commits](https://www.conventionalcommits.org): start every message with a
**type prefix**, then a short, lowercase, imperative summary.

```
<type>: <what changed>
```

| Prefix | Use it for | Example |
|---|---|---|
| `feat:` | a new feature | `feat: add cash invoice PDF export` |
| `fix:` | a bug fix | `fix: stop payroll list crashing on empty DTR` |
| `chore:` | deps, tooling, config — no app behaviour change | `chore: bump intervention/image to 2.7.2` |
| `style:` | formatting only (whitespace, lint) — no logic change | `style: reformat chart of accounts blade` |
| `docs:` | documentation only | `docs: document the php 8 shim` |
| `refactor:` | restructuring without changing behaviour | `refactor: extract invoice total calculation` |
| `test:` | adding or fixing tests | `test: cover login validation` |
| `ci:` | GitHub Actions / build pipeline | `ci: run build check on pull requests` |

- Keep the summary under ~72 characters, **imperative mood** ("add", not "added").
- One logical change per commit.
- PRs are **squash-merged**, so the **PR title** must follow the same format — GitHub is
  configured to use the PR title as the commit message on `main`. CI checks this for you.

### Rules

- ✅ Only ever `--force-with-lease` your **own** feature branch. **Never** force-push `main`.
- ✅ Don't rebase a branch someone else is actively committing to.
- ✅ Keep `main` linear: merge only via PR using **Squash** or **Rebase** (never a merge commit).
- ⚠️ Merging to `main` does **not** deploy — deploys are manual, see [Deployment](#deployment).
  Merge freely; shipping is a separate, deliberate step.

#### What GitHub enforces, and what it doesn't

| | |
|---|---|
| ✅ Enforced | Merge commits are disabled — only Squash or Rebase are offered |
| ✅ Enforced | Merged branches auto-delete |
| ✅ Enforced | Squash commits use the **PR title** as the message |
| ⚠️ Checked, not blocking | `PR title` and `CI build check` run on every PR, but a PR can still be merged while they're red |
| ❌ Not enforced | Nothing stops a direct `git push` to `main` |

Required status checks and push protection need **GitHub Pro** on a private repo (the API
returns *"Upgrade to GitHub Pro or make this repository public"*). Until then the last two
rows are honour-system: **don't push straight to `main`, and don't merge a red PR.**

### Recommended one-time local git config

```bash
git config --global pull.ff only            # never auto-create merge commits on pull
git config --global rebase.autoStash true   # auto-stash WIP during a rebase
git config --global rebase.autosquash true  # enable `git commit --fixup` workflow
```

---

## Deployment

Deploys are **manual**. Run [`.github/workflows/deploy.yml`](.github/workflows/deploy.yml)
from **Actions → Deploy → Run workflow**, or:

```bash
gh workflow run deploy.yml --ref main
```

The workflow:

1. Checks out the code, then prunes what shouldn't ship — `.git`, `.github`, `storage`,
   `tests`, and defensively any `*.sql`, `phpinfo.php` or `composer.phar` that reappears.
2. Uploads the app to the server via SFTP.
3. On the server: snapshots `vendor/`, runs `composer install --no-dev`, applies
   `php8-compat-patch.php` (verified with `--check`), then clears `bootstrap/cache` and the
   compiled views. If any of that fails, the previous `vendor/` is restored automatically.
4. Asserts `php artisan --version` boots, then curls the site and **fails the run** if it
   doesn't return 2xx/3xx.

The server script runs under `set -euo pipefail`. Without it the script continues past a
failure and exits with the status of its *last* command — which is how a broken deploy
once reported success while `composer install` had failed.

See [Dependencies](#dependencies) for the `composer.json` config the install step depends on.

FTP/SSH credentials are stored as GitHub Actions **secrets** (`FTP_SERVER`, `FTP_USERNAME`,
`FTP_PASSWORD`), not in the repo.

### If a deploy takes the site down

`storage/logs/laravel.log` will be empty — the failure happens before Laravel's logger
exists. Check the framework directly over SSH instead:

```bash
cd /home/bigbyte/public_html/botkeeping
PHP_BIN=/opt/cpanel/ea-php80/root/usr/bin/php   # the WEB php, not plain `php`
"$PHP_BIN" artisan --version            # silence here means the framework can't boot
"$PHP_BIN" php8-compat-patch.php --check
```
