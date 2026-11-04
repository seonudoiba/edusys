<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Generates seed files: application/seeds/{platform,school_1,school_2}/NNN_<table>.php
// Usage: php gen_seeds.php central.sql school182.sql manifest.json out_dir
require __DIR__ . '/parse.php';
require __DIR__ . '/rows.php';
require __DIR__ . '/config.php';
ini_set('memory_limit', '2G');

[$_, $centralFile, $schoolFile, $manifestFile, $outDir] = $argv;
$manifest = json_decode(file_get_contents($manifestFile), true);
$order = array_column($manifest, 'n', 'table');

$sources = [
    1 => ['schema' => parse_dump($centralFile), 'rows' => parse_rows($centralFile), 'label' => 'smartschool (central database)'],
    2 => ['schema' => parse_dump($schoolFile), 'rows' => parse_rows($schoolFile), 'label' => 'school_182 (XYZ University)'],
];
$OFF = SCHOOL_2_ID_OFFSET;
$LEGACY_SCHOOL_2_ID = 182;

function is_int_type($def) { return (bool) preg_match('/^(tinyint|smallint|mediumint|int|bigint)\b/i', $def); }
function shift($v, $off) { return ($v !== null && preg_match('/^[1-9]\d*$/', (string) $v)) ? (string) ((int) $v + $off) : $v; }
function lit($v, $isInt) {
    if ($v === null) return 'null';
    if ($isInt && preg_match('/^-?(0|[1-9]\d*)$/', $v)) return $v;
    return var_export($v, true);
}

// Columns to shift per table for school 2: id, declared FKs to non-platform/non-hybrid tables, implicit refs.
function shift_columns($table, $schema) {
    $cols = ['id' => true];
    foreach ($schema['fks'] as $f) {
        if (table_scope($f['ref']) !== 'tenant') continue;
        foreach (explode(',', str_replace('`', '', $f['cols'])) as $c) $cols[trim($c)] = true;
    }
    foreach (IMPLICIT_TENANT_REFS[$table] ?? [] as $c) {
        if (!isset($schema['cols'][$c])) fwrite(STDERR, "WARN implicit ref $table.$c not in schema\n");
        $cols[$c] = true;
    }
    return $cols;
}

function write_seed($dir, $num, $group, $table, $columns, $intCols, $rows, $note) {
    $lname = strtolower($table);
    $class = "Seed_{$group}_{$lname}";
    $colList = implode(', ', array_map(function ($c) { return var_export($c, true); }, $columns));
    $body = [];
    foreach ($rows as $r) {
        $vals = [];
        foreach ($columns as $i => $c) $vals[] = lit($r[$i], isset($intCols[$c]));
        $body[] = '            [' . implode(', ', $vals) . '],';
    }
    $php = "<?php\n\ndefined('BASEPATH') or exit('No direct script access allowed');\n\n"
        . "/**\n * Seeds `$table` for $group.\n *\n * $note\n * Rows: " . count($rows) . "\n */\n"
        . "class $class extends Seeder\n{\n"
        . "    public \$table = '$table';\n\n"
        . "    public \$columns = [$colList];\n\n"
        . "    public function rows()\n    {\n        return [\n" . implode("\n", $body) . "\n        ];\n    }\n}\n";
    file_put_contents("$dir/" . sprintf('%03d', $num) . "_{$lname}.php", $php);
}

foreach (['platform', 'school_1', 'school_2'] as $g) {
    @mkdir("$outDir/$g", 0777, true);
    foreach (glob("$outDir/$g/*.php") as $old) unlink($old);
}
$stats = [];

