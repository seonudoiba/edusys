<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Per-school upload folders (config/uploads.php). The code keeps the logical path
 * "uploads/<path>" (also what the database stores and what URLs use); on disk each school's
 * files are in uploads/s<school id>/<path>.
 */

if (!function_exists('upload_school_id')) {
    /** School whose files the request uses, or null (CLI without EDUSYS_SCHOOL_ID). */
    function upload_school_id()
    {
        return class_exists('Tenant', false) && Tenant::has() ? Tenant::id() : null;
    }
}

if (!function_exists('upload_root')) {
    /**
     * The current school's upload folder, relative like the legacy "./uploads/":
     * "./uploads/s<id>/". Without a school (e.g. a migration on the CLI) it is "./uploads/".
     * The folder is created, with its subfolders and default images, on first use.
     */
    function upload_root()
    {
        $id = upload_school_id();
        if ($id === null) {
            return './uploads/';
        }
        upload_init_school($id);
        return './uploads/s' . $id . '/';
    }
}

if (!function_exists('upload_abs_root')) {
    /** Absolute form of upload_root(): <project>/uploads/s<id>/ (for PDF libraries, mkdir). */
    function upload_abs_root()
    {
        return FCPATH . substr(upload_root(), 2);
    }
}

if (!function_exists('upload_fs_path')) {
    /**
     * Maps a logical upload path to the current school's file on disk.
     * Accepts "uploads/x", "./uploads/x", "/uploads/x" and absolute paths under the project's
     * uploads/ folder; paths that don't point into uploads/ are returned unchanged, as are
     * paths already in a school's folder.
     */
    function upload_fs_path($path)
    {
        $path = (string) $path;
        $norm = str_replace('\\', '/', $path);
        $root = str_replace('\\', '/', rtrim(FCPATH, '/\\')) . '/';
        if (strpos($norm, $root) === 0) {
            $norm = substr($norm, strlen($root));
        }
        $norm = preg_replace('~^(\./|/)+~', '', $norm);
        if (strpos($norm, 'uploads/') !== 0 || preg_match('~^uploads/(s\d+|_shared)/~', $norm)) {
            return $path;
        }
        return upload_root() . substr($norm, strlen('uploads/'));
    }
}

if (!function_exists('upload_init_school')) {
    /**
     * Creates uploads/s<id>/ with every upload folder and the default images from
     * uploads/_shared/. Idempotent; checked once per request.
     */
    function upload_init_school($school_id)
    {
        static $done = array();
        $school_id = (int) $school_id;
        if (isset($done[$school_id])) {
            return;
        }
        $dir = FCPATH . 'uploads/s' . $school_id . '/';
        $marker = $dir . '.initialized';
        if (!is_file($marker)) {
            $CI = &get_instance();
            $CI->config->load('uploads', true);
            $folders = (array) $CI->config->item('upload_folders', 'uploads');
            foreach ($folders as $folder) {
                if (!is_dir($dir . $folder)) {
                    @mkdir($dir . $folder, 0775, true);
                }
            }
            upload_copy_defaults(FCPATH . 'uploads/_shared/', $dir);
            if (!is_file($dir . 'index.html')) {
                @file_put_contents($dir . 'index.html', '');
            }
            @file_put_contents($marker, date('c'));
        }
        $done[$school_id] = true;
    }
}

if (!function_exists('upload_copy_defaults')) {
    /** Copies the default images (recursively) without overwriting a school's own files. */
    function upload_copy_defaults($from, $to)
    {
        if (!is_dir($from)) {
            return;
        }
        foreach (scandir($from) as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }
            if (is_dir($from . $name)) {
                if (!is_dir($to . $name)) {
                    @mkdir($to . $name, 0775, true);
                }
                upload_copy_defaults($from . $name . '/', $to . $name . '/');
            } elseif (!is_file($to . $name)) {
                @copy($from . $name, $to . $name);
            }
        }
    }
}

if (!function_exists('login_background_url')) {
    /**
     * The login pages' background image: the school's own (System Settings), or the EduSYS
     * default (backend/images/login_default.jpg) when none is set.
     */
    function login_background_url($file)
    {
        $file = trim((string) $file);
        if ($file === '') {
            $default = 'backend/images/login_default.jpg';
            return base_url($default) . '?v=' . (int) @filemtime(FCPATH . $default);
        }
        return base_url('uploads/school_content/login_image/' . $file);
    }
}
