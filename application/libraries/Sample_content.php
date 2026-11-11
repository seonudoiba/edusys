<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The sample website of the default data was an Indian school's ("Mount Carmel School, Bhopal":
 * Independence Day, Fit India Week, bhajan sandhya, NCC...). This removes it from a school:
 *
 *   - sample events, gallery albums and news are deleted (with their photo links; the banner stays);
 *   - sample pages keep their place in the menus, with a short note to replace their text.
 *
 * clean($school_id, $only_unchanged): with $only_unchanged (existing schools), a page or post is
 * touched only while its text is still exactly the sample's, so nothing the school wrote is lost.
 */
class Sample_content
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        require_once APPPATH . 'libraries/Seeder.php';
    }

    /** @return string what was removed, or '' */
    public function clean($school_id, $only_unchanged)
    {
        $db = $this->CI->db;
        $sid = (int) $school_id;
        $pages = 0;
        $posts = 0;

        foreach ($this->rows('057_front_cms_pages.php') as $p) {
            if (!empty($p['is_homepage']) || in_array($p['slug'], array('404-page', 'contact-us', 'complain'), true)) {
                continue; // the homepage has its own layout; these pages hold forms, not sample text
            }
            if (!preg_match('/Mount Carmel|Bhopal|India|lorem ipsum/i', strip_tags((string) $p['description']))) {
                continue;
            }
            $row = $db->select('id, description')->where(array('school_id' => $sid, 'slug' => $p['slug']))->get('front_cms_pages')->row_array();
            if (!$row || ($only_unchanged && $row['description'] !== $p['description'])) {
                continue;
            }
            $db->where('id', (int) $row['id'])->update('front_cms_pages', array(
                'description' => '<p>This page is ready for your school\'s ' . html_escape(strtolower($p['title'])) . '. Replace this text in Front CMS &gt; Pages.</p>',
                'meta_description' => '',
                'meta_keyword' => '',
            ));
            $pages++;
        }

        foreach ($this->rows('060_front_cms_programs.php') as $p) {
            if ($p['type'] === 'banner') {
                continue;
            }
            $where = array('school_id' => $sid, 'slug' => $p['slug'], 'type' => $p['type']);
            if ($only_unchanged) {
                $where['title'] = $p['title'];
            }
            $row = $db->select('id, description')->where($where)->get('front_cms_programs')->row_array();
            if (!$row || ($only_unchanged && (string) $row['description'] !== (string) $p['description'])) {
                continue;
            }
            $db->where(array('school_id' => $sid, 'program_id' => (int) $row['id']))->delete('front_cms_program_photos');
            $db->where('id', (int) $row['id'])->delete('front_cms_programs');
            $posts++;
        }
        return $pages || $posts ? "sample website: $pages pages blanked, $posts events/news/albums removed" : '';
    }

    /** The rows of a default seed file, as column => value. */
    private function rows($file)
    {
        $path = APPPATH . 'seeds/default/' . $file;
        if (!is_file($path)) {
            return array();
        }
        $before = get_declared_classes();
        require_once $path;
        $class = null;
        foreach (array_diff(get_declared_classes(), $before) as $c) {
            $class = $c;
        }
        if ($class === null) { // already loaded earlier in this request
            $class = 'Seed_default_' . preg_replace('/^\d+_/', '', basename($file, '.php'));
        }
        $seed = new $class();
        $out = array();
        foreach ($seed->rows() as $r) {
            $out[] = array_combine($seed->columns, $r);
        }
        return $out;
    }
}
