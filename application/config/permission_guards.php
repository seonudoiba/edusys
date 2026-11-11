<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Permission checks for admin actions that change data but had none of their own
 * (Admin_Controller::guard_action). Key: "<directory/>controller/method" in lower case.
 * Value: [permission category, action]. When a category has no such action (e.g. no "delete"),
 * "can_edit" is required instead.
 */
$config['permission_guards'] = array(
    // fees
    'feecategory/delete' => array('fees_type', 'can_delete'),
    'feemaster/delete' => array('fees_master', 'can_delete'),
    'feetype/delete' => array('fees_type', 'can_delete'),
    'studentfee/deletestudentdiscount' => array('collect_fees', 'can_delete'),
    'studentfee/deletetransportfee' => array('collect_fees', 'can_delete'),
    'studentfee/delete' => array('collect_fees', 'can_delete'),
    'admin/feediscount/delete' => array('fees_discount', 'can_delete'),
    'admin/feemaster/deletegrp' => array('fees_master', 'can_delete'),
    // students
    'student/bulkdelete' => array('student', 'can_delete'),
    'student/disablestudent' => array('student', 'can_edit'),
    'student/enablestudent' => array('student', 'can_edit'),
    'student/disable_reason' => array('student', 'can_edit'),
    'admin/timeline/delete_timeline' => array('student_timeline', 'can_delete'),
    'admin/alumni/deletestudent' => array('manage_alumni', 'can_delete'),
    'admin/alumni/delete_event' => array('events', 'can_delete'),
    // teaching
    'homework/deletehomework' => array('homework', 'can_delete'),
    'admin/examgroup/deleteexam' => array('exam', 'can_delete'),
    'admin/examschedule/delete' => array('exam_schedule', 'can_delete'),
    'admin/mark/delete' => array('exam_marks', 'can_delete'),
    'admin/onlineexam/delete' => array('online_examination', 'can_delete'),
    'admin/onlineexam/deleteexamquestions' => array('add_questions_in_exam', 'can_delete'),
    'admin/syllabus/delete_subject_syllabus' => array('manage_lesson_plan', 'can_delete'),
    'admin/syllabus/deletemessage' => array('lesson_plan_comments', 'can_delete'),
    'admin/timetable/delete' => array('class_timetable', 'can_delete'),
    'admin/content/delete_content' => array('upload_content', 'can_delete'),
    'admin/content/delete' => array('upload_content', 'can_delete'),
    'admin/content/delete_array' => array('upload_content', 'can_delete'),
    'admin/conference/delete' => array('live_classes', 'can_delete'),
    'admin/gmeet/delete' => array('gmeet_live_classes', 'can_delete'),
    // attendance, leave, staff
    'admin/approve_leave/status' => array('approve_leave', 'can_edit'),
    'admin/approve_leave/remove_leave' => array('approve_leave', 'can_delete'),
    'admin/leaverequest/remove' => array('approve_leave_request', 'can_delete'),
    'admin/staff/delete_rateing' => array('teachers_rating', 'can_delete'),
    'admin/timeline/delete_staff_timeline' => array('staff_timeline', 'can_delete'),
    'admin/adminuser/delete' => array('staff', 'can_delete'),
    'admin/staffidcard/delete' => array('staff_id_card', 'can_delete'),
    'admin/studentidcard/delete' => array('student_id_card', 'can_delete'),
    // front office, transport, inventory
    'admin/enquiry/change_status' => array('admission_enquiry', 'can_edit'),
    'admin/pickuppoint/delete' => array('pickup_point', 'can_delete'),
    'admin/pickuppoint/delete_point' => array('route_pickup_point', 'can_delete'),
    'admin/vehroute/delete' => array('assign_vehicle', 'can_delete'),
    'admin/issueitem/delete' => array('issue_item', 'can_delete'),
    // settings and logs
    'admin/captcha/changestatus' => array('general_setting', 'can_edit'),
    'admin/systemfield/changestatus' => array('system_fields', 'can_edit'),
    'admin/customfield/delete' => array('custom_fields', 'can_delete'),
    'admin/module/changestatus' => array('superadmin', 'can_view'), // Super Admin only
    'admin/audit/delete' => array('audit_trail_report', 'can_delete'),
    'admin/userlog/delete' => array('user_log', 'can_delete'),
    'admin/chat/delete_message' => array('chat', 'can_view'),
);
