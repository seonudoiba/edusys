<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Enforces the school's plan on school hosts (post_controller_constructor):
 *   - adding students (admission, import, online admission enrolment) or staff (add, import)
 *     beyond the plan's limit is refused;
 *   - an upload that would exceed the plan's storage is refused.
 * Imports count the rows of the uploaded file. Nothing happens on the platform host, on the
 * CLI, or for a school without a plan (Plan_limits).
 */
class Plan_policy_hook
{
    /** controller/method => what it adds */
    const ADDS = array(
        'student/create' => 'students',
        'student/import' => 'students',
        'onlinestudent/edit' => 'students',
        'staff/create' => 'staff',
        'staff/import' => 'staff',
    );

    public function enforce()
    {
        if (is_cli() || !class_exists('Tenant', false) || !Tenant::has() || Tenant::is_platform()) {
            return;
        }
        $CI = &get_instance();
        if ($CI->input->method() !== 'post') {
            return;
        }
        $CI->load->library('Plan_limits');
        $school_id = Tenant::id();
        $route = strtolower($CI->router->fetch_class() . '/' . $CI->router->fetch_method());

        if (isset(self::ADDS[$route])) {
            $count = substr($route, -7) === '/import' ? $this->import_rows() : 1;
            $error = $count > 0 ? $CI->plan_limits->add_error($school_id, self::ADDS[$route], $count) : null;
            if ($error !== null) {
                $this->refuse($CI, $error);
            }
        }

        $bytes = $this->upload_bytes($_FILES);
        if ($bytes > 0) {
            $error = $CI->plan_limits->storage_error($school_id, $bytes);
            if ($error !== null) {
                $this->refuse($CI, $error);
            }
        }
    }

    /** Data rows in the uploaded import file (CSV: lines after the header). */
    private function import_rows()
    {
        foreach ($_FILES as $f) {
            $tmp = is_array($f['tmp_name']) ? reset($f['tmp_name']) : $f['tmp_name'];
            if ($tmp && is_uploaded_file($tmp)) {
                $lines = 0;
                $h = fopen($tmp, 'r');
                while ($h && ($row = fgetcsv($h)) !== false) {
                    if (array_filter($row, 'strlen')) {
                        $lines++;
                    }
                }
                if ($h) {
                    fclose($h);
                }
                return max(0, $lines - 1);
            }
        }
        return 0;
    }

    private function upload_bytes(array $files)
    {
        $total = 0;
        foreach ($files as $f) {
            $sizes = isset($f['size']) ? (array) $f['size'] : array();
            array_walk_recursive($sizes, function ($s) use (&$total) { $total += (int) $s; });
        }
        return $total;
    }

    private function refuse($CI, $message)
    {
        if ($CI->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'fail', 'error' => array('plan' => $message), 'message' => $message, 'msg' => $message));
            exit;
        }
        show_error(html_escape($message) . '<br><br><a href="javascript:history.back()">Go back</a>', 403, 'Plan limit reached');
    }
}
