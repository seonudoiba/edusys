<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * CLI migration runner for the single-database, school_id-scoped schema.
 *
 *   php index.php migrate              # migrate to the latest version
 *   php index.php migrate version 7    # migrate up or down to version 7 (0 drops everything)
 *   php index.php migrate status       # show current and latest versions
 *
 * The migrations build a fresh schema. They refuse to run against a legacy
 * database (one that already has application tables but no migration ledger).
 */
class Migrate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        $this->load->database();
        $this->guard_legacy_database();
        $this->load->library('migration');
    }

    public function index()
    {
        $this->report($this->migration->latest());
    }

    public function version($target = null)
    {
        if ($target === null || !ctype_digit((string) $target)) {
            $this->fail('Usage: php index.php migrate version <number>');
        }
        $this->report($this->migration->version((int) $target));
    }

    public function status()
    {
        $files = $this->migration->find_migrations();
        $latest = $files ? max(array_keys($files)) : 0;
        echo 'Database: ' . $this->db->database . PHP_EOL;
        echo 'Current version: ' . $this->current_version() . PHP_EOL;
        echo 'Latest version:  ' . $latest . ' (' . count($files) . ' files)' . PHP_EOL;
    }

    private function report($result)
    {
        if ($result === false) {
            $this->fail($this->migration->error_string());
        }
        echo 'Database is at version ' . $this->current_version() . PHP_EOL;
    }

    private function current_version()
    {
        $table = $this->config->item('migration_table');
        if (!$this->db->table_exists($table)) {
            return 0;
        }
        $row = $this->db->select('version')->get($table)->row();
        return $row ? (int) $row->version : 0;
    }

    private function guard_legacy_database()
    {
        $this->config->load('migration');
        $ledger = $this->config->item('migration_table');
        if (!$this->db->table_exists($ledger) && $this->db->table_exists('sch_settings')) {
            $this->fail('Refusing to migrate: ' . $this->db->database . ' looks like a legacy database '
                . '(application tables exist but there is no migration ledger). '
                . 'Point the application at a new, empty database.');
        }
    }

    private function fail($message)
    {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}
