<?php
/*
 * Homepage highlights under the page text (a homepage not built with the block editor): quick
 * actions (admission, portal, contact, gallery), the latest news as cards, and a contact band.
 * Only shows what the school has: pages that exist and are on, news that is published.
 */
$e = function ($v) { return html_escape((string) $v); };
$CI = get_instance();
$slugs = array();
foreach ($CI->db->select('slug, is_active')->where_in('slug', array('contact-us', 'gallery', 'about-us', 'news', 'events'))->get('front_cms_pages')->result_array() as $p) {
    if ($p['is_active'] === 'yes') {
        $slugs[$p['slug']] = true;
    }
}
$admission = !empty($school_setting->online_admission);
$portal = !empty($setting_data[0]['student_panel_login']) || !empty($setting_data[0]['parent_panel_login']);
$actions = array();
if ($admission) {
    $actions[] = array('fa-graduation-cap', $CI->lang->line('online_admission') ?: 'Admissions', 'Apply online for the new session in a few minutes.', site_url('online_admission'));
}
if ($portal) {
    $actions[] = array('fa-user-circle', 'Student & parent portal', 'Results, fees, timetable, homework and online exams.', site_url('site/userlogin'));
}
if (isset($slugs['about-us'])) {
    $actions[] = array('fa-university', 'About us', 'Our story, values and the people who make the school.', site_url('page/about-us'));
}
if (isset($slugs['gallery'])) {
    $actions[] = array('fa-picture-o', 'Gallery', 'Moments from classrooms, sports and celebrations.', site_url('page/gallery'));
}
if (isset($slugs['contact-us'])) {
    $actions[] = array('fa-map-marker', 'Visit & contact', 'Find us, call us or send a message.', site_url('page/contact-us'));
}
$actions = array_slice($actions, 0, 4);
$news = isset($notices) && is_array($notices) ? array_slice($notices, 0, 3) : array();
$excerpt = function ($html) {
    $t = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $html), ENT_QUOTES, 'UTF-8')));
    return mb_strlen($t) > 130 ? rtrim(mb_substr($t, 0, 127)) . '…' : $t;
};
if (!$actions && !$news) {
    return;
}
?>
<section class="st-hl" aria-label="Highlights">
    <?php if ($actions) { ?>
        <div class="st-wrap">
            <div class="st-hl-actions st-hl-n<?php echo count($actions); ?>">
                <?php foreach ($actions as $i => $a) { ?>
                    <a class="st-hl-card st-reveal" style="--d:<?php echo $i * 80; ?>ms" href="<?php echo $e($a[3]); ?>">
                        <span class="st-hl-icon"><i class="fa <?php echo $a[0]; ?>" aria-hidden="true"></i></span>
                        <b><?php echo $e($a[1]); ?></b>
                        <span><?php echo $e($a[2]); ?></span>
                        <i class="fa fa-arrow-right st-hl-go" aria-hidden="true"></i>
                    </a>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <?php if ($news) { ?>
        <div class="st-hl-news-band">
            <div class="st-wrap">
                <div class="st-hl-head st-reveal">
                    <div><span class="st-hl-kicker">What's happening</span><h2><?php echo $e($CI->lang->line('latest_news') ?: 'Latest news'); ?></h2></div>
                    <?php if (isset($slugs['news'])) { ?><a class="st-btn st-btn-sm" href="<?php echo $e(site_url('page/news')); ?>">All news <i class="fa fa-angle-right" aria-hidden="true"></i></a><?php } ?>
                </div>
                <div class="st-hl-news">
                    <?php foreach ($news as $i => $n) {
                        $img = !empty($n['feature_image']) ? $n['feature_image'] : ''; ?>
                        <a class="st-hl-post st-reveal" style="--d:<?php echo $i * 90; ?>ms" href="<?php echo $e(site_url('read/' . $n['slug'])); ?>">
                            <div class="st-hl-thumb<?php echo $img ? '' : ' st-hl-thumb-empty'; ?>">
                                <?php if ($img) { ?><img src="<?php echo $e(base_url($img)); ?>" alt="" loading="lazy"><?php } else { ?><i class="fa fa-newspaper-o" aria-hidden="true"></i><?php } ?>
                                <?php if (!empty($n['date'])) { ?><span class="st-hl-date"><b><?php echo date('d', strtotime($n['date'])); ?></b><?php echo date('M', strtotime($n['date'])); ?></span><?php } ?>
                            </div>
                            <div class="st-hl-body">
                                <h3><?php echo $e($n['title']); ?></h3>
                                <?php $x = $excerpt($n['description']); if ($x !== '') { ?><p><?php echo $e($x); ?></p><?php } ?>
                                <span class="st-hl-more">Read more <i class="fa fa-long-arrow-right" aria-hidden="true"></i></span>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($school_setting->phone) || !empty($school_setting->email) || $admission) { ?>
        <div class="st-wrap">
            <div class="st-hl-cta st-reveal">
                <div>
                    <h2><?php echo $admission ? 'Give your child a great start' : 'We would love to hear from you'; ?></h2>
                    <p><?php echo $admission ? 'Admissions are open. Visit us, ask questions, or apply online today.' : 'Questions about the school? Get in touch with our team.'; ?></p>
                </div>
                <div class="st-hl-cta-actions">
                    <?php if ($admission) { ?><a class="st-btn st-btn-lg st-hl-light" href="<?php echo $e(site_url('online_admission')); ?>">Apply now</a><?php } ?>
                    <?php if (!empty($school_setting->phone)) { ?><a class="st-btn st-btn-lg st-btn-ghost" href="tel:<?php echo $e(preg_replace('/[^0-9+]/', '', $school_setting->phone)); ?>"><i class="fa fa-phone" aria-hidden="true"></i> <?php echo $e($school_setting->phone); ?></a><?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</section>
