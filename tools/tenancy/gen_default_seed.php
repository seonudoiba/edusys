<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Generates application/seeds/default/NNN_<table>.php: the reference data every new school starts with,
// taken from school 1 (central dump) with school-specific values removed.
// Usage: php gen_default_seed.php central.sql manifest.json out_dir [demo.json]
//   demo.json: phpMyAdmin JSON export of the demo school (smartschool_test.json). When given, the
//   school content (DEMO_SOURCE: academics, fees setup, transport, front website, courses, ...)
//   comes from it; system tables (permissions, menus, settings) always come from central.sql.
require __DIR__ . '/parse.php';
require __DIR__ . '/rows.php';
require __DIR__ . '/config.php';
ini_set('memory_limit', '1G');

[$_, $centralFile, $manifestFile, $outDir] = $argv;
$demoFile = $argv[4] ?? null;
$manifest = json_decode(file_get_contents($manifestFile), true);
$order = array_column($manifest, 'n', 'table');
$schema = parse_dump($centralFile);
$data = parse_rows($centralFile);
$demo = [];
if ($demoFile) {
    foreach (json_decode(file_get_contents($demoFile), true) as $entry) {
        if (($entry['type'] ?? '') === 'table') $demo[$entry['name']] = $entry['data'] ?? [];
    }
}

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

// Demo school content (agreed 2026-11-05): a new school starts with a working example of every
// module. Never people (students, parents, staff, logins) or their records (attendance, marks,
// payments, income/expenses, messages, logs), credentials, platform tables, dated meetings, or
// the Indian-board (CBSE) exam instances.
const DEMO_CONTENT = [
    // academics
    'classes', 'sections', 'class_sections', 'subjects', 'subject_groups', 'subject_group_subjects',
    'subject_group_class_sections', 'subject_timetable', 'lesson', 'topic',
    // exams and question bank
    'exam_groups', 'exam_group_class_batch_exams', 'exam_group_class_batch_exam_subjects',
    'questions', 'onlineexam', 'onlineexam_questions',
    // fees setup (not payments)
    'feetype', 'fee_groups', 'fee_session_groups', 'fee_groups_feetype', 'fees_discounts', 'fees_reminder',
    // transport, hostel, library, inventory
    'transport_route', 'vehicles', 'pickup_point', 'route_pickup_point', 'vehicle_routes', 'transport_feemaster',
    'hostel', 'hostel_rooms', 'books', 'item_supplier', 'item',
    // front website
    'front_cms_media_gallery', 'front_cms_programs', 'front_cms_program_photos',
    // online courses and videos
    'online_courses', 'online_course_section', 'online_course_lesson', 'online_course_quiz',
    'course_quiz_question', 'course_lesson_quiz_order', 'online_course_class_sections',
    'video_tutorial', 'video_tutorial_class_sections',
    // notices, download centre, custom fields, behaviour types
    'send_notification', 'notification_roles', 'upload_contents', 'custom_fields', 'student_behaviour',
];
// Tables taken from the demo export when it is given (everything else: central.sql).
const DEMO_SOURCE = array(
    'department', 'staff_designation', 'leave_types', 'categories', 'school_houses', 'room_types',
    'income_head', 'expense_head', 'item_category', 'item_store', 'reference', 'source',
    'visitors_purpose', 'complaint_type', 'content_types', 'course_category', 'disable_reason',
    'grades', 'mark_divisions',
    'print_headerfooter', 'id_card', 'staff_id_card', 'certificates', 'template_admitcards',
    'template_marksheets', 'front_cms_menus', 'front_cms_pages', 'front_cms_menu_items', 'front_cms_page_contents',
);
// The demo's current session (id 19): its content moves to the seed's current session, and the
// provisioner then moves it to the academic year the school starts in. The demo's dates (events,
// notices, due dates) are from the 2024-25 school year, so they move by (start year - 2024).
const DEMO_SESSION = '19';
const DEMO_START_YEAR = 2024;

