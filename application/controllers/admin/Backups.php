<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Platform > Backups: full database dumps (.sql.gz) taken from the browser, listed, downloaded
 * and deleted. Kept in EDUSYS_BACKUP_DIR (default application/backups, never served by the web
 * server). Restoring is done on the server (see the page and DEPLOYMENT.md), never from here.
 */
class Backups extends Admin_Controller
{
    const PERMISSION = 'platform_settings';
    const NAME_PATTERN = '/^edusys-[0-9]{8}-[0-9]{6}(-[a-z0-9]+)?\.sql\.gz$/';

    public function __construct()
    {
        // Platform feature: only on the central host (404 on school subdomains).
        Tenant::require_platform();
        parent::__construct();
        $this->load->library(array('Form_token', 'Data_dump', 'Platform_audit'));
    }

    public function index()
    {
        $this->require_privilege('can_view');
        $this->session->set_userdata('top_menu', 'platform_admin');
        $this->session->set_userdata('sub_menu', 'admin/backups');
        $dir = Data_dump::backup_dir();
        $files = array();
        foreach ((array) glob($dir . '/edusys-*.sql.gz') as $path) {
            if (preg_match(self::NAME_PATTERN, basename($path))) {
                $files[] = array('name' => basename($path), 'size' => filesize($path), 'time' => filemtime($path));
            }
        }
        usort($files, function ($a, $b) { return $b['time'] - $a['time']; });
        $db = $this->db;
        $this->load->view('layout/header');
        $this->load->view('admin/platform/backups', array(
            'files' => $files,
            'dir' => $dir,
            'writable' => is_dir($dir) && is_writable($dir),
            'custom_dir' => trim((string) getenv('EDUSYS_BACKUP_DIR')) !== '',
            'database' => $db->database,
            'db_host' => $db->hostname,
            'token' => $this->form_token->field(),
            'message' => $this->session->flashdata('backup_msg'),
            'error' => $this->session->flashdata('backup_error'),
        ));
        $this->load->view('layout/footer');
    }

    public function create()
    {
        $this->require_privilege('can_add');
        $this->form_token->require_post();
        @set_time_limit(0);
        $name = 'edusys-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.sql.gz';
        $path = Data_dump::backup_dir() . '/' . $name;
        try {
            $this->data_dump->full($path);
        } catch (Exception $ex) {
            @unlink($path);
            $this->session->set_flashdata('backup_error', 'The backup failed: ' . $ex->getMessage());
            redirect('admin/backups');
        }
        $this->platform_audit->record('backup.create', 'Created the backup ' . $name . ' (' . round(filesize($path) / 1048576, 1) . ' MB)');
        $this->session->set_flashdata('backup_msg', 'Backup created: ' . $name . '. Download it and keep a copy off this server.');
        redirect('admin/backups');
    }

    public function download($name = '')
    {
        $this->require_privilege('can_view');
        $path = $this->path_or_404($name);
        $this->platform_audit->record('backup.download', 'Downloaded the backup ' . basename($path));
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/gzip');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: no-store');
        readfile($path);
        exit;
    }

    public function delete()
    {
        $this->require_privilege('can_delete');
        $this->form_token->require_post();
        $path = $this->path_or_404((string) $this->input->post('name'));
        @unlink($path);
        $this->platform_audit->record('backup.delete', 'Deleted the backup ' . basename($path));
        $this->session->set_flashdata('backup_msg', 'Deleted ' . basename($path) . '.');
        redirect('admin/backups');
    }

    private function path_or_404($name)
    {
        $name = (string) $name;
        $path = Data_dump::backup_dir() . '/' . $name;
        if (!preg_match(self::NAME_PATTERN, $name) || !is_file($path)) {
            show_404();
        }
        return $path;
    }

    private function require_privilege($action)
    {
        if (!$this->rbac->hasPrivilege(self::PERMISSION, $action)) {
            access_denied();
        }
    }
}
