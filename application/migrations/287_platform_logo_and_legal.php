<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The platform's new EduSYS logo in the database (the admin panel, printouts and Front CMS settings):
 *
 *   sch_settings.admin_logo        edusys-logo-white.png   admin header, login pages
 *   sch_settings.admin_small_logo  edusys-mark.png         collapsed sidebar, admin favicon
 *   sch_settings.image             edusys-logo-print.jpg   receipts, reports, ID cards
 *   sch_settings.app_logo          edusys-logo.png         mobile app
 *   front_cms_settings.logo        edusys-logo.png         website logo setting
 *   front_cms_settings.fav_icon    edusys-favicon.png      website favicon setting
 *
 * The files come from application/seeds/platform_content/files/ and are copied into the platform
 * school's uploads (uploads/s<id>/school_content/...). A column changes only if its file was copied,
 * so a failed copy leaves the old logo in place. The old files are not deleted.
 *
 * Also reloads the Privacy Policy and Terms of Service from application/seeds/platform_content/pages/
 * (their layout now puts each subsection in a card, like the FleetMan documents).
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Platform_logo_and_legal extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $school = $db->select('id')->where('is_platform', 1)->get('school')->row();
            if (!$school) {
                return; // a fresh install: the content seed brings the same files
            }
            $sid = (int) $school->id;
            foreach (array('privacy-policy', 'terms-of-service') as $slug) {
                $file = APPPATH . 'seeds/platform_content/pages/' . $slug . '.html';
                if (is_file($file)) {
                    $db->where(array('school_id' => $sid, 'slug' => $slug))->update('front_cms_pages', array('description' => file_get_contents($file)));
                }
            }
            $from = APPPATH . 'seeds/platform_content/files/';
            $to = FCPATH . 'uploads/s' . $sid . '/school_content/';
            $copy = function ($file, $dir) use ($from, $to) {
                if (!is_file($from . $file)) {
                    log_message('error', 'Migration 287: missing ' . $from . $file);
                    return false;
                }
                if (!is_dir($to . $dir) && !@mkdir($to . $dir, 0775, true)) {
                    log_message('error', 'Migration 287: cannot create ' . $to . $dir);
                    return false;
                }
                if (!@copy($from . $file, $to . $dir . '/' . $file)) {
                    log_message('error', 'Migration 287: cannot copy ' . $file . ' to ' . $to . $dir);
                    return false;
                }
                @chmod($to . $dir . '/' . $file, 0664);
                return true;
            };

            $settings = array();
            if ($copy('edusys-logo-white.png', 'admin_logo')) {
                $settings['admin_logo'] = 'edusys-logo-white.png';
            }
            if ($copy('edusys-mark.png', 'admin_small_logo')) {
                $settings['admin_small_logo'] = 'edusys-mark.png';
            }
            if ($copy('edusys-logo-print.jpg', 'logo')) {
                $settings['image'] = 'edusys-logo-print.jpg';
            }
            $website_logo = $copy('edusys-logo.png', 'logo');
            if ($website_logo) {
                $settings['app_logo'] = 'edusys-logo.png';
            }
            if ($settings) {
                $db->where('school_id', $sid)->update('sch_settings', $settings);
            }

            $front = array();
            if ($website_logo) {
                $front['logo'] = './uploads/school_content/logo/edusys-logo.png';
            }
            if ($copy('edusys-favicon.png', 'logo')) {
                $front['fav_icon'] = './uploads/school_content/logo/edusys-favicon.png';
            }
            if ($front) {
                $db->where('school_id', $sid)->update('front_cms_settings', $front);
            }
            echo 'Platform logo: ' . implode(', ', array_merge(array_keys($settings), array_keys($front))) . PHP_EOL;
        });
    }

    public function down()
    {
        // The previous file names are in the pre-deploy dump; the old files are still in uploads.
    }
}
