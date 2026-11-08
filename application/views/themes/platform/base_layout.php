<?php
$title = !empty($page['meta_title']) ? $page['meta_title'] : (!empty($page['title']) ? $page['title'] : '');
$is_home = !empty($page['is_homepage']);
$description = !empty($page['meta_description']) ? $page['meta_description'] : 'EduSYS is school management software for Nigerian schools: admissions, fees, attendance, examinations, staff, parent communication and your own school website.';
$css = 'backend/themes/platform/site.css';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $is_home || $title === '' ? 'EduSYS: school management software by HyperCom' : html_escape($title) . ' | EduSYS'; ?></title>
    <meta name="description" content="<?php echo html_escape($description); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Plus+Jakarta+Sans:wght@600;700;800&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url($css) . '?v=' . (int) @filemtime(FCPATH . $css); ?>">
    <link rel="icon" type="image/png" href="<?php echo base_url('backend/themes/platform/edusys-icon.png'); ?>">
    <link rel="apple-touch-icon" href="<?php echo base_url('backend/themes/platform/edusys-icon.png'); ?>">
    <meta name="theme-color" content="#0b2d64">
</head>
<body>
    <a class="skip" href="#main">Skip to content</a>
    <?php echo $header; ?>
    <main id="main"><?php echo $content; ?></main>
    <?php echo $footer; ?>
</body>
</html>
