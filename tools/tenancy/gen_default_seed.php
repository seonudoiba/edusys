<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Generates application/seeds/default/NNN_<table>.php: the reference data every new school starts with,
// taken from school 1 (central dump) with school-specific values removed.
// Usage: php gen_default_seed.php central.sql manifest.json out_dir
require __DIR__ . '/parse.php';
require __DIR__ . '/rows.php';
require __DIR__ . '/config.php';
ini_set('memory_limit', '1G');

[$_, $centralFile, $manifestFile, $outDir] = $argv;
$manifest = json_decode(file_get_contents($manifestFile), true);
$order = array_column($manifest, 'n', 'table');
$schema = parse_dump($centralFile);
$data = parse_rows($centralFile);

// --- What a new school gets (agreed 2026-11-04) -------------------------------------------------
const REQUIRED = [
    'permission_group', 'permission_category', 'permission_student', 'roles_permissions',
    'sidebar_menus', 'sidebar_sub_menus', 'sessions', 'currencies', 'sch_settings',
    'email_template', 'sms_template', 'notification_setting', 'captcha', 'online_admission_fields',
    'student_edit_fields', 'attendence_type', 'staff_attendance_type', 'filetypes',
    'behaviour_settings', 'online_course_settings', 'QR_code_settings', 'google_authenticator',
    'front_cms_settings', 'front_cms_menus',
];
const STARTER_LISTS = [
    'department', 'staff_designation', 'leave_types', 'categories', 'school_houses', 'room_types',
    'income_head', 'expense_head', 'item_category', 'item_store', 'reference', 'source',
    'visitors_purpose', 'complaint_type', 'content_types', 'course_category', 'disable_reason',
    'grades', 'mark_divisions', 'cbse_exam_grades', 'cbse_exam_grades_range', 'cbse_terms',
    'cbse_exam_assessments', 'cbse_exam_assessment_types', 'cbse_marksheet_type',
    'cbse_observation_parameters',
];
const TEMPLATES = [
    'print_headerfooter', 'id_card', 'staff_id_card', 'certificates', 'template_admitcards',
    'template_marksheets', 'front_cms_pages', 'front_cms_menu_items', 'front_cms_page_contents',
];

// Platform-only modules (school registry, plans, subscriptions) are not given to schools.
const PLATFORM_PERMISSION_GROUPS = ['school_information', 'plan_list', 'subscription'];

// Values that identify school 1: blanked ('' or NULL when the column is nullable).
const BLANK = [
    'sch_settings' => ['base_url', 'folder_path', 'name', 'email', 'phone', 'address', 'dise_code', 'cron_secret_key',
        'image', 'admin_logo', 'admin_small_logo', 'admin_login_page_background', 'user_login_page_background',
        'mobile_api_url', 'admin_mobile_api_url', 'app_logo', 'zoom_api_key', 'offline_bank_payment_instruction',
        'online_admission_instruction', 'online_admission_conditions'],
    'front_cms_settings' => ['logo', 'contact_us_email', 'complain_form_email', 'whatsapp_url', 'fb_url', 'twitter_url',
        'youtube_url', 'google_plus', 'instagram_url', 'pinterest_url', 'linkedin_url', 'google_analytics', 'footer_text', 'fav_icon'],
    'print_headerfooter' => ['header_image', 'footer_content'],
    'id_card' => ['school_name', 'school_address', 'background', 'logo', 'sign_image'],
    'staff_id_card' => ['school_name', 'school_address', 'background', 'logo', 'sign_image'],
    'certificates' => ['left_header', 'center_header', 'right_header', 'left_footer', 'right_footer', 'center_footer', 'background_image'],
    'template_admitcards' => ['left_logo', 'right_logo', 'school_name', 'exam_center', 'sign', 'background_img'],
    'template_marksheets' => ['header_image', 'left_logo', 'right_logo', 'school_name', 'exam_center', 'left_sign',
        'middle_sign', 'right_sign', 'background_img'],
    'front_cms_pages' => ['feature_image'],
];

// Undeclared references between default tables (column => referenced table).
const IMPLICIT_TARGETS = [
    'sch_settings' => ['session_id' => 'sessions', 'currency' => 'currencies'],
    'front_cms_menu_items' => ['page_id' => 'front_cms_pages', 'parent_id' => 'front_cms_menu_items'],
];
// References to rows a new school doesn't have (staff, hostels, ...): reset.
const RESET_REFS = [
    'sch_settings' => ['hostel_id'],
    'print_headerfooter' => ['created_by'],
    'cbse_exam_grades_range' => ['created_by'], 'cbse_terms' => ['created_by'],
    'cbse_exam_assessment_types' => ['created_by'],
];

