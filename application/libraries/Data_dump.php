<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SQL dumps written in PHP (no mysqldump on most hosts):
 *   full()            every table, structure and rows (Platform > Backups)
 *   school()          one school's rows: tenant tables, its own roles, its school row and the
 *                     platform rows that name it (Schools > a school > Export data)
 *   purge_school()    deletes one school's rows from the tenant and hybrid tables
 *
 * Reads go straight through mysqli (unbuffered, streamed into a gzip file), outside the
 * school filter: platform code only.
 */
class Data_dump
{
    const ROWS_PER_INSERT = 200;

    /** @var CI_Controller */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    /** Where backups are kept: EDUSYS_BACKUP_DIR, or application/backups (never served by the web server). */
    public static function backup_dir()
    {
        $dir = trim((string) getenv('EDUSYS_BACKUP_DIR'));
        $dir = $dir !== '' ? rtrim(str_replace('\\', '/', $dir), '/') : rtrim(str_replace('\\', '/', APPPATH), '/') . '/backups';
        if (!is_dir($dir)) {
            @mkdir($dir, 0750, true);
        }
        return $dir;
    }

    /** Full dump of the database to a .sql.gz file. */
    public function full($path)
    {
        $gz = $this->open($path);
        $this->header($gz, 'Full backup of ' . $this->CI->db->database);
        foreach ($this->tables() as $table) {
            $create = $this->conn()->query('SHOW CREATE TABLE ' . $this->q($table))->fetch_row();
            gzwrite($gz, "\n-- " . $table . "\nDROP TABLE IF EXISTS " . $this->q($table) . ";\n" . $create[1] . ";\n");
            $this->rows($gz, $table, 'SELECT * FROM ' . $this->q($table));
        }
        $this->footer($gz);
        gzclose($gz);
    }

    /**
     * One school's data as INSERT statements (load into a database with the same schema version).
     * @return array table => rows written
     */
    public function school($school_id, $path)
    {
        $id = (int) $school_id;
        $gz = $this->open($path);
        $this->header($gz, 'Data of school #' . $id . ' (school_id = ' . $id . ')');
        $counts = array();
        $existing = array_flip($this->tables());
        foreach ($this->school_tables() as $table) {
            if (isset($existing[$table])) {
                $counts[$table] = $this->rows($gz, $table, 'SELECT * FROM ' . $this->q($table) . ' WHERE school_id = ' . $id);
            }
        }
        $counts['school'] = $this->rows($gz, 'school', 'SELECT * FROM school WHERE id = ' . $id);
        foreach ($this->platform_school_tables() as $table => $column) {
            $counts[$table] = $this->rows($gz, $table, 'SELECT * FROM ' . $this->q($table) . ' WHERE ' . $this->q($column) . ' = ' . $id);
        }
        $this->footer($gz);
        gzclose($gz);
        return array_filter($counts);
    }

    /**
     * Deletes every row of the school in the tenant and hybrid tables (not the school row and not
     * the platform's billing records).
     * @return int rows deleted
     */
    public function purge_school($school_id)
    {
        $id = (int) $school_id;
        if ($id <= 0) {
            throw new InvalidArgumentException('No school given.');
        }
        $conn = $this->conn();
        $existing = array_flip($this->tables());
        $deleted = 0;
        $conn->query('SET FOREIGN_KEY_CHECKS = 0'); // tenancy-lint: ok session setting, no table
        try {
            foreach ($this->school_tables() as $table) {
                if (isset($existing[$table])) {
                    $conn->query('DELETE FROM ' . $this->q($table) . ' WHERE school_id = ' . $id);
                    $deleted += max(0, (int) $conn->affected_rows);
                }
            }
            foreach (array('platform_impersonation_tokens', 'platform_billing_tokens', 'platform_school_domains') as $table) {
                if (isset($existing[$table])) {
                    $conn->query('DELETE FROM ' . $this->q($table) . ' WHERE school_ref = ' . $id);
                }
            }
        } finally {
            $conn->query('SET FOREIGN_KEY_CHECKS = 1'); // tenancy-lint: ok session setting, no table
        }
        return $deleted;
    }

