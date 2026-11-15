<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Subjects and classes from an Excel or CSV file (Academics > Subjects / Class > Import).
 * Rows come from Import_sheet::keyed_rows(); each method returns array(imported count, array of "Row n: problem").
 */
class Academic_import
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('Class_lookup');
    }

    /** Columns: name, code (optional, unique), type (Theory or Practical; empty = Theory). */
    public function subjects(array $rows)
    {
        $db = $this->CI->db;
        $names = array();
        $codes = array();
        foreach ($db->select('name, code')->get('subjects')->result_array() as $s) {
            $names[Class_lookup::norm($s['name'])] = 1;
            if (trim((string) $s['code']) !== '') {
                $codes[strtolower(trim($s['code']))] = 1;
            }
        }
        $types = $this->CI->customlib->subjectType(); // theory, practical
        $count = 0;
        $problems = array();
        foreach ($rows as $i => $row) {
            $label = 'Row ' . ($i + 1);
            $name = trim(isset($row['name']) ? $row['name'] : '');
            $code = trim(isset($row['code']) ? $row['code'] : '');
            $type = strtolower(trim(isset($row['type']) ? $row['type'] : ''));
            if ($name === '') {
                $problems[] = "$label: the subject name is empty.";
                continue;
            }
            if (isset($names[Class_lookup::norm($name)])) {
                $problems[] = "$label: there is already a subject called \"$name\".";
                continue;
            }
            if ($code !== '' && isset($codes[strtolower($code)])) {
                $problems[] = "$label: the code \"$code\" is already used by another subject.";
                continue;
            }
            if ($type === '' || $type === 't') {
                $type = 'theory';
            } elseif ($type === 'p') {
                $type = 'practical';
            }
            if (!isset($types[$type])) {
                $problems[] = "$label: the type must be Theory or Practical.";
                continue;
            }
            $this->CI->subject_model->add(array('name' => $name, 'code' => $code, 'type' => $type));
            $names[Class_lookup::norm($name)] = 1;
            if ($code !== '') {
                $codes[strtolower($code)] = 1;
            }
            $count++;
        }
        return array($count, $problems);
    }

    /**
     * Columns: class, sections (names separated by commas, e.g. "A, B"; empty = one "General" section).
     * Sections the school doesn't have yet are created. A class that already exists gets the sections it
     * doesn't have yet (and is otherwise left as it is).
     */
    public function classes(array $rows)
    {
        $db = $this->CI->db;
        $classes = array(); // norm name => id
        foreach ($db->select('id, class')->get('classes')->result_array() as $c) {
            $classes[Class_lookup::norm($c['class'])] = (int) $c['id'];
        }
        $sections = array(); // norm name => id
        foreach ($db->select('id, section')->get('sections')->result_array() as $s) {
            $sections[Class_lookup::norm($s['section'])] = (int) $s['id'];
        }
        $this->CI->load->library('Student_numbers');
        $count = 0;
        $problems = array();
        foreach ($rows as $i => $row) {
            $label = 'Row ' . ($i + 1);
            $class = trim(isset($row['class']) ? $row['class'] : '');
            if ($class === '') {
                $problems[] = "$label: the class name is empty.";
                continue;
            }
            $section_ids = array();
            foreach (preg_split('/[,;\/]+/', (string) (isset($row['sections']) ? $row['sections'] : '')) as $name) {
                $name = trim($name);
                if ($name === '') {
                    continue;
                }
                $key = Class_lookup::norm($name);
                if ($key === '') {
                    continue;
                }
                if (!isset($sections[$key])) {
                    $db->insert('sections', array('section' => $name, 'is_active' => 'no'));
                    $sections[$key] = (int) $db->insert_id();
                }
                $section_ids[$sections[$key]] = $sections[$key];
            }
            $key = Class_lookup::norm($class);
            if (isset($classes[$key])) {
                // existing class: add only the sections it doesn't have
                $have = array_map('intval', array_column($db->select('section_id')->where('class_id', $classes[$key])->get('class_sections')->result_array(), 'section_id'));
                $new = array_diff($section_ids, $have);
                if (!$new) {
                    $problems[] = "$label: \"$class\" already exists" . ($section_ids ? ' with these sections.' : '.');
                    continue;
                }
                foreach ($new as $sid) {
                    $db->insert('class_sections', array('class_id' => $classes[$key], 'section_id' => $sid));
                }
                $count++;
                continue;
            }
            if (!$section_ids) {
                $section_ids = array($this->CI->student_numbers->general_section_id());
            }
            $this->CI->classsection_model->add(array('class' => $class), array_values($section_ids));
            $classes[$key] = (int) $db->select('id')->where('class', $class)->order_by('id', 'desc')->limit(1)->get('classes')->row('id');
            $count++;
        }
        return array($count, $problems);
    }

    /** The message shown after an import: how many came in, and each skipped row with its reason. */
    public static function message($what, $total, $count, array $problems)
    {
        $msg = "$total $what in the file, $count imported.";
        if ($problems) {
            $msg .= '<br><b>' . count($problems) . ' row(s) skipped:</b><br>' . implode('<br>', array_map('html_escape', array_slice($problems, 0, 30))) . (count($problems) > 30 ? '<br>...' : '');
        }
        return '<div class="alert ' . ($problems ? ($count ? 'alert-warning' : 'alert-danger') : 'alert-success') . ' text-left">' . $msg . '</div>';
    }
}
