<?php
/* One news item, event or gallery: its text, then its pictures and videos. */
$e = function ($v) { return html_escape((string) $v); };
$meta = array();
if (!empty($page['event_start'])) {
    $meta[] = '<i class="fa fa-calendar-o" aria-hidden="true"></i> ' . $e(date('j M Y', strtotime($page['event_start']))) . (!empty($page['event_end']) && $page['event_end'] !== $page['event_start'] ? ' &ndash; ' . $e(date('j M Y', strtotime($page['event_end']))) : '');
} elseif (!empty($page['date'])) {
    $meta[] = '<i class="fa fa-calendar-o" aria-hidden="true"></i> ' . $e(date('j M Y', strtotime($page['date'])));
}
if (!empty($page['event_venue'])) {
    $meta[] = '<i class="fa fa-map-marker" aria-hidden="true"></i> ' . $e($page['event_venue']);
}
?>
<article class="st-article">
    <?php if ($meta) { ?><p class="st-article-meta"><?php echo implode('<span aria-hidden="true">&middot;</span>', $meta); ?></p><?php } ?>
    <div class="st-prose"><?php echo $page['description']; ?></div>
    <?php if (!empty($page['page_contents'])) { ?>
        <div class="st-gallery gallery">
            <?php foreach ($page['page_contents'] as $c) {
                $url = $c->file_type === 'video' ? $c->vid_url : base_url($c->dir_path . $c->img_name);
                $thumb = $c->file_type === 'video' ? base_url($c->thumb_path . $c->thumb_name) : $url; ?>
                <a class="st-gallery-item" href="<?php echo $e($url); ?>" data-toggle="lightbox" data-gallery="mixedgallery" aria-label="<?php echo $c->file_type === 'video' ? 'Play video' : 'View picture'; ?>">
                    <img src="<?php echo $e($thumb); ?>" alt="" loading="lazy">
                    <span class="st-gallery-zoom"><i class="fa <?php echo $c->file_type === 'video' ? 'fa-play' : 'fa-search-plus'; ?>" aria-hidden="true"></i></span>
                </a>
            <?php } ?>
        </div>
    <?php } ?>
</article>
