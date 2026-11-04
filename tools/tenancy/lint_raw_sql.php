<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
/**
 * Static check for hand-written SQL that touches school-owned tables without school_id.
 *
 * Finds every ->query(...) call (database and Datatables) in application/, reconstructs
 * the SQL text it runs from string literals (directly in the call, or assigned to the
 * argument variable with = / .= in the same function), and applies the same rule as
 * the runtime guard (libraries/tenancy/Tenant_raw_sql_guard.php): a statement that names
 * a school-owned table after FROM/JOIN/UPDATE/INTO must mention school_id.
 *
 * Usage:
 *   php tools/tenancy/lint_raw_sql.php                 # report problems, exit 1 if any
 *   php tools/tenancy/lint_raw_sql.php --all           # also list compliant calls
 *   php tools/tenancy/lint_raw_sql.php --summary       # counts per file only
 *   php tools/tenancy/lint_raw_sql.php --show FILE     # print the source lines that build each flagged SQL
 *   php tools/tenancy/lint_raw_sql.php path/to/file.php ...
 *
 * Statuses:
 *   MISSING  names school-owned tables, never mentions school_id
 *   UNKNOWN  SQL text could not be reconstructed (built elsewhere); review by hand
 *   OK       compliant, or touches no school-owned table
 * A call can be exempted with a trailing comment on its line: // tenancy-lint: ok <reason>
 */

$root = dirname(__DIR__, 2);
define('BASEPATH', $root . '/system/');
define('APPPATH', $root . '/application/');
require APPPATH . 'libraries/Tenant.php';
require APPPATH . 'libraries/tenancy/Tenant_raw_sql_guard.php';
$config = array();
require APPPATH . 'config/tenancy_tables.php';
$guard = new Tenant_raw_sql_guard('throw', array_merge($config['tenancy_tenant_tables'], $config['tenancy_hybrid_tables']), sys_get_temp_dir());

$args = array_slice($argv, 1);
$showAll = in_array('--all', $args, true);
$summary = in_array('--summary', $args, true);
$show = in_array('--show', $args, true);
$paths = array_values(array_filter($args, function ($a) { return strpos($a, '--') !== 0; }));
if (!$paths) {
    $paths = array();
    $skipDirs = array('third_party', 'language', 'logs', 'cache', 'migrations', 'seeds', 'tenancy');
    $it = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator(APPPATH, FilesystemIterator::SKIP_DOTS),
        function ($f) use ($skipDirs) { return !($f->isDir() && in_array($f->getFilename(), $skipDirs, true)); }
    ));
    foreach ($it as $f) {
        $p = str_replace('\\', '/', $f->getPathname());
        if (substr($p, -4) !== '.php' || strpos($p, '/third_party/') !== false || strpos($p, '/libraries/tenancy/') !== false
            || strpos($p, '/language/') !== false || strpos($p, '/controllers/Tenancy_test.php') !== false
            || strpos($p, '/libraries/Datatables.php') !== false || strpos($p, '/migrations/') !== false || strpos($p, '/seeds/') !== false) {
            continue;
        }
        $paths[] = $p;
    }
    sort($paths);
}

/** Literal SQL text of a token slice: string literals and interpolated text, variables as placeholders. */
function literal_text(array $tokens)
{
    $out = '';
    foreach ($tokens as $t) {
        if (is_array($t)) {
            if ($t[0] === T_CONSTANT_ENCAPSED_STRING) {
                $q = $t[1][0];
                $s = substr($t[1], 1, -1);
                $out .= $q === "'" ? str_replace(array("\\'", '\\\\'), array("'", '\\'), $s) : stripcslashes($s);
            } elseif ($t[0] === T_ENCAPSED_AND_WHITESPACE) {
                $out .= $t[1];
            } elseif ($t[0] === T_STRING && ($t[1] === 'school_filter' || $t[1] === 'school_id')) {
                $out .= ' school_id '; // $this->db->school_filter(...) / school_id() add the school condition
            } elseif ($t[0] === T_VARIABLE || $t[0] === T_STRING_VARNAME) {
                $out .= ' 0 '; // interpolated or concatenated value
            }
        }
    }
    return $out;
}

/** Index of the token closing the parenthesis/statement that starts at $i. */
function statement_end(array $tokens, $i, $stopAtSemicolonDepth0 = true)
{
    $depth = 0;
    for ($n = count($tokens); $i < $n; $i++) {
        $t = $tokens[$i];
        if ($t === '(' || $t === '[' || $t === '{') $depth++;
        if ($t === ')' || $t === ']' || $t === '}') { $depth--; if ($depth < 0) return $i; }
        if ($t === ';' && $depth === 0 && $stopAtSemicolonDepth0) return $i;
    }
    return $i;
}

