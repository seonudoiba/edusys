<?php
$e = function ($v) { return html_escape((string) $v); };
$groups = array();
foreach ($fields as $name => $f) {
    $groups[$f['group']][$name] = $f;
}
?>
<style>
    .sd-wrap { display: flex; gap: 16px; align-items: flex-start; }
    .sd-side { flex: 0 0 400px; max-width: 400px; }
    .sd-preview { flex: 1 1 auto; min-width: 0; position: sticky; top: 60px; }
    .sd-box { background: #fff; border: 1px solid #e3e6ea; border-radius: 4px; margin-bottom: 14px; }
    .sd-box > h3 { margin: 0; padding: 12px 14px; border-bottom: 1px solid #e3e6ea; font-size: 15px; font-weight: 600; }
    .sd-box-body { padding: 14px; }
    .sd-themes { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; max-height: 520px; overflow-y: auto; padding: 2px; }
    .sd-theme { position: relative; padding: 0; border: 2px solid #e3e6ea; border-radius: 6px; background: #fff; text-align: left; overflow: hidden; cursor: pointer; }
    .sd-theme:hover { border-color: #9fb3c8; }
    .sd-theme.active { border-color: #2271b1; box-shadow: 0 0 0 2px rgba(34, 113, 177, .25); }
    .sd-theme .sd-check { position: absolute; top: 6px; right: 6px; display: none; width: 22px; height: 22px; border-radius: 50%; background: #2271b1; color: #fff; font-size: 12px; line-height: 22px; text-align: center; }
    .sd-theme.active .sd-check { display: block; }
    .sd-mock { height: 92px; display: flex; flex-direction: column; }
    .sd-mock-bar { height: 16px; display: flex; align-items: center; gap: 4px; padding: 0 6px; }
    .sd-mock-bar i { display: block; height: 4px; width: 14px; border-radius: 2px; opacity: .75; }
    .sd-mock-hero { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 0 8px; gap: 5px; }
    .sd-mock-hero b { font-size: 13px; line-height: 1.1; }
    .sd-mock-hero span { display: inline-block; width: 46px; height: 12px; }
    .sd-mock-foot { height: 12px; }
    .sd-theme-name { padding: 7px 9px 8px; }
    .sd-theme-name b { display: block; font-size: 13px; }
    .sd-theme-name small { display: block; color: #777; font-size: 11px; line-height: 1.3; }
    .sd-field { margin-bottom: 12px; }
    .sd-field label { font-size: 12px; font-weight: 600; margin-bottom: 4px; }
    .sd-field .help-block { font-size: 11px; margin: 3px 0 0; }
    .sd-color { display: flex; gap: 6px; align-items: center; }
    .sd-color input[type=color] { width: 44px; height: 34px; padding: 2px; border: 1px solid #ccc; border-radius: 3px; background: #fff; }
    .sd-color input[type=text] { width: 100px; }
    .sd-group { margin: 16px 0 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #777; letter-spacing: .04em; }
    .sd-group:first-child { margin-top: 0; }
    .sd-actions { position: sticky; bottom: 0; display: flex; gap: 8px; padding: 12px 14px; background: #fff; border-top: 1px solid #e3e6ea; }
    .sd-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding: 8px 10px; background: #fff; border: 1px solid #e3e6ea; border-bottom: 0; border-radius: 4px 4px 0 0; }
    .sd-frame-wrap { display: flex; justify-content: center; background: #e5e7eb; border: 1px solid #e3e6ea; border-radius: 0 0 4px 4px; padding: 10px; }
    #sdFrame { width: 100%; height: calc(100vh - 170px); min-height: 520px; border: 0; background: #fff; box-shadow: 0 1px 4px rgba(0, 0, 0, .2); transition: width .2s; }
    @media (max-width: 1199px) { .sd-wrap { flex-direction: column; } .sd-side { flex: none; max-width: none; width: 100%; } .sd-preview { position: static; width: 100%; } }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-paint-brush"></i> Website design</h1>
    </section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($is_platform && !$uses_theme) { ?>
            <div class="alert alert-info">The platform's own website uses its own design. These themes are for school websites.</div>
        <?php } else { ?>
        <div class="sd-wrap">
            <div class="sd-side">
                <form method="post" action="<?php echo site_url('admin/front/design/save'); ?>" id="sdForm">
                    <input type="hidden" name="form_token" value="<?php echo $e($token); ?>">
                    <input type="hidden" name="theme" id="sdTheme" value="<?php echo $e($current['theme']); ?>">
                    <input type="hidden" name="options" id="sdOptions" value="">
                    <div class="sd-box">
                        <h3>1. Choose a theme</h3>
                        <div class="sd-box-body">
                            <div class="sd-themes" role="radiogroup" aria-label="Themes">
                                <?php foreach ($themes as $key => $t) {
                                    $o = $t['options'];
                                    $hdr = $o['header_style'] === 'brand' ? $o['primary'] : ($o['header_style'] === 'dark' ? '#1f2933' : '#ffffff');
                                    $ftr = $o['footer'] === 'brand' ? $o['primary'] : ($o['footer'] === 'dark' ? '#1f2933' : '#eef1f4');
                                    $font = $fonts[$o['heading_font']][2]; ?>
                                    <button type="button" class="sd-theme<?php echo $key === $current['theme'] ? ' active' : ''; ?>" data-theme="<?php echo $e($key); ?>" role="radio" aria-checked="<?php echo $key === $current['theme'] ? 'true' : 'false'; ?>">
                                        <span class="sd-check"><i class="fa fa-check"></i></span>
                                        <span class="sd-mock" aria-hidden="true">
                                            <span class="sd-mock-bar" style="background:<?php echo $e($hdr); ?>;border-bottom:1px solid #e5e7eb"><i style="background:<?php echo $e($o['primary']); ?>"></i><i style="background:<?php echo $e($o['accent']); ?>"></i></span>
                                            <span class="sd-mock-hero" style="background:<?php echo $e($o['primary']); ?>12">
                                                <b style="font-family:<?php echo $e($font); ?>;color:<?php echo $e($o['primary']); ?>">Our school</b>
                                                <span style="background:<?php echo $e($o['primary']); ?>;border-radius:<?php echo $o['buttons'] === 'pill' ? '6px' : ($o['corners'] === 'square' ? '0' : '3px'); ?>"></span>
                                            </span>
                                            <span class="sd-mock-foot" style="background:<?php echo $e($ftr); ?>"></span>
                                        </span>
                                        <span class="sd-theme-name"><b><?php echo $e($t['label']); ?></b><small><?php echo $e($t['description']); ?></small></span>
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
                                        <?php if ($f['type'] === 'check') { ?>
                                            <label style="font-weight:400;font-size:13px"><input type="checkbox" id="<?php echo $id; ?>"> <?php echo $e($f['label']); ?></label>
                                        <?php } else { ?>
                                            <label for="<?php echo $id; ?>"><?php echo $e($f['label']); ?></label>
                                            <?php if ($f['type'] === 'color') { ?>
                                                <div class="sd-color"><input type="color" id="<?php echo $id; ?>" aria-label="<?php echo $e($f['label']); ?>"><input type="text" class="form-control input-sm sd-hex" maxlength="7" aria-label="<?php echo $e($f['label']); ?> code"></div>
                                            <?php } elseif ($f['type'] === 'font') { ?>
                                                <select id="<?php echo $id; ?>" class="form-control input-sm"><?php foreach ($fonts as $k => $font) { ?><option value="<?php echo $e($k); ?>"><?php echo $e($font[0]); ?></option><?php } ?></select>
                                            <?php } elseif ($f['type'] === 'select') { ?>
                                                <select id="<?php echo $id; ?>" class="form-control input-sm"><?php foreach ($f['choices'] as $k => $label) { ?><option value="<?php echo $e($k); ?>"><?php echo $e($label); ?></option><?php } ?></select>
                                            <?php } else { ?>
                                                <input type="text" id="<?php echo $id; ?>" class="form-control input-sm" maxlength="<?php echo (int) (isset($f['max']) ? $f['max'] : 200); ?>">
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if (!empty($f['help'])) { ?><p class="help-block"><?php echo $e($f['help']); ?></p><?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <p class="help-block">The logo, favicon, social links and footer text are in Front CMS &gt; Setting. Banner pictures are in Front CMS &gt; Banner Images.</p>
                        </div>
                        <div class="sd-actions">
                            <?php if ($can_edit) { ?><button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save and publish</button><?php } ?>
                            <button type="button" class="btn btn-default" id="sdReset">Reset to the theme's settings</button>
                        </div>
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
                    <a class="btn btn-link btn-sm" href="<?php echo $e($site_url); ?>" target="_blank" rel="noopener">Open the website</a>
                </div>
                <div class="sd-frame-wrap"><iframe id="sdFrame" title="Website preview"></iframe></div>
            </div>
        </div>
        <?php } ?>
    </section>
</div>
<?php if (!($is_platform && !$uses_theme)) { ?>
<script>
(function ($) {
    'use strict';
    var THEMES = <?php echo json_encode($themes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var state = { theme: <?php echo json_encode($current['theme']); ?>, options: <?php echo json_encode(array_diff_key($current, array('theme' => 1)), JSON_HEX_TAG | JSON_HEX_AMP); ?> };
    var token = <?php echo json_encode($token); ?>, timer = null;
    var siteUrl = <?php echo json_encode($site_url); ?>;
    var frame = document.getElementById('sdFrame');

    function fill() {
        $('#sdFields .sd-field').each(function () {
            var name = $(this).data('name'), type = $(this).data('type'), v = state.options[name];
            var $in = $('#sd_' + name);
            if (type === 'check') { $in.prop('checked', !!v); }
            else if (type === 'color') { $in.val(v); $(this).find('.sd-hex').val(v); }
            else { $in.val(v); }
        });
        $('#sdTheme').val(state.theme);
        $('.sd-theme').removeClass('active').attr('aria-checked', 'false').filter('[data-theme="' + state.theme + '"]').addClass('active').attr('aria-checked', 'true');
    }

    function refresh() {
        clearTimeout(timer);
        timer = setTimeout(function () {
            $.post('<?php echo site_url('admin/front/design/preview'); ?>', { form_token: token, theme: state.theme, options: JSON.stringify(state.options) }, function () {
                frame.src = siteUrl + '?st_preview=1&t=' + Date.now();
            }, 'json');
        }, 350);
    }

    $('.sd-theme').on('click', function () {
        state.theme = $(this).data('theme');
        state.options = $.extend({}, THEMES[state.theme].options);
        fill();
        refresh();
    });
    $('#sdReset').on('click', function () {
        state.options = $.extend({}, THEMES[state.theme].options);
        fill();
        refresh();
    });
    $('#sdFields').on('input change', 'input, select', function () {
        var $f = $(this).closest('.sd-field'), name = $f.data('name'), type = $f.data('type'), v;
        if (type === 'check') { v = this.checked ? 1 : 0; }
        else if (type === 'color') {
            v = $(this).val();
            if (!/^#[0-9a-fA-F]{6}$/.test(v)) { return; }
            $f.find('input').not(this).val(v);
        } else { v = $(this).val(); }
        state.options[name] = v;
        refresh();
    });
    $('.sd-bar').on('click', '[data-width]', function () {
        $(this).addClass('active').siblings().removeClass('active');
        frame.style.width = $(this).data('width');
    });
    $('#sdForm').on('submit', function () { $('#sdOptions').val(JSON.stringify(state.options)); });

    fill();
    refresh();
})(jQuery);
</script>
<?php } ?>
