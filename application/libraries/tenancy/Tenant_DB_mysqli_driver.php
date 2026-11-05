<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/Tenant.php';
require_once APPPATH . 'libraries/tenancy/Tenant_violation.php';
require_once APPPATH . 'libraries/tenancy/Tenant_raw_sql_guard.php';

/**
 * MySQLi driver that confines every query-builder statement to the current school.
 *
 * Loaded by core/MY_Loader.php in place of CI_DB_mysqli_driver. Table scopes come
 * from config/tenancy_tables.php:
 *
 *   tenant   SELECT/UPDATE/DELETE: WHERE (<original conditions>) AND t.school_id = <id>
 *            JOIN:                 ON (<original condition>) AND t.school_id = <id>
 *            INSERT/REPLACE:       school_id set to <id>; another school's id is refused
 *            UPDATE SET school_id: refused unless it is <id>
 *            TRUNCATE:             refused
 *   hybrid   reads:  (t.school_id IS NULL OR t.school_id = <id>)   (shared system rows + own)
 *            writes: t.school_id = <id>                             (system rows are read-only)
 *   platform never filtered
 *
 * The original WHERE is wrapped in parentheses so OR conditions cannot escape the
 * school filter (login queries use `username = ? OR admission_no = ?`).
 *
 * Raw SQL passed to query() is not rewritten; Tenant_raw_sql_guard reports it when it
 * touches a school-owned table without mentioning school_id.
 *
 * No school set (CLI migrations and seeds only): nothing is filtered. On the web the
 * tenant hook always sets a school first; a web request without one is refused.
 */
class Tenant_DB_mysqli_driver extends CI_DB_mysqli_driver
{
    /** @var array<string,true> lowercase table name => true */
    private $tenant_tables = array();

    /** @var array<string,true> */
    private $hybrid_tables = array();

    /** @var Tenant_raw_sql_guard|null */
    private $raw_guard = null;

    /** @var int >0 while inside without_tenant() */
    private $bypass_depth = 0;

    /** @var array[] Tables the statement being compiled is scoped to: [['ref' => quoted name, 'hybrid' => bool]] */
    private $scope_targets = array();

    /** @var string 'read' or 'write' for the statement being compiled */
    private $scope_mode = 'read';

    /** @var bool The next query() call runs SQL this builder compiled (and scoped). */
    private $builder_sql_pending = false;

    /**
     * @param array $tenant_tables
     * @param array $hybrid_tables
     * @param Tenant_raw_sql_guard|null $guard
     */
    public function configure_tenancy(array $tenant_tables, array $hybrid_tables, $guard = null)
    {
        $this->tenant_tables = array_fill_keys(array_map('strtolower', $tenant_tables), true);
        $this->hybrid_tables = array_fill_keys(array_map('strtolower', $hybrid_tables), true);
        $this->raw_guard = $guard;
    }

    /**
     * Runs $callback with school filtering switched off, for platform code that must see
     * every school (registry, cross-school jobs). Keep these calls rare and reviewed.
     *
     * @param callable $callback receives this connection
     * @return mixed the callback's return value
     */
    public function without_tenant($callback)
    {
        $this->bypass_depth++;
        try {
            return call_user_func($callback, $this);
        } finally {
            $this->bypass_depth--;
        }
    }

    /**
     * Condition confining a table to the current school, for hand-written SQL:
     *
     *   "SELECT ... FROM students s WHERE s.is_active = 'yes' AND " . $this->db->school_filter('s')
     *
     * @param string $ref    table name or alias as written in the statement
     * @param bool   $hybrid true for hybrid tables (roles): also allow shared system rows
     * @return string e.g. "`s`.`school_id` = 2"
     * @throws Tenant_violation when no school is set
     */
    public function school_filter($ref, $hybrid = false)
    {
        if (!preg_match('/^[A-Za-z0-9_$]+$/', $ref)) {
            throw new InvalidArgumentException("school_filter() needs a table name or alias, got '$ref'.");
        }
        return $this->scope_condition($this->escape_identifiers($ref), $hybrid);
    }

