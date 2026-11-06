<?php
/* The background of a page's opening section: the images from Front CMS > Banner, one after another. */
$CI = &get_instance();
if (!isset($CI->platform_bg_images)) {
    $CI->load->model('cms_program_model');
    $program = $CI->cms_program_model->getByCategory($CI->config->item('ci_front_banner_content'));
    $CI->platform_bg_images = array();
    foreach (!empty($program) ? $CI->cms_program_model->front_cms_program_photos($program[0]['id']) : array() as $photo) {
        if (strpos((string) $photo->file_type, 'image') === 0) {
            $CI->platform_bg_images[] = base_url($photo->dir_path . rawurlencode($photo->img_name));
        }
    }
}
if ($CI->platform_bg_images) { ?>
    <div class="bg" aria-hidden="true">
        <?php foreach ($CI->platform_bg_images as $i => $url) { ?>
            <div class="bg-slide<?php echo $i === 0 ? ' on' : ''; ?>" style="background-image:url('<?php echo html_escape($url); ?>')"></div>
        <?php } ?>
    </div>
    <?php if (count($CI->platform_bg_images) > 1) { ?>
        <script>
        (function () {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
            var slides = document.currentScript.previousElementSibling.children, at = 0;
            setInterval(function () {
                slides[at].classList.remove('on');
                at = (at + 1) % slides.length;
                slides[at].classList.add('on');
            }, 6000);
        })();
        </script>
    <?php }
}
