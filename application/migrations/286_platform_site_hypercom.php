<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The platform website (edusys.ng), for the redesign:
 *   - About, Privacy Policy and Terms of Service get their new text, from
 *     application/seeds/platform_content/pages/<slug>.html (the same files the content seed uses);
 *   - the company is called "HyperCom" everywhere ("Hypercom Network Ltd" and "Hypercom" before).
 * Only the platform school's pages and website settings change; school websites are untouched.
 *
 * Hand-written migration (not produced by tools/tenancy/gen_migrations.php).
 */
class Migration_Platform_site_hypercom extends CI_Migration
{
    public function up()
    {
        $this->db->without_tenant(function ($db) {
            $school = $db->select('id')->where('is_platform', 1)->get('school')->row();
            if (!$school) {
                return; // a fresh install: the content seed brings the same pages
            }
            $sid = (int) $school->id;
            $dir = APPPATH . 'seeds/platform_content/pages/';
            $titles = array('about-us' => 'About HyperCom', 'privacy-policy' => 'Privacy Policy', 'terms-of-service' => 'Terms of Service');
            foreach ($titles as $slug => $title) {
                $html = is_file($dir . $slug . '.html') ? file_get_contents($dir . $slug . '.html') : '';
                if ($html === '') {
                    continue;
                }
                $db->where(array('school_id' => $sid, 'slug' => $slug))->update('front_cms_pages', array('title' => $title, 'description' => $html));
            }
            // The rest of the platform's pages and its footer text: the new name.
            foreach ($db->select('id, title, meta_title, meta_description, description')->where('school_id', $sid)->get('front_cms_pages')->result_array() as $p) {
                $new = array();
                foreach (array('title', 'meta_title', 'meta_description', 'description') as $col) {
                    $value = self::rename((string) $p[$col]);
                    if ($value !== (string) $p[$col]) {
                        $new[$col] = $value;
                    }
                }
                if ($new) {
                    $db->where('id', (int) $p['id'])->update('front_cms_pages', $new);
                }
            }
            $front = $db->select('id, footer_text')->where('school_id', $sid)->get('front_cms_settings')->row_array();
            if ($front) {
                $footer = trim(self::rename((string) $front['footer_text']));
                if (preg_match('/^©?\s*HyperCom\b.*All rights reserved\.?$/i', $footer)) {
                    $footer = '© HyperCom. All rights reserved.';
                }
                $db->where('id', (int) $front['id'])->update('front_cms_settings', array('footer_text' => $footer));
            }
        });
    }

    public function down()
    {
        // Website copy: nothing to undo (the previous text is in the pre-deploy dump).
    }

    /** "Hypercom Network Ltd" and "Hypercom" become "HyperCom"; the hypercom.net.ng addresses stay. */
    private static function rename($text)
    {
        $text = preg_replace('/Hypercom Network (Ltd|Limited)\.?/', 'HyperCom', $text);
        return preg_replace('/\bHypercom\b/', 'HyperCom', $text);
    }
}