    /**
     * Current school id for hand-written SQL (inserts, bound parameters).
     *
     * @return int
     * @throws Tenant_violation when no school is set
     */
    public function school_id()
    {
        if (!Tenant::has()) {
            throw new Tenant_violation('No school is set for this request.');
        }
        return Tenant::id();
    }

    /** True when statements are currently filtered by school. */
    public function tenant_scoping_active()
    {
        if ($this->bypass_depth > 0) {
            return false;
        }
        if (Tenant::has()) {
            return true;
        }
        if (!is_cli()) {
            throw new Tenant_violation('Database access before a school was resolved for this request.');
        }
        return false; // CLI without EDUSYS_SCHOOL_ID: migrations and seeds
    }

    // ------------------------------------------------------------------
    // SELECT (get, get_where, count_all_results, get_compiled_select)
    // ------------------------------------------------------------------

    protected function _compile_select($select_override = false)
    {
        if (!$this->tenant_scoping_active()) {
            return parent::_compile_select($select_override);
        }
        $saved_targets = $this->scope_targets;
        $saved_mode = $this->scope_mode;
        $saved_joins = $this->qb_join;
        try {
            $this->_merge_cache();
            $this->scope_targets = $this->targets_from_list($this->qb_from);
            $this->scope_mode = 'read';
            $this->qb_join = array_map(array($this, 'scope_join'), $this->qb_join);
            $sql = parent::_compile_select($select_override);
        } finally {
            $this->qb_join = $saved_joins;
            $this->scope_targets = $saved_targets;
            $this->scope_mode = $saved_mode;
        }
        $this->builder_sql_pending = true;
        return $sql;
    }

    // ------------------------------------------------------------------
    // WHERE: wrap the original conditions, then add the school filter
    // ------------------------------------------------------------------

    protected function _compile_wh($qb_key)
    {
        $sql = parent::_compile_wh($qb_key);
        if ($qb_key !== 'qb_where' || $this->scope_targets === array() || !$this->tenant_scoping_active()) {
            return $sql;
        }
        $conditions = array();
        foreach ($this->scope_targets as $t) {
            $conditions[] = $this->scope_condition($t['ref'], $t['hybrid'] && $this->scope_mode === 'read');
        }
        $scope = implode("\nAND ", $conditions);
        if ($sql === '') {
            return "\nWHERE " . $scope;
        }
        return "\nWHERE (" . substr($sql, strlen("\nWHERE ")) . ")\nAND " . $scope;
    }

    // ------------------------------------------------------------------
    // UPDATE / DELETE (update, update_batch, delete, empty_table)
    // ------------------------------------------------------------------

    protected function _update($table, $values)
    {
        if (!$this->tenant_scoping_active()) {
            return parent::_update($table, $values);
        }
        $info = $this->table_info($table);
        if ($info !== null) {
            foreach ($values as $key => $val) {
                if ($this->is_school_id_column($key)) {
                    $this->assert_own_school($val, $info['name']);
                }
            }
        }
        return $this->with_write_targets($table, function () use ($table, $values) {
            return parent::_update($table, $values);
        });
    }

    protected function _update_batch($table, $values, $index)
    {
        if (!$this->tenant_scoping_active()) {
            return parent::_update_batch($table, $values, $index);
        }
        $info = $this->table_info($table);
        if ($info !== null) {
            foreach ($values as $row) {
                foreach ($row as $field => $cell) {
                    if ($this->is_school_id_column($cell['field'])) {
                        $this->assert_own_school($cell['value'], $info['name']);
                    }
                }
            }
        }
        return $this->with_write_targets($table, function () use ($table, $values, $index) {
            return parent::_update_batch($table, $values, $index);
        });
    }

    protected function _delete($table)
    {
        if (!$this->tenant_scoping_active()) {
            return parent::_delete($table);
        }
        return $this->with_write_targets($table, function () use ($table) {
            return parent::_delete($table);
        });
    }

    // Query Builder caching re-merges cached JOINs after scoping and would add unscoped copies.
    // The application never uses it; refuse it rather than scope it half-way.
    public function start_cache()
    {
        if ($this->tenant_scoping_active()) {
            throw new Tenant_violation('Query Builder caching (start_cache) is not supported with school scoping.');
        }
        return parent::start_cache();
    }

