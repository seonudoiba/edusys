<?php
/*
 * Entry point for /uploads/<path> (the root .htaccess sends every /uploads/ request here).
 * Files are stored per school (uploads/s<id>/); the Files controller serves the copy of the
 * school of the host. The requested path travels in a server variable, so file names with
 * characters the router doesn't accept (e.g. "!") still work.
 */
$path = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '', PHP_URL_PATH);
$_SERVER['EDUSYS_UPLOAD_REQUEST'] = rawurldecode((string) $path);
$_SERVER['REQUEST_URI'] = '/files/serve';
$_SERVER['QUERY_STRING'] = '';
$_GET = array();
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
require __DIR__ . '/index.php';
