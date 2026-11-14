<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
/**
 * Logged-in smoke crawl of the admin panel for one school (TEST database only).
 *
 * Logs in as a staff user, then follows same-host links breadth-first with GET only,
 * skipping links whose path looks like it changes data. Each page is checked for a
 * database error page, an HTTP 5xx, and a tenancy violation.
 *
 *   php tools/tenancy/tests/crawl.php --base=http://xyz.edusys.test:8099 --resolve=127.0.0.1 \
 *       --user=superadmin@gmail.com --pass='School2#pw' [--max=800] [--out=report.tsv]
 *
 * Exit status 1 when any page failed.
 */

// --login=staff (default: site/login, admin panel) or --login=user (students and parents:
// gauthenticate/verfiy_userlogin, student/parent panel).
$opt = getopt('', array('base:', 'resolve::', 'user:', 'pass:', 'max::', 'out::', 'start::', 'login::'));
$mode = isset($opt['login']) && $opt['login'] === 'user' ? 'user' : 'staff';
foreach (array('base', 'user', 'pass') as $k) {
    if (empty($opt[$k])) { fwrite(STDERR, "Missing --$k\n"); exit(2); }
}
$base = rtrim($opt['base'], '/');
$parts = parse_url($base);
$host = $parts['host'];
$port = isset($parts['port']) ? $parts['port'] : 80;
$max = isset($opt['max']) ? (int) $opt['max'] : 800;
$jar = tempnam(sys_get_temp_dir(), 'crawl');
$resolve = isset($opt['resolve']) ? array("$host:$port:{$opt['resolve']}") : array();

// Paths containing these words are never requested (they may change data or end the session).
$unsafe = '/(delete|remove|logout|disable|enable|status|change|update|reset|restore|backup|approve|reject|cancel|revert|'
    . 'truncate|clear|unassign|assign|send|generate|import|install|upgrade|activate|deactivate|toggle|lock|unlock|'
    . 'default|markas|switch|save|add|create|edit|insert|upload|download|export|print|pdf|csv|excel|cron|migrate|'
    . 'seed|run|start|stop|close|pay|refund|collect|checkout|login|verify|resend|copy|move|promote|demote|'
    . 'leave|join|attempt|submit|result_publish|publish|unpublish|bulk|mail|sms|notify|call|chat|zoom|gmeet)/i';

function fetch($url, $post = null)
{
    global $jar, $resolve;
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 4,
        CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar, CURLOPT_TIMEOUT => 60, CURLOPT_RESOLVE => $resolve,
    ));
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $body = (string) curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $final = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return array($code, $final, $body);
}

function login()
{
    global $base, $opt, $mode;
    if ($mode === 'user') {
        list($code, , $body) = fetch("$base/gauthenticate/verfiy_userlogin", array('username' => $opt['user'], 'password' => $opt['pass']));
        if (strpos($body, '"status":2') === false) {
            return false;
        }
        // The student/parent panel first asks which class (student session) to open: pick the first.
        list(, , $page) = fetch("$base/user/user/choose");
        if (preg_match('/value="(\d+)"[^>]*name="clschg"/', $page, $m)) {
            fetch("$base/user/user/choose", array('clschg' => $m[1], 'submit' => 'submit'));
        }
        return true;
    }
    list($code, $landing) = fetch("$base/site/login", array('username' => $opt['user'], 'password' => $opt['pass']));
    return strpos($landing, '/admin/') !== false || preg_match('~/dashboard$~', (string) $landing) === 1;
}
if (!login()) {
    fwrite(STDERR, "Login failed for {$opt['user']} ($mode)\n");
    exit(2);
}
$login_path = $mode === 'user' ? '/site/userlogin' : '/site/login';

$start = isset($opt['start']) ? $opt['start'] : ($mode === 'user' ? '/user/user/dashboard' : '/admin/admin/dashboard');
$queue = array("$base$start");
$seen = array($queue[0] => true);
$results = array();
while ($queue && count($results) < $max) {
    $url = array_shift($queue);
    list($code, $final, $body) = fetch($url);
    if ((strpos($final, $login_path) !== false || strpos($final, '/site/login') !== false) && strpos($url, $login_path) === false) {
        // Logged out (or not allowed): log back in and note it.
        $results[] = array($url, $code, 'LOGGED_OUT', '');
        login();
        continue;
    }
    $problem = '';
    $detail = '';
    if (strpos($body, 'A Database Error Occurred') !== false) {
        $problem = 'DB_ERROR';
        if (preg_match('#<p>(Error Number:.*?)</p>.*?<p>(.*?)</p>#s', $body, $m)) $detail = strip_tags($m[1] . ' ' . $m[2]);
    } elseif (strpos($body, 'Tenant_violation') !== false || strpos($body, 'Raw SQL touches') !== false) {
        $problem = 'TENANCY';
        $detail = substr(strip_tags($body), 0, 300);
    } elseif ($code >= 500) {
        $problem = 'HTTP_' . $code;
        $detail = substr(trim(preg_replace('/\s+/', ' ', strip_tags($body))), 0, 300);
    }
    $results[] = array($url, $code, $problem ?: 'OK', preg_replace('/\s+/', ' ', $detail));

    if (preg_match_all('/href\s*=\s*["\']([^"\'#]+)["\']/i', $body, $links)) {
        foreach ($links[1] as $href) {
            $href = html_entity_decode(trim($href));
            if (preg_match('#^(javascript:|mailto:|tel:|data:)#i', $href)) continue;
            if (strpos($href, '//') === 0) $href = 'http:' . $href;
            if (!preg_match('#^https?://#i', $href)) $href = $base . '/' . ltrim($href, '/');
            $p = parse_url($href);
            if (!isset($p['host']) || strtolower($p['host']) !== strtolower($host)) continue;
            $path = isset($p['path']) ? $p['path'] : '/';
            if (preg_match('/\.(js|css|png|jpe?g|gif|svg|ico|pdf|zip|woff2?|ttf|eot|mp4|mp3|xlsx?|docx?|csv|txt|json)$/i', $path)) continue;
            if (preg_match($unsafe, $path)) continue;
            if (strpos($path, '/backend/') === 0 || strpos($path, '/uploads/') === 0) continue;
            $clean = $base . $path . (isset($p['query']) ? '?' . $p['query'] : '');
            if (!isset($seen[$clean])) {
                $seen[$clean] = true;
                $queue[] = $clean;
            }
        }
    }
}
@unlink($jar);

$failed = array_filter($results, function ($r) { return $r[2] !== 'OK' && $r[2] !== 'LOGGED_OUT'; });
if (!empty($opt['out'])) {
    $fh = fopen($opt['out'], 'w');
    foreach ($results as $r) fwrite($fh, implode("\t", $r) . "\n");
    fclose($fh);
}
foreach ($failed as $r) echo "$r[2]\t$r[1]\t" . substr($r[0], strlen($base)) . "\t$r[3]\n";
$counts = array_count_values(array_column($results, 2));
ksort($counts);
fwrite(STDERR, count($results) . ' pages: ' . implode(', ', array_map(function ($k, $v) { return "$k=$v"; }, array_keys($counts), $counts)) . "\n");
exit($failed ? 1 : 0);
