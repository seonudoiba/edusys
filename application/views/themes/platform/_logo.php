<?php /* The EduSYS logo, shipped with the theme (backend/themes/platform/edusys-logo.png). */
$logo = 'backend/themes/platform/edusys-logo.png';
?><img src="<?php echo html_escape(base_url($logo) . '?v=' . (int) @filemtime(FCPATH . $logo)); ?>" alt="EduSYS" width="406" height="120">