    public function truncate($table = '')
    {
        $name = $table === '' ? (isset($this->qb_from[0]) ? $this->qb_from[0] : '') : $table;
        if ($this->tenant_scoping_active() && $this->table_info($name) !== null) {
            throw new Tenant_violation("TRUNCATE of school-owned table '" . $this->bare_name($name) . "' is not allowed.");
        }
        return parent::truncate($table);
    }

    public function count_all($table = '')
    {
        if ($table === '' || !$this->tenant_scoping_active() || $this->table_info($table) === null) {
            return parent::count_all($table);
        }
        return $this->count_all_results($table);
    }

    // ------------------------------------------------------------------
    // INSERT / REPLACE
    // ------------------------------------------------------------------

    protected function _insert($table, $keys, $values)
    {
        if ($this->tenant_scoping_active() && ($info = $this->table_info($table)) !== null) {
            list($keys, $values) = $this->with_school_value($info['name'], $keys, $values);
        }
        $this->builder_sql_pending = true;
        return parent::_insert($table, $keys, $values);
    }

    protected function _replace($table, $keys, $values)
    {
        if ($this->tenant_scoping_active() && ($info = $this->table_info($table)) !== null) {
            list($keys, $values) = $this->with_school_value($info['name'], $keys, $values);
        }
        $this->builder_sql_pending = true;
        return parent::_replace($table, $keys, $values);
    }

    public function insert_batch($table, $set = null, $escape = null, $batch_size = 100)
    {
        if ($set !== null && $this->tenant_scoping_active() && ($info = $this->table_info($table)) !== null) {
            $set = $this->_object_to_array_batch($set);
            $rows = array();
            foreach ((array) $set as $row) {
                $row = $this->_object_to_array($row);
                if (array_key_exists('school_id', $row)) {
                    $this->assert_own_school($row['school_id'], $info['name']);
                }
                $row['school_id'] = Tenant::id();
                $rows[] = $row;
            }
            $set = $rows;
        }
        return parent::insert_batch($table, $set, $escape, $batch_size);
    }

    protected function _insert_batch($table, $keys, $values)
    {
        if ($this->tenant_scoping_active() && ($info = $this->table_info($table)) !== null) {
            $has_school = false;
            foreach ($keys as $k) {
                $has_school = $has_school || $this->is_school_id_column($k);
            }
            if (!$has_school) {
                // Rows staged with set_insert_batch(): add the column to every tuple.
                $keys[] = $this->protect_identifiers('school_id');
                foreach ($values as $i => $tuple) {
                    $values[$i] = substr($tuple, 0, -1) . ',' . Tenant::id() . ')';
                }
            }
            // With the column present, insert_batch() has already checked every row.
        }
        $this->builder_sql_pending = true;
        return parent::_insert_batch($table, $keys, $values);
    }

    // ------------------------------------------------------------------
    // Raw SQL
    // ------------------------------------------------------------------

    public function query($sql, $binds = false, $return_object = null)
    {
        $from_builder = $this->builder_sql_pending;
        $this->builder_sql_pending = false;
        if (!$from_builder && $this->raw_guard !== null && $this->tenant_scoping_active()) {
            $this->raw_guard->inspect($sql, $binds);
        }
        return parent::query($sql, $binds, $return_object);
    }

    // get_compiled_*() return SQL without running it; the pending flag must not leak to the next query().
    public function get_compiled_select($table = '', $reset = true)
    {
        $sql = parent::get_compiled_select($table, $reset);
        $this->builder_sql_pending = false;
        return $sql;
    }

    public function get_compiled_insert($table = '', $reset = true)
    {
        $sql = parent::get_compiled_insert($table, $reset);
        $this->builder_sql_pending = false;
        return $sql;
    }

    public function get_compiled_update($table = '', $reset = true)
    {
        $sql = parent::get_compiled_update($table, $reset);
        $this->builder_sql_pending = false;
        return $sql;
    }

