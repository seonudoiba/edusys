<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The exam area of a school's sidebar:
 *   Report Cards  Classes & Results (class → subjects → assessments → scores → report cards),
 *                 Broadsheet & Transcripts, Result Checker PINs, Promotion, Grading Scales, Terms, Comment Bank
 *   CBT Exams     computer-based tests and the question bank (Online Examinations)
 * The older screens (exams list, exam timetable, score breakdowns, report card designs and printing,
 * skills & conduct ratings, result reports, exam settings) and the classic Exam Groups reports are
 * hidden; their pages and data stay. Labels: helpers/nav_label_helper.php.
 * Migrations 309, 310 for existing schools, School_provisioner for new ones. Running it again changes nothing.
 */
class Exam_menus
{
    /** Report Cards entries, in order: url => [key, access, controller, methods] */
    const REPORT_CARDS = array(
        'cbseexam/results' => array('class_results', "('cbse_exam', 'can_view') || ('cbse_exam_marks', 'can_view')", 'results', 'index,klass,subject,scores,comments,card'),
        'cbseexam/broadsheet' => null,
        'cbseexam/pins' => null,
        'cbseexam/promotion' => null,
        'cbseexam/grade/gradelist' => null,
        'cbseexam/term' => null,
        'cbseexam/exam/comments' => array('comment_bank', "('cbse_exam_teacher_remark', 'can_view')", 'exam', 'comments'),
    );

    /** hidden (pages still open by their address) */
    const HIDDEN = array('cbseexam/exam', 'cbseexam/exam/examtimetable', 'cbseexam/assessment', 'cbseexam/observationparameter', 'cbseexam/observation',
        'cbseexam/observation/assign', 'cbseexam/setting/index', 'cbseexam/template', 'cbseexam/result/marksheet', 'cbseexam/report/index', 'admin/examresult/examinations');

    /** Call inside without_tenant (or with the school as tenant). */
    public function install($school_id)
    {
        $db = get_instance()->db;
        $sid = (int) $school_id;
        $rc = $db->where(array('school_id' => $sid, 'lang_key' => 'cbse_exam'))->get('sidebar_menus')->row_array();
        if (!$rc) {
            return false;
        }
        $db->where(array('school_id' => $sid, 'id' => (int) $rc['id']))->update('sidebar_menus', array('icon' => 'fa fa-file-text-o', 'menu' => 'Report Cards'));
        $exams = $db->where(array('school_id' => $sid, 'lang_key' => 'school_exams'))->get('sidebar_menus')->row_array();
        $menus = array((int) $rc['id']);
        if ($exams) {
            $menus[] = (int) $exams['id'];
            $db->where('id', (int) $exams['id'])->update('sidebar_menus', array('sidebar_display' => 0));
        }
        $level = 1;
        foreach (self::REPORT_CARDS as $url => $new) {
            $row = $db->where(array('school_id' => $sid, 'url' => $url))->get('sidebar_sub_menus')->row_array();
            if (!$row && $new) {
                $db->insert('sidebar_sub_menus', array('school_id' => $sid, 'sidebar_menu_id' => (int) $rc['id'], 'menu' => $new[0], 'key' => $new[0], 'lang_key' => $new[0],
                    'url' => $url, 'level' => $level, 'access_permissions' => $new[1], 'permission_group_id' => $rc['permission_group_id'], 'activate_controller' => $new[2],
                    'activate_methods' => $new[3], 'addon_permission' => '', 'is_active' => 1));
            } elseif ($row) {
                $db->where('id', (int) $row['id'])->update('sidebar_sub_menus', array('sidebar_menu_id' => (int) $rc['id'], 'level' => $level, 'is_active' => 1));
            }
            $level++;
        }
        $db->where('school_id', $sid)->where_in('url', self::HIDDEN)->update('sidebar_sub_menus', array('is_active' => 0));
        // CBT Exams: the menu is Online Examinations, named for what it is; CBTs by class first
        $db->where(array('school_id' => $sid, 'lang_key' => 'online_examinations'))->update('sidebar_menus', array('menu' => 'CBT Exams', 'icon' => 'fa fa-laptop'));
        $cbt = $db->where(array('school_id' => $sid, 'lang_key' => 'online_examinations'))->get('sidebar_menus')->row_array();
        if ($cbt && !$db->where(array('school_id' => $sid, 'url' => 'admin/cbtexams'))->count_all_results('sidebar_sub_menus')) {
            $db->set('level', 'level + 1', false)->where(array('school_id' => $sid, 'sidebar_menu_id' => (int) $cbt['id']))->update('sidebar_sub_menus');
            $db->insert('sidebar_sub_menus', array('school_id' => $sid, 'sidebar_menu_id' => (int) $cbt['id'], 'menu' => 'cbt_by_class', 'key' => 'cbt_by_class', 'lang_key' => 'cbt_by_class',
                'url' => 'admin/cbtexams', 'level' => 1, 'access_permissions' => "('online_examination', 'can_view')", 'permission_group_id' => $cbt['permission_group_id'],
                'activate_controller' => 'cbtexams', 'activate_methods' => 'index,klass,subject', 'addon_permission' => '', 'is_active' => 1));
        }
        return true;
    }
}
