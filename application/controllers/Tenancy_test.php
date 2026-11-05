<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Tests for the tenant-aware database layer (CLI only, needs a migrated + seeded database).
 *
 *   EDUSYS_SCHOOL_ID=1 php index.php tenancy_test
 *   EDUSYS_SCHOOL_ID=2 php index.php tenancy_test
 *   php index.php tenancy_test            # no school: nothing may be filtered
 *
 * Writes run inside transactions that are rolled back.
 */
class Tenancy_test extends CI_Controller
{
    private $failures = 0;
    private $count = 0;

    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        $this->load->database();
    }

    public function index()
    {
        $db = $this->db;
        $this->check('driver is tenant-aware', 'Tenant_DB_mysqli_driver', get_class($db));

        if (!Tenant::has()) {
            $this->system_mode($db);
        } else {
            $this->compiled_sql($db, Tenant::id());
            $this->writes($db, Tenant::id());
            $this->executed($db, Tenant::id());
            $this->raw_guard($db, Tenant::id());
        }

        $who = Tenant::has() ? 'school ' . Tenant::id() : 'no school';
        echo ($this->failures ? "{$this->failures} of {$this->count} checks FAILED" : "All {$this->count} checks passed") . " ($who)\n";
        exit($this->failures ? 1 : 0);
    }

    private function system_mode($db)
    {
        $this->check('no school: select unfiltered', "SELECT *\nFROM `students`", $db->from('students')->get_compiled_select());
        $all = (int) $db->query('SELECT COUNT(*) n FROM students')->row()->n;
        $this->check('no school: count_all sees every school', $all, $db->count_all('students'));
        $this->check('no school: seeded data spans schools', true, $all > 0);
    }

    private function compiled_sql($db, $t)
    {
        $this->check('select', "SELECT *\nFROM `students`\nWHERE `students`.`school_id` = $t",
            $db->from('students')->get_compiled_select());

        $this->check('or_where is wrapped',
            "SELECT *\nFROM `users`\nWHERE (`username` = 'std1'\nOR `admission_no` = 'std1')\nAND `users`.`school_id` = $t",
            $db->from('users')->where('username', 'std1')->or_where('admission_no', 'std1')->get_compiled_select());

        $this->check('grouped conditions are wrapped',
            "SELECT *\nFROM `students`\nWHERE (  (\n`is_active` = 'yes'\nOR `id` = 1\n ))\nAND `students`.`school_id` = $t",
            $db->from('students')->group_start()->where('is_active', 'yes')->or_where('id', 1)->group_end()->get_compiled_select());

        $this->check('alias', "SELECT *\nFROM `students` `s`\nWHERE (`s`.`id` = 1)\nAND `s`.`school_id` = $t",
            $db->from('students s')->where('s.id', 1)->get_compiled_select());

        $this->check('get_where', true, strpos(
            $db->where('id', 3)->get_compiled_select('classes'), "AND `classes`.`school_id` = $t") !== false);

        $sql = $db->select('sch_settings.id')->from('sch_settings')
            ->join('sessions', 'sessions.id = sch_settings.session_id')
            ->join('languages', 'languages.id = sch_settings.lang_id')
            ->join('currencies', 'currencies.id = sch_settings.currency', 'left')
            ->get_compiled_select();
        $this->check('join school-owned table', true,
            strpos($sql, "JOIN `sessions` ON (`sessions`.`id` = `sch_settings`.`session_id`) AND `sessions`.`school_id` = $t") !== false);
        $this->check('left join keeps the filter in ON', true,
            strpos($sql, "LEFT JOIN `currencies` ON (`currencies`.`id` = `sch_settings`.`currency`) AND `currencies`.`school_id` = $t") !== false);
        $this->check('join platform table unfiltered', true,
            strpos($sql, "JOIN `languages` ON `languages`.`id` = `sch_settings`.`lang_id`\n") !== false);
        $this->check('base table filtered', true, substr($sql, -strlen("WHERE `sch_settings`.`school_id` = $t")) === "WHERE `sch_settings`.`school_id` = $t");

        $sql = $db->from('users')->join('students', 'students.id = users.user_id OR students.parent_id = users.id', 'left')->get_compiled_select();
        $this->check('join with OR is wrapped', true,
            strpos($sql, "ON (`students`.`id` = `users`.`user_id` OR `students`.`parent_id` = `users`.`id`) AND `students`.`school_id` = $t") !== false);

        $this->check('hybrid read includes system rows',
            "SELECT *\nFROM `roles`\nWHERE (`roles`.`school_id` IS NULL OR `roles`.`school_id` = $t)",
            $db->from('roles')->get_compiled_select());
        $sql = $db->from('staff_roles')->join('roles', 'roles.id = staff_roles.role_id')->get_compiled_select();
        $this->check('hybrid join includes system rows', true,
            strpos($sql, "AND (`roles`.`school_id` IS NULL OR `roles`.`school_id` = $t)") !== false);

        $this->check('platform table unfiltered', "SELECT *\nFROM `languages`", $db->from('languages')->get_compiled_select());
        $this->check('school registry unfiltered', "SELECT *\nFROM `school`", $db->from('school')->get_compiled_select());

        $this->check('implicit join in FROM list', true, strpos(
            $db->from('students, classes')->get_compiled_select(),
            "WHERE `students`.`school_id` = $t\nAND `classes`.`school_id` = $t") !== false);

        $sub = $db->select('student_id')->from('student_session')->get_compiled_select();
        $sql = $db->from('students')->where("id IN ($sub)", null, false)->get_compiled_select();
        $this->check('subquery filtered too', 2, substr_count($sql, "`school_id` = $t"));

        $this->check('count_all_results', true, strpos(
            $db->from('students')->where('is_active', 'yes')->get_compiled_select(), "AND `students`.`school_id` = $t") !== false);

        $this->check('update', "UPDATE `classes` SET `class` = 'X'\nWHERE (`id` = 5)\nAND `classes`.`school_id` = $t",
            $db->set('class', 'X')->where('id', 5)->get_compiled_update('classes'));
        $this->check('update without where still filtered', "UPDATE `classes` SET `class` = 'X'\nWHERE `classes`.`school_id` = $t",
            $db->set('class', 'X')->get_compiled_update('classes'));
        $this->check('hybrid update cannot touch system rows', "UPDATE `roles` SET `name` = 'X'\nWHERE (`id` = 7)\nAND `roles`.`school_id` = $t",
            $db->set('name', 'X')->where('id', 7)->get_compiled_update('roles'));
        $this->check('delete', "DELETE FROM `classes`\nWHERE (`id` = 5)\nAND `classes`.`school_id` = $t",
            $db->where('id', 5)->get_compiled_delete('classes'));
        $this->check('insert sets school_id', "INSERT INTO `classes` (`class`, `school_id`) VALUES ('X', $t)",
            $db->set('class', 'X')->get_compiled_insert('classes'));
        $this->check('insert with own school_id kept', "INSERT INTO `classes` (`class`, `school_id`) VALUES ('X', $t)",
            $db->set('class', 'X')->set('school_id', $t)->get_compiled_insert('classes'));
        $this->check('insert into platform table untouched', "INSERT INTO `languages` (`language`) VALUES ('X')",
            $db->set('language', 'X')->get_compiled_insert('languages'));

        $other = $t === 1 ? 2 : 1;
        $this->throws('insert with another school_id', function () use ($db, $other) {
            $db->set('class', 'X')->set('school_id', $other)->get_compiled_insert('classes');
        });
        $db->reset_query();
        $this->throws('update moving a row to another school', function () use ($db, $other) {
            $db->set('school_id', $other)->where('id', 5)->get_compiled_update('classes');
        });
        $db->reset_query();
        $this->throws('truncate school-owned table', function () use ($db) { $db->truncate('classes'); });
        $this->throws('truncate hybrid table', function () use ($db) { $db->truncate('roles'); });
        $this->throws('query caching refused', function () use ($db) { $db->start_cache(); });
        $this->throws('join USING refused', function () use ($db) {
            $db->from('student_session')->join('students', 'id')->get_compiled_select();
        });
        $db->reset_query();
        $this->check('no scope state leaks after a refused statement', "SELECT *\nFROM `languages`",
            $db->from('languages')->get_compiled_select());
    }

    private function writes($db, $t)
    {
        $other = $t === 1 ? 2 : 1;
        $db->trans_begin();
        $db->insert('classes', array('class' => 'Tenancy test A', 'is_active' => 'no'));
        $a = $db->insert_id();
        $db->insert_batch('classes', array(
            array('class' => 'Tenancy test B', 'is_active' => 'no'),
            array('class' => 'Tenancy test C', 'is_active' => 'no'),
        ));
        $rows = $db->without_tenant(function ($db) {
            return $db->select('school_id')->like('class', 'Tenancy test', 'after')->get('classes')->result_array();
        });
        $this->check('insert + insert_batch wrote 3 rows for this school', array_fill(0, 3, (string) $t), array_column($rows, 'school_id'));

        $this->throws('insert_batch with another school_id', function () use ($db, $other) {
            $db->insert_batch('classes', array(array('class' => 'X', 'is_active' => 'no', 'school_id' => $other)));
        });

        $victim = $db->without_tenant(function ($db) use ($other) {
            return $db->select('id')->where('school_id', $other)->limit(1)->get('classes')->row();
        });
        $db->where('id', $victim->id)->update('classes', array('class' => 'hijacked'));
        $this->check("update of another school's row affects nothing", 0, $db->affected_rows());
        $db->where('id', $victim->id)->delete('classes');
        $this->check("delete of another school's row affects nothing", 0, $db->affected_rows());

        // `source` has rows in both schools and nothing references it.
        $other_before = $db->without_tenant(function ($db) use ($other) { return $db->where('school_id', $other)->count_all_results('source'); });
        $db->empty_table('source'); // must only empty this school's rows
        $own_after = $db->without_tenant(function ($db) use ($t) { return $db->where('school_id', $t)->count_all_results('source'); });
        $other_after = $db->without_tenant(function ($db) use ($other) { return $db->where('school_id', $other)->count_all_results('source'); });
        $this->check("empty_table empties only this school", array(0, $other_before), array($own_after, $other_after));
        $this->check('other school had rows to protect', true, $other_before > 0);

        $db->update_batch('classes', array(array('id' => $victim->id, 'class' => 'hijacked')), 'id');
        $still = $db->without_tenant(function ($db) use ($victim) { return $db->where('id', $victim->id)->get('classes')->row(); });
        $this->check("update_batch can't touch another school's row", true, $still !== null && $still->class !== 'hijacked');

        $db->insert('roles', array('name' => 'Tenancy test role', 'is_active' => 1, 'is_system' => 0, 'is_superadmin' => 0));
        $role = $db->without_tenant(function ($db) { return $db->where('name', 'Tenancy test role')->get('roles')->row(); });
        $this->check('custom role belongs to this school', (string) $t, $role ? $role->school_id : null);
        $db->where('id', 7)->update('roles', array('name' => 'renamed'));
        $this->check('system role is read-only for a school', 0, $db->affected_rows());
        $db->trans_rollback();

        $left = $db->without_tenant(function ($db) { return $db->like('class', 'Tenancy test', 'after')->count_all_results('classes'); });
        $this->check('test writes rolled back', 0, $left);
    }

    private function executed($db, $t)
    {
        $other = $t === 1 ? 2 : 1;
        $own = $db->without_tenant(function ($db) use ($t) { return $db->where('school_id', $t)->count_all_results('students'); });
        $all = $db->without_tenant(function ($db) { return $db->count_all_results('students'); });
        $this->check('count_all_results = own students', $own, $db->count_all_results('students'));
        $this->check('count_all = own students', $own, $db->count_all('students'));
        $this->check('get = own students', $own, $db->get('students')->num_rows());
        $this->check('without_tenant sees every school', true, $all > $own);

        // The login lookup: the same username exists in both seeded schools.
        $rows = $db->select('users.id, users.school_id')->from('users')->join('students', 'students.id = users.user_id')
            ->where('username', 'std1')->or_where('students.admission_no', 'std1')->get()->result_array();
        $schools = array_values(array_unique(array_column($rows, 'school_id')));
        // Seeded schools have this user; a new school has none. Either way no other school's row may appear.
        $this->check('login lookup finds only this school', true, $schools === array() || $schools === array((string) $t));

        $foreign = $db->without_tenant(function ($db) use ($other) { return $db->where('school_id', $other)->limit(1)->get('students')->row(); });
        $this->check("another school's student id returns nothing", 0, $db->get_where('students', array('id' => $foreign->id))->num_rows());

        $settings = $db->select('sch_settings.name, sessions.session, currencies.short_name')->from('sch_settings')
            ->join('sessions', 'sessions.id = sch_settings.session_id')
            ->join('currencies', 'currencies.id = sch_settings.currency')
            ->join('languages', 'languages.id = sch_settings.lang_id')->get()->result_array();
        $this->check('exactly one settings row (this school)', 1, count($settings));

        $roles = $db->select('id')->order_by('id')->get('roles')->result_array();
        $this->check('system roles visible', array('1', '2', '3', '4', '6', '7'), array_column($roles, 'id'));
    }

    private function raw_guard($db, $t)
    {
        $guard = new Tenant_raw_sql_guard('throw', array('students', 'classes', 'staff', 'roles'), APPPATH . 'logs');
        $cases = array(
            'SELECT * FROM students' => array('students'),
            'SELECT * FROM `students` s WHERE s.id = 1' => array('students'),
            'SELECT * FROM students WHERE school_id = 1' => array(),
            'SELECT * FROM languages' => array(),
            "SELECT 'FROM students' AS x" => array(),
            '/* FROM students */ SELECT 1' => array(),
            'SELECT * FROM languages l, students s' => array('students'),
            'SELECT * FROM languages l JOIN classes c ON c.id = l.id' => array('classes'),
            "UPDATE staff SET name = 'x' WHERE id = 1" => array('staff'),
            'INSERT INTO classes (class) VALUES (1)' => array('classes'),
            'DELETE FROM classes WHERE id = 1' => array('classes'),
            'SELECT * FROM (SELECT * FROM students) t' => array('students'),
            'SHOW COLUMNS FROM students' => array(),
            'SELECT * FROM roles' => array('roles'),
        );
        foreach ($cases as $sql => $expected) {
            $this->check("guard: $sql", $expected, $guard->unscoped_tables($sql));
        }

        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tenancy_guard_test_' . getmypid();
        @mkdir($dir);
        $log = new Tenant_raw_sql_guard('log', array('students'), $dir);
        for ($i = 0; $i < 3; $i++) {
            $log->inspect('SELECT * FROM students'); // same caller + SQL: reported once
        }
        $log->inspect('SELECT * FROM students WHERE school_id = 1');
        $files = glob($dir . DIRECTORY_SEPARATOR . 'tenancy-raw-sql-*.log');
        $lines = $files ? file($files[0], FILE_IGNORE_NEW_LINES) : array();
        $this->check('log mode writes one line per unscoped statement', 1, count($lines));
        $this->check('log line names school, caller and table', true, isset($lines[0])
            && strpos($lines[0], "school=$t\tcontrollers/Tenancy_test.php:") !== false && strpos($lines[0], 'table(s) students') !== false);
        array_map('unlink', $files ?: array());
        @rmdir($dir);

        list($tenant_tables, $hybrid_tables) = $this->table_config();
        $db->configure_tenancy($tenant_tables, $hybrid_tables, new Tenant_raw_sql_guard('throw', array_merge($tenant_tables, $hybrid_tables), APPPATH . 'logs'));
        $this->check('builder query not reported', true, $db->get('students')->num_rows() >= 0);
        $this->check('builder insert/update not reported', true, $db->set('class', 'x')->where('id', 0)->update('classes'));
        $this->throws('unscoped raw query reported', function () use ($db) { $db->query('SELECT COUNT(*) n FROM students'); });
        $this->check('scoped raw query allowed', true,
            $db->query('SELECT COUNT(*) n FROM students WHERE school_id = ?', array($t))->row()->n >= 0);
        $db->from('students')->get_compiled_select();
        $this->throws('compiled-but-not-run SQL does not whitelist the next raw query', function () use ($db) {
            $db->query('SELECT COUNT(*) n FROM students');
        });
    }

    private function table_config()
    {
        $this->config->load('tenancy_tables', true);
        $c = $this->config->item('tenancy_tables');
        return array($c['tenancy_tenant_tables'], $c['tenancy_hybrid_tables']);
    }

    private function check($label, $expected, $actual)
    {
        $this->count++;
        if ($expected !== $actual) {
            $this->failures++;
            echo "FAIL $label\n  expected: " . var_export($expected, true) . "\n  actual:   " . var_export($actual, true) . "\n";
        }
    }

    private function throws($label, $fn)
    {
        $this->count++;
        try {
            $fn();
        } catch (Tenant_violation $e) {
            return;
        }
        $this->failures++;
        echo "FAIL $label: no Tenant_violation\n";
    }
}
