<?php
/*
 * School website layout for every theme (see config/site_themes.php and libraries/Site_theme.php).
 * $st: the design options in effect. Content views this theme doesn't have (admission forms,
 * online courses, exam results) come from material_pink and are styled by site.css.
 */
$e = function ($v) { return html_escape((string) $v); };
$pink = base_url('backend/themes/material_pink/');
$css = 'backend/themes/modern/site.css';
$rtl = !empty($front_setting->is_active_rtl);
$is_home = !empty($page['is_homepage']);
$school_name = isset($school_setting->name) ? $school_setting->name : '';
$title = !empty($page['meta_title']) ? $page['meta_title'] : (isset($page['title']) ? $page['title'] : '');
$full_title = $is_home || $title === '' ? $school_name : $title . ' | ' . $school_name;
$notices = isset($banner_notices) && is_array($banner_notices) ? $banner_notices : array();
$sidebar_options = json_decode((string) $front_setting->sidebar_options, true) ?: array();
$show_ticker = in_array('news', $sidebar_options, true) && $notices;
$builder = !empty($page['builder_html']);
$title_band = !$is_home && !$builder && !empty($st['page_titles']) && $title !== '' && empty($hide_title_band);
?><!DOCTYPE html>
<html dir="<?php echo $rtl ? 'rtl' : 'ltr'; ?>" lang="<?php echo $rtl ? 'ar' : 'en'; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $e($full_title); ?></title>
    <?php if (!empty($page['meta_description'])) { ?><meta name="description" content="<?php echo $e($page['meta_description']); ?>"><?php } ?>
    <?php if (!empty($page['meta_keyword'])) { ?><meta name="keywords" content="<?php echo $e($page['meta_keyword']); ?>"><?php } ?>
    <meta property="og:title" content="<?php echo $e($full_title); ?>">
    <meta name="theme-color" content="<?php echo $e($st['primary']); ?>">
    <?php if (!empty($front_setting->fav_icon)) { ?><link rel="icon" href="<?php echo $e(base_url($front_setting->fav_icon)); ?>"><?php } ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo $e($st_fonts_url); ?>">
    <link rel="stylesheet" href="<?php echo $pink; ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $pink; ?>css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $pink; ?>css/owl.carousel.css">
    <link rel="stylesheet" href="<?php echo $pink; ?>datepicker/bootstrap-datepicker3.css">
    <link rel="stylesheet" href="<?php echo base_url('backend/dist/css/dropify.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('backend/dist/css/bootstrap-select.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo $pink; ?>css/online-course.css">
    <?php if ($rtl) { ?>
        <link rel="stylesheet" href="<?php echo $pink; ?>rtl/bootstrap-rtl.min.css">
    <?php } ?>
    <link rel="stylesheet" href="<?php echo base_url($css) . '?v=' . (int) @filemtime(FCPATH . $css); ?>">
    <style><?php echo $st_css; ?></style>
    <script src="<?php echo base_url('backend/custom/jquery.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('backend/toast-alert/toastr.css'); ?>">
    <script src="<?php echo base_url('backend/toast-alert/toastr.js'); ?>"></script>
    <script src="<?php echo base_url('backend/dist/js/moment.min.js'); ?>"></script>
    <script src="<?php echo base_url('backend/dist/js/dropify.min.js'); ?>"></script>
    <script src="<?php echo base_url('backend/dist/js/bootstrap-select.min.js'); ?>"></script>
    <script>var base_url = "<?php echo base_url(); ?>";</script>
    <?php echo $front_setting->google_analytics; ?>
