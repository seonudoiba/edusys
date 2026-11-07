<?php
/*
 * The homepage banner: the school's banner images (Front CMS > Banner Images) in one of four styles.
 * A homepage built with the block editor has its own banner block, and this one is not shown.
 */
$e = function ($v) { return html_escape((string) $v); };
$images = isset($banner_images) && is_array($banner_images) ? $banner_images : array();
$style = isset($st['hero']) ? $st['hero'] : 'full';
if (!$images || $style === 'none') {
    return;
}
$show_text = !empty($st['hero_text']);
$tagline = isset($st['tagline']) ? trim($st['tagline']) : '';
$admission = !empty($school_setting->online_admission);
$text = function () use ($e, $school_setting, $tagline, $admission) {
    $out = '<h1>' . $e($school_setting->name) . '</h1>';
    if ($tagline !== '') {
        $out .= '<p>' . $e($tagline) . '</p>';
    }
    $out .= '<div class="st-hero-actions">';
    if ($admission) {
        $out .= '<a class="st-btn st-btn-lg" href="' . $e(site_url('online_admission')) . '">' . $e(get_instance()->lang->line('online_admission')) . '</a>';
    }
    $out .= '<a class="st-btn st-btn-lg st-btn-ghost" href="#st-main">' . $e(get_instance()->lang->line('read_more') ?: 'Learn more') . '</a></div>';
    return $out;
};
$slides = function ($id) use ($images, $e) {
    $out = '<div id="' . $id . '" class="carousel slide st-carousel" data-ride="carousel" data-interval="6000"><div class="carousel-inner">';
    foreach ($images as $i => $img) {
        $out .= '<div class="item' . ($i === 0 ? ' active' : '') . '"><img src="' . $e(base_url($img->dir_path . $img->img_name)) . '" alt=""' . ($i ? ' loading="lazy"' : '') . '></div>';
    }
    $out .= '</div>';
    if (count($images) > 1) {
        $out .= '<a class="left carousel-control" href="#' . $id . '" role="button" data-slide="prev" aria-label="Previous"><i class="fa fa-angle-left" aria-hidden="true"></i></a>'
            . '<a class="right carousel-control" href="#' . $id . '" role="button" data-slide="next" aria-label="Next"><i class="fa fa-angle-right" aria-hidden="true"></i></a>'
            . '<ol class="carousel-indicators">';
        foreach ($images as $i => $img) {
            $out .= '<li data-target="#' . $id . '" data-slide-to="' . $i . '"' . ($i === 0 ? ' class="active"' : '') . '></li>';
        }
        $out .= '</ol>';
    }
    return $out . '</div>';
};
?>
<?php if ($style === 'split') { ?>
    <section class="st-hero st-hero-split">
        <div class="st-wrap st-hero-split-grid">
            <div class="st-hero-copy"><?php echo $show_text ? $text() : '<h1>' . $e($school_setting->name) . '</h1>'; ?></div>
            <div class="st-hero-media"><?php echo $slides('st-hero-carousel'); ?></div>
        </div>
    </section>
<?php } elseif ($style === 'contained') { ?>
    <section class="st-hero st-hero-contained">
        <div class="st-wrap">
            <div class="st-hero-frame">
                <?php echo $slides('st-hero-carousel'); ?>
                <?php if ($show_text) { ?><div class="st-hero-overlay"><div class="st-hero-copy"><?php echo $text(); ?></div></div><?php } ?>
            </div>
        </div>
    </section>
<?php } else { ?>
    <section class="st-hero st-hero-full">
        <?php echo $slides('st-hero-carousel'); ?>
        <?php if ($show_text) { ?><div class="st-hero-overlay"><div class="st-wrap"><div class="st-hero-copy"><?php echo $text(); ?></div></div></div><?php } ?>
    </section>
<?php } ?>
