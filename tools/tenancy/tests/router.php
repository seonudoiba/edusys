<?php
// Router for PHP's built-in server, doing what the root .htaccess does on Apache:
//   php -S 127.0.0.1:8099 tools/tenancy/tests/router.php   (from the project directory)
$root = dirname(__DIR__, 3);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('~^/uploads/(s[0-9]+|_shared)(/|$)~', $path)) {
    http_response_code(403);
    exit('Forbidden');
}
chdir($root);
if (strpos($path, '/uploads/') === 0) {
    require $root . '/file.php';
    return true;
}
if ($path !== '/' && is_file($root . $path)) {
    return false; // static file (css, js, images outside uploads/)
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
require $root . '/index.php';
return true;