    /** Tenant tables plus hybrid ones (a school's own roles; shared system rows have no school_id). */
    private function school_tables()
    {
        $this->CI->config->load('tenancy_tables', true);
        $c = (array) $this->CI->config->item('tenancy_tables');
        $tables = array_merge(isset($c['tenancy_tenant_tables']) ? $c['tenancy_tenant_tables'] : array(), isset($c['tenancy_hybrid_tables']) ? $c['tenancy_hybrid_tables'] : array());
        if (!$tables) {
            // Never purge or export "nothing" silently.
            throw new RuntimeException('The tenancy table lists (config/tenancy_tables.php) are missing.');
        }
        return $tables;
    }

    /**
     * Platform tables naming the school: table => column (school_ref for invoices, notes, tickets...;
     * school_id for subscriptions, which predate school_ref).
     */
    private function platform_school_tables()
    {
        $school_tables = array_flip($this->school_tables());
        $rows = $this->conn()->query("SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE()"
            . " AND COLUMN_NAME IN ('school_ref', 'school_id') ORDER BY TABLE_NAME, COLUMN_NAME DESC");
        $out = array();
        while ($r = $rows->fetch_row()) {
            if (!isset($school_tables[strtolower($r[0])]) && !isset($out[$r[0]])) {
                $out[$r[0]] = $r[1];
            }
        }
        return $out;
    }

    private function tables()
    {
        $rows = $this->conn()->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $out = array();
        while ($r = $rows->fetch_row()) {
            $out[] = $r[0];
        }
        return $out;
    }

    /** Streams a SELECT into multi-row INSERTs; returns the row count. */
    private function rows($gz, $table, $sql)
    {
        $conn = $this->conn();
        $result = $conn->query($sql, MYSQLI_USE_RESULT); // tenancy-lint: ok platform dumps; callers pass whole tables or WHERE school_id/school_ref
        if (!$result) {
            throw new RuntimeException('Reading ' . $table . ' failed: ' . $conn->error);
        }
        $count = 0;
        $batch = array();
        $columns = null;
        while ($row = $result->fetch_assoc()) {
            if ($columns === null) {
                $columns = '(' . implode(', ', array_map(array($this, 'q'), array_keys($row))) . ')';
            }
            $values = array();
            foreach ($row as $v) {
                $values[] = $v === null ? 'NULL' : "'" . $conn->real_escape_string($v) . "'";
            }
            $batch[] = '(' . implode(',', $values) . ')';
            $count++;
            if (count($batch) >= self::ROWS_PER_INSERT) {
                gzwrite($gz, 'INSERT INTO ' . $this->q($table) . ' ' . $columns . " VALUES\n" . implode(",\n", $batch) . ";\n");
                $batch = array();
            }
        }
        $result->free();
        if ($batch) {
            gzwrite($gz, 'INSERT INTO ' . $this->q($table) . ' ' . $columns . " VALUES\n" . implode(",\n", $batch) . ";\n");
        }
        return $count;
    }

    private function open($path)
    {
        $gz = gzopen($path, 'wb6');
        if (!$gz) {
            throw new RuntimeException('Cannot write ' . $path);
        }
        return $gz;
    }

    private function header($gz, $title)
    {
        $version = $this->conn()->query('SELECT MAX(version) FROM migrations')->fetch_row();
        gzwrite($gz, "-- " . $title . "\n-- Created " . date('Y-m-d H:i:s') . ' · schema version ' . ($version ? $version[0] : '?') . "\n"
            . "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\nSET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n");
    }

    private function footer($gz)
    {
        gzwrite($gz, "\nSET FOREIGN_KEY_CHECKS = 1;\n");
    }

    private function q($name)
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }

    /** @return mysqli */
    private function conn()
    {
        if (!$this->CI->db->conn_id) {
            $this->CI->db->initialize();
        }
        $this->CI->db->conn_id->set_charset('utf8mb4');
        return $this->CI->db->conn_id;
    }
}