</head>
<body class="st <?php echo $e($st_classes); ?><?php echo $is_home ? ' st-home' : ''; ?>">
    <script>document.body.classList.add('st-js');</script>
    <a class="st-skip" href="#st-main">Skip to content</a>
    <?php echo $header; ?>

    <?php if ($show_ticker) { ?>
        <div class="st-ticker" role="region" aria-label="<?php echo $e($this->lang->line('latest_news')); ?>">
            <div class="st-wrap st-ticker-inner">
                <span class="st-ticker-label"><?php echo $e($this->lang->line('latest_news')); ?></span>
                <div class="st-ticker-track"><div class="st-ticker-move">
                    <?php foreach ($notices as $n) { ?>
                        <a href="<?php echo $e(site_url('read/' . $n['slug'])); ?>"><span><?php echo $e(date('d M', strtotime($n['date']))); ?></span><?php echo $e($n['title']); ?></a>
                    <?php } ?>
                </div></div>
            </div>
        </div>
    <?php } ?>

    <?php echo $slider; ?>

    <?php if ($title_band) { ?>
        <div class="st-title-band">
            <div class="st-wrap">
                <nav class="st-crumbs" aria-label="Breadcrumb"><a href="<?php echo $e(base_url()); ?>"><?php echo $e($this->lang->line('home') ?: 'Home'); ?></a><span aria-hidden="true">/</span><span><?php echo $e($page['title']); ?></span></nav>
                <h1><?php echo $e($page['title']); ?></h1>
            </div>
        </div>
    <?php } ?>

    <main id="st-main" class="st-main<?php echo $builder ? ' st-main-builder' : ''; ?>">
        <div class="st-wrap">
            <?php if (!empty($page_side_bar)) { ?>
                <div class="st-with-sidebar">
                    <div class="st-content"><?php echo $content; ?></div>
                    <aside class="st-sidebar">
                        <?php if (in_array('news', $sidebar_options, true) && $notices) { ?>
                            <div class="st-side-box">
                                <h2><?php echo $e($this->lang->line('latest_news')); ?></h2>
                                <ul class="st-side-news">
                                    <?php foreach ($notices as $n) { ?>
                                        <li><a href="<?php echo $e(site_url('read/' . $n['slug'])); ?>"><span class="st-date"><b><?php echo date('d', strtotime($n['date'])); ?></b><?php echo date('M', strtotime($n['date'])); ?></span><?php echo $e($n['title']); ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>
                    </aside>
                </div>
            <?php } else { ?>
                <div class="st-content"><?php echo $content; ?></div>
            <?php } ?>
        </div>
    </main>

    <?php if ($is_home && !$builder) { $this->load->view('themes/modern/_home_highlights', array('notices' => $notices)); } ?>

    <?php echo $footer; ?>
    <?php $this->load->view('themes/modern/_account'); ?>

    <script src="<?php echo $pink; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo base_url('backend/custom/modal_cancel.js'); ?>?v=<?php echo (int) @filemtime(FCPATH . 'backend/custom/modal_cancel.js'); ?>"></script>
    <script src="<?php echo $pink; ?>js/owl.carousel.min.js"></script>
    <script src="<?php echo $pink; ?>js/jquery.waypoints.min.js"></script>
    <script src="<?php echo $pink; ?>js/jquery.counterup.min.js"></script>
    <script src="<?php echo $pink; ?>js/ss-lightbox.js"></script>
    <script src="<?php echo $pink; ?>datepicker/bootstrap-datepicker.min.js"></script>
    <script>
        (function () {
            // Header shadow once the page scrolls; back-to-top button.
            var body = document.body, top = document.querySelector('.st-top');
            function onScroll() {
                body.classList.toggle('st-scrolled', window.scrollY > 10);
                if (top) { top.classList.toggle('show', window.scrollY > 500); }
            }
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
            if (top) { top.addEventListener('click', function (e) { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); }); }
            $(function () { $('.languageselectpicker').selectpicker && $('.languageselectpicker').selectpicker(); });
            // Sections rise in as they come into view (shown at once without IntersectionObserver).
            [].forEach.call(document.querySelectorAll('.pw-section > .pw-inner > .pw-widget, .pw-columns > div, .pw-stat, .pw-grid > *'), function (el, i) { if (!el.closest('.st-reveal')) { el.classList.add('st-reveal'); } });
            var items = document.querySelectorAll('.st-reveal');
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('st-in'); io.unobserve(en.target); } });
                }, { rootMargin: '0px 0px -8% 0px' });
                [].forEach.call(items, function (el) { io.observe(el); });
            } else {
                [].forEach.call(items, function (el) { el.classList.add('st-in'); });
            }
        })();
    </script>
<script>window.date_format = window.date_format || <?php echo json_encode(strtr($this->customlib->getSchoolDateFormat(), array("d" => "dd", "m" => "mm", "Y" => "yyyy", "M" => "M"))); ?>;</script>
<script src="<?php echo base_url(); ?>backend/custom/form_rules.js?v=2"></script>
</body>
</html>
