<?php
$this->load->view('themes/platform/_icons');
$legal_slugs = array('privacy-policy', 'terms-of-service');
$links = array();
foreach (!empty($footer_menus) ? $footer_menus : array() as $m) {
    $slug = isset($m['page_slug']) ? $m['page_slug'] : '';
    if (in_array($slug, $legal_slugs, true)) {
        continue; // shown under Legal
    }
    $links[] = array('label' => $m['menu'], 'url' => $m['ext_url'] ? front_menu_url($m['ext_url_link']) : front_page_url($m));
}
?>
<footer class="site-foot">
    <div class="wrap">
        <div class="foot-grid">
            <div class="foot-brand">
                <a class="mark" href="<?php echo html_escape(base_url()); ?>"><?php $this->load->view('themes/platform/_logo'); ?></a>
                <p>HyperCom is an integrated information technology company founded by Dr Joshua A. Denila. We work with businesses of every size, from small enterprises to large corporations, on telecoms, internet technology and green energy.</p>
                <p>EduSYS is our school management platform. A school's administration is the same work every term: admissions, fees, attendance, results, keeping parents informed. EduSYS puts that work in one place and automates what repeats, so that a school can give its attention to teaching.</p>
                <p>Each school runs on its own web address, with its own data, staff and branding.</p>
            </div>
            <div>
                <h2>EduSYS</h2>
                <ul>
                    <?php foreach ($links as $l) { ?>
                        <li><a href="<?php echo html_escape($l['url']); ?>"><?php echo html_escape($l['label']); ?></a></li>
                    <?php } ?>
                    <li><a href="<?php echo html_escape(site_url('plans/quote')); ?>">Request a quote</a></li>
                    <li><a href="<?php echo html_escape(site_url('subscription/add')); ?>">Register your school</a></li>
                    <li><a href="<?php echo html_escape(site_url('account')); ?>">School owner sign in</a></li>
                </ul>
            </div>
            <div>
                <h2>Legal</h2>
                <ul>
                    <li><a href="<?php echo html_escape(site_url('page/privacy-policy')); ?>">Privacy Policy</a></li>
                    <li><a href="<?php echo html_escape(site_url('page/terms-of-service')); ?>">Terms of Service</a></li>
                </ul>
            </div>
            <div>
                <h2>Where to find us</h2>
                <ul class="foot-contact">
                    <li><?php echo pf_icon('pin'); ?><span>14 Akinremi Street, Ikeja, Lagos</span></li>
                    <li><?php echo pf_icon('pin'); ?><span>1 Melcut Road, Okapa, Udu, Warri</span></li>
                    <li><?php echo pf_icon('phone'); ?><a href="tel:+2348033836341">+234 803 383 6341</a></li>
                    <li><?php echo pf_icon('mail'); ?><a href="mailto:info@hypercom.net.ng">info@hypercom.net.ng</a></li>
                </ul>
            </div>
        </div>
        <div class="legal">
            <span>&copy; <?php echo date('Y'); ?> HyperCom. All rights reserved.</span>
            <nav aria-label="Legal">
                <a href="<?php echo html_escape(site_url('page/privacy-policy')); ?>">Privacy Policy</a>
                <a href="<?php echo html_escape(site_url('page/terms-of-service')); ?>">Terms of Service</a>
                <a href="<?php echo html_escape(site_url('account')); ?>">Sign in</a>
            </nav>
        </div>
    </div>
</footer>