$problems = 0;
$report = array();
foreach ($paths as $file) {
    $code = file_get_contents($file);
    if (strpos($code, '->query(') === false) continue;
    $tokens = token_get_all($code);
    $lines = explode("\n", $code);
    $rel = str_replace(str_replace('\\', '/', $root) . '/', '', str_replace('\\', '/', $file));

    // Function boundaries: [start index, end index, name]
    $functions = array();
    for ($i = 0, $n = count($tokens); $i < $n; $i++) {
        if (is_array($tokens[$i]) && $tokens[$i][0] === T_FUNCTION) {
            $name = '';
            for ($j = $i + 1; $j < $n && $tokens[$j] !== '(' ; $j++) if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) $name = $tokens[$j][1];
            for ($j = $i; $j < $n && $tokens[$j] !== '{' && $tokens[$j] !== ';'; $j++);
            if ($j < $n && $tokens[$j] === '{') $functions[] = array($j, statement_end($tokens, $j + 1, false), $name);
        }
    }

    for ($i = 0, $n = count($tokens); $i < $n; $i++) {
        if (!(is_array($tokens[$i]) && $tokens[$i][0] === T_OBJECT_OPERATOR)) continue;
        $j = $i + 1;
        if (!(is_array($tokens[$j]) && $tokens[$j][0] === T_STRING && $tokens[$j][1] === 'query')) continue;
        $k = $j + 1;
        while (is_array($tokens[$k]) && $tokens[$k][0] === T_WHITESPACE) $k++;
        if ($tokens[$k] !== '(') continue;
        $line = $tokens[$j][2];
        // Receiver: the token(s) before ->query
        $receiver = '';
        for ($r = $i - 1; $r >= 0 && $r > $i - 6; $r--) {
            if (is_array($tokens[$r]) && in_array($tokens[$r][0], array(T_STRING, T_VARIABLE), true)) { $receiver = $tokens[$r][1]; break; }
        }
        if ($receiver !== 'db' && $receiver !== 'datatables' && !preg_match('/^\$(db|conn|mydb|this_db|query_db)/', $receiver)) {
            continue; // e.g. $this->input->..., $mysqli->query on non-app connections are listed separately
        }
        $end = statement_end($tokens, $k + 1);
        $argTokens = array_slice($tokens, $k + 1, $end - $k - 1);
        // First argument only
        $depth = 0; $first = array();
        foreach ($argTokens as $t) {
            if ($t === '(' || $t === '[') $depth++;
            if ($t === ')' || $t === ']') $depth--;
            if ($t === ',' && $depth === 0) break;
            $first[] = $t;
        }
        $sql = literal_text($first);
        // Variables used as (part of) the argument: collect their assignments in this function.
        $vars = array();
        foreach ($first as $t) if (is_array($t) && $t[0] === T_VARIABLE) $vars[$t[1]] = true;
        $fn = null;
        foreach ($functions as $f) if ($i > $f[0] && $i < $f[1] && ($fn === null || $f[0] > $fn[0])) $fn = $f;
        $traced = false;
        $srcLines = array($line => true);
        if ($vars && $fn) {
            for ($a = $fn[0]; $a < $i; $a++) {
                if (!(is_array($tokens[$a]) && $tokens[$a][0] === T_VARIABLE && isset($vars[$tokens[$a][1]]))) continue;
                $b = $a + 1;
                while (is_array($tokens[$b]) && $tokens[$b][0] === T_WHITESPACE) $b++;
                $op = is_array($tokens[$b]) ? $tokens[$b][1] : $tokens[$b];
                if ($op !== '=' && $op !== '.=') continue;
                $stmtEnd = statement_end($tokens, $b + 1);
                $sql .= ' ' . literal_text(array_slice($tokens, $b + 1, $stmtEnd - $b - 1));
                $traced = true;
                $endLine = $tokens[$a][2];
                for ($q = $a; $q <= $stmtEnd; $q++) if (is_array($tokens[$q])) $endLine = $tokens[$q][2] + substr_count($tokens[$q][1], "\n");
                for ($q = $tokens[$a][2]; $q <= $endLine; $q++) $srcLines[$q] = true;
            }
        }
        $sourceLine = isset($lines[$line - 1]) ? $lines[$line - 1] : '';
        $exempt = preg_match('#(//|/\*)\s*tenancy-lint:\s*ok\b#', $sourceLine);
        $clean = Tenant_raw_sql_guard::strip_literals($sql);
        $hasSqlWords = (bool) preg_match('/\b(SELECT|UPDATE|DELETE|INSERT|REPLACE)\b/i', $clean);
        if ($exempt) {
            $status = 'OK'; $tables = array();
        } elseif (!$hasSqlWords) {
            $status = 'UNKNOWN'; $tables = array();
        } else {
            $tables = $guard->unscoped_tables($sql);
            $status = $tables ? 'MISSING' : 'OK';
        }
        if ($status !== 'OK') $problems++;
        ksort($srcLines);
        $source = array();
        foreach (array_keys($srcLines) as $q) if (isset($lines[$q - 1])) $source[] = sprintf('%5d| %s', $q, rtrim($lines[$q - 1]));
        $report[] = array('file' => $rel, 'line' => $line, 'fn' => $fn ? $fn[2] : '-', 'via' => $receiver, 'status' => $status, 'tables' => $tables, 'source' => $source);
    }
}

if ($summary) {
    $per = array();
    foreach ($report as $r) {
        if (!isset($per[$r['file']])) $per[$r['file']] = array('MISSING' => 0, 'UNKNOWN' => 0, 'OK' => 0);
        $per[$r['file']][$r['status']]++;
    }
    uasort($per, function ($a, $b) { return ($b['MISSING'] + $b['UNKNOWN']) - ($a['MISSING'] + $a['UNKNOWN']); });
    foreach ($per as $f => $c) printf("%-70s missing=%-3d unknown=%-3d ok=%d\n", $f, $c['MISSING'], $c['UNKNOWN'], $c['OK']);
} else {
    foreach ($report as $r) {
        if ($r['status'] === 'OK' && !$showAll) continue;
        printf("%-7s %s:%d  %s()  via %s  %s\n", $r['status'], $r['file'], $r['line'], $r['fn'], $r['via'], implode(', ', $r['tables']));
        if ($show) echo implode("\n", $r['source']) . "\n\n";
    }
}
$totals = array_count_values(array_column($report, 'status'));
fprintf(STDERR, "%d raw query calls: %d missing school_id, %d unknown, %d ok\n",
    count($report), isset($totals['MISSING']) ? $totals['MISSING'] : 0, isset($totals['UNKNOWN']) ? $totals['UNKNOWN'] : 0, isset($totals['OK']) ? $totals['OK'] : 0);
exit($problems ? 1 : 0);
