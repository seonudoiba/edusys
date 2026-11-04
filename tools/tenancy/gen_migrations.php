<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Generates one CodeIgniter 3 migration per table (school_id-scoped single-database schema).
// Usage: php gen_migrations.php central.sql school.sql out_dir manifest.json
require __DIR__ . '/parse.php';
require __DIR__ . '/config.php';

[$_, $centralFile, $schoolFile, $outDir, $manifestFile] = $argv;
$central = parse_dump($centralFile);
$school = parse_dump($schoolFile);

// Union of both schemas (central wins; school-only tables added).
$tables = [];
foreach ($central + $school as $name => $t) {
    if (in_array($name, SKIP_TABLES, true)) continue;
    $tables[canonical_table($name)] = $t;
}

// Dependency order: platform tables first in a fixed order, then FK topological order.
$deps = [];
foreach ($tables as $n => $t) {
    $deps[$n] = [];
    foreach ($t['fks'] as $f) if ($f['ref'] !== $n) $deps[$n][] = canonical_table($f['ref']);
    if (table_scope($n) !== 'platform') $deps[$n][] = 'school';
}
$order = [];
$seen = [];
$visit = function ($n) use (&$visit, &$order, &$seen, $deps) {
    if (isset($seen[$n])) return;
    $seen[$n] = true;
    foreach ($deps[$n] as $d) $visit($d);
    $order[] = $n;
};
foreach (PLATFORM_TABLES as $p) $visit($p);
foreach (HYBRID_TABLES as $h) $visit($h);
$rest = array_keys($tables);
sort($rest, SORT_STRING | SORT_FLAG_CASE);
foreach ($rest as $n) $visit($n);

function build_ddl($name, $t) {
    $scope = table_scope($name);
    $cols = [];
    $hasSchoolId = isset($t['cols']['school_id']);
    foreach ($t['cols'] as $c => $def) {
        if ($c === 'school_id' && $scope !== 'platform') continue; // re-added below in a fixed position
        $cols[] = "`$c` $def";
        if ($c === array_key_first($t['cols']) && $scope !== 'platform') {
            $cols[] = $scope === 'hybrid'
                ? "`school_id` int DEFAULT NULL COMMENT 'NULL = system row shared by all schools'"
                : "`school_id` int NOT NULL";
        }
    }
    if ($name === 'school') {
        // Tenant routing: one subdomain per school; is_platform marks the school served on the central host.
        $out = [];
        foreach ($cols as $line) {
            $out[] = $line;
            if (strpos($line, '`school_code`') === 0) {
                $out[] = "`subdomain` varchar(63) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL COMMENT 'Lowercase DNS label, e.g. greenfield for greenfield.edusys.ng'";
                $out[] = "`is_platform` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = school served on the central host (platform owner)'";
            }
        }
        $cols = $out;
    }
    $keys = [];
    $checks = [];
    foreach ($t['keys'] as $k) {
        if (stripos($k, 'CONSTRAINT') === 0) { $checks[] = $k; continue; }
        if ($scope === 'tenant' && preg_match('/^UNIQUE KEY `([^`]+)` \((.+)\)$/', $k, $m) && $m[2] !== '`id`') {
            $k = "UNIQUE KEY `{$m[1]}` (`school_id`,{$m[2]})"; // uniqueness is per school
        }
        $keys[] = $k;
    }
    if ($name === 'school') $keys[] = 'UNIQUE KEY `uq_school_subdomain` (`subdomain`)';
    $fks = array_map(function ($f) { return $f['line']; }, $t['fks']);
    if ($scope !== 'platform') {
        $keys[] = "KEY `idx_{$name}_school_id` (`school_id`)";
        $fks[] = "CONSTRAINT `fk_{$name}_school` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`)";
    }
    $lines = array_merge($cols, $keys, $fks, $checks);
    $opts = preg_replace('/ AUTO_INCREMENT=\d+/', '', $t['opts']);
    return "CREATE TABLE `$name` (\n  " . implode(",\n  ", $lines) . "\n) $opts";
}

@mkdir($outDir, 0777, true);
// Only generated files are replaced; hand-written migrations (NNN_<change>.php, numbered after the generated ones) stay.
foreach (glob("$outDir/[0-9][0-9][0-9]_create_*.php") as $old) unlink($old);
$manifest = [];
$i = 0;
foreach ($order as $name) {
    $i++;
    $t = $tables[$name];
    $scope = table_scope($name);
    $lname = strtolower($name);
    $num = sprintf('%03d', $i);
    $file = "{$num}_create_{$lname}.php";
    $class = 'Migration_Create_' . $lname;
    $ddl = build_ddl($name, $t);
    $refs = array_values(array_unique(array_map(function ($f) { return canonical_table($f['ref']); }, $t['fks'])));
    $scopeText = [
        'platform' => 'Platform table: one shared copy, no school_id.',
        'hybrid' => 'Hybrid table: school_id NULL = system row shared by every school; otherwise owned by that school.',
        'tenant' => 'School-owned table: every row belongs to exactly one school (school_id).',
    ][$scope];
    $refText = $refs ? 'References: ' . implode(', ', $refs) . '.' : 'References: none.';
    if ($scope !== 'platform' && !in_array('school', $refs, true)) $refText = rtrim($refText, '.') . ($refs ? ', school.' : '') ;
    if ($scope !== 'platform' && !$refs) $refText = 'References: school.';
    $php = "<?php\n\ndefined('BASEPATH') or exit('No direct script access allowed');\n\n"
        . "/**\n * Creates `$name`.\n *\n * $scopeText\n * $refText\n */\n"
        . "class $class extends CI_Migration\n{\n"
        . "    public function up()\n    {\n"
        . "        \$this->db->query(<<<'SQL'\n$ddl\nSQL\n        );\n    }\n\n"
        . "    public function down()\n    {\n"
        . "        \$this->db->query('DROP TABLE IF EXISTS `$name`');\n    }\n}\n";
    file_put_contents("$outDir/$file", $php);
    $manifest[] = ['n' => $i, 'table' => $name, 'scope' => $scope, 'file' => $file, 'refs' => $refs];
}
file_put_contents($manifestFile, json_encode($manifest, JSON_PRETTY_PRINT));
echo "wrote $i migrations\n";
$counts = array_count_values(array_column($manifest, 'scope'));
print_r($counts);
