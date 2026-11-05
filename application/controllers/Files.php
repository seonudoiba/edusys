<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Serves /uploads/<path> for the school of the host (phase 9, config/uploads.php).
 *
 * Requests reach it through file.php (.htaccess sends every /uploads/ request there), which
 * passes the requested path in $_SERVER['EDUSYS_UPLOAD_REQUEST']. The file is looked up in
 * the current school's folder uploads/s<id>/ only, so one school's host never serves another
 * school's files.
 *
 * - Private folders (documents, receipts, attachments) need a logged-in staff member, student
 *   or parent of this school; otherwise 404.
 * - Images, PDF and media are shown inline; every other type is sent as a download, so an
 *   uploaded HTML or SVG file can't run scripts on the school's site.
 */
class Files extends CI_Controller
{
    public function serve()
    {
        $requested = isset($_SERVER['EDUSYS_UPLOAD_REQUEST']) ? (string) $_SERVER['EDUSYS_UPLOAD_REQUEST'] : '';
        $this->config->load('uploads', true);
        $relative = $this->relative_path($requested);
        if ($relative === null || !Tenant::has()) {
            return $this->not_found();
        }
        if (substr($relative, -1) === '/') {
            // A folder: the page printed an empty image setting (no logo uploaded yet, ...).
            $default = $this->default_image($relative);
            return $default ? $this->send($default, false, true) : $this->not_found();
        }
        $private = false;
        foreach ((array) $this->config->item('upload_private_prefixes', 'uploads') as $prefix) {
            if (strpos($relative, $prefix) === 0) {
                $private = true;
                break;
            }
        }
        // The session is bound to its school (MY_Session), so a login on another school's host doesn't count.
        $logged_in = $this->session->has_userdata('admin') || $this->session->has_userdata('student');
        session_write_close(); // don't hold the session lock while streaming (pages load many images)
        if ($private && !$logged_in) {
            return $this->not_found();
        }

        $file = upload_abs_root() . $relative;
        if (!is_file($file) && !$private) {
            $shared = FCPATH . 'uploads/_shared/' . $relative; // a default image added after the school's folder was created
            $file = is_file($shared) ? $shared : $file;
        }
        if (!is_file($file) && !$private && preg_match('/\.(jpe?g|png|gif|webp|bmp)$/i', $relative)) {
            $default = $this->default_image($relative);
            if ($default) {
                return $this->send($default, false, true);
            }
        }
        if (!is_file($file)) {
            return $this->not_found();
        }
        $this->send($file, $private);
    }

    /** The default image (uploads/_shared/...) for a path in one of the folders that have one. */
    private function default_image($relative)
    {
        $best = null;
        foreach ((array) $this->config->item('upload_default_images', 'uploads') as $folder => $image) {
            if (strpos($relative, $folder) === 0 && ($best === null || strlen($folder) > strlen($best[0]))) {
                $best = array($folder, $image);
            }
        }
        if ($best === null) {
            return null;
        }
        $path = FCPATH . 'uploads/_shared/' . $best[1];
        return is_file($path) ? $path : null;
    }

    /** "uploads/<path>" from the request path, or null when it isn't a plain file path inside uploads/. */
    private function relative_path($requested)
    {
        $pos = strpos($requested, '/uploads/');
        if ($pos === false) {
            return null;
        }
        $relative = substr($requested, $pos + strlen('/uploads/'));
        if ($relative === '' || strpos($relative, "\0") !== false || strpos($relative, '\\') !== false) {
            return null;
        }
        foreach (explode('/', rtrim($relative, '/')) as $segment) {
            if ($segment === '' || $segment === '..' || $segment[0] === '.') {
                return null;
            }
        }
        if (preg_match('~^(s\d+|_shared)/~', $relative)) {
            return null; // the storage layout itself is never addressable
        }
        return $relative;
    }

    private function send($file, $private, $is_default = false)
    {
        $inline_types = (array) $this->config->item('upload_inline_types', 'uploads');
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $inline = isset($inline_types[$ext]);
        $type = $inline ? $inline_types[$ext] : 'application/octet-stream';
        $size = filesize($file);
        $mtime = filemtime($file);
        $etag = '"' . md5($mtime . '-' . $size) . '"';

        header_remove('Set-Cookie');
        header('X-Content-Type-Options: nosniff');
        header("Content-Security-Policy: default-src 'none'; img-src 'self'; media-src 'self'; style-src 'unsafe-inline'; sandbox");
        header('Cache-Control: ' . ($private ? 'private, no-store' : ($is_default ? 'public, max-age=300' : 'public, max-age=86400')));
        header('ETag: ' . $etag);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
        if (!$private && isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
            http_response_code(304);
            exit;
        }
        $name = str_replace(array('"', "\r", "\n"), '', substr(basename($file), strpos(basename($file), '!') !== false ? strpos(basename($file), '!') + 1 : 0));
        header('Content-Type: ' . $type);
        header('Content-Length: ' . $size);
        header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . $name . '"');
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        readfile($file);
        exit;
    }

    private function not_found()
    {
        header_remove('Set-Cookie');
        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: no-store');
        echo 'Not found';
        exit;
    }
}
