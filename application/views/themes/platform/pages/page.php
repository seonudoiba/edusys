<?php
/* A page from Front CMS: About, Laboratory, Contact, Privacy, Terms, or one the platform adds. */
$this->load->view('themes/platform/_icons');
$html = isset($page['description']) ? (string) $page['description'] : '';
if (!empty($page_form) && !empty($form)) {
    $form_html = '';
    if (validation_errors() != false) {
        $form_html .= '<div class="alert alert-danger" role="alert">' . validation_errors() . '</div>';
    }
    if ($this->session->flashdata('msg')) {
        $form_html .= '<div class="alert alert-success">' . $this->session->flashdata('msg') . '</div>';
        $this->session->unset_userdata('msg');
    }
    $form_html .= $this->form_builder->open_form(array('action' => '', 'id' => 'open', 'class' => 'theme-form'));
    $form_html .= "<input type='hidden' value='" . html_escape($form_name) . "' name='form_name'/>";
    $form_html .= $this->form_builder->build_form_horizontal($form, null);
    $form_html .= $this->form_builder->close_form();
    $html = str_replace('[form-builder:' . $form_name . ']', $form_html, $html);
}
$items = array();
foreach (isset($page['category_content']) && is_array($page['category_content']) ? $page['category_content'] : array() as $list) {
    if (is_array($list)) { $items = array_merge($items, $list); }
}

// Long documents (Privacy Policy, Terms of Service): sections written as <h2 id="..."> get a contents
// list, and the opening "meta" (last updated) and "summary" paragraphs move into the header card.
$toc = array();
if (preg_match_all('~<h2\s+id="([a-z0-9-]+)"[^>]*>(.*?)</h2>~is', $html, $m, PREG_SET_ORDER) && count($m) >= 4) {
    foreach ($m as $h) {
        $toc[] = array('id' => $h[1], 'text' => trim(strip_tags($h[2])));
    }
}
$doc_meta = $doc_summary = '';
if ($toc) {
    if (preg_match('~<p class="meta">(.*?)</p>\s*~is', $html, $mm)) {
        $doc_meta = $mm[1];
        $html = str_replace($mm[0], '', $html);
    }
    if (preg_match('~<p class="summary">(.*?)</p>\s*~is', $html, $mm)) {
        $doc_summary = $mm[1];
        $html = str_replace($mm[0], '', $html);
    }
}
$toc_list = function () use ($toc) {
    $out = '<ol>';
    foreach ($toc as $t) {
        $out .= '<li><a href="#' . html_escape($t['id']) . '">' . html_escape($t['text']) . '</a></li>';
    }
    return $out . '</ol>';
};
?>
<?php if (!empty($page['builder_html'])) { ?>
    <div class="wrap"><?php echo $html; ?></div>
<?php } elseif ($toc) { ?>
    <div class="doc-page">
        <div class="wrap">
            <header class="doc-card">
                <p class="eyebrow">Legal</p>
                <h1><?php echo html_escape($page['title']); ?></h1>
                <?php if ($doc_meta !== '') { ?><p class="doc-meta"><?php echo $doc_meta; ?></p><?php } ?>
                <?php if ($doc_summary !== '') { ?><p class="doc-summary"><?php echo $doc_summary; ?></p><?php } ?>
            </header>
            <div class="doc">
                <nav class="toc" aria-label="On this page"><p>On this page</p><?php echo $toc_list(); ?></nav>
                <div>
                    <details class="toc-mobile"><summary>On this page</summary><?php echo $toc_list(); ?></details>
                    <div class="prose doc-body"><?php echo $html; ?></div>
                    <aside class="doc-contact">
                        <h2>Questions about this document?</h2>
                        <p>Our team can explain how it applies to your school, your staff and your students' records.</p>
                        <div class="chips">
                            <a href="mailto:info@hypercom.net.ng"><?php echo pf_icon('mail'); ?>info@hypercom.net.ng</a>
                            <a href="tel:+2348033836341"><?php echo pf_icon('phone'); ?>+234 803 383 6341</a>
                            <a href="<?php echo html_escape(site_url('page/contact-us')); ?>"><?php echo pf_icon('support'); ?>Contact us</a>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <section class="hero hero-sm has-bg">
        <?php $this->load->view('themes/platform/_bg'); ?>
        <div class="wrap"><h1><?php echo html_escape($page['title']); ?></h1></div>
    </section>
    <div class="wrap">
        <div class="page-body">
            <div class="prose"><?php echo $html; ?></div>
            <?php if ($items) { ?>
                <div class="rows" id="postList">
                    <?php foreach ($items as $item) { ?>
                        <div><h3><a href="<?php echo html_escape(site_url($item['url'])); ?>"><?php echo html_escape($item['title']); ?></a></h3></div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>
