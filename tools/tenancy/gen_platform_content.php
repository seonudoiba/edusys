<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
/**
 * Generates application/seeds/platform_content/ (content.json and the logos) from the platform school
 * of a database that holds the real website content. Only public website content goes in: identity,
 * pages, menus and logos, never students, staff, subscriptions or passwords.
 *
 *   php tools/tenancy/gen_platform_content.php [db name, default edusys] [host] [port] [user] [password]
 *
 * Applied on a server with: php index.php seed content
 */
$db = new mysqli($argv[3] ?? '127.0.0.1', $argv[5] ?? 'root', $argv[6] ?? '', $argv[1] ?? 'edusys', (int) ($argv[4] ?? 3306));
$db->set_charset('utf8mb4');
$root = dirname(__DIR__, 2);
$out = $root . '/application/seeds/platform_content';
$sid = (int) $db->query('SELECT id FROM school WHERE is_platform = 1 LIMIT 1')->fetch_row()[0];
$one = function ($sql) use ($db) { return $db->query($sql)->fetch_assoc(); };

/** The legal pages were pasted as Markdown: only the formatting changes, never the wording. */
function markdown_to_html($text)
{
    $inline = function ($s) {
        $s = preg_replace('/\[([^\]]+)\]\(((?:https?:\/\/|mailto:)[^)\s]+)\)/', '<a href="$2">$1</a>', $s);
        $s = preg_replace('/^\[(?=[^\]]*$)/', '', str_replace('[edusys.ng (the', 'edusys.ng (the', $s));
        return preg_replace('/\*([^*\n]+)\*/', '<b>$1</b>', $s);
    };
    $html = '';
    $list = false;
    foreach (preg_split('/<br\s*\/?>/i', str_replace('&nbsp;', ' ', $text)) as $line) {
        $line = trim($line);
        $is_item = strncmp($line, '- ', 2) === 0;
        if ($list && !$is_item) {
            $html .= "</ul>\n";
            $list = false;
        }
        if ($line === '' || $line === '---') {
            continue;
        }
        if ($is_item) {
            $html .= ($list ? '' : "<ul>\n") . '<li>' . $inline(substr($line, 2)) . "</li>\n";
            $list = true;
        } elseif (preg_match('/^#{1,6}\s*\*?(.+?)\*?$/', $line, $m)) {
            $html .= '<h3>' . $inline($m[1]) . "</h3>\n";
        } elseif (preg_match('/^\*([^*]+)\*$/', $line, $m)) {
            $html .= '<h4>' . $inline($m[1]) . "</h4>\n";
        } else {
            $html .= '<p>' . $inline($line) . "</p>\n";
        }
    }
    return $html . ($list ? "</ul>\n" : '');
}

// ---------------------------------------------------------------- pages
$pages = array();
foreach (array('about-us', 'edusys-solar-computer-lab', 'privacy-policy', 'terms-of-service', 'contact-us') as $slug) {
    $p = $one("SELECT title, type, url, slug, meta_title, meta_description, meta_keyword, description FROM front_cms_pages WHERE school_id = $sid AND slug = '$slug'");
    if (!$p) {
        exit("Page $slug not found\n");
    }
    if (in_array($slug, array('privacy-policy', 'terms-of-service'), true)) {
        $p['description'] = markdown_to_html($p['description']);
    }
    if ($slug === 'contact-us') {
        // The demo map (Times Square) and the icons hotlinked from another company's demo site.
        $p['description'] = preg_replace('~<iframe\b[^>]*maps\.google[^>]*>.*?</iframe>~is',
            '<iframe src="https://www.google.com/maps?q=' . rawurlencode('14 Akinremi Street, Ikeja, Lagos, Nigeria') . '&amp;output=embed" width="100%" height="340" style="border:0" loading="lazy" title="Map"></iframe>', $p['description']);
        $p['description'] = preg_replace('~<img\b[^>]*demo\.smart-school\.in[^>]*>~i', '', $p['description']);
        // The theme already wraps pages in a container; a second one pushes the page too wide.
        $p['description'] = str_replace('<div class="container">', '<div>', $p['description']);
    }
    if (preg_match('~<script\b|edusys_ng\.test~i', $p['description'])) {
        exit("Page $slug has a script or a local address: check it\n");
    }
    $pages[] = $p;
}

