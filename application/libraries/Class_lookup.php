<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Finds a class and section of the current school from the names typed in an import file.
 * Names are compared without case, spaces or punctuation, so "JSS 1", "jss1" and "JSS-1" are the same class.
 */
class Class_lookup
{
    private $CI;
    private $classes;   // normalised class name => class id
    private $sections;  // class id => (normalised section name => section id)
    private $names;     // class id => class name as written in the school

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public static function norm($s)
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string) $s)));
    }

    private function load()
    {
        if ($this->classes !== null) {
            return;
        }
        $db = $this->CI->db;
        $this->classes = array();
        $this->names = array();
        foreach ($db->select('id, class')->get('classes')->result_array() as $c) {
            $this->classes[self::norm($c['class'])] = (int) $c['id'];
            $this->names[(int) $c['id']] = $c['class'];
        }
        $this->sections = array();
        $rows = $db->select('class_sections.class_id, sections.id, sections.section')
            ->from('class_sections')->join('sections', 'sections.id = class_sections.section_id')
            ->get()->result_array();
        foreach ($rows as $r) {
            $this->sections[(int) $r['class_id']][self::norm($r['section'])] = (int) $r['id'];
        }
    }

    /**
     * array(class_id, section_id, problem): problem is '' when found. An empty section means the class's
     * only section (or its "General" one, see Student_numbers::default_section).
     */
    public function place($class, $section)
    {
        $this->load();
        $key = self::norm($class);
        if (!isset($this->classes[$key])) {
            return array(0, 0, 'no class called "' . $class . '". Use a class name as it appears under Academics > Class.');
        }
        $class_id = $this->classes[$key];
        if (self::norm($section) === '') {
            $this->CI->load->library('Student_numbers');
            return array($class_id, $this->CI->student_numbers->section_or_default($class_id, 0), '');
        }
        $sec = self::norm($section);
        if (!isset($this->sections[$class_id][$sec])) {
            return array(0, 0, $this->names[$class_id] . ' has no section "' . $section . '".');
        }
        return array($class_id, $this->sections[$class_id][$sec], '');
    }
}
