<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CLI wrapper around School_provisioner (the same code the approve button runs).
 *
 *   php index.php school_setup check <school id>   # validate only
 *   php index.php school_setup run <school id>     # set the school up
 */
class School_setup extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        $this->load->library('School_provisioner');
    }

    public function check($school_id = null)
    {
        $school = $this->school($school_id);
        try {
            $this->school_provisioner->validate($school);
            echo "School $school_id can be set up at " . $this->school_provisioner->url_for($school) . "\n";
        } catch (RuntimeException $e) {
            $this->fail($e->getMessage());
        }
    }

    public function run($school_id = null)
    {
        $school = $this->school($school_id);
        try {
            $result = $this->school_provisioner->provision((int) $school_id);
        } catch (RuntimeException $e) {
            $this->fail($e->getMessage());
        }
        echo "School {$result['school_id']} is set up at " . $this->school_provisioner->url_for($school) . "\n";
        echo "Super admin: {$result['admin_email']}\n";
        echo "Set-password link: {$result['set_password_url']}\n";
    }

    private function school($school_id)
    {
        if ($school_id === null || !ctype_digit((string) $school_id)) {
            $this->fail('Usage: php index.php school_setup <check|run> <school id>');
        }
        $school = $this->db->where('id', (int) $school_id)->get('school')->row_array();
        if (!$school) {
            $this->fail("No school $school_id.");
        }
        return $school;
    }

    private function fail($message)
    {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}