// Platform-only modules (school registry, plans, subscriptions) are not given to schools.
const PLATFORM_PERMISSION_GROUPS = ['school_information', 'plan_list', 'subscription', 'platform_admin'];

// Values that identify school 1: blanked ('' or NULL when the column is nullable).
const BLANK = [
    'sch_settings' => ['base_url', 'folder_path', 'name', 'email', 'phone', 'address', 'dise_code', 'cron_secret_key',
        'image', 'admin_logo', 'admin_small_logo', 'admin_login_page_background', 'user_login_page_background',
        'mobile_api_url', 'admin_mobile_api_url', 'app_logo', 'zoom_api_key', 'offline_bank_payment_instruction',
        'online_admission_instruction', 'online_admission_conditions'],
    'front_cms_settings' => ['logo', 'contact_us_email', 'complain_form_email', 'whatsapp_url', 'fb_url', 'twitter_url',
        'youtube_url', 'google_plus', 'instagram_url', 'pinterest_url', 'linkedin_url', 'google_analytics', 'footer_text', 'fav_icon'],
    'print_headerfooter' => ['header_image', 'footer_content'],
    // Background images stay: the demo files ship in uploads/_shared/.
    'id_card' => ['school_name', 'school_address', 'logo', 'sign_image'],
    'staff_id_card' => ['school_name', 'school_address', 'logo', 'sign_image'],
    'certificates' => ['left_header', 'center_header', 'right_header', 'left_footer', 'right_footer', 'center_footer'],
    'template_admitcards' => ['left_logo', 'right_logo', 'school_name', 'exam_center', 'sign', 'background_img'],
    'template_marksheets' => ['header_image', 'left_logo', 'right_logo', 'school_name', 'exam_center', 'left_sign',
        'middle_sign', 'right_sign', 'background_img'],
    'front_cms_pages' => ['feature_image'],
    'notification_setting' => ['template_id'], // school 1's SMS provider registrations
];

