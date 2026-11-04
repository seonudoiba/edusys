<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Shared classification used by both generators.

// Platform tables: one copy for the whole platform, no school_id.
const PLATFORM_TABLES = ['school', 'plan_list', 'plan', 'subscription', 'settings', 'languages'];

// Hybrid tables: nullable school_id. NULL = system row shared by every school
// (role ids 1..7 are hard-coded throughout the code), non-NULL = school-specific row.
const HYBRID_TABLES = ['roles'];

// Never generated: CI manages `migrations`; `qr_code_settings` is merged into `QR_code_settings`.
const SKIP_TABLES = ['migrations', 'qr_code_settings'];

// Dump table name -> canonical table name.
const TABLE_ALIASES = ['qr_code_settings' => 'QR_code_settings'];

// Tables whose schema is migrated but whose data is not seeded.
// multi_branch rows hold another database's connection credentials (legacy multi-DB branches).
const NO_SEED_TABLES = ['multi_branch'];

// ID shift applied to every school-2 row id and every reference to a school-owned row.
const SCHOOL_2_ID_OFFSET = 1000000;

// Undeclared reference columns that point to school-owned rows (shifted for school 2).
const IMPLICIT_TENANT_REFS = [
    'cbse_exam_assessment_types' => ['created_by'],
    'cbse_exam_grades_range' => ['created_by'],
    'cbse_exam_students' => ['staff_id', 'delete_student_id'],
    'cbse_exam_timetable' => ['created_by'],
    'cbse_exams' => ['created_by'],
    'cbse_observation_class_section' => ['cbse_observation_parameter_id', 'class_section_id'],
    'cbse_observation_subparameter' => ['created_by'],
    'cbse_student_subject_result' => ['cbse_exam_timetable_id', 'cbse_exam_student_id'],
    'cbse_template' => ['created_by'],
    'cbse_terms' => ['created_by'],
    'conferences' => ['class_id', 'section_id', 'session_id'],
    'course_lesson_quiz_order' => ['lesson_quiz_id'],
    'course_progress' => ['guest_id', 'lesson_quiz_id'],
    'course_quiz_answer' => ['guest_id'],
    'course_rating' => ['student_id', 'guest_id', 'course_id'],
    'custom_field_values' => ['belong_table_id'],
    'email_template_attachment' => ['email_template_id'],
    'exam_schedules' => ['teacher_subject_id'],
    'feetype' => ['feecategory_id'],
    'front_cms_menu_items' => ['page_id', 'parent_id'],
    'front_cms_program_photos' => ['media_gallery_id'],
    'guest' => ['currency_id'],
    'item_issue' => ['issue_to', 'issue_by'],
    'libarary_members' => ['member_id'],
    'logs' => ['user_id', 'record_id'],
    'messages' => ['email_template_id', 'sms_template_id', 'schedule_class', 'schedule_section'],
    'online_admissions' => ['route_id', 'vehroute_id'],
    'online_course_payment' => ['student_id', 'guest_id', 'online_courses_id'],
    'online_course_processing_payment' => ['student_id', 'guest_id', 'online_courses_id', 'gateway_ins_id'],
    'online_courses' => ['teacher_id', 'category_id'],
    'print_headerfooter' => ['created_by'],
    'read_notification' => ['parent_id'],
    'sch_settings' => ['currency', 'session_id', 'hostel_id'],
    'staff' => ['currency_id', 'user_id'],
    'staff_payslip' => ['generated_by'],
    'staff_rating' => ['user_id'],
    'student_doc' => ['student_id'],
    'student_incident_comments' => ['staff_id', 'student_id'],
    'student_incidents' => ['session_id', 'assign_by'],
    'student_quiz_status' => ['guest_id'],
    'student_timeline' => ['created_student_id'],
    'student_transport_fees' => ['generated_by'],
    'students' => ['parent_id', 'category_id', 'school_house_id', 'hostel_room_id'],
    'user_google_authenticate_codes' => ['guest_id'],
    'users' => ['user_id', 'currency_id'],
    'users_authentication' => ['users_id', 'staff_id'],
];

// JSON columns whose listed keys hold ids of school-owned rows (shifted for school 2).
const JSON_TENANT_REFS = [
    'messages' => ['user_list' => ['user_id']],
    'student_fees_deposite' => ['amount_detail' => ['received_by']],
];

function canonical_table($t) { return TABLE_ALIASES[$t] ?? $t; }
function table_scope($t) {
    $t = canonical_table($t);
    if (in_array($t, PLATFORM_TABLES, true)) return 'platform';
    if (in_array($t, HYBRID_TABLES, true)) return 'hybrid';
    return 'tenant';
}

// Comma-separated id lists of school-owned rows (shifted element-wise for school 2).
const CSV_TENANT_REFS = [
    'users' => ['childs'], // parent account -> its students' ids
];
