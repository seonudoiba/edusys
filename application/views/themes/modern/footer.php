<?php
$e = function ($v) { return html_escape((string) $v); };
$menus = !empty($footer_menus) && is_array($footer_menus) ? $footer_menus : array();
$menu_url = function ($m) { return $m['ext_url'] ? front_menu_url($m['ext_url_link']) : front_page_url($m); };
$cookie_consent = $this->customlib->cookie_consent();
$logo = !empty($front_setting->logo) ? base_url($front_setting->logo) : '';
?>
<footer class="st-footer">
    <div class="st-wrap st-footer-grid">
        <div class="st-footer-about">
            <a class="st-footer-logo" href="<?php echo $e(base_url()); ?>">
                <?php if ($logo) { ?><img src="<?php echo $e($logo); ?>" alt="<?php echo $e($school_setting->name); ?>"><?php } else { ?><?php echo $e($school_setting->name); ?><?php } ?>
            </a>
            <?php if (!empty($school_setting->address)) { ?><p><?php echo nl2br($e($school_setting->address)); ?></p><?php } ?>
            <ul class="st-social"><?php $this->load->view('themes/modern/social_media'); ?></ul>
        </div>
        <?php if ($menus) { ?>
            <div>
                <h2><?php echo $e($this->lang->line('links')); ?></h2>
                <ul class="st-footer-links">
                    <?php foreach ($menus as $m) { ?>
                        <li><a href="<?php echo $e($menu_url($m)); ?>"<?php echo !empty($m['open_new_tab']) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo $e($m['menu']); ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <div>
            <h2><?php echo $e($this->lang->line('contact')); ?></h2>
            <ul class="st-footer-contact">
                <?php if (!empty($school_setting->phone)) { ?><li><i class="fa fa-phone" aria-hidden="true"></i><a href="tel:<?php echo $e(preg_replace('/[^0-9+]/', '', $school_setting->phone)); ?>"><?php echo $e($school_setting->phone); ?></a></li><?php } ?>
                <?php if (!empty($school_setting->email)) { ?><li><i class="fa fa-envelope-o" aria-hidden="true"></i><a href="mailto:<?php echo $e($school_setting->email); ?>"><?php echo $e($school_setting->email); ?></a></li><?php } ?>
            </ul>
        </div>
        <div>
            <h2><?php echo $e($this->lang->line('feedback')); ?></h2>
            <p><a class="st-btn st-btn-ghost" href="<?php echo $e(site_url('page/complain')); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php echo $e($this->lang->line('complain')); ?></a></p>
        </div>
    </div>
    <div class="st-footer-bottom">
        <div class="st-wrap">
            <p><?php echo $front_setting->footer_text !== '' && $front_setting->footer_text !== null ? $e($front_setting->footer_text) : '&copy; ' . date('Y') . ' ' . $e($school_setting->name); ?></p>
        </div>
    </div>
</footer>
<a class="st-top" href="#" aria-label="Back to top"><i class="fa fa-angle-up" aria-hidden="true"></i></a>
<?php if (!empty($cookie_consent)) { ?>
    <div id="cookieConsent" class="st-cookies cookieConsent" role="region" aria-label="Cookies">
        <p><?php echo $cookie_consent; ?> <a href="<?php echo $e(base_url('page/cookie-policy')); ?>" target="_blank" rel="noopener"><?php echo $e($this->lang->line('read_more') ?: 'Read more'); ?></a></p>
        <button type="button" class="st-btn st-btn-sm" onclick="setsitecookies()"><?php echo $e($this->lang->line('ok') ?: 'OK'); ?></button>
    </div>
    <script>
        function setsitecookies() {
            $.post('<?php echo base_url('welcome/setsitecookies'); ?>', {}, function () { $('.cookieConsent').hide(); });
        }
        if (/(^| )sitecookies=/.test(document.cookie)) { document.getElementById('cookieConsent').style.display = 'none'; }
    </script>
<?php } ?>
<?php if ($this->module_lib->hasModule('online_course') && $this->module_lib->hasActive('online_course')) { ?>
    <script src="<?php echo base_url('backend/js/online_course.js'); ?>"></script>
<?php } ?>