// ---------------------------------------------------------------- files
if (!is_dir($out . '/files')) {
    mkdir($out . '/files', 0755, true);
}
array_map('unlink', glob($out . '/files/*') ?: array());
$s = $one("SELECT image, admin_logo, admin_small_logo, app_logo FROM sch_settings WHERE school_id = $sid");
$f = $one("SELECT logo, fav_icon FROM front_cms_settings WHERE school_id = $sid");
$files = array();
$add = function ($relative, $name) use ($root, $sid, $out, &$files) {
    $source = $root . '/uploads/s' . $sid . '/' . ltrim(preg_replace('~^\./?uploads/~', '', $relative), '/');
    if (!is_file($source)) {
        echo "  skipped (file not found): $relative\n";
        return false;
    }
    $safe = $name . '.' . strtolower(pathinfo($source, PATHINFO_EXTENSION));
    copy($source, $out . '/files/' . $safe);
    $files[$safe] = dirname(preg_replace('~^\./?uploads/~', '', $relative)) . '/' . $safe;
    return $safe;
};
$identity = array(
    'name' => 'Edusys',
    'phone' => '',
    'email' => '',
    'address' => '',
);
$row = $one("SELECT phone, email, address FROM sch_settings WHERE school_id = $sid");
$identity = array_merge($identity, $row);
$front = array('footer_text' => $one("SELECT footer_text FROM front_cms_settings WHERE school_id = $sid")['footer_text']);
foreach (array('image' => 'school_content/logo/', 'admin_logo' => 'school_content/admin_logo/', 'admin_small_logo' => 'school_content/admin_small_logo/', 'app_logo' => 'school_content/logo/') as $col => $dir) {
    if (!empty($s[$col]) && ($safe = $add($dir . $s[$col], 'edusys-' . str_replace('_', '-', $col)))) {
        $identity[$col] = $safe;
    }
}
foreach (array('logo' => 'edusys-front-logo', 'fav_icon' => 'edusys-favicon') as $col => $name) {
    if (!empty($f[$col]) && ($safe = $add($f[$col], $name))) {
        $front[$col] = './uploads/school_content/logo/' . $safe;
    }
}

// ---------------------------------------------------------------- menus (page = a slug, or link = an address)
$menus = array(
    'main-menu' => array(
        array('menu' => 'Home', 'page' => 'home', 'slug' => 'home'),
        array('menu' => 'Plan', 'link' => 'plans', 'slug' => 'plans'),
        array('menu' => 'About Us', 'page' => 'about-us', 'slug' => 'about-us'),
        array('menu' => 'EduSyS Laboratory', 'page' => 'edusys-solar-computer-lab', 'slug' => 'edusys-laboratory'),
        array('menu' => 'Contact', 'page' => 'contact-us', 'slug' => 'contact'),
    ),
    'bottom-menu' => array(
        array('menu' => 'Home', 'page' => 'home', 'slug' => 'home-1'),
        array('menu' => 'About Us', 'page' => 'about-us', 'slug' => 'about-us-1'),
        array('menu' => 'Contact Us', 'page' => 'contact-us', 'slug' => 'contact-us'),
        array('menu' => 'Privacy Policy', 'page' => 'privacy-policy', 'slug' => 'privacy-policy'),
        array('menu' => 'Terms of Service', 'page' => 'terms-of-service', 'slug' => 'terms-of-service'),
    ),
);

$json = json_encode(array('version' => 1, 'identity' => $identity, 'front' => $front, 'pages' => $pages, 'menus' => $menus, 'files' => $files),
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
file_put_contents($out . '/content.json', $json);
printf("content.json: %d KB, %d pages, %d files\n", strlen($json) / 1024, count($pages), count($files));
print_r($identity);
print_r($front);
print_r($files);
