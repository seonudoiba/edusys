<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Runs a cron task for every active, set-up school (CLI only). One crontab line for the
 * whole platform instead of one cron URL per school.
 *
 *   php index.php cron_all run              # daily jobs (Cron::index: fee and event reminders, scheduled messages)
 *   php index.php cron_all run student_attendance
 *   php index.php cron_all run schedulesmsemails
 *   php index.php cron_all schools          # list the schools a run would cover
 *
 * Each school runs in its own PHP process (`php index.php cron <task>` with EDUSYS_SCHOOL_ID),
 * because the tenant is fixed once per request. A failing school is reported and the run
 * continues. A lock file prevents two runs of the same task overlapping. Exit status 1 when
 * any school failed.
 *
 * Example crontab: run `cron_all run student_attendance` every 5 minutes and `cron_all run`
 * once a day (e.g. 06:00), both from the project directory.
 */
class Cron_all extends CI_Controller
{
    /** Cron methods that may be run for all schools (autobackup is a platform task now). */
    const TASKS = array('index', 'student_attendance', 'feereminder', 'eventreminder', 'schedulesmsemails');

    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        if (Tenant::has()) {
            $this->fail('Run cron_all without EDUSYS_SCHOOL_ID: it goes through every school itself.');
        }
        $this->load->database();
    }

    public function schools()
    {
        foreach ($this->active_schools() as $s) {
            echo str_pad($s['id'], 6) . str_pad((string) $s['subdomain'], 24) . $s['school_name'] . "\n";
        }
    }

    public function run($task = 'index')
    {
        if (!in_array($task, self::TASKS, true)) {
            $this->fail("Unknown task '$task'. Tasks: " . implode(', ', self::TASKS));
        }
        $lock = fopen(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'edusys_cron_all_' . substr(md5(FCPATH), 0, 8) . '_' . $task . '.lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
            $this->fail("cron_all $task is already running.");
        }

        $schools = $this->active_schools();
        $failed = 0;
        $started = microtime(true);
        echo date('Y-m-d H:i:s') . " cron_all $task: " . count($schools) . " school(s)\n";
        foreach ($schools as $s) {
            $t = microtime(true);
            list($code, $output) = $this->run_for_school((int) $s['id'], $task);
            $label = 'school ' . $s['id'] . ' (' . ($s['subdomain'] ?: 'central') . ')';
            if ($code === 0) {
                printf("  ok     %-32s %.1fs\n", $label, microtime(true) - $t);
            } else {
                $failed++;
                $tail = trim(implode("\n", array_slice(explode("\n", trim($output)), -5)));
                printf("  FAILED %-32s exit %d\n%s\n", $label, $code, $tail === '' ? '' : '         ' . str_replace("\n", "\n         ", $tail));
            }
        }
        printf("done in %.1fs, %d failed\n", microtime(true) - $started, $failed);
        flock($lock, LOCK_UN);
        fclose($lock);
        exit($failed ? 1 : 0);
    }

    /** Enabled schools that have been set up (they have a settings row). */
    private function active_schools()
    {
        return $this->db->select('school.id, school.subdomain, school.school_name')
            ->from('school')
            ->join('sch_settings', 'sch_settings.school_id = school.id')
            ->where('school.status', 1)
            ->group_by('school.id')
            ->order_by('school.id')
            ->get()->result_array();
    }

    /** @return array [exit code, combined output] */
    private function run_for_school($school_id, $task)
    {
        $env = getenv();
        $env['EDUSYS_SCHOOL_ID'] = (string) $school_id;
        if (getenv('EDUSYS_DB_PASSWORD') === '') {
            // An empty variable doesn't survive into a child process on Windows.
            $env['EDUSYS_DB_PASSWORD_EMPTY'] = 'true';
        }
        // Three segments: routes.php maps cron/<one segment> to cron/index/<key>, which would run the daily jobs.
        $cmd = array(PHP_BINARY, FCPATH . 'index.php', 'cron', $task, 'cli');
        $proc = proc_open($cmd, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, FCPATH, $env);
        if (!is_resource($proc)) {
            return array(-1, 'could not start PHP');
        }
        $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        return array(proc_close($proc), $output);
    }

    private function fail($message)
    {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}
