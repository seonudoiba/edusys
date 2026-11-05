<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Executes model methods that contain hand-written SQL and reports SQL errors
 * (CLI only, TEST database only; every call runs in a transaction that is rolled back).
 *
 *   EDUSYS_SCHOOL_ID=2 php index.php tenancy_sqlcheck run <list-file> [<out-file>]
 *   EDUSYS_SCHOOL_ID=2 TENANCY_CHECK_LIST=... TENANCY_CHECK_OUT=... php index.php tenancy_sqlcheck run
 *
 * <list-file>: one "path/Model.php method" per line (the lint output can be turned into this).
 * Arguments are chosen by parameter name from this school's data (student_id -> a student of
 * the school, class_id -> a class, ...). Only SQL errors matter here: syntax errors, unknown
 * or ambiguous columns, missing tables. Wrong-meaning arguments just return empty results.
 *
 * Output per method: OK | SQL <errno> <message> | PHP <exception> (not necessarily a problem).
 */
class Tenancy_sqlcheck extends MY_Controller
{
    private $samples = array();

    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        $this->load->database();
        if (!Tenant::has()) {
            fwrite(STDERR, "Set EDUSYS_SCHOOL_ID.\n");
            exit(2);
        }
        $this->load->library(array('customlib', 'session'));
    }

    public function run($list = null, $out = null)
    {
        // Paths with drive letters are not valid URI segments: allow them via the environment.
        $list = $list ?: (getenv("TENANCY_CHECK_LIST") ?: null);
        $out = $out ?: (getenv("TENANCY_CHECK_OUT") ?: null);
        if ($list === null || !is_file($list)) {
            fwrite(STDERR, "Usage: php index.php tenancy_sqlcheck run <list-file> [<out-file>]\n");
            exit(2);
        }
        $this->samples = $this->load_samples();
        $results = array();
        foreach (file($list, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            list($path, $method) = preg_split('/\s+/', trim($line)) + array(null, null);
            if (!$path || !$method) continue;
            $results[] = array($path, $method, $this->check($path, $method));
        }
        $lines = array_map(function ($r) { return "$r[0]\t$r[1]\t$r[2]"; }, $results);
        if ($out) file_put_contents($out, implode("\n", $lines) . "\n");
        $sql = array_filter($results, function ($r) { return strpos($r[2], 'SQL ') === 0; });
        foreach ($sql as $r) echo "$r[0]::$r[1]\t$r[2]\n";
        fwrite(STDERR, count($results) . ' methods: ' . count($sql) . " with SQL errors\n");
    }

    private function check($path, $method)
    {
        $class = basename($path, '.php');
        $subdir = trim(str_replace(array('application/models', '\\'), array('', '/'), dirname($path)), '/');
        $name = ($subdir !== '' ? $subdir . '/' : '') . $class;
        $alias = 'chk_' . strtolower(str_replace('/', '_', $name));
        try {
            if (!isset($this->$alias)) {
                $this->load->model($name, $alias);
            }
            $model = $this->$alias;
            $ref = new ReflectionMethod($model, $method);
        } catch (Throwable $e) {
            return 'LOAD ' . get_class($e) . ': ' . $e->getMessage();
        }
        $args = array();
        foreach ($ref->getParameters() as $p) {
            $args[] = $this->argument($p);
        }
        $db = $this->db;
        $db->db_debug = false;
        $db->trans_begin();
        $error = null;
        $phpError = null;
        set_error_handler(function () { return true; });
        ob_start();
        try {
            $ref->invokeArgs($model, $args);
        } catch (Throwable $e) {
            $phpError = get_class($e) . ': ' . $e->getMessage();
        }
        ob_end_clean();
        restore_error_handler();
        $err = $db->error();
        if (!empty($err['code'])) {
            $error = 'SQL ' . $err['code'] . ' ' . preg_replace('/\s+/', ' ', $err['message']);
        }
        $db->trans_rollback();
        $db->db_debug = true;
        if ($error && in_array((int) $err['code'], array(1064, 1054, 1052, 1146, 1248, 1222, 1241, 1060), true)) {
            return $error;
        }
        if ($error) {
            return 'DATA ' . substr($error, 4); // e.g. FK or duplicate errors from sample arguments
        }
        return $phpError ? 'PHP ' . substr($phpError, 0, 200) : 'OK';
    }

    private function argument(ReflectionParameter $p)
    {
        $n = strtolower($p->getName());
        $s = $this->samples;
        $map = array(
            'student_session_id' => 'student_session', 'stu_session_id' => 'student_session',
            'student_id' => 'students', 'staff_id' => 'staff', 'class_id' => 'classes', 'section_id' => 'sections',
            'session_id' => 'session', 'parent_id' => 'parent', 'exam_id' => 'exam_groups', 'subject_id' => 'subjects',
            'fee_groups_feetype_id' => 'fee_groups_feetype', 'student_fees_master_id' => 'student_fees_master',
            'role_id' => 'role', 'group_id' => 'permission_group', 'user_id' => 'users', 'users_id' => 'users',
            'department_id' => 'department', 'designation_id' => 'staff_designation', 'book_id' => 'books',
            'hostel_id' => 'hostel', 'route_id' => 'transport_route', 'vehicle_id' => 'vehicles',
            'course_id' => 'online_courses', 'onlineexam_id' => 'onlineexam', 'homework_id' => 'homework',
        );
        foreach ($map as $key => $table) {
            if ($n === $key || substr($n, -strlen($key)) === $key) {
                return isset($s[$table]) ? $s[$table] : 1;
            }
        }
        if (strpos($n, 'date') !== false || $n === 'start' || $n === 'end' || $n === 'from' || $n === 'to') return date('Y-m-d');
        if (strpos($n, 'month') !== false) return date('m');
        if (strpos($n, 'year') !== false) return date('Y');
        if ($p->isOptional()) {
            try { return $p->getDefaultValue(); } catch (Throwable $e) { return null; }
        }
        $type = $p->getType();
        if ($type instanceof ReflectionNamedType && $type->getName() === 'array') return array($s['students']);
        if (preg_match('/(array|ids|list|data|students|students_id)$/', $n)) return array($s['students']);
        if ($n === 'id' || substr($n, -2) === 'id') return $s['students'];
        if (strpos($n, 'search') !== false || strpos($n, 'term') !== false || strpos($n, 'text') !== false) return 'a';
        if (strpos($n, 'condition') !== false || strpos($n, 'where') !== false) return '';
        return 1;
    }

    private function load_samples()
    {
        $db = $this->db;
        $one = function ($table, $where = array()) use ($db) {
            $row = $db->select('id')->where($where)->order_by('id')->limit(1)->get($table)->row();
            return $row ? (int) $row->id : 1;
        };
        $samples = array();
        foreach (array('students', 'student_session', 'staff', 'classes', 'sections', 'subjects', 'exam_groups',
                     'fee_groups_feetype', 'student_fees_master', 'permission_group', 'users', 'department',
                     'staff_designation', 'books', 'hostel', 'transport_route', 'vehicles', 'online_courses',
                     'onlineexam', 'homework') as $t) {
            $samples[$t] = $one($t);
        }
        $samples['parent'] = $one('users', array('role' => 'parent'));
        $samples['role'] = 7;
        $setting = $db->select('session_id')->get('sch_settings')->row();
        $samples['session'] = $setting ? (int) $setting->session_id : 1;
        return $samples;
    }
}
