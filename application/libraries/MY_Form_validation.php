<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Extra rules, the server side of backend/custom/form_rules.js:
 *   valid_phone      08031234567, +2348031234567 or another country's +<8-15 digits> (spaces, dashes allowed)
 *   valid_amount     a number, not negative (commas allowed: 45,000)
 *   valid_date       a real date in the school's date format (or Y-m-d)
 *   not_future_date  not after today
 *
 * check_formats(): call before run() on a form; every posted field gets the rule for what it is
 * (by name, as the browser checks do): phone, email, amount, date, date of birth. A field's own
 * rules are kept, and empty fields are left to those rules (required or not).
 */
class MY_Form_validation extends CI_Form_validation
{
    const PHONE = '/phone|mobile|contact_no|contactno|whatsapp/i';
    const EMAIL_NOT = '/template|subject|body|config|setting|type|status|notification|sender_name/i';
    const NUMBER = '/(^|_|\[)(amount|amt|fees?|fine|fine_amount|discount|salary|basic_salary|price|cost|perunitcost|unit_cost|qty|quantity|marks?|max_marks|min_marks|passing_marks|percentage|percent|balance|total)(_|\d|\]|$)/i';
    const NUMBER_NOT = '/_id$|type|name|code|title|remark|note|description|mode|status/i';
    const DOB = '/dob|date_of_birth|birth/i';

    public function __construct($rules = array())
    {
        parent::__construct($rules);
    }

    /** What a field is from its name: phone, email, number, dob, date or ''. */
    public static function field_kind($name)
    {
        if (preg_match('/email/i', $name) && !preg_match(self::EMAIL_NOT, $name)) {
            return 'email';
        }
        if (preg_match(self::PHONE, $name)) {
            return 'phone';
        }
        if (preg_match('/time/i', $name)) {
            return '';
        }
        if (preg_match(self::DOB, $name)) {
            return 'dob';
        }
        if (preg_match('/(^|_)(date|dob)(_|$)|_date|date_/i', $name)) {
            return 'date';
        }
        if (preg_match(self::NUMBER, $name) && !preg_match(self::NUMBER_NOT, $name)) {
            return 'number';
        }
        return '';
    }

    /** Adds the format rule to every posted, non-empty field ($only: just these field names). */
    public function check_formats(?array $only = null)
    {
        $rule = array('phone' => 'valid_phone', 'email' => 'valid_email', 'number' => 'valid_amount', 'date' => 'valid_date', 'dob' => 'valid_date|not_future_date');
        foreach ($_POST as $name => $value) {
            if (!is_string($value) || trim($value) === '' || ($only !== null && !in_array($name, $only, true))) {
                continue;
            }
            $kind = self::field_kind($name);
            if ($kind === '') {
                continue;
            }
            $add = explode('|', $rule[$kind]);
            if (isset($this->_field_data[$name])) {
                $have = $this->_field_data[$name]['rules'];
                foreach ($add as $r) {
                    if (!in_array($r, $have, true)) {
                        $have[] = $r;
                    }
                }
                $this->_field_data[$name]['rules'] = $have;
            } else {
                $this->set_rules($name, ucfirst(str_replace('_', ' ', $name)), array_merge(array('trim'), $add));
            }
        }
        return $this;
    }

    public function valid_phone($str)
    {
        $p = preg_replace('/[\s\-().]/', '', (string) $str);
        if ($p === '' || preg_match('/^0\d{10}$/', $p) || preg_match('/^\+?234\d{10}$/', $p) || (preg_match('/^\+\d{8,15}$/', $p) && strpos($p, '+234') !== 0)) {
            return true;
        }
        $this->set_message('valid_phone', 'The {field} must be a valid phone number, for example 08031234567.');
        return false;
    }

    public function valid_amount($str)
    {
        $n = str_replace(',', '', trim((string) $str));
        if ($n === '' || (preg_match('/^\d+(\.\d+)?$/', $n))) {
            return true;
        }
        $this->set_message('valid_amount', preg_match('/^-\d/', $n) ? 'The {field} can\'t be negative.' : 'The {field} must be a number.');
        return false;
    }

    public function valid_date($str)
    {
        if (trim((string) $str) === '' || $this->to_date($str)) {
            return true;
        }
        $CI = &get_instance();
        $this->set_message('valid_date', 'The {field} must be a real date (' . $CI->customlib->getSchoolDateFormat() . ').');
        return false;
    }

    public function not_future_date($str)
    {
        $d = $this->to_date($str);
        if (!$d || $d <= new DateTime('today')) {
            return true;
        }
        $this->set_message('not_future_date', 'The {field} can\'t be in the future.');
        return false;
    }

    /** The date in the school's format (or Y-m-d), or null when it isn't a real date. */
    private function to_date($str)
    {
        $str = trim((string) $str);
        $CI = &get_instance();
        $formats = array(isset($CI->customlib) ? $CI->customlib->getSchoolDateFormat() : 'd-m-Y', 'Y-m-d');
        foreach ($formats as $f) {
            // 1-3-2015 is fine; 31-02-2015 is not (PHP reports the overflow as a warning)
            $d = DateTime::createFromFormat('!' . $f, $str);
            $e = DateTime::getLastErrors();
            if ($d && (!$e || ($e['warning_count'] === 0 && $e['error_count'] === 0)) && (int) $d->format('Y') > 1900 && (int) $d->format('Y') < 2200) {
                return $d;
            }
        }
        return null;
    }
}
