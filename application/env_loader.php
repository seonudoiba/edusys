<?php
/**
 * Loads settings from a .env file for LOCAL development (index.php calls it before anything else).
 *
 * - File: <project>/.env, or the path in EDUSYS_ENV_FILE. No file, nothing happens.
 * - Only fills in variables that are NOT already set: Apache SetEnv, the systemd EnvironmentFile
 *   (/etc/edusys/edusys.env on the server) and the shell always win. Production never relies on
 *   this file (deploy/release.sh deletes any .env from a release).
 * - Format: KEY=value per line; "#" starts a comment line; "export " is allowed; values may be
 *   wrapped in single or double quotes (no variable expansion); an unquoted value ends at " #".
 *   Keys are upper case letters, digits and "_". KEY= sets an empty value.
 *
 * The file sits in the web root: the root .htaccess refuses every dotfile (403/404), and it is
 * ignored by git. Never put production secrets in it.
 */
if (!function_exists('edusys_load_env')) {
    function edusys_load_env($file)
    {
        if (!is_file($file) || !is_readable($file)) {
            return 0;
        }
        $loaded = 0;
        foreach (file($file, FILE_IGNORE_NEW_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            if (strncmp($line, 'export ', 7) === 0) {
                $line = ltrim(substr($line, 7));
            }
            if (!preg_match('/^([A-Z_][A-Z0-9_]*)\s*=\s*(.*)$/', $line, $m)) {
                continue; // not a setting: ignored rather than fatal
            }
            list(, $key, $value) = $m;
            if (getenv($key) !== false) {
                continue; // already set by the server or the shell
            }
            $quote = $value !== '' ? $value[0] : '';
            if (($quote === '"' || $quote === "'") && strlen($value) >= 2 && substr($value, -1) === $quote) {
                $value = substr($value, 1, -1);
                if ($quote === '"') {
                    $value = strtr($value, array('\\n' => "\n", '\\"' => '"', '\\\\' => '\\'));
                }
            } else {
                $value = trim(preg_replace('/\s+#.*$/', '', $value));
            }
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $loaded++;
        }
        return $loaded;
    }
}

edusys_load_env(getenv('EDUSYS_ENV_FILE') ?: __DIR__ . '/../.env');
