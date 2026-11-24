<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Certificate and ID Card Helper
 * Provides robust dynamic tag parsing and data formatting for certificates and ID cards.
 */

if (!function_exists('parse_certificate_tags')) {
    /**
     * Parses and replaces dynamic tags in certificate template text.
     *
     * Supported tags:
     * [name], [firstname], [lastname], [middlename],
     * [dob], [admission_date], [created_at], [date], [current_date], [issue_date],
     * [admission_no], [roll_no], [class], [section], [class_section],
     * [gender], [category], [cast], [religion], [blood_group],
     * [father_name], [mother_name], [guardian], [guardian_name], [guardian_relation], [guardian_phone], [guardian_address],
     * [email], [phone], [mobileno], [present_address], [current_address], [permanent_address],
     * [school_name], [school_address], [school_phone], [school_email], [session],
     * plus any custom fields [field_name].
     *
     * @param string $text Template text containing bracketed tags
     * @param object|array $student Student record
     * @param object|array|null $school School setting
     * @param string|null $session Current academic session name
     * @param array $custom_fields Custom fields definition
     * @return string Parsed text with all tags resolved
     */
    function parse_certificate_tags($text, $student, $school = null, $session = null, $custom_fields = array())
    {
        if (empty($text)) {
            return '';
        }

        $ci = function_exists('get_instance') ? get_instance() : null;
        $std = (object) $student;

        // Determine school date format
        $date_format = 'd-m-Y';
        if ($ci && isset($ci->customlib) && method_exists($ci->customlib, 'getSchoolDateFormat')) {
            $df = $ci->customlib->getSchoolDateFormat();
            if (!empty($df)) {
                $date_format = $df;
            }
        }

        // Helper date formatter
        $format_date = function ($date_val) use ($date_format, $ci) {
            if (empty($date_val) || $date_val == '0000-00-00' || $date_val == '1970-01-01') {
                return '';
            }
            if ($ci && isset($ci->customlib) && method_exists($ci->customlib, 'dateYYYYMMDDtoStrtotime')) {
                $time = $ci->customlib->dateYYYYMMDDtoStrtotime($date_val);
                if ($time) {
                    return date($date_format, $time);
                }
            }
            $time = strtotime($date_val);
            return $time ? date($date_format, $time) : $date_val;
        };

        // Student names
        $first_name  = isset($std->firstname) ? trim($std->firstname) : '';
        $middle_name = isset($std->middlename) ? trim($std->middlename) : '';
        $last_name   = isset($std->lastname) ? trim($std->lastname) : '';

        $full_name = '';
        if ($ci && isset($ci->customlib) && method_exists($ci->customlib, 'getFullName')) {
            $mid_setting  = isset($school->middlename) ? $school->middlename : 1;
            $last_setting = isset($school->lastname) ? $school->lastname : 1;
            $full_name    = $ci->customlib->getFullName($first_name, $middle_name, $last_name, $mid_setting, $last_setting);
        }
        if (empty($full_name)) {
            $full_name = trim($first_name . ' ' . $middle_name . ' ' . $last_name);
        }

        // Addresses
        $current_address   = isset($std->current_address) ? trim($std->current_address) : '';
        $permanent_address = isset($std->permanent_address) ? trim($std->permanent_address) : '';
        if (empty($current_address) && !empty($permanent_address)) {
            $current_address = $permanent_address;
        } elseif (empty($permanent_address) && !empty($current_address)) {
            $permanent_address = $current_address;
        }

        // Phone numbers
        $phone = isset($std->mobileno) ? trim($std->mobileno) : '';
        if (empty($phone) && isset($std->father_phone)) {
            $phone = trim($std->father_phone);
        }

        // Class & Section
        $class_name   = isset($std->class) ? trim($std->class) : '';
        $section_name = isset($std->section) ? trim($std->section) : '';
        $class_section = $class_name;
        if (!empty($section_name)) {
            $class_section .= ' (' . $section_name . ')';
        }

        // School details
        $school_name    = '';
        $school_address = '';
        $school_phone   = '';
        $school_email   = '';
        if (!empty($school)) {
            $sch_obj        = (object) $school;
            $school_name    = isset($sch_obj->name) ? $sch_obj->name : (isset($sch_obj->school_name) ? $sch_obj->school_name : '');
            $school_address = isset($sch_obj->address) ? $sch_obj->address : (isset($sch_obj->school_address) ? $sch_obj->school_address : '');
            $school_phone   = isset($sch_obj->phone) ? $sch_obj->phone : '';
            $school_email   = isset($sch_obj->email) ? $sch_obj->email : '';
        }

        // Academic session
        $session_name = !empty($session) ? $session : '';
        if (empty($session_name) && isset($ci->setting_model)) {
            $curr_session = $ci->setting_model->getCurrentSessionName();
            if (!empty($curr_session)) {
                $session_name = $curr_session;
            }
        }

        // Current issue date
        $current_date = date($date_format);

        // Core replacements dictionary
        $replacements = array(
            '[name]'              => $full_name,
            '[firstname]'         => $first_name,
            '[lastname]'          => $last_name,
            '[middlename]'        => $middle_name,
            '[dob]'               => isset($std->dob) ? $format_date($std->dob) : '',
            '[admission_date]'    => isset($std->admission_date) ? $format_date($std->admission_date) : '',
            '[created_at]'        => isset($std->created_at) ? $format_date($std->created_at) : $current_date,
            '[date]'              => $current_date,
            '[current_date]'      => $current_date,
            '[issue_date]'        => $current_date,
            '[admission_no]'      => isset($std->admission_no) ? (string) $std->admission_no : '',
            '[roll_no]'           => isset($std->roll_no) && $std->roll_no !== '' ? (string) $std->roll_no : '',
            '[class]'             => $class_name,
            '[section]'           => $section_name,
            '[class_section]'     => $class_section,
            '[gender]'            => isset($std->gender) ? ucfirst(strtolower($std->gender)) : '',
            '[category]'          => isset($std->category) ? (string) $std->category : '',
            '[cast]'              => isset($std->cast) ? (string) $std->cast : '',
            '[religion]'          => isset($std->religion) ? (string) $std->religion : '',
            '[blood_group]'       => isset($std->blood_group) ? (string) $std->blood_group : '',
            '[father_name]'       => isset($std->father_name) ? (string) $std->father_name : '',
            '[mother_name]'       => isset($std->mother_name) ? (string) $std->mother_name : '',
            '[guardian]'          => isset($std->guardian_name) ? (string) $std->guardian_name : '',
            '[guardian_name]'     => isset($std->guardian_name) ? (string) $std->guardian_name : '',
            '[guardian_relation]' => isset($std->guardian_relation) ? (string) $std->guardian_relation : '',
            '[guardian_phone]'    => isset($std->guardian_phone) ? (string) $std->guardian_phone : '',
            '[guardian_address]'  => isset($std->guardian_address) ? (string) $std->guardian_address : '',
            '[email]'             => isset($std->email) ? (string) $std->email : '',
            '[phone]'             => $phone,
            '[mobileno]'          => $phone,
            '[present_address]'   => $current_address,
            '[current_address]'   => $current_address,
            '[permanent_address]' => $permanent_address,
            '[school_name]'       => $school_name,
            '[school_address]'    => $school_address,
            '[school_phone]'      => $school_phone,
            '[school_email]'      => $school_email,
            '[session]'           => $session_name,
        );

        // Custom field replacements
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $cf) {
                $cf_name = is_object($cf) ? $cf->name : (is_array($cf) ? $cf['name'] : (string) $cf);
                if (!empty($cf_name) && isset($std->$cf_name)) {
                    $replacements['[' . $cf_name . ']'] = (string) $std->$cf_name;
                }
            }
        }

        // Also check any properties present directly on the student object
        foreach ($std as $key => $val) {
            $tag = '[' . $key . ']';
            if (!isset($replacements[$tag]) && is_scalar($val)) {
                $replacements[$tag] = (string) $val;
            }
        }

        // Execute replacements
        $parsed = str_ireplace(array_keys($replacements), array_values($replacements), $text);

        // Clean up any remaining unresolved [tags] that might have had no value
        // but preserve HTML tags like <b>, <span>, etc.
        $parsed = preg_replace('/\[[a-zA-Z0-9_\-\s]+\]/', '', $parsed);

        return $parsed;
    }
}