// ---------- School-owned (tenant) seeds ----------
foreach ([1, 2] as $sid) {
    $src = $sources[$sid];
    $group = "school_$sid";
    foreach ($src['rows'] as $dumpTable => $rows) {
        $table = canonical_table($dumpTable);
        if (in_array($dumpTable, SKIP_TABLES, true) && !isset(TABLE_ALIASES[$dumpTable])) continue;
        if (table_scope($table) !== 'tenant' || in_array($table, NO_SEED_TABLES, true)) continue;
        $schema = $src['schema'][$dumpTable];
        $srcCols = array_keys($schema['cols']);
        // References come from the canonical schema (central dump): school_182 lacks 100 of the 312 FK constraints.
        $canonical = $sources[1]['schema'][$dumpTable] ?? $schema;
        $shiftCols = $sid === 2 ? shift_columns($table, ['fks' => $canonical['fks'], 'cols' => $schema['cols']]) : [];
        $json = $sid === 2 ? (JSON_TENANT_REFS[$table] ?? []) : [];
        // Target columns: id, school_id, then the rest (source school_id, e.g. guest.school_id, is replaced).
        $columns = [];
        foreach ($srcCols as $c) {
            if ($c === 'school_id') continue;
            $columns[] = $c;
            if ($c === $srcCols[0]) $columns[] = 'school_id';
        }
        $intCols = ['school_id' => true];
        foreach ($schema['cols'] as $c => $def) if (is_int_type($def)) $intCols[$c] = true;
        $out = [];
        foreach ($rows as $r) {
            $assoc = array_combine($srcCols, $r);
            foreach ($shiftCols as $c => $_) if (array_key_exists($c, $assoc)) $assoc[$c] = shift($assoc[$c], $OFF);
            if ($sid === 2) foreach (CSV_TENANT_REFS[$table] ?? [] as $col) {
                if ($assoc[$col] === null || $assoc[$col] === '') continue;
                $assoc[$col] = implode(',', array_map(function ($v) use ($OFF) { return shift(trim($v), $OFF); }, explode(',', $assoc[$col])));
            }
            foreach ($json as $col => $keys) {
                $data = json_decode((string) $assoc[$col], true);
                if (!is_array($data)) continue;
                array_walk_recursive($data, function (&$v, $k) use ($keys, $OFF) { if (in_array($k, $keys, true)) $v = shift($v, $OFF); });
                $assoc[$col] = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            $assoc['school_id'] = (string) $sid;
            $out[] = $assoc;
        }
        $note = "Source: {$src['label']}, table `$dumpTable`." . ($sid === 2 ? " Ids and references to school-owned rows are offset by $OFF." : '');
        $pending[$table] = ['columns' => $columns, 'int' => $intCols, 'rows' => $out, 'note' => $note, 'fks' => $canonical['fks']];
    }

    // Orphans: rows whose referenced parent row does not exist in this school's data. The source databases
    // were not always enforcing the constraints, so apply each FK's ON DELETE rule as MySQL would have:
    // SET NULL clears the column, CASCADE / RESTRICT / NO ACTION drop the row. Repeat until stable.
    $globalIds = [
        'roles' => array_flip(array_column($sources[1]['rows']['roles'], 0)),
        'languages' => array_flip(array_column($sources[1]['rows']['languages'], 0)),
    ];
    $report = [];
    do {
        $changed = false;
        $ids = $globalIds;
        foreach ($pending as $t => $p) {
            $ids[$t] = [];
            foreach ($p['rows'] as $r) $ids[$t][$r['id']] = true;
        }
        foreach ($pending as $t => &$p) {
            foreach ($p['fks'] as $f) {
                $col = trim(str_replace('`', '', $f['cols']));
                $ref = canonical_table($f['ref']);
                if (strpos($col, ',') !== false || trim(str_replace('`', '', $f['refcols'])) !== 'id') {
                    throw new Exception("Unsupported FK shape on $t.$col");
                }
                $parents = $ids[$ref] ?? [];
                $setNull = stripos($f['rest'], 'ON DELETE SET NULL') !== false;
                foreach ($p['rows'] as $k => $r) {
                    $v = $r[$col] ?? null;
                    if ($v === null || isset($parents[$v])) continue;
                    if ($setNull) {
                        $p['rows'][$k][$col] = null;
                        $report[] = "$t id={$r['id']}: $col=$v (missing $ref) set to NULL";
                    } else {
                        unset($p['rows'][$k]);
                        $report[] = "$t id={$r['id']}: dropped, $col=$v references missing $ref";
                    }
                    $changed = true;
                }
            }
        }
        unset($p);
    } while ($changed);

    foreach ($pending as $t => $p) {
        $dropped = count(array_filter($report, function ($l) use ($t) { return strpos($l, "$t id=") === 0; }));
        $note = $p['note'] . ($dropped ? " $dropped orphan fix(es) applied, see seeds/ORPHANS.md." : '');
        $rows = array_map(function ($a) use ($p) { return array_map(function ($c) use ($a) { return $a[$c]; }, $p['columns']); }, array_values($p['rows']));
        write_seed("$outDir/$group", $order[$t], $group, $t, $p['columns'], $p['int'], $rows, $note);
        $stats[$group] = ($stats[$group] ?? 0) + 1;
    }
    $orphanReport[$group] = $report;
    $pending = [];
}

$md = "# Orphan rows resolved while generating seeds\n\n"
    . "The source databases contain rows that reference parent rows that no longer exist (school_182 was\n"
    . "created without 100 of the 312 foreign keys, so deletes did not cascade). Each foreign key's ON DELETE\n"
    . "rule was applied as MySQL would have: SET NULL clears the column; CASCADE/RESTRICT drop the row.\n"
    . "Generated by the seed generator; do not edit by hand.\n";
foreach ($orphanReport as $g => $lines) {
    $md .= "\n## $g (" . count($lines) . ")\n\n" . ($lines ? '- ' . implode("\n- ", $lines) . "\n" : "None.\n");
}
file_put_contents("$outDir/ORPHANS.md", $md);

// ---------- Platform and hybrid seeds (from the central database) ----------
$c = $sources[1];
$settingsRow = array_combine(array_keys($c['schema']['sch_settings']['cols']), $c['rows']['sch_settings'][0]);
$schoolCols = array_keys($c['schema']['school']['cols']);
$legacy182 = null;
foreach ($c['rows']['school'] as $r) { $a = array_combine($schoolCols, $r); if ((int) $a['id'] === $LEGACY_SCHOOL_2_ID) $legacy182 = $a; }

$schoolColumns = ['id', 'school_name', 'school_code', 'subdomain', 'is_platform', 'school_type', 'school_category', 'school_address', 'phone_number', 'email_address', 'website', 'principal_name', 'owner_name', 'status', 'payment_status', 'cron_flag', 'created_at'];
$schoolRows = [
    // School 1: the school data held in the central smartschool database; served on the central host.
    ['1', $settingsRow['name'], null, null, '1', null, null, $settingsRow['address'], $settingsRow['phone'], $settingsRow['email'], null, null, null, '1', '1', '1', null],
    // School 2: legacy registry row 182 (XYZ University). Subdomain is a placeholder to confirm before go-live.
    ['2', $legacy182['school_name'], $legacy182['school_code'], 'xyz', '0', $legacy182['school_type'], $legacy182['school_category'], $legacy182['school_address'], $legacy182['phone_number'], $legacy182['email_address'], $legacy182['website'], $legacy182['principal_name'], $legacy182['owner_name'], $legacy182['status'], $legacy182['payment_status'], $legacy182['cron_flag'], $legacy182['created_at']],
];
$schoolInt = array_flip(['id', 'is_platform', 'school_type', 'school_category', 'status', 'payment_status', 'cron_flag']);
write_seed("$outDir/platform", $order['school'], 'platform', 'school', $schoolColumns, $schoolInt, $schoolRows,
    "School registry. School 1 = central smartschool data (is_platform = 1, served on the central host). School 2 = legacy school 182 (XYZ University), subdomain 'xyz' is a placeholder.");

foreach (['plan_list', 'plan', 'subscription', 'settings', 'languages', 'roles'] as $table) {
    $schema = $c['schema'][$table];
    $cols = array_keys($schema['cols']);
    $intCols = [];
    foreach ($schema['cols'] as $col => $def) if (is_int_type($def)) $intCols[$col] = true;
    $rows = $c['rows'][$table] ?? [];
    $note = "Source: smartschool (central database), table `$table`.";
    if ($table === 'subscription') {
        $idx = array_search('school_id', $cols);
        foreach ($rows as &$r) {
            if ((int) $r[$idx] === $LEGACY_SCHOOL_2_ID) $r[$idx] = '2';
        }
        unset($r);
        $note .= " Legacy school_id $LEGACY_SCHOOL_2_ID is mapped to school 2.";
    }
    if ($table === 'roles') {
        $out = [];
        foreach ($rows as $r) { $a = array_combine($cols, $r); $a['school_id'] = null; $out[] = $a; }
        $cols2 = $cols; array_splice($cols2, 1, 0, ['school_id']);
        $rows = array_map(function ($a) use ($cols2) { return array_map(function ($k) use ($a) { return $a[$k]; }, $cols2); }, $out);
        $cols = $cols2;
        $intCols['school_id'] = true;
        $note .= ' System roles (school_id NULL) shared by every school; role ids are hard-coded in the application.';
    }
    if (!$rows) { $note .= ' No rows in source; file kept so the table is accounted for.'; }
    write_seed("$outDir/platform", $order[$table], 'platform', $table, $cols, $intCols, $rows, $note);
    $stats['platform'] = ($stats['platform'] ?? 0) + 1;
}
$stats['platform']++;
print_r($stats);
