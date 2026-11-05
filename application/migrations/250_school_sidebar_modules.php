<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Shows the school modules in the sidebar of schools set up from the old default data.
 *
 * The default data's sidebar came from school 1, the platform school, which hides every school
 * module (Front Office, Student Information, Fees, Academics, ...): new schools started with only
 * Front CMS and System Setting. The default seed now follows a normal school's sidebar; this
 * migration does the same for schools already set up whose sidebar is still that trimmed one
 * (two menus or fewer shown), so a sidebar a school admin has changed is left alone.
 * The platform school is not touched. Backup Restore stays hidden (a platform task).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_School_sidebar_modules extends CI_Migration
{
    private $menus = array(
        'front_office', 'student_information', 'fees_collection', 'income', 'expenses', 'attendance',
        'examinations', 'online_examinations', 'lesson_plan', 'academics', 'human_resource', 'communicate',
        'download_center', 'homework', 'library', 'inventory', 'transport', 'hostel', 'certificate', 'alumni',
        'reports', 'online_course', 'gmeet_live_classes', 'zoom_live_classes', 'behaviour_records',
        'two_factor_authentication', 'cbse_exam', 'qr_code_attendance',
    );

    private $sub_menus = array(
        'admin/front/events', 'admin/front/gallery', 'admin/front/notice', 'admin/notification/setting',
        'smsconfig', 'admin/paymentsettings', 'admin/print_headerfooter', 'admin/roles', 'admin/users',
        'admin/module', 'admin/customfield', 'admin/captcha', 'admin/systemfield', 'student/profilesetting',
        'admin/onlineadmission/admissionsetting', 'admin/admin/filetype', 'admin/sidemenu',
    );

    public function up()
    {
        $menus = $this->menus;
        $sub_menus = $this->sub_menus;
        $this->db->without_tenant(function ($db) use ($menus, $sub_menus) {
            $schools = $db->select('id')->where('is_platform', 0)->get('school')->result();
            foreach ($schools as $school) {
                $id = (int) $school->id;
                $shown = $db->where(array('school_id' => $id, 'sidebar_display' => 1))->count_all_results('sidebar_menus');
                $total = $db->where('school_id', $id)->count_all_results('sidebar_menus');
                if ($total === 0 || $shown > 2) {
                    continue; // not set up, or a sidebar that isn't the trimmed default
                }
                $db->where('school_id', $id)->where_in('lang_key', $menus)->update('sidebar_menus', array('sidebar_display' => 1));
                $db->where('school_id', $id)->where_in('url', $sub_menus)->update('sidebar_sub_menus', array('is_active' => 1));
            }
        });
    }

    public function down()
    {
        // Not reversed: hiding a school's modules again isn't a useful state to return to.
    }
}
