<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Question bank import (Online Examination > Question Bank > Import): an Excel (.xlsx) or CSV file,
 * one question per row, with a header row.
 *
 *   A type      single / singlechoice, multiple / multichoice, true_false / true/false, descriptive / theory
 *   B level     low, medium, high (empty: medium)
 *   C question
 *   D-H         options A to E (empty for true/false and written questions)
 *   I answer    single: A or opt_a; multiple: A,C (or ["opt_a","opt_c"]); true/false: true or false
 *   J passage   optional: a reading passage title; questions with the same title share it
 *   K passage text   the passage itself (needed once per title; an existing title is reused)
 *
 * Text is imported as plain text (shown exactly as typed). Rows with problems are skipped and
 * reported by row number.
 */
class Question_import
{
    const MAX_ROWS = 2000;
    const OPTIONS = array('opt_a', 'opt_b', 'opt_c', 'opt_d', 'opt_e');

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** array(rows, error): the rows of the first sheet (xlsx) or of the CSV, header row included. */
    public static function read($path, $ext)
    {
        $ext = strtolower($ext);
        if ($ext === 'csv') {
            $rows = array();
            $f = fopen($path, 'r');
            if (!$f) {
                return array(array(), 'The file could not be read.');
            }
            $first = fgets($f);
            $bom = "\xEF\xBB\xBF";
            rewind($f);
            if (strpos((string) $first, $bom) === 0) {
                fread($f, 3);
            }
            while (($row = fgetcsv($f, 0, ',')) !== false) {
                $rows[] = $row;
            }
            fclose($f);
            return array($rows, null);
        }
        if ($ext !== 'xlsx') {
            return array(array(), 'Upload an Excel (.xlsx) or CSV file.');
        }
        if (!class_exists('ZipArchive')) {
            return array(array(), 'Excel files can\'t be read on this server. Save the sheet as CSV and import that.');
        }
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return array(array(), 'This is not a valid Excel file.');
        }
        $shared = array();
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml !== false) {
            $doc = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NONET);
            if ($doc) {
                foreach ($doc->si as $si) {
                    $text = '';
                    if (isset($si->t)) {
                        $text = (string) $si->t;
                    } else {
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                    }
                    $shared[] = $text;
                }
            }
        }
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheet === false) {
            return array(array(), 'The Excel file has no first sheet.');
        }
        $doc = @simplexml_load_string($sheet, 'SimpleXMLElement', LIBXML_NONET);
        if (!$doc || !isset($doc->sheetData)) {
            return array(array(), 'The Excel sheet could not be read.');
        }
        $rows = array();
        foreach ($doc->sheetData->row as $row) {
            $out = array();
            foreach ($row->c as $c) {
                $ref = (string) $c['r'];
                $col = 0;
                if (preg_match('/^([A-Z]+)/', $ref, $m)) {
                    foreach (str_split($m[1]) as $ch) {
                        $col = $col * 26 + (ord($ch) - 64);
                    }
                    $col--;
                } else {
                    $col = count($out);
                }
                $type = (string) $c['t'];
                if ($type === 's') {
                    $value = isset($shared[(int) $c->v]) ? $shared[(int) $c->v] : '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) $c->is->t;
                } elseif ($type === 'b') {
                    $value = ((string) $c->v) === '1' ? 'true' : 'false';
                } else {
                    $value = (string) $c->v;
                }
                $out[$col] = $value;
            }
            if ($out) {
                $max = max(array_keys($out));
                $rows[] = array_replace(array_fill(0, $max + 1, ''), $out);
            }
            if (count($rows) > self::MAX_ROWS + 1) {
                break;
            }
        }
        return array($rows, null);
    }

    /**
     * Checks and saves the rows (header row skipped). $defaults: subject_id, class_id, section_id, staff_id.
     * Returns array(imported count, array of "Row n: problem").
     */
    public function import(array $rows, array $defaults)
    {
        $db = $this->CI->db;
        $errors = array();
        $insert = array();
        $passages = array(); // lower-case title => id
        array_shift($rows); // header
        foreach (array_slice($rows, 0, self::MAX_ROWS) as $i => $cells) {
            $n = $i + 2; // spreadsheet row number
            $cells = array_map(function ($v) { return trim((string) $v); }, array_pad($cells, 11, ''));
            if (implode('', $cells) === '') {
                continue;
            }
            list($type, $level, $question, $a, $b, $c, $d, $e, $answer, $p_title, $p_text) = array_slice($cells, 0, 11);
            $type = self::type($type);
            if (!$type) {
                $errors[] = "Row $n: the type must be single, multiple, true_false or descriptive.";
                continue;
            }
            if ($question === '') {
                $errors[] = "Row $n: the question is empty.";
                continue;
            }
            $level = strtolower($level) ?: 'medium';
            if (!in_array($level, array('low', 'medium', 'high'), true)) {
                $errors[] = "Row $n: the level must be low, medium or high.";
                continue;
            }
            $options = array_combine(self::OPTIONS, array($a, $b, $c, $d, $e));
            $correct = '';
            if ($type === 'true_false') {
                $correct = strtolower($answer);
                if (!in_array($correct, array('true', 'false'), true)) {
                    $errors[] = "Row $n: the answer must be true or false.";
                    continue;
                }
                $options = array_fill_keys(self::OPTIONS, '');
            } elseif ($type === 'descriptive') {
                $options = array_fill_keys(self::OPTIONS, '');
            } else {
                $filled = array_keys(array_filter($options, function ($v) { return $v !== ''; }));
                if (count($filled) < 2) {
                    $errors[] = "Row $n: give at least two options.";
                    continue;
                }
                $keys = self::answer_keys($answer);
                if (!$keys || array_diff($keys, $filled)) {
                    $errors[] = "Row $n: the answer must be the letter of a filled option (e.g. B" . ($type === 'multichoice' ? ' or A,C' : '') . ").";
                    continue;
                }
                if ($type === 'singlechoice' && count($keys) !== 1) {
                    $errors[] = "Row $n: a single-answer question has exactly one answer.";
                    continue;
                }
                $correct = $type === 'singlechoice' ? $keys[0] : json_encode($keys);
            }
            $passage_id = null;
            if ($p_title !== '') {
                $key = mb_strtolower($p_title);
                if (!isset($passages[$key])) {
                    $found = $db->select('id')->where('title', $p_title)->get('question_passages')->row();
                    if ($found) {
                        $passages[$key] = (int) $found->id;
                    } elseif ($p_text !== '') {
                        $db->insert('question_passages', array('title' => mb_substr($p_title, 0, 255), 'body' => self::html($p_text),
                            'subject_id' => (int) $defaults['subject_id'] ?: null));
                        $passages[$key] = (int) $db->insert_id();
                    } else {
                        $errors[] = "Row $n: the passage \"$p_title\" is new, so put its text in column K.";
                        continue;
                    }
                }
                $passage_id = $passages[$key];
            }
            $row = array(
                'staff_id' => (int) $defaults['staff_id'],
                'subject_id' => (int) $defaults['subject_id'],
                'class_id' => (int) $defaults['class_id'],
                'section_id' => (int) $defaults['section_id'],
                'question_type' => $type,
                'level' => $level,
                'question' => self::html($question),
                'correct' => $correct,
                'passage_id' => $passage_id,
            );
            foreach ($options as $k => $v) {
                $row[$k] = $v === '' ? '' : self::html($v);
            }
            $insert[] = $row;
        }
        if (count($rows) > self::MAX_ROWS) {
            $errors[] = 'Only the first ' . self::MAX_ROWS . ' rows were read. Split the file and import the rest separately.';
        }
        if ($insert) {
            $this->CI->load->model('question_model');
            $this->CI->question_model->add_question_bulk($insert);
        }
        return array(count($insert), $errors);
    }

    private static function type($t)
    {
        $t = strtolower(str_replace(array(' ', '-', '/'), '_', $t));
        $map = array('single' => 'singlechoice', 'singlechoice' => 'singlechoice', 'single_choice' => 'singlechoice', 'objective' => 'singlechoice',
            'multiple' => 'multichoice', 'multichoice' => 'multichoice', 'multi' => 'multichoice', 'multiple_choice' => 'multichoice',
            'true_false' => 'true_false', 'truefalse' => 'true_false', 'tf' => 'true_false',
            'descriptive' => 'descriptive', 'theory' => 'descriptive', 'written' => 'descriptive', 'essay' => 'descriptive');
        return isset($map[$t]) ? $map[$t] : null;
    }

    /** "B", "opt_b", "A, C", ["opt_a","opt_c"] -> option keys. */
    private static function answer_keys($answer)
    {
        $list = json_decode($answer, true);
        if (!is_array($list)) {
            $list = preg_split('/[\s,;]+/', $answer, -1, PREG_SPLIT_NO_EMPTY);
        }
        $keys = array();
        foreach ($list as $v) {
            $v = strtolower(trim((string) $v));
            if (preg_match('/^[a-e]$/', $v)) {
                $v = 'opt_' . $v;
            }
            if (!in_array($v, self::OPTIONS, true)) {
                return array();
            }
            $keys[] = $v;
        }
        $keys = array_values(array_unique($keys));
        sort($keys);
        return $keys;
    }

    private static function html($text)
    {
        return nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'), false);
    }
}
