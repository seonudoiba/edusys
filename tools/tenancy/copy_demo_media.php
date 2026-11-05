<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Copies the media files the default seed refers to (gallery, courses, video tutorials, download
// centre, certificate and ID card backgrounds) into uploads/_shared/, which every new school's
// upload folder starts from (helpers/upload_helper.php). Run after gen_default_seed.php.
//
// Usage: php tools/tenancy/copy_demo_media.php [source uploads dir, default: uploads]
$root = dirname(__DIR__, 2);
$source = rtrim(str_replace('\\', '/', $argv[1] ?? $root . '/uploads'), '/');
$target = $root . '/uploads/_shared';
define('BASEPATH', true);
if (!class_exists('Seeder')) { class Seeder {} }

// table => list of [directory column or fixed folder, file column]
$media = [
    'front_cms_media_gallery' => [['dir_path', 'img_name'], ['thumb_path', 'thumb_name']],
    'upload_contents' => [['dir_path', 'img_name'], ['thumb_path', 'thumb_name']],
    'video_tutorial' => [['dir_path', 'img_name'], ['thumb_path', 'thumb_name']],
    'online_courses' => [['=uploads/course/course_thumbnail/', 'course_thumbnail']],
    'certificates' => [['=uploads/certificate/', 'background_image']],
    'id_card' => [['=uploads/student_id_card/background/', 'background']],
    'staff_id_card' => [['=uploads/staff_id_card/background/', 'background']],
];

// Index of the source tree by file name, for files stored in another folder than the seed says.
$byName = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    $p = str_replace('\\', '/', $f->getPathname());
    if ($f->isFile() && !preg_match('~/uploads/(s\d+|_shared)/~', $p)) $byName[$f->getFilename()][] = $p;
}

$copied = $present = 0;
$missing = [];
foreach ($media as $table => $pairs) {
    $files = glob($root . "/application/seeds/default/[0-9][0-9][0-9]_$table.php");
    if (!$files) continue;
    require_once $files[0];
    $class = 'Seed_default_' . strtolower($table);
    $seed = new $class();
    foreach ($seed->rows() as $row) {
        $r = array_combine($seed->columns, $row);
        foreach ($pairs as [$dirCol, $fileCol]) {
            $file = (string) ($r[$fileCol] ?? '');
            if ($file === '' || strpos($file, '..') !== false) continue;
            $dir = $dirCol[0] === '=' ? substr($dirCol, 1) : (string) $r[$dirCol];
            $rel = preg_replace('~^(\./)?uploads/~', '', rtrim($dir, '/') . '/' . $file);
            $to = "$target/$rel";
            if (is_file($to)) { $present++; continue; }
            $from = is_file("$source/$rel") ? "$source/$rel" : ($byName[basename($file)][0] ?? null);
            if ($from === null) { $missing[] = "$table: uploads/$rel"; continue; }
            if (!is_dir(dirname($to))) mkdir(dirname($to), 0755, true);
            copy($from, $to);
            $copied++;
        }
    }
}
echo "copied $copied, already there $present, missing " . count($missing) . "\n";
foreach ($missing as $m) echo "  missing $m\n";
