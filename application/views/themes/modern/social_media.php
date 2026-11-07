<?php
$links = array(
    'whatsapp_url' => array('fa-whatsapp', 'WhatsApp'), 'fb_url' => array('fa-facebook', 'Facebook'), 'twitter_url' => array('fa-twitter', 'X (Twitter)'),
    'instagram_url' => array('fa-instagram', 'Instagram'), 'youtube_url' => array('fa-youtube-play', 'YouTube'), 'linkedin_url' => array('fa-linkedin', 'LinkedIn'),
    'pinterest_url' => array('fa-pinterest', 'Pinterest'),
);
foreach ($links as $field => $l) {
    $url = isset($front_setting->$field) ? trim((string) $front_setting->$field) : '';
    if ($url === '' || !preg_match('~^https?://~i', $url)) {
        continue;
    } ?>
    <li><a href="<?php echo html_escape($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo html_escape($l[1]); ?>"><i class="fa <?php echo $l[0]; ?>" aria-hidden="true"></i></a></li>
<?php }