// Undeclared references between default tables (column => referenced table).
const IMPLICIT_TARGETS = [
    'sch_settings' => ['session_id' => 'sessions', 'currency' => 'currencies'],
    'front_cms_menu_items' => ['page_id' => 'front_cms_pages', 'parent_id' => 'front_cms_menu_items'],
    'class_sections' => ['class_id' => 'classes', 'section_id' => 'sections'],
    'subject_groups' => ['session_id' => 'sessions'],
    'subject_group_subjects' => ['subject_group_id' => 'subject_groups', 'subject_id' => 'subjects', 'session_id' => 'sessions'],
    'subject_group_class_sections' => ['subject_group_id' => 'subject_groups', 'class_section_id' => 'class_sections', 'session_id' => 'sessions'],
    'subject_timetable' => ['session_id' => 'sessions', 'class_id' => 'classes', 'section_id' => 'sections',
        'subject_group_id' => 'subject_groups', 'subject_group_subject_id' => 'subject_group_subjects'],
    'lesson' => ['session_id' => 'sessions', 'subject_group_subject_id' => 'subject_group_subjects',
        'subject_group_class_sections_id' => 'subject_group_class_sections'],
    'topic' => ['session_id' => 'sessions', 'lesson_id' => 'lesson'],
    'exam_group_class_batch_exams' => ['session_id' => 'sessions', 'exam_group_id' => 'exam_groups'],
    'exam_group_class_batch_exam_subjects' => ['exam_group_class_batch_exams_id' => 'exam_group_class_batch_exams', 'subject_id' => 'subjects'],
    'questions' => ['subject_id' => 'subjects', 'class_id' => 'classes', 'section_id' => 'sections', 'class_section_id' => 'class_sections'],
    'onlineexam' => ['session_id' => 'sessions'],
    'onlineexam_questions' => ['question_id' => 'questions', 'onlineexam_id' => 'onlineexam', 'session_id' => 'sessions'],
    'fee_session_groups' => ['fee_groups_id' => 'fee_groups', 'session_id' => 'sessions'],
    'fee_groups_feetype' => ['fee_session_group_id' => 'fee_session_groups', 'fee_groups_id' => 'fee_groups', 'feetype_id' => 'feetype', 'session_id' => 'sessions'],
    'fees_discounts' => ['session_id' => 'sessions'],
    'route_pickup_point' => ['transport_route_id' => 'transport_route', 'pickup_point_id' => 'pickup_point'],
    'vehicle_routes' => ['route_id' => 'transport_route', 'vehicle_id' => 'vehicles'],
    'transport_feemaster' => ['session_id' => 'sessions'],
    'hostel_rooms' => ['hostel_id' => 'hostel', 'room_type_id' => 'room_types'],
    'item' => ['item_category_id' => 'item_category', 'item_store_id' => 'item_store', 'item_supplier_id' => 'item_supplier'],
    'front_cms_program_photos' => ['program_id' => 'front_cms_programs', 'media_gallery_id' => 'front_cms_media_gallery'],
    'online_courses' => ['category_id' => 'course_category'],
    'online_course_section' => ['online_course_id' => 'online_courses'],
    'online_course_lesson' => ['course_section_id' => 'online_course_section'],
    'online_course_quiz' => ['course_section_id' => 'online_course_section'],
    'course_quiz_question' => ['course_quiz_id' => 'online_course_quiz'],
    'course_lesson_quiz_order' => ['course_section_id' => 'online_course_section'],
    'online_course_class_sections' => ['course_id' => 'online_courses', 'class_section_id' => 'class_sections'],
    'video_tutorial_class_sections' => ['video_tutorial_id' => 'video_tutorial', 'class_section_id' => 'class_sections'],
    'notification_roles' => ['send_notification_id' => 'send_notification'],
    'upload_contents' => ['content_type_id' => 'content_types'],
];
// References whose target table depends on another column (column => [type column, [type => table]]).
const TYPED_TARGETS = [
    'course_lesson_quiz_order' => ['lesson_quiz_id' => ['type', ['lesson' => 'online_course_lesson', 'quiz' => 'online_course_quiz']]],
];
// References to rows a new school doesn't have (staff, hostels, ...): reset.
const RESET_REFS = [
    'sch_settings' => ['hostel_id'],
    'print_headerfooter' => ['created_by'],
    'cbse_exam_grades_range' => ['created_by'], 'cbse_terms' => ['created_by'],
    'cbse_exam_assessment_types' => ['created_by'],
    'subject_timetable' => ['staff_id'], 'questions' => ['staff_id'],
    'online_courses' => ['teacher_id'], 'send_notification' => ['created_by'], 'onlineexam' => ['created_by'],
];
// "Created/uploaded by" columns: the school's super admin (created before the default data).
const OWNER_REFS = [
    'online_courses' => ['created_by'], 'online_course_quiz' => ['created_by'], 'video_tutorial' => ['created_by'],
    'send_notification' => ['created_id'], 'upload_contents' => ['upload_by'],
];
// *_id columns that are values, not references to school rows (checked: every other *_id
// column of a default table must be a reference or reset, so no row can point at another school's data).
const NOT_REFERENCES = [
    // role_id: system roles (shared); video_id: a YouTube id
    '*' => ['id', 'role_id', 'lang_id', 'video_id', 'currency_id'],
    'feetype' => ['feecategory_id'], // always NULL in the demo
    'notification_setting' => ['template_id'], // an SMS provider template registration number (blanked)
];

