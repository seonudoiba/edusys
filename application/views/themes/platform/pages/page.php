<?php
/* A page from Front CMS: About, Laboratory, Contact, Privacy, Terms, or one the platform adds. */
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
?>
<?php if (!empty($page['builder_html'])) { ?>
    <div class="wrap"><?php echo $html; ?></div>
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
