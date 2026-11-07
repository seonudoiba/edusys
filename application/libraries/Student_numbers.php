<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admission numbers, always generated: the school's prefix (which may contain {YEAR} or {YY}),
 * then a number padded to the chosen digits, never below the starting number. The next number is
 * one more than the highest already used with the same prefix, and is checked to be free, so
 * numbers never clash (manually entered older numbers included).
 *
 * Also: the section a student goes in when none is chosen (default_section).
 */
class Student_numbers
{
    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    /** The school's numbering settings, with sensible values where none are set. */
    public function format()
    {
        $s = $this->CI->db->select('adm_prefix, adm_no_digit, adm_start_from')->get('sch_settings')->row();
        $digits = $s && (int) $s->adm_no_digit > 0 ? (int) $s->adm_no_digit : 4;
        $start = $s && ctype_digit((string) $s->adm_start_from) && (int) $s->adm_start_from > 0 ? (int) $s->adm_start_from : 1;
        return array('prefix' => $s ? (string) $s->adm_prefix : '', 'digits' => min(10, $digits), 'start' => $start);
    }

    /** The prefix with {YEAR} and {YY} filled in. */
    public static function expand($prefix, $time = null)
    {
        $time = $time ?: time();
        return strtr((string) $prefix, array('{YEAR}' => date('Y', $time), '{YY}' => date('y', $time)));
    }

    /** What the format looks like, for the settings page and forms. */
    public function example(array $format = null)
    {
        $format = $format ?: $this->format();
        return self::expand($format['prefix']) . str_pad((string) $format['start'], $format['digits'], '0', STR_PAD_LEFT);
    }

    /** The next free admission number. */
    public function next()
    {
        $f = $this->format();
        $prefix = self::expand($f['prefix']);
        $highest = $f['start'] - 1;
        $db = $this->CI->db;
        $db->select('admission_no')->from('students');
        if ($prefix !== '') {
            $db->like('admission_no', $prefix, 'after');
        }
        foreach ($db->get()->result_array() as $row) {
            $rest = substr((string) $row['admission_no'], strlen($prefix));
            if ($rest !== '' && ctype_digit($rest) && strlen($rest) <= 12) {
                $highest = max($highest, (int) $rest);
            }
        }
        for ($n = $highest + 1, $tries = 0; $tries < 1000; $n++, $tries++) {
            $number = $prefix . str_pad((string) $n, $f['digits'], '0', STR_PAD_LEFT);
            if ($db->where('admission_no', $number)->count_all_results('students') === 0) {
                return $number;
            }
        }
        throw new RuntimeException('No free admission number could be found.');
    }

    /**
     * The class_sections row for a class when no section was chosen: the class's only section if it
     * has one, otherwise a section called "General" (created and linked to the class if needed).
     * @return array class_sections row (id, class_id, section_id)
     */
    public function default_section($class_id)
    {
        $db = $this->CI->db;
        $class_id = (int) $class_id;
        $rows = $db->where('class_id', $class_id)->get('class_sections')->result_array();
        if (count($rows) === 1) {
            return $rows[0];
        }
        $general = array('id' => $this->general_section_id());
        foreach ($rows as $r) {
            if ((int) $r['section_id'] === (int) $general['id']) {
                return $r;
            }
        }
        $db->insert('class_sections', array('class_id' => $class_id, 'section_id' => (int) $general['id'], 'is_active' => 'no'));
        return array('id' => $db->insert_id(), 'class_id' => $class_id, 'section_id' => (int) $general['id']);
    }

    /** The school's "General" section, created if it doesn't exist yet. */
    public function general_section_id()
    {
        $db = $this->CI->db;
        $row = $db->select('id')->where('section', 'General')->get('sections')->row();
        if ($row) {
            return (int) $row->id;
        }
        $db->insert('sections', array('section' => 'General', 'is_active' => 'no'));
        return (int) $db->insert_id();
    }

    /** The section id to use: the one chosen, or the default for the class. */
    public function section_or_default($class_id, $section_id)
    {
        if ((int) $section_id > 0) {
            return (int) $section_id;
        }
        $row = $this->default_section($class_id);
        return (int) $row['section_id'];
    }
}
