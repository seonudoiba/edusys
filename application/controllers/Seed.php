<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CLI seed runner.
 *
 *   php index.php seed run platform    # school registry, plans, languages, system roles
 *   php index.php seed run school_1    # school 1 data (from the central smartschool database)
 *   php index.php seed run school_2    # school 2 data (from school_182)
 *   php index.php seed run all         # platform, then every school_<id> group
 *   php index.php seed status          # list groups and file counts
 *
 * Seed files live in application/seeds/<group>/NNN_<table>.php, one table per file.
 * A school group's id comes from its folder name (school_<id>).
 */
class Seed extends CI_Controller
{
    private $path;

    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        $this->path = APPPATH . 'seeds/';
        require_once APPPATH . 'libraries/Seeder.php';
        $this->load->database();
    }

    public function status()
    {
        foreach ($this->groups() as $group) {
            echo str_pad($group, 12) . count($this->files($group)) . " seed files\n";
        }
    }

    public function run($group = null)
    {
        if ($group === null) {
            $this->fail('Usage: php index.php seed run <platform|school_<id>|all>');
        }
        $groups = $group === 'all' ? $this->groups() : [$group];
        foreach ($groups as $g) {
            if (!in_array($g, $this->groups(), true)) {
                $this->fail("Unknown seed group '$g'. Available: " . implode(', ', $this->groups()));
            }
        }
        foreach ($groups as $g) {
            $this->run_group($g);
        }
    }

    private function run_group($group)
    {
        $school_id = null;
        if ($group !== 'platform') {
            $school_id = (int) substr($group, strlen('school_'));
            if ($this->db->where('id', $school_id)->count_all_results('school') === 0) {
                $this->fail("School $school_id is not in the registry. Run the platform seed first.");
            }
        }
        $seeds = array_map([$this, 'load_seed'], $this->files($group));

        echo "Seeding $group (" . count($seeds) . " tables)\n";
        $this->db->trans_begin();
        try {
            foreach (array_reverse($seeds) as $seed) {
                $seed->clear($this->db, $school_id);
            }
            $total = 0;
            foreach ($seeds as $seed) {
                $n = $seed->insert($this->db, $school_id);
                $total += $n;
                echo '  ' . str_pad($seed->table, 44) . $n . "\n";
            }
            if ($this->db->trans_status() === false) {
                throw new RuntimeException('Transaction failed.');
            }
            $this->db->trans_commit();
            echo "Done: $total rows.\n";
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            $this->fail("Rolled back $group: " . $e->getMessage());
        }
    }

    private function load_seed($file)
    {
        require_once $file;
        $group = basename(dirname($file));
        $table = preg_replace('/^\d{3}_/', '', basename($file, '.php'));
        $class = 'Seed_' . $group . '_' . $table;
        if (!class_exists($class, false)) {
            $this->fail("Seed class $class not found in $file");
        }
        return new $class();
    }

    private function groups()
    {
        $groups = [];
        foreach (glob($this->path . '*', GLOB_ONLYDIR) as $dir) {
            $name = basename($dir);
            if ($name === 'platform' || preg_match('/^school_[1-9]\d*$/', $name)) {
                $groups[] = $name;
            }
        }
        usort($groups, function ($a, $b) {
            if ($a === 'platform') return -1;
            if ($b === 'platform') return 1;
            return (int) substr($a, 7) - (int) substr($b, 7);
        });
        return $groups;
    }

    private function files($group)
    {
        $files = glob($this->path . $group . '/[0-9][0-9][0-9]_*.php');
        sort($files, SORT_STRING);
        return $files;
    }

    private function fail($message)
    {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}