    public function get_compiled_delete($table = '', $reset = true)
    {
        $sql = parent::get_compiled_delete($table, $reset);
        $this->builder_sql_pending = false;
        return $sql;
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function with_write_targets($table, $compile)
    {
        $saved_targets = $this->scope_targets;
        $saved_mode = $this->scope_mode;
        try {
            $this->scope_targets = $this->targets_from_list(array($table));
            $this->scope_mode = 'write';
            $sql = $compile();
        } finally {
            $this->scope_targets = $saved_targets;
            $this->scope_mode = $saved_mode;
        }
        $this->builder_sql_pending = true;
        return $sql;
    }

    /** @param string[] $entries FROM entries such as "`students`", "`students` `s`", "students AS s" */
    private function targets_from_list(array $entries)
    {
        $targets = array();
        foreach ($entries as $entry) {
            foreach (explode(',', $entry) as $part) {
                $info = $this->table_info($part);
                if ($info !== null) {
                    $targets[] = $info;
                }
            }
        }
        return $targets;
    }

    /**
     * Parses one table reference. Returns null for platform tables, subqueries and
     * anything that is not a plain table name.
     *
     * @return array|null ['name' => lowercase table, 'ref' => quoted alias or table, 'hybrid' => bool]
     */
    private function table_info($reference)
    {
        $reference = trim((string) $reference);
        if (!preg_match('/^`?([A-Za-z0-9_$]+)`?(?:\s+(?:AS\s+)?`?([A-Za-z0-9_$]+)`?)?$/i', $reference, $m)) {
            return null;
        }
        $name = strtolower($m[1]);
        if ($this->dbprefix !== '' && strpos($name, strtolower($this->dbprefix)) === 0) {
            $name = substr($name, strlen($this->dbprefix));
        }
        $hybrid = isset($this->hybrid_tables[$name]);
        if (!$hybrid && !isset($this->tenant_tables[$name])) {
            return null;
        }
        $ref = isset($m[2]) && $m[2] !== '' ? $m[2] : $m[1];
        return array('name' => $name, 'ref' => $this->escape_identifiers($ref), 'hybrid' => $hybrid);
    }

    /** Adds the school condition to one compiled JOIN clause. */
    private function scope_join($join)
    {
        if (!preg_match('/^(.*?JOIN )(.+?)( ON | USING )(.*)$/s', $join, $m)) {
            return $join;
        }
        $info = $this->table_info($m[2]);
        if ($info === null) {
            return $join;
        }
        if ($m[3] === ' USING ') {
            throw new Tenant_violation("JOIN ... USING on school-owned table '{$info['name']}' cannot be scoped; use ON.");
        }
        return $m[1] . $m[2] . ' ON (' . $m[4] . ') AND ' . $this->scope_condition($info['ref'], $info['hybrid']);
    }

    private function scope_condition($ref, $include_system_rows)
    {
        $column = $ref . '.' . $this->escape_identifiers('school_id');
        $id = Tenant::id();
        return $include_system_rows ? "($column IS NULL OR $column = $id)" : "$column = $id";
    }

    /** Sets school_id in an INSERT/REPLACE column list, refusing another school's id. */
    private function with_school_value($table, array $keys, array $values)
    {
        foreach ($keys as $i => $key) {
            if ($this->is_school_id_column($key)) {
                $this->assert_own_school($values[$i], $table);
                $values[$i] = (string) Tenant::id();
                return array($keys, $values);
            }
        }
        $keys[] = $this->escape_identifiers('school_id');
        $values[] = (string) Tenant::id();
        return array($keys, $values);
    }

    private function is_school_id_column($key)
    {
        $key = strtolower(trim(str_replace('`', '', (string) $key)));
        return $key === 'school_id' || substr($key, -10) === '.school_id';
    }

    /** @param mixed $value escaped SQL literal ("'2'", "2", "NULL") or raw value */
    private function assert_own_school($value, $table)
    {
        $v = trim((string) $value, " '\"");
        if ($v !== (string) Tenant::id()) {
            throw new Tenant_violation("Refused to write school_id=$v into '$table' as school " . Tenant::id() . '.');
        }
    }

    private function bare_name($reference)
    {
        $info = preg_match('/^`?([A-Za-z0-9_$]+)`?/', trim($reference), $m) ? $m[1] : $reference;
        return strtolower($info);
    }
}