$tables = array_merge(REQUIRED, STARTER_LISTS, TEMPLATES, $demo ? DEMO_CONTENT : []);
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
    foreach (IMPLICIT_TARGETS[$t] ?? [] as $ref) if ($ref !== $t && in_array($ref, $tables, true)) $deps[$t][] = $ref;
    foreach (TYPED_TARGETS[$t] ?? [] as [$typeCol, $targets]) {
        foreach ($targets as $ref) if ($ref !== $t && in_array($ref, $tables, true)) $deps[$t][] = $ref;
    }
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
    global $demo;
    $dump = $table === 'QR_code_settings' ? 'qr_code_settings' : $table;
    $cols = array_keys($schema[$dump]['cols']);
    if ($demo && (in_array($table, DEMO_CONTENT, true) || in_array($table, DEMO_SOURCE, true))) {
        // Demo export rows: this schema's columns; a column the export lacks gets its empty value.
        return array_map(function ($r) use ($cols, $schema, $dump) {
            $row = [];
            foreach ($cols as $c) {
                if (array_key_exists($c, $r)) {
                    // The export wrote the demo site's address as {{BASEURL}}: root-relative works on every school's host.
                    $row[$c] = $r[$c] === null ? null : str_replace('{{BASEURL}}', '/', (string) $r[$c]);
                } else {
                    $def = $schema[$dump]['cols'][$c];
                    $row[$c] = stripos($def, 'NOT NULL') === false ? null : (preg_match('/^(tiny|small|medium|big)?int|decimal|float|double/i', $def) ? '0' : '');
                }
            }
            return $row;
        }, $demo[$table] ?? []);
    }
    return array_map(function ($r) use ($cols) { return array_combine($cols, $r); }, $data[$dump] ?? []);
}
// Date columns of a table (shifted by the provisioner to the year the school starts in).
function date_columns($table, $schema) {
    $out = [];
    foreach ($schema[$table]['cols'] as $c => $def) {
        if (preg_match('/^(date|datetime|timestamp)\b/i', $def)) $out[] = $c;
    }
    return $out;
}

$kept = []; // table => [id => true], for filters on references
$rowsBy = [];
foreach ($tables as $t) $rowsBy[$t] = rows_of($t, $schema, $data);

// Rows added after the legacy data (not in the dump). Template ids from 900001 up.
// "Bank Transfer Payments" under Subscription (menu 38, permission group 1204: platform-only).
$rowsBy['sidebar_sub_menus'][] = [
    'id' => '900001', 'school_id' => '1', 'sidebar_menu_id' => '38', 'menu' => 'subscription_bank_transfers',
    'key' => 'subscription_bank_transfers', 'lang_key' => 'bank_transfer_payments', 'url' => 'admin/subscription_bank_transfers',
    'level' => null, 'access_permissions' => "('subscription_list', 'can_view')", 'permission_group_id' => '1204',
    'activate_controller' => 'subscription_bank_transfers', 'activate_methods' => 'index,view', 'addon_permission' => null,
    'is_active' => '1', 'created_at' => '2026-11-05 00:00:00',
];
// "Fees Setting" under System Setting (menu 27), beside General Setting (146), for every school.
$rowsBy['sidebar_sub_menus'][] = [
    'id' => '900002', 'school_id' => '1', 'sidebar_menu_id' => '27', 'menu' => 'fees_setting',
    'key' => null, 'lang_key' => 'fees_setting', 'url' => 'schsettings/fees',
    'level' => '1', 'access_permissions' => "('general_setting', 'can_view')", 'permission_group_id' => null,
    'activate_controller' => 'schsettings', 'activate_methods' => 'fees', 'addon_permission' => '',
    'is_active' => '1', 'created_at' => '2026-11-05 00:00:00',
];
// Platform administration (migration 258): permission group, categories, menus and sub menus.
// Platform-only through PLATFORM_PERMISSION_GROUPS (platform_admin, school_information).
$rowsBy['permission_group'][] = ['id' => '900010', 'school_id' => '1', 'name' => 'Platform administration', 'short_code' => 'platform_admin',
    'is_active' => '1', 'system' => '0', 'created_at' => '2026-11-05 00:00:00'];
