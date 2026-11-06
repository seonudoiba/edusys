<header class="site-head">
    <div class="wrap">
        <a class="mark" href="<?php echo html_escape(base_url()); ?>"><?php $this->load->view('themes/platform/_logo'); ?></a>
        <details class="nav-toggle" onToggle="document.getElementById('nav').classList.toggle('open', this.open)"><summary>Menu</summary></details>
        <nav class="nav" id="nav" aria-label="Main">
            <?php if (!empty($main_menus)) {
                foreach ($main_menus as $m) {
                    if (!empty($m['is_homepage'])) { continue; } // the name above goes home
                    $url = $m['ext_url'] ? front_menu_url($m['ext_url_link']) : front_page_url($m);
                    $on = isset($active_menu) && ($active_menu === $m['page_slug'] || (!empty($m['ext_url_link']) && $active_menu === $m['ext_url_link'])); ?>
                    <a href="<?php echo html_escape($url); ?>"<?php echo $on ? ' class="on" aria-current="page"' : ''; ?>><?php echo html_escape($m['menu']); ?></a>
                <?php }
            } ?>
            <a class="end" href="<?php echo html_escape(site_url('account')); ?>">Sign in</a>
            <a class="btn btn-sm" href="<?php echo html_escape(site_url('plans')); ?>">Register your school</a>
        </nav>
    </div>
</header>
