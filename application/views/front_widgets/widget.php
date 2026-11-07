<?php
/*
 * One page builder widget (see Page_widgets). $type, $s settings (already cleaned), $d data, $tokens,
 * $preview, $label, $hint, $uid. Dynamic widgets with nothing to show stay hidden on the public page;
 * the preview says why, so the admin can fix it.
 */
$e = function ($v) use ($tokens) { return html_escape(strtr((string) $v, $tokens)); };
$rich = function ($v) use ($tokens) {
    $safe = array_map('html_escape', $tokens);
    $v = strtr((string) $v, $safe);
    return strpos($v, '<') === false ? nl2br($v) : $v;
};
$href = function ($url) { return preg_match('~^(https?://|mailto:|tel:)~i', (string) $url) ? html_escape($url) : html_escape(site_url($url)); };
$src = function ($url) { return preg_match('~^https?://~i', (string) $url) ? html_escape($url) : html_escape(base_url($url)); };
// Text written on the page: already cleaned to basic formatting; {school_name} etc. filled in.
$inline = function ($v) use ($tokens) { return strtr((string) $v, array_map('html_escape', $tokens)); };
$align = function () use ($s) { return isset($s['align']) && $s['align'] !== 'left' ? ' style="text-align:' . ($s['align'] === 'right' ? 'right' : 'center') . '"' : ''; };
$heading = function () use ($s, $e) { return !empty($s['title']) ? '<h2>' . $e($s['title']) . '</h2>' : ''; };
$empty = function ($why) use ($preview, $label) {
    return $preview ? '<div class="pw-empty"><b>' . html_escape($label) . ':</b> ' . html_escape($why) . ' Visitors don\'t see this box.</div>' : '';
};
$date_box = function ($date) use ($e) {
    $t = strtotime((string) $date);
    return $t ? '<div class="pw-date"><b>' . date('d', $t) . '</b>' . date('M', $t) . '</div>' : '';
};
$card_image = function ($row, $fallback) use ($src) { return !empty($row['feature_image']) ? $src($row['feature_image']) : html_escape(base_url($fallback)); };

