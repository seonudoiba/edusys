<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Base class for data seeds in application/seeds/<group>/NNN_<table>.php.
 *
 * Each seed file covers exactly one table. A seed declares its target table,
 * its column list and its rows (positional, matching $columns). File numbers
 * follow the migration numbers, so parents are always seeded before children.
 *
 * Re-running a group is safe: the runner first clears every table in reverse
 * order, then inserts in forward order.
 *  - school groups (school_<id>) clear only that school's rows;
 *  - the platform group upserts by primary key (never deletes).
 *
 * Seeds write through a plain database connection with no tenant scoping,
 * so they must only be run from the CLI (see controllers/Seed.php).
 */
abstract class Seeder
{
    /** @var string Target table. */
    public $table;

    /** @var string[] Column names, in the order used by rows(). */
    public $columns = [];

    /** @var int Rows per INSERT statement. */
    protected $batch_size = 200;

    /** @return array[] Positional rows matching $columns. */
    abstract public function rows();

    /**
     * Removes the rows this seed owns.
     *
     * @param CI_DB_query_builder $db
     * @param int|null $school_id School being seeded, or null for platform seeds.
     */
    public function clear($db, $school_id = null)
    {
        if ($school_id !== null) {
            $db->where('school_id', (int) $school_id)->delete($this->table);
        }
        // Platform rows are upserted by insert(); deleting them would be blocked by (or cascade into)
        // the school data that references them.
    }

    /**
     * Inserts this seed's rows.
     *
     * @param CI_DB_query_builder $db
     * @param int|null $school_id School being seeded, or null for platform seeds.
     * @return int Number of rows inserted.
     */
    public function insert($db, $school_id = null)
    {
        $rows = $this->rows();
        $school_col = array_search('school_id', $this->columns, true);
        $table = $db->protect_identifiers($this->table, true);
        $cols = array_map(function ($c) use ($db) { return $db->protect_identifiers($c); }, $this->columns);
        $sql = "INSERT INTO $table (" . implode(', ', $cols) . ') VALUES ';
        $upsert = '';
        if ($school_id === null) {
            $upsert = ' ON DUPLICATE KEY UPDATE ' . implode(', ', array_map(function ($c) { return "$c = VALUES($c)"; }, $cols));
        }
        foreach (array_chunk($rows, $this->batch_size) as $chunk) {
            $tuples = [];
            foreach ($chunk as $row) {
                if ($school_id !== null) {
                    $row[$school_col] = (int) $school_id;
                }
                $tuples[] = '(' . implode(', ', array_map([$db, 'escape'], $row)) . ')';
            }
            if ($db->query($sql . implode(', ', $tuples) . $upsert) === false) {
                $error = $db->error();
                throw new RuntimeException("Seeding {$this->table} failed: {$error['message']}");
            }
        }
        return count($rows);
    }
}
