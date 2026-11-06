<footer class="site-foot">
    <div class="wrap">
        <div class="foot-grid">
            <div>
                <a class="mark" href="<?php echo html_escape(base_url()); ?>"><?php $this->load->view('themes/platform/_logo'); ?></a>
                <p style="margin-top:12px">School management software by Hypercom Network Ltd, an information technology company established in Nigeria in 2000.</p>
            </div>
            <div>
                <h2>Website</h2>
                <ul>
                    <?php if (!empty($footer_menus)) {
                        foreach ($footer_menus as $m) {
                            $url = $m['ext_url'] ? front_menu_url($m['ext_url_link']) : front_page_url($m); ?>
                            <li><a href="<?php echo html_escape($url); ?>"><?php echo html_escape($m['menu']); ?></a></li>
                        <?php }
                    } ?>
                </ul>
            </div>
            <div>
                <h2>Hypercom Network Ltd</h2>
                <ul>
                    <li>14 Akinremi Street, Ikeja, Lagos</li>
                    <li><a href="tel:+2348033836341">+234 803 383 6341</a></li>
                    <li><a href="mailto:info@hypercom.net.ng">info@hypercom.net.ng</a></li>
                </ul>
            </div>
        </div>
        <div class="legal">
            <span>&copy; <?php echo date('Y'); ?> Hypercom Network Ltd. All rights reserved.</span>
            <a href="<?php echo html_escape(site_url('account')); ?>">Sign in</a>
        </div>
    </div>
</footer>
