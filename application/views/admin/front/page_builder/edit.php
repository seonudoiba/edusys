<?php
$e = function ($v) { return html_escape((string) $v); };
$groups = array('dynamic' => 'From your school\'s data', 'platform' => 'Platform website', 'content' => 'Content');
$flash = $this->session->flashdata('pb_msg');
$js_file = 'backend/js/page_builder.js';
?>
<style>
    .pb-wrap { display: flex; gap: 14px; align-items: flex-start; }
    .pb-side { flex: 0 0 240px; position: sticky; top: 60px; max-height: calc(100vh - 80px); overflow-y: auto; background: #fff; border: 1px solid #e3e6ea; border-radius: 4px; padding: 10px; }
    .pb-main { flex: 1 1 auto; min-width: 0; }
    .pb-side h5 { margin: 12px 0 6px; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase; }
    .pb-tool { display: block; width: 100%; text-align: left; padding: 7px 9px; margin-bottom: 5px; background: #f7f8fa; border: 1px solid #e3e6ea; border-radius: 3px; cursor: grab; font-size: 13px; color: #333; z-index: 2000; }
    .pb-tool:hover { background: #eef3f8; }
    .pb-tool i { width: 18px; color: #3c8dbc; }
    .pb-tool.ui-draggable-dragging { width: 200px; box-shadow: 0 3px 10px rgba(0, 0, 0, .25); }
    .pb-tool[disabled] { opacity: .45; cursor: not-allowed; }
    .pb-section { background: #fff; border: 1px solid #d9dee4; border-radius: 4px; margin-bottom: 12px; }
    .pb-section.pb-bg-light { background: #f5f7fa; }
    .pb-section.pb-bg-dark { background: #e3e7ec; }
    .pb-sec-bar { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; padding: 6px 8px; border-bottom: 1px solid #e3e6ea; background: rgba(0, 0, 0, .03); font-size: 12px; }
    .pb-sec-bar select { width: auto; height: 26px; padding: 0 4px; font-size: 12px; }
    .pb-handle { cursor: grab; color: #888; padding: 0 4px; }
    .pb-spacer { flex: 1; }
    .pb-cols { display: flex; gap: 8px; padding: 8px; flex-wrap: wrap; }
    .pb-col { min-height: 62px; min-width: 120px; padding: 6px; border: 1px dashed #c9d0d8; border-radius: 3px; background: rgba(255, 255, 255, .6); }
    .pb-col:empty::before { content: "Drop widgets here"; display: block; text-align: center; color: #9aa3ad; padding: 14px 0; font-size: 12px; }
    .pb-widget { display: flex; align-items: center; gap: 6px; padding: 7px 8px; margin-bottom: 5px; background: #fff; border: 1px solid #d9dee4; border-left: 3px solid #3c8dbc; border-radius: 3px; cursor: grab; font-size: 13px; }
    .pb-widget .pb-name { flex: 1; min-width: 0; overflow: hidden; }
    .pb-widget small { display: block; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .pb-widget .btn-link { padding: 0 3px; color: #666; }
    .pb-ph { height: 40px; margin-bottom: 5px; border: 2px dashed #3c8dbc; background: #eef6fb; border-radius: 3px; }
    .pb-sec-ph { height: 80px; margin-bottom: 12px; border: 2px dashed #3c8dbc; background: #eef6fb; border-radius: 4px; }
    .pb-actions { position: sticky; top: 50px; z-index: 20; display: flex; flex-wrap: wrap; gap: 6px; align-items: center; padding: 8px 10px; background: #fff; border: 1px solid #e3e6ea; border-radius: 4px; margin-bottom: 12px; }
    .pb-item { border: 1px solid #e3e6ea; border-radius: 3px; padding: 8px; margin-bottom: 8px; background: #fafbfc; }
    #pbMedia { z-index: 1070; }
    @media (max-width: 991px) { .pb-wrap { flex-direction: column; } .pb-side { position: static; max-height: none; flex: none; width: 100%; } }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-th-large"></i> Page builder <small><?php echo $e($page['title']); ?></small></h1>
    </section>
    <section class="content">
        <?php if ($flash) { ?><div class="alert alert-success"><?php echo $e($flash); ?></div><?php } ?>
        <?php if (empty($page['is_homepage']) && $page['is_active'] !== 'yes') { ?>
            <div class="alert alert-warning">This page is switched off in Front CMS &gt; Pages, so visitors can't see it even after publishing.</div>
        <?php } ?>
        <div class="pb-actions">
            <b style="margin-right:6px"><?php echo $e($page['title']); ?></b>
            <button type="button" class="btn btn-primary btn-sm" id="pbPublish"><i class="fa fa-globe"></i> Publish</button>
            <button type="button" class="btn btn-default btn-sm" id="pbSave"><i class="fa fa-save"></i> Save draft</button>
            <button type="button" class="btn btn-default btn-sm" id="pbPreview"><i class="fa fa-eye"></i> Preview</button>
            <button type="button" class="btn btn-default btn-sm" id="pbAddSection"><i class="fa fa-plus"></i> Add section</button>
            <span id="pbStatus" class="text-muted" role="status" style="margin-left:6px"><?php echo $unpublished ? 'You have unpublished changes.' : ''; ?></span>
            <span class="pb-spacer" style="flex:1"></span>
            <a class="btn btn-link btn-sm" href="<?php echo site_url('admin/front/page_builder/staff'); ?>"><i class="fa fa-users"></i> Staff on website</a>
            <form method="post" action="<?php echo site_url('admin/front/page_builder/starter/' . (int) $page['id']); ?>" style="display:inline" onsubmit="return confirm('Replace the draft with the ready-made layout?');">
                <input type="hidden" name="form_token" value="<?php echo $e($token); ?>">
                <button class="btn btn-link btn-sm" type="submit">Use starter layout</button>
            </form>
            <?php if (empty($page['is_homepage'])) { ?>
                <form method="post" action="<?php echo site_url('admin/front/page_builder/classic/' . (int) $page['id']); ?>" style="display:inline" onsubmit="return confirm('Stop using the builder for this page? Its plain text content is shown again, and this layout is deleted.');">
                    <input type="hidden" name="form_token" value="<?php echo $e($token); ?>">
                    <button class="btn btn-link btn-sm text-danger" type="submit">Use plain text</button>
                </form>
            <?php } ?>
        </div>
        <div class="pb-wrap">
            <aside class="pb-side" aria-label="Widgets">
                <p class="text-muted" style="font-size:12px;margin:0">Drag a widget onto the page, or click it to add it to the last section.</p>
                <?php foreach ($groups as $group => $title) {
                    $tools = array_filter($catalogue, function ($w) use ($group) { return $w['group'] === $group; });
                    if (!$tools) { continue; } ?>
                    <h5><?php echo $e($title); ?></h5>
                    <?php foreach ($tools as $type => $w) { ?>
                        <button type="button" class="pb-tool" data-type="<?php echo $e($type); ?>" title="<?php echo $e(isset($w['hint']) ? $w['hint'] : ''); ?>">
                            <i class="fa <?php echo $e($w['icon']); ?>"></i> <?php echo $e($w['label']); ?>
                        </button>
                    <?php } ?>
                <?php } ?>
            </aside>
            <div class="pb-main">
                <div id="pbCanvas"></div>
                <p id="pbEmpty" class="text-muted text-center" style="padding:30px;display:none">This page is empty. Click <b>Add section</b>, then drag widgets into it.</p>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="pbSettings" tabindex="-1" role="dialog" aria-labelledby="pbSettingsTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="pbSettingsTitle"></h4>
            </div>
            <div class="modal-body" id="pbSettingsBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="pbSettingsApply">Apply</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pbMedia" tabindex="-1" role="dialog" aria-labelledby="pbMediaTitle">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title modal-media-title" id="pbMediaTitle"><?php echo $this->lang->line('media_manager'); ?></h4>
            </div>
            <div class="modal-body modal-media-body pupscroll"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="button" class="btn btn-primary" id="pbMediaUse">Use this image</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>backend/dist/js/jquery-ui.min.js"></script>
<script>
    window.PB = <?php echo json_encode(array(
        'catalogue' => $catalogue,
        'layouts' => $layouts,
        'backgrounds' => $backgrounds,
        'layout' => $layout,
        'formsAllowed' => $forms_allowed,
        'formTypes' => array_keys(Page_widgets::FORMS),
        'token' => $token,
        'saveUrl' => site_url('admin/front/page_builder/save/' . (int) $page['id']),
        'previewUrl' => $preview_url,
        'mediaUrl' => site_url('admin/front/media/getMedia'),
    ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<script src="<?php echo base_url($js_file) . '?v=' . (int) @filemtime(FCPATH . $js_file); ?>"></script>