$out = '';
switch ($type) {
    case 'paragraph':
        if (trim(strip_tags($s['html'], '<br>')) === '') { $out = $empty('This paragraph is empty.'); break; }
        $out .= '<p' . $align() . '>' . $inline($s['html']) . '</p>';
        break;

    case 'heading':
        if (trim(strip_tags($s['html'])) === '') { $out = $empty('This heading is empty.'); break; }
        $level = in_array($s['level'], array('2', '3', '4'), true) ? $s['level'] : '2';
        $out .= '<h' . $level . ' class="pw-h"' . $align() . '>' . $inline($s['html']) . '</h' . $level . '>';
        break;

    case 'list':
        if ($s['html'] === '') { $out = $empty('This list is empty.'); break; }
        $tag = !empty($s['ordered']) ? 'ol' : 'ul';
        $out .= '<' . $tag . ' class="pw-ul">' . $inline($s['html']) . '</' . $tag . '>';
        break;

    case 'quote':
        if (trim(strip_tags($s['html'])) === '') { $out = $empty('This quote is empty.'); break; }
        $out .= '<blockquote class="pw-blockquote"><p>' . $inline($s['html']) . '</p>' . ($s['cite'] !== '' ? '<cite>' . $e($s['cite']) . '</cite>' : '') . '</blockquote>';
        break;

    case 'table':
        $filled = false;
        foreach ($s['rows'] as $row) { foreach ($row as $cell) { if ($cell !== '') { $filled = true; } } }
        if (!$filled) { $out = $empty('This table is empty.'); break; }
        $out .= '<div class="table-responsive"><table class="table table-bordered pw-table">';
        foreach ($s['rows'] as $i => $row) {
            $cell = $i === 0 && !empty($s['header']) ? 'th' : 'td';
            $out .= '<tr>';
            foreach ($row as $c) {
                $out .= '<' . $cell . '>' . $e($c) . '</' . $cell . '>';
            }
            $out .= '</tr>';
        }
        $out .= '</table></div>';
        break;

    case 'separator':
        $out .= '<hr class="pw-hr">';
        break;

    case 'image_gallery':
        if (!$s['images']) { $out = $empty('Add images to this gallery.'); break; }
        $out .= '<div class="pw-grid" style="--pw-cols:' . (int) $s['columns'] . '">';
        foreach ($s['images'] as $img) {
            $out .= '<img class="pw-img pw-gallery-img" src="' . $src($img['src']) . '" alt="' . $e($img['alt']) . '" loading="lazy">';
        }
        $out .= '</div>';
        break;
    case 'banner':
        if (!$d) { $out = $empty('No banner images yet. Add them in Front CMS > Banner Images.'); break; }
        $out .= '<div id="' . $uid . '" class="carousel slide pw-carousel" data-ride="carousel" data-interval="5000"><div class="carousel-inner">';
        foreach ($d as $i => $img) {
            $out .= '<div class="item' . ($i === 0 ? ' active' : '') . '"><img src="' . html_escape(base_url($img->dir_path . $img->img_name)) . '" alt=""></div>';
        }
        $out .= '</div>';
        if (count($d) > 1) {
            $out .= '<a class="left carousel-control" href="#' . $uid . '" data-slide="prev" aria-label="Previous"><span class="fa fa-angle-left"></span></a>'
                . '<a class="right carousel-control" href="#' . $uid . '" data-slide="next" aria-label="Next"><span class="fa fa-angle-right"></span></a>';
        }
        $out .= '</div>';
        break;

    case 'news':
    case 'events':
        if (!$d) { $out = $empty($type === 'news' ? 'No news yet. Add it in Front CMS > News.' : 'No upcoming events. Add them in Front CMS > Events.'); break; }
        $out .= $heading() . '<ul class="pw-list">';
        foreach ($d as $row) {
            $when = $type === 'events' ? $row['event_start'] : $row['date'];
            $out .= '<li>' . $date_box($when) . '<div><a href="' . $href($row['url']) . '"><b>' . html_escape($row['title']) . '</b></a>';
            if ($type === 'events' && !empty($row['event_venue'])) {
                $out .= '<div class="pw-muted">' . html_escape($row['event_venue']) . '</div>';
            }
            $out .= '</div></li>';
        }
        $out .= '</ul>';
        break;

    case 'gallery':
        if (!$d) { $out = $empty('No galleries yet. Add them in Front CMS > Gallery.'); break; }
        $out .= $heading() . '<div class="pw-grid" style="--pw-cols:' . (int) $s['columns'] . '">';
        foreach ($d as $row) {
            $out .= '<a class="pw-card" href="' . $href($row['url']) . '"><img src="' . $card_image($row, 'uploads/gallery/gallery_default.png') . '" alt="" loading="lazy">'
                . '<div class="pw-card-body"><h4>' . html_escape($row['title']) . '</h4></div></a>';
        }
        $out .= '</div>';
        break;

    case 'stats':
        if (!$d) { $out = $empty('Choose at least one number to show.'); break; }
        $out .= '<div class="pw-stats">';
        foreach ($d as $st) {
            // Live totals are integers; numbers the school typed (a founding year, "98%") stay as typed.
            $value = is_int($st['value']) ? number_format($st['value']) : $e($st['value']);
            $out .= '<div class="pw-stat"><b>' . $value . '</b><span class="pw-muted">' . $e($st['label']) . '</span></div>';
        }
        $out .= '</div>';
        break;

    case 'staff':
        if (!$d) { $out = $empty('No staff are marked "show on website". Choose them in Page Builder > Staff on website.'); break; }
        $out .= $heading() . '<div class="pw-grid" style="--pw-cols:' . (int) $s['columns'] . '">';
        foreach ($d as $p) {
            $photo = !empty($p['image']) ? 'uploads/staff_images/' . $p['image'] : 'uploads/staff_images/no_image.png';
            $out .= '<div class="pw-person"><img src="' . html_escape(base_url($photo)) . '" alt="" loading="lazy"><b>' . html_escape(trim($p['name'] . ' ' . $p['surname'])) . '</b>'
                . (!empty($p['designation']) ? '<div class="pw-muted">' . html_escape($p['designation']) . '</div>' : '') . '</div>';
        }
        $out .= '</div>';
        break;

    case 'classes':
        if (!$d) { $out = $empty('No classes yet. Add them in Academics > Class.'); break; }
        $out .= $heading() . '<div class="pw-tags">';
        foreach ($d as $c) {
            $out .= '<span class="pw-tag">' . html_escape(trim($c['class'])) . '</span>';
        }
        $out .= '</div>';
        break;

    case 'admission':
        if (!$d) { $out = $empty('Online admission is off. Turn it on in System Settings > Online Admission.'); break; }
        $out .= '<div class="pw-cta">' . $heading() . (!empty($s['text']) ? '<p>' . nl2br($e($s['text'])) . '</p>' : '')
            . '<a class="btn btn-primary btn-lg" href="' . html_escape(site_url('online_admission')) . '">' . $e($s['button'] ?: 'Apply now') . '</a></div>';
        break;

    case 'courses':
        if (!$d) { $out = $empty('No published courses, or the Online Course module is off.'); break; }
        $out .= $heading() . '<div class="pw-grid" style="--pw-cols:' . (int) $s['columns'] . '">';
        foreach ($d as $c) {
            $thumb = !empty($c['course_thumbnail']) ? 'uploads/course/course_thumbnail/' . $c['course_thumbnail'] : 'backend/images/wordicon.png';
            $out .= '<a class="pw-card" href="' . html_escape(site_url('course/coursedetail/' . $c['slug'])) . '"><img src="' . html_escape(base_url($thumb)) . '" alt="" loading="lazy">'
                . '<div class="pw-card-body"><h4>' . html_escape($c['title']) . '</h4></div></a>';
        }
        $out .= '</div>';
        break;

    case 'plans':
        if (!$d) { $out = $empty('No pricing bands.'); break; }
        $money = function ($v) { return '&#8358;' . number_format((float) $v); };
        $out .= (!empty($s['title']) ? '<h2 class="pw-center">' . $e($s['title']) . '</h2>' : '') . '<div class="table-responsive"><table class="table"><thead><tr><th>Students</th><th>Per student, per term</th><th>One-time setup</th></tr></thead><tbody>';
        foreach ($d as $b) {
            $range = $b['max_students'] === null ? number_format((int) $b['min_students']) . '+' : number_format((int) $b['min_students']) . '&ndash;' . number_format((int) $b['max_students']);
            $custom = $b['rate_per_term'] === null;
            $out .= '<tr><td>' . $range . '</td><td>' . ($custom ? 'Custom quote' : $money($b['rate_per_term'])) . '</td><td>' . ($custom ? 'From ' : '') . $money($b['setup_fee']) . '</td></tr>';
        }
        $out .= '</tbody></table></div>';
        break;

    case 'platform_stats':
        if ((int) $d['schools'] < 2) { $out = $empty('It appears once there are at least 2 schools.'); break; }
        $out .= '<div class="pw-stats"><div class="pw-stat"><b>' . number_format((int) $d['schools']) . '</b><span class="pw-muted">' . $e($s['label']) . '</span></div></div>';
        break;

    case 'register':
        $out .= '<div class="pw-cta">' . $heading() . (!empty($s['text']) ? '<p>' . nl2br($e($s['text'])) . '</p>' : '')
            . '<a class="btn btn-primary btn-lg" href="' . html_escape(site_url('plans')) . '">' . $e($s['button'] ?: 'Register your school') . '</a></div>';
        break;

    case 'text':
        if ($s['title'] === '' && $s['body'] === '') { $out = $empty('This text widget is empty.'); break; }
        $out .= '<div class="' . ($s['align'] === 'center' ? 'pw-center' : '') . '">' . $heading() . $rich($s['body']) . '</div>';
        break;

    case 'image':
        if ($s['src'] === '') { $out = $empty('Choose an image.'); break; }
        $img = '<img class="pw-img" src="' . $src($s['src']) . '" alt="' . (!empty($s['decorative']) ? '' : $e($s['alt'])) . '" loading="lazy">';
        $out .= $s['link'] !== '' ? '<a href="' . $href($s['link']) . '">' . $img . '</a>' : $img;
        break;

    case 'image_text':
        if ($s['src'] === '' && $s['title'] === '' && $s['body'] === '') { $out = $empty('Choose an image and write some text.'); break; }
        $out .= '<div class="pw-it' . ($s['side'] === 'right' ? ' pw-right' : '') . '"><div>'
            . ($s['src'] !== '' ? '<img class="pw-img" src="' . $src($s['src']) . '" alt="' . $e($s['alt']) . '" loading="lazy">' : '') . '</div><div>' . $heading() . $rich($s['body']) . '</div></div>';
        break;

    case 'video':
        $embed = '';
        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,20})~', $s['url'], $m)) {
            $embed = 'https://www.youtube-nocookie.com/embed/' . $m[1];
        } elseif (preg_match('~vimeo\.com/(?:video/)?(\d{4,15})~', $s['url'], $m)) {
            $embed = 'https://player.vimeo.com/video/' . $m[1];
        }
        if ($embed === '') { $out = $empty('Paste a YouTube or Vimeo link.'); break; }
        $out .= '<div class="pw-embed"><iframe src="' . html_escape($embed) . '" title="Video" loading="lazy" allowfullscreen></iframe></div>';
        break;

    case 'button':
        if ($s['link'] === '') { $out = $empty('Add a link for the button.'); break; }
        $out .= '<div class="' . ($s['align'] === 'center' ? 'pw-center' : '') . '"><a class="btn btn-primary" href="' . $href($s['link']) . '">' . $e($s['text']) . '</a></div>';
        break;

    case 'testimonials':
        if (!$s['items']) { $out = $empty('Add at least one testimonial.'); break; }
        $out .= $heading() . '<div class="pw-grid" style="--pw-cols:' . min(3, max(1, count($s['items']))) . '">';
        foreach ($s['items'] as $t) {
            $out .= '<blockquote class="pw-quote">' . nl2br($e($t['quote'])) . ($t['name'] !== '' ? '<div class="pw-quote-by">' . $e($t['name']) . '</div>' : '') . '</blockquote>';
        }
        $out .= '</div>';
        break;

    case 'faq':
        if (!$s['items']) { $out = $empty('Add at least one question.'); break; }
        $out .= $heading() . '<div class="pw-faq">';
        foreach ($s['items'] as $q) {
            $out .= '<details><summary>' . $e($q['q']) . '</summary><p>' . nl2br($e($q['a'])) . '</p></details>';
        }
        $out .= '</div>';
        break;

    case 'contact_form':
    case 'complaint_form':
        // Replaced with the real form by the theme's pages/page view; Welcome::page() handles the POST.
        $out .= $heading() . '[form-builder:' . Page_widgets::FORMS[$type] . ']';
        break;

    case 'map':
        $address = trim(strtr($s['address'] !== '' ? $s['address'] : '{address}', $tokens));
        if ($address === '') { $out = $empty('Add an address, or fill in the school\'s address in General Setting.'); break; }
        $out .= '<div class="pw-map"><iframe src="https://www.google.com/maps?q=' . rawurlencode($address) . '&amp;output=embed" title="Map" loading="lazy"></iframe></div>';
        break;

    case 'spacer':
        $out .= '<div style="height:' . (int) $s['height'] . 'px" aria-hidden="true"></div>';
        break;
}
if ($out !== '') {
    echo '<div class="pw-widget pw-w-' . html_escape($type) . '">' . $out . '</div>';
}