if (!function_exists('get_certificate_sample_student')) {
    /**
     * Generates a realistic mock student object for previewing certificates.
     *
     * @return object
     */
    function get_certificate_sample_student()
    {
        return (object) array(
            'id'                => 1,
            'firstname'         => 'Alexander',
            'middlename'        => 'James',
            'lastname'          => 'Sterling',
            'name'              => 'Alexander James Sterling',
            'dob'               => '2008-05-15',
            'admission_date'    => '2020-08-01',
            'created_at'        => '2020-08-01 10:00:00',
            'admission_no'      => 'ADM-2024-089',
            'roll_no'           => '18',
            'class'             => 'Class 10',
            'section'           => 'A',
            'gender'            => 'Male',
            'category'          => 'General',
            'cast'              => '',
            'religion'          => '',
            'blood_group'       => 'O+',
            'father_name'       => 'Robert Sterling',
            'mother_name'       => 'Eleanor Sterling',
            'guardian_name'     => 'Robert Sterling',
            'guardian_relation' => 'Father',
            'guardian_phone'    => '+1 (555) 234-5678',
            'guardian_address'  => '742 Evergreen Terrace, Springfield',
            'email'             => 'alexander.sterling@example.com',
            'mobileno'          => '+1 (555) 234-5678',
            'current_address'   => '742 Evergreen Terrace, Springfield',
            'permanent_address' => '742 Evergreen Terrace, Springfield',
            'image'             => 'uploads/student_images/no_image.png',
        );
    }
}