$tables = array_merge(REQUIRED, STARTER_LISTS, TEMPLATES);
usort($tables, function ($a, $b) use ($order) { return $order[$a] - $order[$b]; });
// Own dependency order: declared FKs *and* the undeclared references above (e.g. menu items -> pages),
// so a referenced table is always seeded (and id-mapped) before the tables that point at it.
$deps = [];
foreach ($tables as $t) {
    $dump = $t === 'QR_code_settings' ? 'qr_code_settings' : $t;
    $deps[$t] = [];
    foreach ($schema[$dump]['fks'] as $f) {
        $ref = canonical_table($f['ref']);
        if ($ref !== $t && in_array($ref, $tables, true)) $deps[$t][] = $ref;
    }
    foreach (IMPLICIT_TARGETS[$t] ?? [] as $ref) if ($ref !== $t) $deps[$t][] = $ref;
}
$seen = [];
$sorted = [];
$visit = function ($t) use (&$visit, &$seen, &$sorted, $deps) {
    if (isset($seen[$t])) return;
    $seen[$t] = true;
    foreach ($deps[$t] as $d) $visit($d);
    $sorted[] = $t; // after its dependencies
};
foreach ($tables as $t) $visit($t);
$tables = $sorted;
$seq = array_flip($tables);

function rows_of($table, $schema, $data) {
    $dump = $table === 'QR_code_settings' ? 'qr_code_settings' : $table;
    $cols = array_keys($schema[$dump]['cols']);
    return array_map(function ($r) use ($cols) { return array_combine($cols, $r); }, $data[$dump] ?? []);
}

$kept = []; // table => [id => true], for filters on references
$rowsBy = [];
foreach ($tables as $t) $rowsBy[$t] = rows_of($t, $schema, $data);

// Filters
$platformGroups = [];
foreach ($rowsBy['permission_group'] as $r) if (in_array($r['short_code'], PLATFORM_PERMISSION_GROUPS, true)) $platformGroups[$r['id']] = true;
// Platform-only rows (the School, Plan and Subscription modules and their menus) are kept but
// tagged: School_provisioner loads them for the platform school only. Rows that reference a
// platform-only row are platform-only too.
$platformOnlyRoots = [
    'permission_group' => function ($r) use ($platformGroups) { return isset($platformGroups[$r['id']]); },
    'permission_category' => function ($r) use ($platformGroups) { return isset($platformGroups[$r['perm_group_id']]); },
    'permission_student' => function ($r) use ($platformGroups) { return isset($platformGroups[$r['group_id']]); },
    'sidebar_menus' => function ($r) use ($platformGroups) { return isset($platformGroups[$r['permission_group_id']]); },
    'sidebar_sub_menus' => function ($r) use ($platformGroups) { return isset($platformGroups[$r['permission_group_id']]); },
];
$platformOnly = [];
$filters = [
    // Built-in pages only; school 1's own pages (about us, articles) are its content.
    'front_cms_pages' => function ($r) { return $r['page_type'] === 'default'; },
];