foreach ([['900011', '900010', 'Platform settings and emails', 'platform_settings', '1', '0', '1', '0'],
          ['900012', '1202', 'Plans and features', 'plan_list', '1', '1', '1', '1'],
          ['900013', '1204', 'Subscriptions and bank transfers', 'subscription_list', '1', '0', '0', '1']] as $c) {
    $rowsBy['permission_category'][] = ['id' => $c[0], 'school_id' => '1', 'perm_group_id' => $c[1], 'name' => $c[2], 'short_code' => $c[3],
        'enable_view' => $c[4], 'enable_add' => $c[5], 'enable_edit' => $c[6], 'enable_delete' => $c[7], 'created_at' => '2026-11-05 00:00:00'];
}
foreach ([['900020', 'fa fa-users ftlayer', 'Platform Team', 'platform_team', '3', "('staff', 'can_view') || ('superadmin', 'can_view') || ('audit_trail_report', 'can_view') || ('user_log', 'can_view')"],
          ['900021', 'fa fa-envelope-o ftlayer', 'Communication', 'platform_communication', '4', "('platform_settings', 'can_view')"]] as $m) {
    $rowsBy['sidebar_menus'][] = ['id' => $m[0], 'school_id' => '1', 'permission_group_id' => '900010', 'icon' => $m[1], 'menu' => $m[2],
        'activate_menu' => $m[3], 'lang_key' => $m[3], 'system_level' => '0', 'level' => $m[4], 'sidebar_display' => '1',
        'access_permissions' => $m[5], 'is_active' => '1', 'created_at' => '2026-11-05 00:00:00'];
}
foreach ([['900030', '900020', 'platform_staff', 'platform_staff', 'admin/staff', '1', "('staff', 'can_view')", 'staff', 'index,edit,profile,create', '900010'],
          ['900031', '900020', 'platform_roles', 'roles_permissions', 'admin/roles', '2', "('superadmin', 'can_view')", 'roles', 'index,permission', '900010'],
          ['900032', '900020', 'platform_audit', 'audit_trail_report', 'admin/audit', '3', "('audit_trail_report', 'can_view')", 'audit', 'index', '900010'],
          ['900033', '900020', 'platform_login_history', 'login_history', 'admin/userlog', '4', "('user_log', 'can_view')", 'userlog', 'index', '900010'],
          ['900034', '900021', 'platform_email_templates', 'email_templates', 'admin/platform/emails', '1', "('platform_settings', 'can_view')", 'platform', 'emails,email_edit', '900010'],
          ['900035', '900021', 'platform_email_log', 'email_log', 'admin/platform/email_log', '2', "('platform_settings', 'can_view')", 'platform', 'email_log', '900010'],
          ['900036', '36', 'schools_waiting', 'waiting_for_approval', 'admin/schools?tab=waiting_approval', '3', "('schools', 'can_view')", 'schools', '', '1201'],
          ['900037', '27', 'platform_settings', 'platform_settings_health', 'admin/platform/settings', '0', "('platform_settings', 'can_view')", 'platform', 'settings', '900010']] as $x) {
    $rowsBy['sidebar_sub_menus'][] = ['id' => $x[0], 'school_id' => '1', 'sidebar_menu_id' => $x[1], 'menu' => $x[2], 'key' => $x[2],
        'lang_key' => $x[3], 'url' => $x[4], 'level' => $x[5], 'access_permissions' => $x[6], 'permission_group_id' => $x[9],
        'activate_controller' => $x[7], 'activate_methods' => $x[8], 'addon_permission' => '', 'is_active' => '1', 'created_at' => '2026-11-05 00:00:00'];
}
foreach ($rowsBy['sidebar_sub_menus'] as &$r) {
    // Platform pages added later: highlight their menu entry on the new pages too.
    if ($r['url'] === 'admin/schools') {
        $r['activate_methods'] = 'index,view,edit,save';
    }
    if ($r['url'] === 'admin/plan') {
        $r['activate_methods'] = 'index,create,edit,save';
    }
    if ($r['url'] === 'schsettings') {
        // The Fees Setting entry highlights on schsettings/fees, not General Setting.
        $r['activate_methods'] = implode(',', array_diff(explode(',', $r['activate_methods']), ['fees']));
    }
}
unset($r);

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
$schoolOnly = [];
$platformOverrides = []; // table => [template id => [column => value for the platform school]]
$filters = $demo ? [
    // Demo leftovers and items that don't suit a new school.
    'classes' => function ($r) { return stripos($r['class'], 'ICT technician') === false; },
    'front_cms_pages' => function ($r) { return !in_array($r['slug'], ['new-page-test', 'complain-1', 'privacy-policy'], true); },
    'front_cms_programs' => function ($r) { return !preg_match('/diwali|dussehra|gandhi|jayan/i', $r['title']); },
    // Links that aren't pages (online admission, exam results, CBSE): a school adds them when it uses them.
    'front_cms_menu_items' => function ($r) { return !empty($r['page_id']); },
] : [
    // Built-in pages only; school 1's own pages (about us, articles) are its content.
    'front_cms_pages' => function ($r) { return $r['page_type'] === 'default'; },
];

