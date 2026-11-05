<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Per-school uploads (SINGLE_DB_TENANCY_PLAN.md §4.7, phase 9)
| -------------------------------------------------------------------------
| Files live in uploads/s<school id>/<path>. Code and database keep the logical path
| "uploads/<path>"; upload_root() / upload_fs_path() (helpers/upload_helper.php) map it to
| the current school's folder, and /uploads/<path> URLs are served by the Files controller
| for the school of the host.
|
| uploads/_shared/ holds the default images every school starts with (copied into a
| school's folder when it is created). Neither folder is reachable directly from the web.
*/

/** Folders created in every school's upload folder (code writes into them without mkdir). */
$config['upload_folders'] = array(
    'accountant_images', 'admission_form', 'admit_card', 'alumni_event_images', 'alumni_student_images',
    'book', 'cbseexam/template/background_img', 'cbseexam/template/header_image', 'cbseexam/template/left_sign',
    'cbseexam/template/middle_sign', 'cbseexam/template/right_sign', 'certificate',
    'communicate/email_attachments', 'communicate/email_template_images', 'course/course_thumbnail',
    'course_content', 'front_office/complaints', 'front_office/dispatch_receive', 'front_office/visitors',
    'gallery/event_images/thumb', 'gallery/media/thumb', 'gallery/youtube_video/thumb', 'guest_images',
    'homework/assignment', 'homework/daily_assignment', 'inventory_items', 'librarian_images', 'marksheet',
    'notice_board_images', 'offline_payments', 'onlinexam_images',
    'print_headerfooter/online_admission_receipt', 'print_headerfooter/online_exam',
    'print_headerfooter/staff_payslip', 'print_headerfooter/student_receipt',
    'school_content/admin_logo', 'school_content/admin_small_logo', 'school_content/login_image',
    'school_content/logo/app_logo', 'school_content/material/media/thumb', 'school_expense', 'school_income',
    'staff_documents', 'staff_id_card/background', 'staff_id_card/barcodes', 'staff_id_card/logo',
    'staff_id_card/qrcode', 'staff_id_card/signature', 'staff_images', 'staff_timeline',
    'student_documents/online_admission_doc', 'student_id_card/background', 'student_id_card/barcodes',
    'student_id_card/logo', 'student_id_card/qrcode', 'student_id_card/signature',
    'student_images/online_admission_image', 'student_leavedocuments', 'student_timeline',
    'subscription_receipts', 'syllabus_attachment/lacture_video', 'teacher_images', 'transport_vehicle',
    'vehicle_photo', 'video_tutorial/youtube_video/thumb',
);

/**
 * Private folders: served only to a user logged in to the same school (staff, student or
 * parent). Everything else (photos, logos, gallery, course material) is served to anyone on
 * the school's own host, as before, but never to another school's host.
 */
$config['upload_private_prefixes'] = array(
    'staff_documents/', 'student_documents/', 'student_leavedocuments/', 'staff_timeline/',
    'student_timeline/', 'offline_payments/', 'subscription_receipts/', 'school_income/',
    'school_expense/', 'front_office/', 'communicate/email_attachments/', 'homework/',
    'admission_form/', 'inventory_items/',
);

/**
 * Default images (in uploads/_shared/) served when a page asks for an image of these folders that
 * the school hasn't uploaded: the folder itself (an empty setting, e.g. ".../admin_logo/") or a
 * missing file. Longest matching folder wins.
 */
$config['upload_default_images'] = array(
    'school_content/admin_logo/' => 'school_content/default_logo.png',
    'school_content/admin_small_logo/' => 'school_content/default_logo.png',
    'school_content/logo/app_logo/' => 'school_content/default_logo.png',
    'school_content/logo/' => 'school_content/default_logo.png',
    'school_content/login_image/' => 'school_content/login_image/default_login_bg.jpg',
    'student_images/' => 'student_images/no_image.png',
    'staff_images/' => 'staff_images/no_image.png',
    'teacher_images/' => 'teacher_images/no_image.png',
    'librarian_images/' => 'librarian_images/no_image.png',
    'accountant_images/' => 'staff_images/no_image.png',
    'guest_images/' => 'staff_images/no_image.png',
);

/** Served inline (shown in the browser). Anything else is sent as a download. */
$config['upload_inline_types'] = array(
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
    'webp' => 'image/webp', 'bmp' => 'image/bmp', 'ico' => 'image/x-icon', 'pdf' => 'application/pdf',
    'mp4' => 'video/mp4', 'webm' => 'video/webm', 'mp3' => 'audio/mpeg', 'ogg' => 'audio/ogg',
);
