<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Staff IDs are given by the system: the school's prefix (General Setting > ID Auto Generation, default STF)
 * followed by the next number, padded to the chosen number of digits (default 4), e.g. STF0001, STF0002.
 * The next number is one more than the highest ID with that prefix (never below "start from"), so IDs typed
 * in the past or imported don't clash.
 */
class Staff_ids
{
    const PREFIX = 'STF';
    const DIGITS = 4;

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /** prefix, digits and first number from the school's settings, with defaults for empty ones */
    public function format($setting = null)
    {
        if ($setting === null) {
            $setting = $this->CI->setting_model->getSetting();
        }
        $prefix = trim((string) (isset($setting->staffid_prefix) ? $setting->staffid_prefix : ''));
        $digits = (int) (isset($setting->staffid_no_digit) ? $setting->staffid_no_digit : 0);
        $start  = (int) (isset($setting->staffid_start_from) ? $setting->staffid_start_from : 0);
        return array(
            'prefix' => $prefix === '' ? self::PREFIX : $prefix,
            'digits' => $digits > 0 ? min($digits, 10) : self::DIGITS,
            'start'  => max(1, $start),
        );
    }

    /** the next free staff ID for this school; $taken holds IDs already handed out in this request (imports) */
    public function next($setting = null, array $taken = array())
    {
        $f   = $this->format($setting);
        $len = strlen($f['prefix']);
        $max = $f['start'] - 1;
        $rows = $this->CI->db->select('employee_id')->like('employee_id', $f['prefix'], 'after')->get('staff')->result_array();
        foreach (array_merge(array_column($rows, 'employee_id'), $taken) as $id) {
            $id = (string) $id;
            if (strncasecmp($id, $f['prefix'], $len) === 0) {
                $rest = substr($id, $len);
                if ($rest !== '' && ctype_digit($rest)) {
                    $max = max($max, (int) $rest);
                }
            }
        }
        $n = $max + 1;
        do {
            $id = $f['prefix'] . str_pad((string) $n, $f['digits'], '0', STR_PAD_LEFT);
            $n++;
        } while (in_array($id, $taken, true) || $this->CI->db->where('employee_id', $id)->count_all_results('staff') > 0);
        return $id;
    }
}
