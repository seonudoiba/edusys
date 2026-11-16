<?php
/* Front CMS > Website design on the platform: the platform website's look and its homepage slider (Platform_theme). */
$e = function ($v) { return html_escape((string) $v); };
$groups = array();
foreach ($fields as $name => $f) {
    $groups[$f['group']][$name] = $f;
}
$json = function ($v) { return json_encode($v, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); };
?>
<style>
    .sd-wrap { display: flex; gap: 16px; align-items: flex-start; }
    .sd-side { flex: 0 0 430px; max-width: 430px; }
    .sd-preview { flex: 1 1 auto; min-width: 0; position: sticky; top: 60px; }
    .sd-box { background: #fff; border: 1px solid #e3e6ea; border-radius: 4px; margin-bottom: 14px; }
    .sd-box > h3 { margin: 0; padding: 12px 14px; border-bottom: 1px solid #e3e6ea; font-size: 15px; font-weight: 600; }
    .sd-box-body { padding: 14px; }
    .sd-themes { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .sd-theme { position: relative; padding: 0; border: 2px solid #e3e6ea; border-radius: 6px; background: #fff; text-align: left; overflow: hidden; cursor: pointer; }
    .sd-theme:hover { border-color: #9fb3c8; }
    .sd-theme.active { border-color: #2271b1; box-shadow: 0 0 0 2px rgba(34, 113, 177, .25); }
    .sd-theme .sd-check { position: absolute; top: 6px; right: 6px; display: none; width: 22px; height: 22px; border-radius: 50%; background: #2271b1; color: #fff; font-size: 12px; line-height: 22px; text-align: center; }
    .sd-theme.active .sd-check { display: block; }
    .sd-mock { height: 74px; display: flex; flex-direction: column; }
    .sd-mock-bar { height: 14px; display: flex; align-items: center; gap: 4px; padding: 0 6px; }
    .sd-mock-bar i { display: block; height: 4px; width: 14px; border-radius: 2px; }
    .sd-mock-hero { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 0 8px; gap: 5px; }
    .sd-mock-hero b { font-size: 12px; line-height: 1.1; color: #fff; }
    .sd-mock-hero span { display: inline-block; width: 46px; height: 11px; }
    .sd-theme-name { padding: 7px 9px 8px; }
    .sd-theme-name b { display: block; font-size: 13px; }
    .sd-theme-name small { display: block; color: #777; font-size: 11px; line-height: 1.3; }
    .sd-field { margin-bottom: 12px; }
    .sd-field label, .sd-slide label { font-size: 12px; font-weight: 600; margin-bottom: 4px; }
    .sd-field .help-block, .sd-slide .help-block { font-size: 11px; margin: 3px 0 0; }
    .sd-color { display: flex; gap: 6px; align-items: center; }
    .sd-color input[type=color] { width: 44px; height: 34px; padding: 2px; border: 1px solid #ccc; border-radius: 3px; background: #fff; }
    .sd-color input[type=text] { width: 100px; }
    .sd-group { margin: 16px 0 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #777; letter-spacing: .04em; }
    .sd-group:first-child { margin-top: 0; }
    .sd-actions { position: sticky; bottom: 0; z-index: 2; display: flex; flex-wrap: wrap; gap: 8px; padding: 12px 14px; background: #fff; border: 1px solid #e3e6ea; border-radius: 4px; }
    .sd-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding: 8px 10px; background: #fff; border: 1px solid #e3e6ea; border-bottom: 0; border-radius: 4px 4px 0 0; }
    .sd-frame-wrap { display: flex; justify-content: center; background: #e5e7eb; border: 1px solid #e3e6ea; border-radius: 0 0 4px 4px; padding: 10px; }
    #sdFrame { width: 100%; height: calc(100vh - 170px); min-height: 520px; border: 0; background: #fff; box-shadow: 0 1px 4px rgba(0, 0, 0, .2); transition: width .2s; }
    .sd-slide { border: 1px solid #e3e6ea; border-radius: 4px; margin-bottom: 10px; background: #fafbfc; }
    .sd-slide > summary { display: flex; align-items: center; gap: 8px; padding: 9px 10px; cursor: pointer; list-style: none; font-weight: 600; font-size: 13px; }
    .sd-slide > summary::-webkit-details-marker { display: none; }
    .sd-slide > summary .sd-slide-title { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sd-slide > summary .btn { padding: 2px 7px; }
    .sd-slide-body { padding: 4px 10px 10px; border-top: 1px solid #e3e6ea; background: #fff; }
    .sd-slide-body .form-group { margin: 8px 0 0; }
    .sd-two { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .sd-img { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .sd-img img { width: 96px; height: 60px; object-fit: cover; border-radius: 3px; border: 1px solid #ddd; }
    .sd-img .sd-noimg { width: 96px; height: 60px; display: flex; align-items: center; justify-content: center; border: 1px dashed #ccc; border-radius: 3px; color: #999; font-size: 11px; }
    @media (max-width: 1199px) { .sd-wrap { flex-direction: column; } .sd-side { flex: none; max-width: none; width: 100%; } .sd-preview { position: static; width: 100%; } }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-paint-brush"></i> Website design <small>EduSYS website</small></h1>
    </section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <div class="sd-wrap">
            <div class="sd-side">
                <form method="post" action="<?php echo site_url('admin/front/design/save'); ?>" id="sdForm" data-validate="off">
                    <input type="hidden" name="form_token" value="<?php echo $e($token); ?>">
                    <input type="hidden" name="theme" value="platform">
                    <input type="hidden" name="design" id="sdDesign" value="">
                    <div class="sd-box">
                        <h3>1. Choose a look</h3>
                        <div class="sd-box-body">
                            <div class="sd-themes" role="radiogroup" aria-label="Looks">
                                <?php foreach ($presets as $key => $p) {
                                    $o = $p['options']; ?>
                                    <button type="button" class="sd-theme" data-preset="<?php echo $e($key); ?>" role="radio" aria-checked="false">
                                        <span class="sd-check"><i class="fa fa-check"></i></span>
                                        <span class="sd-mock" aria-hidden="true">
                                            <span class="sd-mock-bar" style="background:<?php echo $o['header_style'] === 'brand' ? $e($o['primary']) : '#fff'; ?>;border-bottom:1px solid #e5e7eb"><i style="background:<?php echo $e($o['accent']); ?>"></i><i style="background:<?php echo $e($o['highlight']); ?>"></i></span>
                                            <span class="sd-mock-hero" style="background:<?php echo $e($o['primary']); ?>">
                                                <b style="font-family:<?php echo $e($fonts[$o['heading_font']][2]); ?>">Run your school</b>
                                                <span style="background:<?php echo $e($o['accent']); ?>;border-radius:<?php echo $o['buttons'] === 'pill' ? '6px' : ($o['buttons'] === 'square' ? '0' : '3px'); ?>"></span>
                                            </span>
                                        </span>
                                        <span class="sd-theme-name"><b><?php echo $e($p['label']); ?></b><small><?php echo $e($p['description']); ?></small></span>
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="sd-box">
                        <h3>2. Make it yours</h3>
                        <div class="sd-box-body" id="sdFields">
                            <?php foreach ($groups as $group => $list) { ?>
                                <div class="sd-group"><?php echo $e($group); ?></div>
                                <?php foreach ($list as $name => $f) {
                                    $id = 'sd_' . $name; ?>
                                    <div class="sd-field" data-name="<?php echo $e($name); ?>" data-type="<?php echo $e($f['type']); ?>">
                                        <label for="<?php echo $id; ?>"><?php echo $e($f['label']); ?></label>
                                        <?php if ($f['type'] === 'color') { ?>
                                            <div class="sd-color"><input type="color" id="<?php echo $id; ?>"><input type="text" class="form-control input-sm sd-hex" maxlength="7" aria-label="<?php echo $e($f['label']); ?> code"></div>
                                        <?php } elseif ($f['type'] === 'font') { ?>
                                            <select id="<?php echo $id; ?>" class="form-control input-sm"><?php foreach ($fonts as $k => $font) { ?><option value="<?php echo $e($k); ?>"><?php echo $e($font[0]); ?></option><?php } ?></select>
                                        <?php } else { ?>
                                            <select id="<?php echo $id; ?>" class="form-control input-sm"><?php foreach ($f['choices'] as $k => $label) { ?><option value="<?php echo $e($k); ?>"><?php echo $e($label); ?></option><?php } ?></select>
                                        <?php } ?>
                                        <?php if (!empty($f['help'])) { ?><p class="help-block"><?php echo $e($f['help']); ?></p><?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="sd-box">
                        <h3>3. Homepage slides</h3>
                        <div class="sd-box-body">
                            <p class="text-muted" style="font-size:12px">Up to <?php echo (int) $max_slides; ?> slides. Visitors can move between them with the arrows and dots. Pictures: JPG, PNG or WebP, up to 5 MB, ideally 1600 &times; 900 or larger.</p>
                            <div id="sdSlides"></div>
                            <button type="button" class="btn btn-default btn-sm" id="sdAddSlide"><i class="fa fa-plus"></i> Add a slide</button>
                            <button type="button" class="btn btn-link btn-sm" id="sdDefaultSlides">Put back the original slides</button>
                        </div>
                    </div>
                    <div class="sd-actions">
                        <?php if ($can_edit) { ?><button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save and publish</button><?php } ?>
                        <button type="button" class="btn btn-default" id="sdReset">Reset to the look's settings</button>
                    </div>
                </form>
            </div>
            <div class="sd-preview">
                <div class="sd-bar">
                    <b>Preview</b> <span class="text-muted" style="font-size:12px">Only you see unsaved changes.</span>
                    <span style="flex:1"></span>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Screen size">
                        <button type="button" class="btn btn-default active" data-width="100%"><i class="fa fa-desktop"></i></button>
                        <button type="button" class="btn btn-default" data-width="768px"><i class="fa fa-tablet"></i></button>
                        <button type="button" class="btn btn-default" data-width="390px"><i class="fa fa-mobile"></i></button>
                    </div>
                    <select class="form-control input-sm" id="sdPage" style="width:auto" aria-label="Page to preview">
                        <option value="">Home</option>
                        <option value="plans">Pricing</option>
                        <option value="product">Product</option>
                        <option value="find-school">Find your school</option>
                    </select>
                    <a class="btn btn-link btn-sm" href="<?php echo $e($site_url); ?>" target="_blank" rel="noopener">Open the website</a>
                </div>
                <div class="sd-frame-wrap"><iframe id="sdFrame" title="Website preview"></iframe></div>
            </div>
        </div>
    </section>
</div>
<script>
(function ($) {
    'use strict';
    var PRESETS = <?php echo $json($presets); ?>;
    var VISUALS = <?php echo $json($visuals); ?>;
    var DEFAULT_SLIDES = <?php echo $json($default_slides); ?>;
    var MAX = <?php echo (int) $max_slides; ?>;
    var state = <?php echo $json($design); ?>;
    var token = <?php echo $json($token); ?>, timer = null;
    var siteUrl = <?php echo $json($site_url); ?>, baseUrl = <?php echo $json(base_url()); ?>;
    var frame = document.getElementById('sdFrame');
    var esc = function (s) { return $('<div>').text(s == null ? '' : String(s)).html(); };

    function fillOptions() {
        $('#sdFields .sd-field').each(function () {
            var name = $(this).data('name'), type = $(this).data('type'), v = state.options[name];
            $('#sd_' + name).val(String(v));
            if (type === 'color') { $(this).find('.sd-hex').val(v); }
        });
        $('.sd-theme').removeClass('active').attr('aria-checked', 'false').filter('[data-preset="' + state.preset + '"]').addClass('active').attr('aria-checked', 'true');
    }

    function slideHtml(s, i) {
        var input = function (name, label, max, attrs) {
            return '<div class="form-group"><label>' + label + '</label><input type="text" class="form-control input-sm" data-k="' + name + '" maxlength="' + max + '" value="' + esc(s[name]) + '"' + (attrs || '') + '></div>';
        };
        var visual = '<select class="form-control input-sm" data-k="visual">';
        $.each(VISUALS, function (k, label) { visual += '<option value="' + k + '"' + (s.visual === k ? ' selected' : '') + '>' + esc(label) + '</option>'; });
        visual += '</select>';
        var img = s.image ? '<img src="' + esc(baseUrl + s.image) + '" alt="">' : '<span class="sd-noimg">No picture</span>';
        return '<details class="sd-slide" data-i="' + i + '"' + (s._open ? ' open' : '') + '>' +
            '<summary><i class="fa fa-caret-right"></i><span class="sd-slide-title">' + (i + 1) + '. ' + esc(s.title || 'New slide') + '</span>' +
            '<button type="button" class="btn btn-default btn-xs" data-act="up" title="Move up" aria-label="Move slide up"' + (i === 0 ? ' disabled' : '') + '><i class="fa fa-arrow-up"></i></button>' +
            '<button type="button" class="btn btn-default btn-xs" data-act="down" title="Move down" aria-label="Move slide down"' + (i === state.slides.length - 1 ? ' disabled' : '') + '><i class="fa fa-arrow-down"></i></button>' +
            '<button type="button" class="btn btn-default btn-xs" data-act="remove" title="Remove" aria-label="Remove slide"><i class="fa fa-trash text-danger"></i></button></summary>' +
            '<div class="sd-slide-body">' +
            '<div class="sd-two">' + input('badge', 'Badge', 20, ' placeholder="New"') + input('label', 'Small label', 80) + '</div>' +
            input('title', 'Heading', 120) +
            '<div class="form-group"><label>Text</label><textarea class="form-control input-sm" rows="3" data-k="text" maxlength="320">' + esc(s.text) + '</textarea></div>' +
            '<div class="form-group"><label>Points with a tick</label><textarea class="form-control input-sm" rows="3" data-k="points" maxlength="200">' + esc(s.points) + '</textarea><p class="help-block">One per line. Leave empty for none.</p></div>' +
            '<div class="sd-two">' + input('btn1_text', 'Main button', 40) + input('btn1_url', 'Goes to', 255, ' placeholder="subscription/add"') + '</div>' +
            '<div class="sd-two">' + input('btn2_text', 'Second button', 40) + input('btn2_url', 'Goes to', 255, ' placeholder="plans"') + '</div>' +
            '<p class="help-block">A page of this website (e.g. plans, product, page/about-us) or a full address starting with https://.</p>' +
            '<div class="form-group"><label>Beside or behind the text</label>' + visual + '</div>' +
            '<div class="form-group"><label>Picture</label><div class="sd-img">' + img +
            '<label class="btn btn-default btn-sm" style="margin:0;font-weight:400"><i class="fa fa-upload"></i> ' + (s.image ? 'Change' : 'Upload') + '<input type="file" accept="image/jpeg,image/png,image/webp" data-act="upload" style="display:none"></label>' +
            (s.image ? '<button type="button" class="btn btn-link btn-sm" data-act="noimage">Remove picture</button>' : '') +
            '</div><p class="help-block sd-upmsg">Used when "Photo beside the text" or "Photo behind the text" is chosen.</p></div>' +
            '<div class="checkbox" style="margin-bottom:0"><label style="font-weight:400"><input type="checkbox" data-k="show_price"' + (+s.show_price ? ' checked' : '') + '> Show the starting price ("From &#8358;... per student, per term")</label></div>' +
            '</div></details>';
    }

    function renderSlides() {
        var html = '';
        $.each(state.slides, function (i, s) { html += slideHtml(s, i); });
        $('#sdSlides').html(html || '<p class="text-muted">No slides: the homepage has no slider. Add one below.</p>');
        $('#sdAddSlide').prop('disabled', state.slides.length >= MAX);
    }

    function design() {
        return { preset: state.preset, options: state.options, slides: $.map(state.slides, function (s) { var c = $.extend({}, s); delete c._open; return c; }) };
    }

    function refresh() {
        clearTimeout(timer);
        timer = setTimeout(function () {
            $.post('<?php echo site_url('admin/front/design/preview'); ?>', { form_token: token, theme: 'platform', design: JSON.stringify(design()) }, function () {
                frame.src = siteUrl + $('#sdPage').val() + '?st_preview=1&t=' + Date.now();
            }, 'json');
        }, 400);
    }

    $('.sd-theme').on('click', function () {
        state.preset = $(this).data('preset');
        state.options = $.extend({}, PRESETS[state.preset].options);
        fillOptions();
        refresh();
    });
    $('#sdReset').on('click', function () {
        state.options = $.extend({}, PRESETS[state.preset].options);
        fillOptions();
        refresh();
    });
    $('#sdFields').on('input change', 'input, select', function () {
        var $f = $(this).closest('.sd-field'), name = $f.data('name'), type = $f.data('type'), v = $(this).val();
        if (type === 'color') {
            if (!/^#[0-9a-fA-F]{6}$/.test(v)) { return; }
            $f.find('input').not(this).val(v);
        }
        state.options[name] = v;
        refresh();
    });

    // slides
    $('#sdSlides')[0].addEventListener('toggle', function (ev) {
        var d = ev.target;
        if (d.classList && d.classList.contains('sd-slide')) { state.slides[+d.getAttribute('data-i')]._open = d.open; }
    }, true);
    $('#sdSlides').on('input change', '[data-k]', function () {
        var i = +$(this).closest('.sd-slide').data('i'), k = $(this).data('k');
        state.slides[i][k] = this.type === 'checkbox' ? (this.checked ? 1 : 0) : $(this).val();
        if (k === 'title') { $(this).closest('.sd-slide').find('.sd-slide-title').text((i + 1) + '. ' + ($(this).val() || 'New slide')); }
        refresh();
    });
    $('#sdSlides').on('click', '[data-act]', function (ev) {
        var act = $(this).data('act');
        if (act === 'upload') { return; }
        ev.preventDefault();
        var i = +$(this).closest('.sd-slide').data('i'), s = state.slides;
        if (act === 'up' && i > 0) { s.splice(i - 1, 0, s.splice(i, 1)[0]); }
        else if (act === 'down' && i < s.length - 1) { s.splice(i + 1, 0, s.splice(i, 1)[0]); }
        else if (act === 'remove') { s.splice(i, 1); }
        else if (act === 'noimage') {
            s[i].image = '';
            if (s[i].visual === 'photo' || s[i].visual === 'background') { s[i].visual = 'none'; }
        }
        renderSlides();
        refresh();
    });
    $('#sdSlides').on('change', 'input[data-act=upload]', function () {
        var $slide = $(this).closest('.sd-slide'), i = +$slide.data('i'), file = this.files[0];
        if (!file) { return; }
        var fd = new FormData();
        fd.append('form_token', token);
        fd.append('image', file);
        $slide.find('.sd-upmsg').text('Uploading...');
        $.ajax({ url: '<?php echo site_url('admin/front/design/slide_image'); ?>', type: 'POST', data: fd, processData: false, contentType: false, dataType: 'json' })
            .done(function (r) {
                if (!r.ok) { $slide.find('.sd-upmsg').addClass('text-danger').text(r.error); return; }
                state.slides[i].image = r.path;
                if (['photo', 'background'].indexOf(state.slides[i].visual) < 0) { state.slides[i].visual = 'background'; }
                state.slides[i]._open = true;
                renderSlides();
                refresh();
            })
            .fail(function () { $slide.find('.sd-upmsg').addClass('text-danger').text('The picture could not be uploaded. Try again.'); });
    });
    $('#sdAddSlide').on('click', function () {
        if (state.slides.length >= MAX) { return; }
        $.each(state.slides, function (i, s) { s._open = false; });
        state.slides.push({ badge: '', label: '', title: 'New slide', text: '', points: '', btn1_text: 'Register your school', btn1_url: 'subscription/add', btn2_text: '', btn2_url: '', visual: 'none', image: '', show_price: 0, _open: true });
        renderSlides();
        refresh();
    });
    $('#sdDefaultSlides').on('click', function () {
        state.slides = $.map(DEFAULT_SLIDES, function (s) { return $.extend({}, s); });
        renderSlides();
        refresh();
    });

    $('.sd-bar').on('click', '[data-width]', function () {
        $(this).addClass('active').siblings().removeClass('active');
        frame.style.width = $(this).data('width');
    });
    $('#sdPage').on('change', refresh);
    $('#sdForm').on('submit', function () { $('#sdDesign').val(JSON.stringify(design())); });

    fillOptions();
    renderSlides();
    refresh();
})(jQuery);
</script>