// The seed's current session (central sch_settings): demo content of the demo's current session
// moves to it; demo rows of other sessions are left out.
$seedSession = (string) $rowsBy['sch_settings'][0]['session_id'];
$seedStartMonth = (int) $rowsBy['sch_settings'][0]['start_month'];
if ($demo) {
    foreach ($tables as $t) {
        if (!in_array($t, DEMO_CONTENT, true) && !in_array($t, DEMO_SOURCE, true)) continue;
        $sessionCols = array_keys(array_filter(IMPLICIT_TARGETS[$t] ?? [], function ($ref) { return $ref === 'sessions'; }));
        foreach ($schema[$t]['fks'] as $f) {
            if (canonical_table($f['ref']) === 'sessions') $sessionCols[] = trim(str_replace('`', '', $f['cols']));
        }
        $sessionCols = array_values(array_unique($sessionCols)); // declared and implicit references may name the same column
        if (!$sessionCols) continue;
        $rowsBy[$t] = array_values(array_filter(array_map(function ($r) use ($sessionCols, $seedSession) {
            foreach ($sessionCols as $c) {
                if ($r[$c] === null || $r[$c] === '' || $r[$c] === '0') continue;
                if ($r[$c] !== DEMO_SESSION) return null;
                $r[$c] = $seedSession;
            }
            return $r;
        }, $rowsBy[$t])));
    }
    // Sidebar: school 1 is the platform school and hides the school modules from its sidebar; a
    // school's sidebar follows the demo school (every module shown). The platform school keeps
    // school 1's values ($platform_overrides, applied by the provisioner to the platform school only).
    $demoFlags = [
        'sidebar_menus' => ['key' => 'lang_key', 'flag' => 'sidebar_display'],
        'sidebar_sub_menus' => ['key' => 'url', 'flag' => 'is_active'],
    ];
    foreach ($demoFlags as $t => $spec) {
        $wanted = [];
        foreach ($demo[$t] ?? [] as $d) $wanted[(string) $d[$spec['key']]] = (string) $d[$spec['flag']];
        foreach ($rowsBy[$t] as &$r) {
            $k = (string) $r[$spec['key']];
            // Backups are a platform task (school-level backup and restore are switched off).
            if ($t === 'sidebar_sub_menus' && $k === 'admin/admin/backup') continue;
            $platformKeeps = $t === 'sidebar_sub_menus' && in_array($k, ['admin/front/events', 'admin/front/gallery', 'admin/front/notice'], true);
            if (isset($wanted[$k]) && (string) $r[$spec['flag']] !== $wanted[$k]) {
                if (!$platformKeeps) $platformOverrides[$t][$r['id']] = [$spec['flag'] => $r[$spec['flag']]];
                $r[$spec['flag']] = $wanted[$k];
            }
        }
        unset($r);
    }
    // Website pages: a new school starts with only its home page on (the 404 page is always on);
    // it switches the others on in Front CMS > Pages. The platform school keeps its pages on.
    foreach ($rowsBy['front_cms_pages'] as &$r) {
        $on = !empty($r['is_homepage']) || $r['slug'] === '404-page';
        if (!$on) $platformOverrides['front_cms_pages'][$r['id']] = ['is_active' => 'yes'];
        $r['is_active'] = $on ? 'yes' : 'no';
    }
    unset($r);
    // New schools start with their front website switched on.
    foreach ($rowsBy['front_cms_settings'] as &$r) $r['is_active_front_cms'] = '1';
    unset($r);
}

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
    $typed = TYPED_TARGETS[$t] ?? [];
    // Every *_id column is a reference, reset, or a known value, so no row can point at another school's data.
    foreach ($cols as $c) {
        if (!preg_match('/_id$/', $c) || preg_match('/^(enable|is)_/', $c) || isset($refs[$c]) || isset($typed[$c]) || isset($refsOut[$t][$c])
            || in_array($c, RESET_REFS[$t] ?? [], true) || in_array($c, OWNER_REFS[$t] ?? [], true) || in_array($c, NOT_REFERENCES['*'], true)
            || in_array($c, NOT_REFERENCES[$t] ?? [], true)) continue;
        fwrite(STDERR, "Unclassified column $t.$c: add it to IMPLICIT_TARGETS, RESET_REFS or NOT_REFERENCES.\n");
        exit(1);
    }

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
        foreach ($typed as $col => [$typeCol, $targets]) {
            $ref = $targets[$r[$typeCol]] ?? null;
            if ($ref === null || ($r[$col] !== null && $r[$col] !== '' && $r[$col] !== '0' && !isset($kept[$ref][$r[$col]]))) $orphan = true;
        }
        if ($orphan) continue;
        $isPlatform = isset($platformOnlyRoots[$t]) && $platformOnlyRoots[$t]($r);
        foreach ($refs as $col => $ref) {
            if ($ref !== $t && $r[$col] !== null && isset($platformOnly[$ref][$r[$col]])) $isPlatform = true;
        }
        if ($isPlatform) $platformOnly[$t][$r['id']] = true;
        // Demo content is for schools: the platform school (central website) keeps the built-in pages only.
        $isSchoolOnly = $demo && (in_array($t, DEMO_CONTENT, true)
            || ($t === 'front_cms_pages' && $r['page_type'] !== 'default')
            || ($t === 'front_cms_menu_items' && (empty($r['page_id']) || isset($schoolOnly['front_cms_pages'][$r['page_id']])))
            || ($t === 'front_cms_page_contents' && isset($schoolOnly['front_cms_pages'][$r['page_id']])));
        foreach ($refs as $col => $ref) {
            if ($ref !== $t && $r[$col] !== null && isset($schoolOnly[$ref][$r[$col]])) $isSchoolOnly = true;
        }
        if ($isSchoolOnly) $schoolOnly[$t][$r['id']] = true;
        foreach (BLANK[$t] ?? [] as $col) {
            $r[$col] = stripos($s['cols'][$col], 'NOT NULL') === false ? null : '';
        }
        foreach (array_merge(RESET_REFS[$t] ?? [], OWNER_REFS[$t] ?? []) as $col) {
            $r[$col] = stripos($s['cols'][$col], 'NOT NULL') === false ? null : '0';
        }
        foreach ($refsOut[$t] ?? [] as $col => $_) {
            $r[$col] = stripos($s['cols'][$col], 'NOT NULL') === false ? null : '0';
        }
        // School 1's home page (platform marketing) and contact page (its address and phone) are its own content.
        if (!$demo && $t === 'front_cms_pages' && in_array($r['slug'], ['home', 'contact-us'], true)) {
            $r['description'] = '';
        }
        // "Create School" under Schools in the platform sidebar (inactive in the old data).
        if ($t === 'sidebar_sub_menus' && $r['url'] === 'admin/schools/create') {
            $r['is_active'] = '1';
        }
        unset($r['school_id']);
        $kept[$t][$r['id']] = true;
        $out[] = $r;
    }
    // Menu items whose page was not kept
    if ($t === 'front_cms_menu_items' || $t === 'front_cms_page_contents') {
        $out = array_values(array_filter($out, function ($r) use ($kept, $demo, $t) {
            if ($demo && $t === 'front_cms_menu_items' && empty($r['page_id'])) return true; // a link, not a page
            return !empty($r['page_id']) && isset($kept['front_cms_pages'][$r['page_id']]);
        }));
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
    $fromDemo = $demo && (in_array($t, DEMO_CONTENT, true) || in_array($t, DEMO_SOURCE, true));
    $group = in_array($t, REQUIRED, true) ? 'required' : (in_array($t, STARTER_LISTS, true) ? 'starter list'
        : (in_array($t, DEMO_CONTENT, true) ? 'demo content' : 'template (school details removed)'));
    $extra = '';
    if (!empty($platformOverrides[$t])) {
        $extra .= "    /** @var array template id => [column => value] used for the platform school instead */\n"
            . '    public $platform_overrides = ' . str_replace("\n", "\n    ", var_export($platformOverrides[$t], true)) . ";\n\n";
    }
    if (!empty(OWNER_REFS[$t])) {
        $extra .= "    /** @var string[] columns set to the school's super admin (staff id) when loaded */\n"
            . "    public \$owner_columns = [" . implode(', ', array_map(function ($c) { return var_export($c, true); }, OWNER_REFS[$t])) . "];\n\n";
    }
    if ($typed) {
        $extra .= "    /** @var array column => [type column, [type => referenced default table]] */\n"
            . '    public $typed_references = ' . str_replace("\n", "\n    ", var_export($typed, true)) . ";\n\n";
    }
    if ($fromDemo && ($dateCols = array_values(array_diff(date_columns($t, $schema), ['created_at', 'updated_at'])))) {
        $extra .= "    /** @var string[] demo dates, moved forward by the years between DEMO_START_YEAR and the school's first session */\n"
            . "    public \$shift_years = [" . implode(', ', array_map(function ($c) { return var_export($c, true); }, $dateCols)) . "];\n\n"
            . '    public $demo_start_year = ' . DEMO_START_YEAR . ";\n\n";
    }
    if ($t === 'sessions') {
        $extra .= "    /** @var int template id of the current session in the default data; the provisioner points it at the academic year the school starts in */\n"
            . '    public $current_template_id = ' . (int) $seedSession . ";\n\n"
            . "    /** @var int first month of the academic year */\n"
            . '    public $start_month = ' . ($seedStartMonth ?: 9) . ";\n\n";
    }
    $lname = strtolower($t);
    $refText = $refs ? implode(', ', array_map(function ($c, $r) { return "'$c' => '$r'"; }, array_keys($refs), $refs)) : '';
    $php = "<?php\n\ndefined('BASEPATH') or exit('No direct script access allowed');\n\n"
        . "/**\n * Default `$t` for a new school ($group).\n *\n"
        . ($fromDemo ? " * Source: the demo school (smartschool_test.json), people and their records excluded.\n"
                     : " * Source: school 1 (central smartschool data), school-specific values removed.\n")
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
        . (isset($schoolOnly[$t]) ? "    /** @var int[] template ids not loaded for the platform school (demo school content) */\n"
            . "    public \$school_only = [" . implode(', ', array_keys($schoolOnly[$t])) . "];\n\n" : '')
        . $extra
        . "    public function rows()\n    {\n        return [\n" . implode("\n", $body) . "\n        ];\n    }\n}\n";
    file_put_contents(sprintf('%s/%03d_%s.php', $outDir, $seq[$t] + 1, $lname), $php);
    $summary[] = sprintf('%-32s %-34s %4d rows', $t, $group, count($out)) . (isset($platformOnly[$t]) ? ' (' . count($platformOnly[$t]) . ' platform-only)' : '');
}
echo implode("\n", $summary) . "\n" . count($tables) . " tables\n";
