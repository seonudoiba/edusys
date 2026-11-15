<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

#[AllowDynamicProperties]
class Role {

    public function __construct() {
        $this->CI = & get_instance();
        // Student and parent usernames start with the school's subdomain (jenastd12, jenaparent12),
        // so they are not simply std1, std2, ... Existing usernames are kept.
        $school = class_exists('Tenant', false) ? Tenant::school() : null;
        $sub = $school ? substr(preg_replace('/[^a-z0-9]/', '', strtolower((string) $school['subdomain'])), 0, 12) : '';
        $this->CI->student_login_prefix = $sub. "_" . "std";
        $this->CI->parent_login_prefix = $sub . "_" . "parent";
        $this->CI->teacher_login_prefix = $sub. "_" . "teacher";
        $this->CI->librarian_login_prefix = $sub . "_" . "librarian";
        $this->CI->accountant_login_prefix = $sub . "_" . "accountant";
    }

    public function get_random_password($chars_min = 6, $chars_max = 8, $use_upper_case = false, $include_numbers = false, $include_special_chars = false) {
        $length = max(10, random_int($chars_min, $chars_max)); // never shorter than 10, whatever the caller asks
        $selection = 'aeuoyibcdfghjkmnpqrstvwxz';
        if ($include_numbers) {
            $selection .= "23456789";
        }
        if ($include_special_chars) {
            $selection .= "!@#$%&?";
        }

        $password = "";
        for ($i = 0; $i < $length; $i++) {
            $letter = $selection[random_int(0, strlen($selection) - 1)];
            $password .= $use_upper_case && random_int(0, 1) ? strtoupper($letter) : $letter;
        }
        return $password;
}

}
