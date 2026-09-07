<?php
/**
 * PHP 8 compatibility shim for this Laravel 5.2 application.
 *
 * Laravel 5.2 and its Symfony 2.8 dependencies predate PHP 8. This applies a
 * small set of source patches to vendor/ that keep the framework working under
 * PHP 8. vendor/ is not committed - composer builds it, and every build rewrites
 * it, so CI and the deploy both re-run this afterwards. It is idempotent.
 *
 * Patches:
 *   1-2. HandleExceptions bootstrapper calls error_reporting(-1) and rethrows
 *        every reported error as an ErrorException, so the first PHP 8
 *        deprecation after boot becomes a blank HTTP 500. Keep E_DEPRECATED out
 *        of both error_reporting() and the rethrow path.
 *   3.   Query\Builder declares `public $wheres;` (null). Under PHP 8,
 *        Builder::callScope() does `count($query->wheres)` when a scope is
 *        applied to a query that has no wheres yet -> count(null) is a fatal
 *        TypeError (e.g. any SoftDeletes/scoped model's index). Initialise it to
 *        [] as newer Laravel does.
 *   4-6. Carbon::setLastErrors() type-hints `array $lastErrors`, but PHP 8's
 *        DateTime::getLastErrors() returns `false` (not an array) when there
 *        are no errors, at both call sites (constructor and
 *        createFromFormat()) -> fatal TypeError on every session read/write
 *        and any date parsing. Coerce false to [] at both call sites, and
 *        give the setter itself a safe default.
 *   7.   helpers.php's str_replace_array() calls preg_replace() once per
 *        binding without checking the binding's type. If a query ever gets a
 *        non-scalar binding (e.g. an array from a mis-posted form field),
 *        QueryException::formatMessage() crashes with a PHP 8 preg_replace()
 *        TypeError while trying to report the *original* SQL error, masking
 *        it behind a blank 500. Cast non-scalar replacement values to a
 *        JSON string so the real error message always surfaces.
 *
 * Usage:  php php8-compat-patch.php  [--check]
 *   --check   report whether all patches are applied; exit 1 if any is missing.
 *             The deploy runs this so it fails loudly rather than shipping an
 *             unpatched vendor/ and 500-ing.
 */

$root = __DIR__;
$fw   = $root . '/vendor/laravel/framework/src/Illuminate';

// Each patch: file, the original snippet, and its patched form.
$patches = [
    [
        'file'    => $fw . '/Foundation/Bootstrap/HandleExceptions.php',
        'search'  => 'error_reporting(-1);',
        'replace' => 'error_reporting(E_ALL & ~E_DEPRECATED);',
    ],
    [
        'file'    => $fw . '/Foundation/Bootstrap/HandleExceptions.php',
        'search'  => 'if (error_reporting() & $level) {',
        'replace' => 'if ((error_reporting() & $level) && ! ($level & E_DEPRECATED)) {',
    ],
    [
        'file'    => $fw . '/Database/Query/Builder.php',
        'search'  => "\n    public \$wheres;\n",
        'replace' => "\n    public \$wheres = [];\n",
    ],
    [
        'file'    => $root . '/vendor/nesbot/carbon/src/Carbon/Carbon.php',
        'search'  => 'static::setLastErrors(parent::getLastErrors());',
        'replace' => 'static::setLastErrors(parent::getLastErrors() ?: []);',
    ],
    [
        'file'    => $root . '/vendor/nesbot/carbon/src/Carbon/Carbon.php',
        'search'  => '$lastErrors = parent::getLastErrors();',
        'replace' => '$lastErrors = parent::getLastErrors() ?: [];',
    ],
    [
        'file'    => $root . '/vendor/nesbot/carbon/src/Carbon/Carbon.php',
        'search'  => 'private static function setLastErrors(array $lastErrors)',
        'replace' => 'private static function setLastErrors(array $lastErrors = [])',
    ],
    [
        'file'    => $fw . '/Support/helpers.php',
        'search'  => "        foreach (\$replace as \$value) {\n            \$subject = preg_replace('/'.\$search.'/', \$value, \$subject, 1);\n        }",
        'replace' => "        foreach (\$replace as \$value) {\n            if (! is_scalar(\$value) && ! is_null(\$value)) {\n                \$value = json_encode(\$value);\n            }\n            \$subject = preg_replace('/'.\$search.'/', \$value, \$subject, 1);\n        }",
    ],
];

$checkOnly = in_array('--check', $argv, true);
$allApplied = true;
$didWork = false;

foreach ($patches as $p) {
    if (! is_file($p['file'])) {
        fwrite(STDERR, "FAIL: not found: {$p['file']}\n");
        fwrite(STDERR, "      Run `composer install` first, or check you are in the project root.\n");
        exit(1);
    }

    $contents = file_get_contents($p['file']);
    $hasPatched  = strpos($contents, $p['replace']) !== false;
    $hasOriginal = strpos($contents, $p['search'])  !== false;

    if ($checkOnly) {
        if (! $hasPatched) {
            $allApplied = false;
            fwrite(STDERR, "MISSING patch in {$p['file']}\n");
        }
        continue;
    }

    if ($hasPatched) {
        continue; // already applied
    }
    if (! $hasOriginal) {
        fwrite(STDERR, "FAIL: expected snippet not found in {$p['file']} - vendor version may differ.\n");
        exit(1);
    }

    $contents = str_replace($p['search'], $p['replace'], $contents);
    if (file_put_contents($p['file'], $contents) === false) {
        fwrite(STDERR, "FAIL: could not write {$p['file']} (permissions?)\n");
        exit(1);
    }
    echo "Patched " . basename($p['file']) . " for PHP 8.\n";
    $didWork = true;
}

if ($checkOnly) {
    if ($allApplied) {
        echo "OK: PHP 8 compatibility patches are applied.\n";
        exit(0);
    }
    fwrite(STDERR, "FAIL: vendor/ is NOT fully patched for PHP 8. Run: php php8-compat-patch.php\n");
    exit(1);
}

echo $didWork ? "PHP 8 patches applied.\n" : "Already patched, nothing to do.\n";
exit(0);