@mkdir($outDir, 0777, true);
foreach (glob("$outDir/*.php") as $old) unlink($old);
$summary = [];
foreach ($tables as $t) {
    $s = $schema[$t === 'QR_code_settings' ? 'qr_code_settings' : $t];
    $cols = array_keys($s['cols']);
    $cols = array_values(array_diff($cols, ['school_id']));
    // References to other default tables
    $refs = [];
    foreach ($s['fks'] as $f) {
        $col = trim(str_replace('`', '', $f['cols']));
        $ref = canonical_table($f['ref']);
        if (in_array($ref, $tables, true)) $refs[$col] = $ref;
        elseif (table_scope($ref) === 'tenant') $refsOut[$t][$col] = $ref;
    }
    foreach (IMPLICIT_TARGETS[$t] ?? [] as $col => $ref) $refs[$col] = $ref;

    $out = [];
    foreach ($rowsBy[$t] as $r) {
        if (isset($filters[$t]) && !$filters[$t]($r)) continue;
        // Rows whose required parent was filtered out are dropped (e.g. sub menus of a platform menu).
        $orphan = false;
        foreach ($refs as $col => $ref) {
            if ($ref === $t || $r[$col] === null || $r[$col] === '' || $r[$col] === '0') continue;
            if (!isset($kept[$ref][$r[$col]])) {
                                $orphan = true;
            }
        }
        if ($orphan) continue;
        $isPlatform = isset($platformOnlyRoots[$t]) && $platformOnlyRoots[$t]($r);
        foreach ($refs as $col => $ref) {
            if ($ref !== $t && $r[$col] !== null && isset($platformOnly[$ref][$r[$col]])) $isPlatform = true;
        }
        if ($isPlatform) $platformOnly[$t][$r['id']] = true;
        foreach (BLANK[$t] ?? [] as $col) {
            $r[$col] = stripos($s['cols'][$col], 'NOT NULL') === false ? null : '';
        }
        foreach (RESET_REFS[$t] ?? [] as $col) {
            $r[$col] = stripos($s['cols'][$col], 'NOT NULL') === false ? null : '0';
        }
        foreach ($refsOut[$t] ?? [] as $col => $_) {
            $r[$col] = stripos($s['cols'][$col], 'NOT NULL') === false ? null : '0';
        }
        // School 1's home page (platform marketing) and contact page (its address and phone) are its own content.
        if ($t === 'front_cms_pages' && in_array($r['slug'], ['home', 'contact-us'], true)) {
            $r['description'] = '';
        }
        unset($r['school_id']);
        $kept[$t][$r['id']] = true;
        $out[] = $r;
    }
    // Menu items whose page was not kept
    if ($t === 'front_cms_menu_items' || $t === 'front_cms_page_contents') {
        $out = array_values(array_filter($out, function ($r) use ($kept) { return !empty($r['page_id']) && isset($kept['front_cms_pages'][$r['page_id']]); }));
        $kept[$t] = array_fill_keys(array_column($out, 'id'), true);
    }

    $intCols = [];
    foreach ($s['cols'] as $c => $def) if (preg_match('/^(tinyint|smallint|mediumint|int|bigint)\b/i', $def)) $intCols[$c] = true;
    $lit = function ($v, $c) use ($intCols) {
        if ($v === null) return 'null';
        if (isset($intCols[$c]) && preg_match('/^-?(0|[1-9]\d*)$/', (string) $v)) return $v;
        return var_export((string) $v, true);
    };
    $body = [];
    foreach ($out as $r) $body[] = '            [' . implode(', ', array_map(function ($c) use ($r, $lit) { return $lit($r[$c], $c); }, $cols)) . '],';
    $group = in_array($t, REQUIRED, true) ? 'required' : (in_array($t, STARTER_LISTS, true) ? 'starter list' : 'template (school details removed)');
    $lname = strtolower($t);
    $refText = $refs ? implode(', ', array_map(function ($c, $r) { return "'$c' => '$r'"; }, array_keys($refs), $refs)) : '';
    $php = "<?php\n\ndefined('BASEPATH') or exit('No direct script access allowed');\n\n"
        . "/**\n * Default `$t` for a new school ($group).\n *\n"
        . " * Source: school 1 (central smartschool data), school-specific values removed.\n"
        . " * Generated by tools/tenancy/gen_default_seed.php; do not edit by hand.\n"
        . " * `id` values are template ids: School_provisioner inserts rows under new ids and remaps \$references.\n"
        . " * Rows: " . count($out) . "\n */\n"
        . "class Seed_default_$lname extends Seeder\n{\n"
        . "    public \$table = '$t';\n\n"
        . "    public \$columns = [" . implode(', ', array_map(function ($c) { return var_export($c, true); }, $cols)) . "];\n\n"
        . "    /** @var array column => referenced default table */\n"
        . "    public \$references = [$refText];\n\n"
        . "    /** @var int[] template ids loaded only for the platform school (school registry, plans, subscriptions) */\n"
        . "    public \$platform_only = [" . implode(', ', array_keys($platformOnly[$t] ?? [])) . "];\n\n"
        . "    public function rows()\n    {\n        return [\n" . implode("\n", $body) . "\n        ];\n    }\n}\n";
    file_put_contents(sprintf('%s/%03d_%s.php', $outDir, $seq[$t] + 1, $lname), $php);
    $summary[] = sprintf('%-32s %-34s %4d rows', $t, $group, count($out)) . (isset($platformOnly[$t]) ? ' (' . count($platformOnly[$t]) . ' platform-only)' : '');
}
echo implode("\n", $summary) . "\n" . count($tables) . " tables\n";
