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
| **Web** | **8.0.30** | Every HTTP request — `ea-php80`, set per-domain in cPanel's MultiPHP Manager |
| CLI (`php`) | 8.0.30 | `artisan`, cron, anything run over SSH as the `bigbyte` user |

> As of 2026-10-12 these match: cPanel sets the account's CLI to the same build as the
> domain, and the deploy logs `web php: 8.0.30  cli php: 8.0.30`. They have not always
> matched (the CLI was 8.2.32), and `php` as **root** may still differ — so the deploy
> keeps using the explicit `ea-php80` path rather than trusting `php`.

**The web version is the one that matters.** Pinning composer to the CLI's 8.2.32 resolved
packages requiring PHP ≥ 8.1 and generated `vendor/composer/platform_check.php`, which
aborted every request with *"Your Composer dependencies require a PHP version >= 8.1.0"* —
while `php artisan --version` still passed happily under the CLI. A green deploy, a dead site.

So: the deploy runs composer, the shim and the boot check through
`/opt/cpanel/ea-php80/root/usr/bin/php`, and CI builds on PHP 8.0. If you ever check
something by hand over SSH, use that binary, not plain `php`.

`ea-php81`, `ea-php82` and `ea-php83` are installed on the box, so moving the site to a
newer PHP is a MultiPHP Manager change plus a re-resolve — but test it, don't assume.

> ⚠️ **Both `.htaccess` files are maintained on the server, not here.** The root one
> carries the rewrite into `public/` plus cPanel's managed handler block; `public/.htaccess`
> is Laravel's front controller config. The deploy prunes `public/.htaccess` before upload
> and never matched the root one (`local_path: "./*"` is a glob and skips top-level
> dotfiles), so **a deploy cannot repair either file**. On 2026-10-05 malware (see
> [Security](#security)) replaced both with a deny-all `<FilesMatch>` block and took the
> site down; recovery was by hand. If PHP files start returning 403, or everything 500s, check these two files
> first — changing the PHP version in MultiPHP Manager will appear to do nothing, because
> `.htaccess` overrides it.

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
   `tests`, `public/.htaccess` (server-managed, see above), and defensively any `*.sql`,
   `phpinfo.php` or `composer.phar` that reappears.
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

---

## Security

The `bigbyte` cPanel account was compromised in 2026-10, and this app was the way in. It
lives inside `public_html`, so **every folder outside `public/` was reachable over HTTP** —
including `vendor/`, which then held dev packages (PHPUnit's `eval-stdin.php` runs any PHP
it is sent). Web shells planted under `vendor/` were used to rewrite the WordPress site on
the same account, and this repo's initial import copied one of them,
`app/Exceptions/cache-handler.php`, straight off the infected server — so every deploy
re-uploaded it until it was removed.

What now guards against a repeat:

- **`.github/scripts/malware-scan.sh`** runs first in both CI and Deploy and fails the run
  if any tracked file matches a signature of the shells found on the server. If it fires,
  look at the file — don't widen the exclusions to make it pass.
- **The deploy writes `Require all denied` into `app/`, `bootstrap/`, `config/`,
  `database/`, `resources/`, `storage/`, `tests/` and `vendor/` on every run**, so only
  `public/` serves requests.
- **Never commit files copied from the server.** Rebuild `vendor/` with
  `composer install --no-dev`; dev packages must never exist on the server.

The lasting fix is to move the app out of `public_html` and point the subdomain's document
root at `public/`, which removes the exposure instead of denying it folder by folder. That
also needs `remote_path` in [`deploy.yml`](.github/workflows/deploy.yml) changed to match.

---

## Database migrations

**Deploys never run migrations.** There is one environment and it holds live accounting
data, so a schema change is always a separate, deliberate act.

Run [`.github/workflows/migrate.yml`](.github/workflows/migrate.yml) from
**Actions → Migrate → Run workflow**, *after* the deploy that ships the migration files —
this workflow does not deploy.

1. Run it once with **`pretend` ticked** (the default). It backs up the database, prints
   the SQL each pending migration would execute, and applies nothing.
2. Read that SQL. If it is right, run it again with **`pretend` unticked**.

Every run snapshots the database to `storage/db-backups/pre-migrate-<timestamp>.sql` first
and refuses to continue unless the dump completed. The last 10 are kept. `storage/` is
pruned from deploys, so they survive.

### Why the workflow checks that it actually ran

`appleboy/ssh-action` reports **success** when a host refuses a shell: the remote prints
`Shell access is not enabled on your account!`, exits 0, and the step goes green having done
nothing. Every server-side deploy step was silently skipped that way until 2026-10-12. The
migrate script therefore echoes a sentinel and a following step greps the captured output
for it, so a no-op fails loudly.

If that step fails, shell access is off for the account. Re-enable it with:

```bash
whmapi1 modifyacct user=bigbyte shell=/usr/local/cpanel/bin/jailshell
```

### Running one by hand

Still possible from a root SSH session, and the fallback if Actions is unavailable:

```bash
su -s /bin/bash bigbyte -c "cd /home/bigbyte/public_html/botkeeping && \
  /opt/cpanel/ea-php80/root/usr/bin/php artisan migrate --path=database/migrations --force"
```

Three things matter there. Run it **as `bigbyte`, never as root** — artisan as root leaves
root-owned files in `storage/` and `bootstrap/cache` that the web user cannot write, which
surfaces as a 500 hours later. Use the **`ea-php80`** binary so it matches the web SAPI. And
`--path` takes a **directory**: Laravel 5.2 globs `$path.'/*_*.php'`, so passing a single
filename silently prints `Nothing to migrate`.

### The migrations table was baselined

On 2026-10-12 the `migrations` table was reconciled with reality. One migration
(`2017_01_26_131745_add_discount_col`) had never been recorded despite its effect being in
the schema for years, so `php artisan migrate` kept trying to replay it and died with
`Unknown column 'discount' in 'discounts'` — a later migration had renamed that column to
`name`. Recording it (batch 0) fixed it, and `migrate` now reports `Nothing to migrate` on a
clean tree.

If that symptom ever returns, the cause is the same: a migration applied by hand without
being recorded. Record it rather than editing the migration.

---
