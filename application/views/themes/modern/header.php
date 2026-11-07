<?php
/*
 * Header for every theme: top bar, logo, menu and account actions. Three layouts (classic, centred,
 * stacked) come from body classes; this markup stays the same.
 */
$e = function ($v) { return html_escape((string) $v); };
$st = isset($st) ? $st : array('topbar' => 1, 'header' => 'classic');
$course_on = $this->module_lib->hasModule('online_course') && $this->module_lib->hasActive('online_course');
$student_login = !empty($setting_data[0]['student_panel_login']);
$active_menu = isset($active_menu) ? $active_menu : '';
$menus = !empty($main_menus) && is_array($main_menus) ? $main_menus : array();
$logo = !empty($front_setting->logo) ? base_url($front_setting->logo) : '';
$menu_url = function ($m) { return $m['ext_url'] ? front_menu_url($m['ext_url_link']) : front_page_url($m); };
$target = function ($m) { return !empty($m['open_new_tab']) ? ' target="_blank" rel="noopener"' : ''; };
?>
<?php if (!empty($st['topbar'])) { ?>
    <div class="st-topbar">
        <div class="st-wrap st-topbar-inner">
            <ul class="st-topbar-contact">
                <?php if (!empty($school_setting->phone)) { ?><li><a href="tel:<?php echo $e(preg_replace('/[^0-9+]/', '', $school_setting->phone)); ?>"><i class="fa fa-phone" aria-hidden="true"></i> <?php echo $e($school_setting->phone); ?></a></li><?php } ?>
                <?php if (!empty($school_setting->email)) { ?><li><a href="mailto:<?php echo $e($school_setting->email); ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i> <?php echo $e($school_setting->email); ?></a></li><?php } ?>
            </ul>
            <ul class="st-social" aria-label="<?php echo $e($this->lang->line('follow_us')); ?>"><?php $this->load->view('themes/modern/social_media'); ?></ul>
        </div>
    </div>
<?php } ?>
<header class="st-header" id="st-header">
    <div class="st-wrap st-header-inner">
        <a class="st-logo" href="<?php echo $e(base_url()); ?>">
            <?php if ($logo) { ?><img src="<?php echo $e($logo); ?>" alt="<?php echo $e($school_setting->name); ?>"><?php } else { ?><span><?php echo $e($school_setting->name); ?></span><?php } ?>
        </a>

        <?php if ($st['header'] === 'stacked') { ?>
            <div class="st-header-contacts">
                <?php if (!empty($school_setting->phone)) { ?>
                    <div class="st-hc"><i class="fa fa-phone" aria-hidden="true"></i><div><small><?php echo $e($this->lang->line('call_us')); ?></small><a href="tel:<?php echo $e(preg_replace('/[^0-9+]/', '', $school_setting->phone)); ?>"><?php echo $e($school_setting->phone); ?></a></div></div>
                <?php } ?>
                <?php if (!empty($school_setting->email)) { ?>
                    <div class="st-hc"><i class="fa fa-envelope-o" aria-hidden="true"></i><div><small><?php echo $e($this->lang->line('email_us') ?: 'Email us'); ?></small><a href="mailto:<?php echo $e($school_setting->email); ?>"><?php echo $e($school_setting->email); ?></a></div></div>
                <?php } ?>
            </div>
        <?php } ?>

        <nav class="st-nav collapse" id="st-nav" aria-label="Main">
                <ul class="st-menu">
                    <?php if (empty($layout_type)) { ?>
                        <li class="active"><a href="<?php echo $e(site_url('online_admission')); ?>"><?php echo $e($this->lang->line('online_admission')); ?></a></li>
                    <?php } else {
                        foreach ($menus as $m) {
                            $has_sub = !empty($m['submenus']);
                            $on = $m['page_slug'] == $active_menu;
                            if (!$on && $has_sub) {
                                foreach ($m['submenus'] as $sm) { if ($sm['page_slug'] == $active_menu) { $on = true; } }
                            } ?>
                            <li class="<?php echo $on ? 'active' : ''; ?><?php echo $has_sub ? ' dropdown' : ''; ?>">
                                <?php if (!$has_sub) { ?>
                                    <a href="<?php echo $e($menu_url($m)); ?>"<?php echo $target($m); ?><?php echo $on ? ' aria-current="page"' : ''; ?>><?php echo $e($m['menu']); ?></a>
                                <?php } else { ?>
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $e($m['menu']); ?> <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($m['submenus'] as $sm) { ?>
                                            <li class="<?php echo $sm['page_slug'] == $active_menu ? 'active' : ''; ?>"><a href="<?php echo $e($menu_url($sm)); ?>"<?php echo $target($sm); ?>><?php echo $e($sm['menu']); ?></a></li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </li>
                        <?php }
                    } ?>
                </ul>
        </nav>

        <div class="st-actions">
            <?php if ($course_on && $active_menu === 'online_course') { ?>
                <?php if (!empty($currencies)) { ?>
                    <select class="languageselectpicker currency_list st-currency" name="currency" aria-label="Currency">
                        <?php foreach ($currencies as $c) { ?>
                            <option value="<?php echo (int) $c->id; ?>" <?php echo $c->id == $this->customlib->getSchoolCurrency() ? 'selected' : ''; ?>><?php echo $e($c->short_name . ' (' . $c->symbol . ')'); ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
                <a class="st-cart" href="<?php echo $e(base_url('cart')); ?>" aria-label="<?php echo $e($this->lang->line('go_to_cart')); ?>">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                    <?php $items = $this->cart->total_items(); if ($items) { ?><span class="st-badge"><?php echo (int) $items; ?></span><?php } ?>
                </a>
            <?php } ?>
            <?php if ($course_on) { ?>
                <?php if (!$this->session->userdata('student')) { ?>
                    <a class="st-btn st-btn-sm" href="#" onclick="openmodal(); return false;"><?php echo $e($this->lang->line('login')); ?></a>
                <?php } else { ?>
                    <a class="st-btn st-btn-sm" href="<?php echo $e(base_url('course/logout')); ?>"><?php echo $e($this->lang->line('logout')); ?></a>
                <?php } ?>
            <?php } elseif ($student_login) { ?>
                <a class="st-btn st-btn-sm" href="<?php echo $e(site_url('site/userlogin')); ?>"><?php echo $e($this->lang->line('login')); ?></a>
            <?php } ?>
            <button type="button" class="st-burger" data-toggle="collapse" data-target="#st-nav" aria-controls="st-nav" aria-expanded="false" aria-label="<?php echo $e($this->lang->line('toggle_navigation') ?: 'Menu'); ?>">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

</header>
