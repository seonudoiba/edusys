<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Rewrites PHP string literals "./uploads/<rest>" to upload_root() . "<rest>" (phase 9: per-school
// upload folders). Uses the PHP tokenizer, so only string literals in PHP code change; HTML and
// JavaScript in views are untouched. Prints what it changed and what it could not change
// (interpolated strings, constant expressions) for manual review.
//
// Usage: php tools/tenancy/rewrite_upload_paths.php [--dry-run] <dir>...
$args = array_slice($argv, 1);
$dry = in_array('--dry-run', $args, true);
$dirs = array_values(array_diff($args, ['--dry-run']));
if (!$dirs) { fwrite(STDERR, "Usage: php rewrite_upload_paths.php [--dry-run] <dir>...\n"); exit(1); }

$changed = 0; $files = 0; $manual = [];
foreach ($dirs as $dir) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        $path = str_replace('\\', '/', $f->getPathname());
        if (substr($path, -4) !== '.php' || strpos($path, '/third_party/') !== false || substr($path, -18) === '/upload_helper.php') continue;
        $src = file_get_contents($path);
        if (strpos($src, './uploads/') === false) continue;
        $tokens = token_get_all($src);
        $out = ''; $n = 0;
        // Inside a constant expression (const, property default, parameter default, static var) a
        // function call is not allowed: report those instead of rewriting.
        $constExpr = false; $parenDepth = 0; $inParams = false;
        foreach ($tokens as $i => $t) {
            if (is_array($t)) {
                [$id, $text, $line] = $t;
                if (in_array($id, [T_CONST, T_STATIC, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_VAR], true)) $constExpr = true;
                if ($id === T_FUNCTION) { $inParams = true; $parenDepth = 0; }
                if ($id === T_CONSTANT_ENCAPSED_STRING && preg_match('~^([\'"])\./uploads/~', $text, $m)) {
                    if ($constExpr || $inParams) { $manual[] = "$path:$line constant expression: $text"; $out .= $text; continue; }
                    $q = $m[1];
                    $rest = substr($text, strlen($q . './uploads/'));
                    $out .= ($rest === $q) ? 'upload_root()' : 'upload_root() . ' . $q . $rest;
                    $n++;
                    continue;
                }
                if ($id === T_ENCAPSED_AND_WHITESPACE && strpos($text, './uploads/') !== false) {
                    $manual[] = "$path:$line interpolated string: " . trim($text);
                }
                $out .= $text;
            } else {
                if ($t === ';' || $t === '{') { $constExpr = false; }
                if ($inParams) {
                    if ($t === '(') $parenDepth++;
                    if ($t === ')') { $parenDepth--; if ($parenDepth === 0) $inParams = false; }
                }
                $out .= $t;
            }
        }
        if ($n) {
            $files++; $changed += $n;
            echo "$path: $n\n";
            if (!$dry) file_put_contents($path, $out);
        }
    }
}
echo "$changed literals in $files files" . ($dry ? ' (dry run)' : '') . "\n";
if ($manual) { echo "Manual review:\n  " . implode("\n  ", $manual) . "\n"; }
