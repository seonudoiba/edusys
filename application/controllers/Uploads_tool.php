<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Per-school upload folders (phase 9), CLI only.
 *
 *   php index.php uploads_tool status                 # files per school, files left in the old shared tree
 *   php index.php uploads_tool init <id|all>          # create the school's folder (normally automatic)
 *   php index.php uploads_tool copy_legacy <id|all>   # copy the old shared files into the school's folder
 *   php index.php uploads_tool remove_legacy          # delete the old shared files (after copying)
 *
 * The old layout kept every school's files in one shared uploads/ tree. A school's folder is
 * uploads/s<id>/ and /uploads/... URLs are served from it only, so files left in the shared
 * tree are no longer reachable. copy_legacy never overwrites a school's own file.
 */
class Uploads_tool extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        $this->load->database();
    }

    public function status()
    {
        foreach ($this->schools('all') as $school) {
            $dir = FCPATH . 'uploads/s' . $school->id;
            $count = is_dir($dir) ? count($this->files($dir)) : 0;
            printf("school %-4d %-40s %5d files%s\n", $school->id, $school->school_name, $count, is_dir($dir) ? '' : ' (no folder yet)');
        }
        printf("old shared tree: %d files\n", count($this->legacy_files()));
    }

    public function init($which = null)
    {
        foreach ($this->schools($which) as $school) {
            upload_init_school((int) $school->id);
            echo "school {$school->id}: uploads/s{$school->id}/ ready\n";
        }
    }

    public function copy_legacy($which = null)
    {
        $legacy = $this->legacy_files();
        foreach ($this->schools($which) as $school) {
            upload_init_school((int) $school->id);
            $copied = 0;
            $kept = 0;
            foreach ($legacy as $relative) {
                $to = FCPATH . 'uploads/s' . $school->id . '/' . $relative;
                if (is_file($to)) {
                    $kept++;
                    continue;
                }
                if (!is_dir(dirname($to))) {
                    mkdir(dirname($to), 0755, true);
                }
                if (copy(FCPATH . 'uploads/' . $relative, $to)) {
                    $copied++;
                }
            }
            echo "school {$school->id}: copied $copied, kept $kept existing\n";
        }
    }

    public function remove_legacy()
    {
        $removed = 0;
        foreach ($this->legacy_files() as $relative) {
            if (@unlink(FCPATH . 'uploads/' . $relative)) {
                $removed++;
            }
        }
        echo "removed $removed files from the old shared tree (folders and index.html kept)\n";
    }

    /** Files of the old shared tree, relative to uploads/ (not school folders, defaults or index files). */
    private function legacy_files()
    {
        $out = array();
        foreach ($this->files(FCPATH . 'uploads') as $relative) {
            if (preg_match('~^(s\d+|_shared)/~', $relative) || in_array(basename($relative), array('index.html', '.htaccess', '.gitkeep'), true)) {
                continue;
            }
            $out[] = $relative;
        }
        return $out;
    }

    private function files($dir)
    {
        $out = array();
        $dir = rtrim(str_replace('\\', '/', $dir), '/');
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            if ($f->isFile()) {
                $out[] = substr(str_replace('\\', '/', $f->getPathname()), strlen($dir) + 1);
            }
        }
        return $out;
    }

    private function schools($which)
    {
        if ($which === null || ($which !== 'all' && !ctype_digit((string) $which))) {
            fwrite(STDERR, "Give a school id or 'all'.\n");
            exit(1);
        }
        $this->db->select('id, school_name')->order_by('id');
        if ($which !== 'all') {
            $this->db->where('id', (int) $which);
        }
        $schools = $this->db->get('school')->result();
        if (!$schools) {
            fwrite(STDERR, "No such school.\n");
            exit(1);
        }
        return $schools;
    }
}
