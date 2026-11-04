<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/Tenant.php';
require_once APPPATH . 'libraries/tenancy/Tenant_violation.php';

/**
 * Safety net for hand-written SQL passed to $this->db->query().
 *
 * Query-builder statements are scoped by the driver; raw SQL is not rewritten. This guard
 * finds the tables a raw statement reads or writes (after FROM, JOIN, UPDATE, INTO) and
 * reports the statement when it touches a school-owned table but never mentions school_id.
 * The check is heuristic and only answers "was school_id forgotten?"; it doesn't prove a
 * query is correctly scoped.
 *
 * Mode (EDUSYS_TENANCY_RAW_SQL): 'log' (default) appends to
 * application/logs/tenancy-raw-sql-YYYY-MM-DD.log, 'throw' raises Tenant_violation,
 * 'off' disables the check. Phase 5 converts the raw queries; after that, use 'throw'.
 */
class Tenant_raw_sql_guard
{
    const MODE_OFF = 'off';
    const MODE_LOG = 'log';
    const MODE_THROW = 'throw';

    /** @var string */
    private $mode;

    /** @var array<string,true> lowercase school-owned and hybrid table names */
    private $scoped_tables;

    /** @var string */
    private $log_dir;

    /** @var array<string,true> already reported in this process */
    private $reported = array();

    /** Statements that never read or write rows. */
    const IGNORED_STATEMENTS = '/^\s*(SHOW|DESCRIBE|DESC|SET|START|BEGIN|COMMIT|ROLLBACK|SAVEPOINT|RELEASE|LOCK|UNLOCK|USE|SELECT\s+(DATABASE|VERSION|LAST_INSERT_ID|FOUND_ROWS|GET_LOCK|RELEASE_LOCK|IS_FREE_LOCK)\s*\()\b/i';

    /** Words that end a FROM list. */
    const FROM_LIST_END = '/\b(WHERE|JOIN|LEFT|RIGHT|INNER|OUTER|CROSS|NATURAL|STRAIGHT_JOIN|GROUP|ORDER|LIMIT|HAVING|UNION|ON|USING|SET|WINDOW|FOR|INTO|PROCEDURE)\b|[();]/i';

    public function __construct($mode, array $scoped_tables, $log_dir)
    {
        $mode = strtolower(trim((string) $mode));
        $this->mode = in_array($mode, array(self::MODE_OFF, self::MODE_LOG, self::MODE_THROW), true) ? $mode : self::MODE_LOG;
        $this->scoped_tables = array_fill_keys(array_map('strtolower', $scoped_tables), true);
        $this->log_dir = rtrim($log_dir, '/\\') . DIRECTORY_SEPARATOR;
    }

    public function mode()
    {
        return $this->mode;
    }

    /**
     * @param string $sql
     * @param mixed $binds
     * @throws Tenant_violation in 'throw' mode
     */
    public function inspect($sql, $binds = false)
    {
        if ($this->mode === self::MODE_OFF) {
            return;
        }
        $tables = $this->unscoped_tables($sql);
        if ($tables === array()) {
            return;
        }
        $message = 'Raw SQL touches school-owned table(s) ' . implode(', ', $tables) . ' without school_id';
        if ($this->mode === self::MODE_THROW) {
            throw new Tenant_violation($message . ': ' . $this->shorten($sql));
        }
        $this->log($message, $sql);
    }

    /**
     * School-owned tables a statement references, when it never mentions school_id.
     *
     * @param string $sql
     * @return string[] empty when the statement is fine
     */
    public function unscoped_tables($sql)
    {
        $clean = self::strip_literals((string) $sql);
        if (preg_match(self::IGNORED_STATEMENTS, $clean)) {
            return array();
        }
        if (preg_match('/\bschool_id\b/i', $clean)) {
            return array();
        }
        $found = array();
        foreach (self::referenced_tables($clean) as $table) {
            if (isset($this->scoped_tables[$table])) {
                $found[$table] = $table;
            }
        }
        return array_values($found);
    }

    /**
     * Table names after FROM (comma lists included), JOIN, UPDATE and INTO.
     * Expects SQL with literals and comments already stripped.
     *
     * @return string[] lowercase names
     */
    public static function referenced_tables($sql)
    {
        $tables = array();
        if (!preg_match_all('/\b(FROM|JOIN|UPDATE|INTO)\b/i', $sql, $m, PREG_OFFSET_CAPTURE)) {
            return $tables;
        }
        foreach ($m[1] as $hit) {
            $rest = substr($sql, $hit[1] + strlen($hit[0]));
            if (strtoupper($hit[0]) === 'FROM') {
                $end = preg_match(self::FROM_LIST_END, $rest, $e, PREG_OFFSET_CAPTURE) ? $e[0][1] : strlen($rest);
                $pieces = explode(',', substr($rest, 0, $end));
            } else {
                $pieces = array($rest);
            }
            foreach ($pieces as $piece) {
                if (preg_match('/^\s*(?:IGNORE\s+|LOW_PRIORITY\s+|DELAYED\s+|HIGH_PRIORITY\s+)*`?([A-Za-z0-9_$]+)`?(?:\s*\.\s*`?([A-Za-z0-9_$]+)`?)?/i', $piece, $t)) {
                    $tables[] = strtolower(isset($t[2]) && $t[2] !== '' ? $t[2] : $t[1]);
                }
            }
        }
        return $tables;
    }

    /** Removes string literals and comments so their contents can't look like SQL. */
    public static function strip_literals($sql)
    {
        return preg_replace(
            array(
                "/'(?:[^'\\\\]|\\\\.|'')*'/s",   // 'single-quoted'
                '/"(?:[^"\\\\]|\\\\.|"")*"/s',   // "double-quoted"
                '#/\*.*?\*/#s',                  // /* block */
                '/(?:--\s|#)[^\n]*/',            // -- line, # line
            ),
            array("''", '""', ' ', ' '),
            $sql
        );
    }

    private function log($message, $sql)
    {
        $caller = $this->caller();
        $key = md5($caller . '|' . $sql);
        if (isset($this->reported[$key])) {
            return;
        }
        $this->reported[$key] = true;
        $school = Tenant::has() ? Tenant::id() : '-';
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'cli';
        $line = date('Y-m-d H:i:s') . "\tschool=$school\t$caller\t$uri\t$message\t" . $this->shorten($sql) . PHP_EOL;
        @file_put_contents($this->log_dir . 'tenancy-raw-sql-' . date('Y-m-d') . '.log', $line, FILE_APPEND | LOCK_EX);
    }

    /** First application frame outside the database layer: file:line. */
    private function caller()
    {
        $app = str_replace('\\', '/', APPPATH);
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 25) as $frame) {
            if (!isset($frame['file'])) {
                continue;
            }
            $file = str_replace('\\', '/', $frame['file']);
            if (strpos($file, $app) === 0 && strpos($file, '/libraries/tenancy/') === false) {
                return substr($file, strlen($app)) . ':' . $frame['line'];
            }
        }
        return 'unknown';
    }

    private function shorten($sql)
    {
        $sql = preg_replace('/\s+/', ' ', trim($sql));
        return strlen($sql) > 600 ? substr($sql, 0, 600) . '...' : $sql;
    }
}
