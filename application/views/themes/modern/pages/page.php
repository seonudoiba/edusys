<?php
/* A Front CMS page: its content (with a form if it has one), then its list of news, events or galleries. */
$e = function ($v) { return html_escape((string) $v); };
$html = isset($page['description']) ? (string) $page['description'] : '';
if (!empty($page_form) && !empty($form)) {
    $form_html = '';
    if (validation_errors() != false) {
        $form_html .= '<div class="alert alert-danger" role="alert">' . validation_errors() . '</div>';
    }
    if ($this->session->flashdata('msg')) {
        $form_html .= '<div class="alert alert-success" role="status">' . $this->session->flashdata('msg') . '</div>';
        $this->session->unset_userdata('msg');
    }
    $form_html .= $this->form_builder->open_form(array('action' => '', 'id' => 'open', 'class' => 'st-form theme-form'));
    $form_html .= "<input type='hidden' value='" . $e($form_name) . "' name='form_name'/>";
    $form_html .= $this->form_builder->build_form_horizontal($form, null);
    $form_html .= $this->form_builder->close_form();
    $html = str_replace('[form-builder:' . $form_name . ']', $form_html, $html);
}
$image = function ($row) { return !empty($row['feature_image']) ? $row['feature_image'] : base_url('uploads/gallery/gallery_default.png'); };
$excerpt = function ($text, $n) { $t = trim(strip_tags(html_entity_decode((string) $text))); return mb_strlen($t) > $n ? mb_substr($t, 0, $n) . '…' : $t; };
?>
<?php if (trim($html) !== '') { ?><div class="st-prose"><?php echo $html; ?></div><?php } ?>

<input type="hidden" name="page_content_type" id="page_content_type" value="<?php echo $e(isset($page_content_type) ? $page_content_type : ''); ?>">
<div class="st-post-list" id="postList">
    <?php if (!empty($page['category_content'])) {
        foreach ($page['category_content'] as $kind => $rows) {
            if (empty($rows)) { continue; }
            if ($kind === 'notice') { ?>
                <div class="st-news-list">
                    <?php foreach ($rows as $row) { ?>
                        <article class="st-news-item">
                            <?php if (!empty($row['date'])) { ?><div class="st-date"><b><?php echo date('d', strtotime($row['date'])); ?></b><?php echo date('M Y', strtotime($row['date'])); ?></div><?php } ?>
                            <div>
                                <h2><a href="<?php echo $e(site_url($row['url'])); ?>"><?php echo $e($row['title']); ?></a></h2>
                                <p><?php echo $e($excerpt($row['description'], 160)); ?></p>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="st-cards">
                    <?php foreach ($rows as $row) { ?>
                        <a class="st-card" href="<?php echo $e(site_url($row['url'])); ?>">
                            <span class="st-card-img"><img src="<?php echo $e($image($row)); ?>" alt="" loading="lazy"></span>
                            <span class="st-card-body">
                                <?php if ($kind === 'events' && !empty($row['event_start'])) { ?><small class="st-card-meta"><i class="fa fa-calendar-o" aria-hidden="true"></i> <?php echo $e(date('j M Y', strtotime($row['event_start']))); ?></small><?php } ?>
                                <strong><?php echo $e($row['title']); ?></strong>
                                <span class="st-card-text"><?php echo $e($excerpt($row['description'], 110)); ?></span>
                            </span>
                        </a>
                    <?php } ?>
                </div>
            <?php }
        }
        echo $this->ajax_pagination->create_links();
    } ?>
</div>
<script>
    function searchFilter(page_num) {
        page_num = page_num ? page_num : 0;
        $.post('<?php echo base_url('welcome/ajaxPaginationData'); ?>/' + page_num, { page: page_num, page_content_type: $('#page_content_type').val() }, function (html) {
            $('#postList').html(html);
        });
    }
    function refreshCaptcha() {
        $.post('<?php echo base_url('site/refreshCaptcha'); ?>', {}, function (captcha) { $('#captcha_image').html(captcha); });
    }
</script>
