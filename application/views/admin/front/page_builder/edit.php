<?php
$e = function ($v) { return html_escape((string) $v); };
$flash = $this->session->flashdata('pb_msg');
$js_file = 'backend/js/block_editor.js';
$css_file = 'backend/dist/css/block_editor.css';
?>
<link rel="stylesheet" href="<?php echo base_url($css_file) . '?v=' . (int) @filemtime(FCPATH . $css_file); ?>">
<?php $this->load->view('front_widgets/styles'); ?>
<div class="content-wrapper be-wrapper">
    <div class="be-topbar" role="toolbar" aria-label="Editor">
        <button type="button" class="be-tb-btn be-tb-primary" id="beToggleInserter" aria-expanded="true" aria-controls="beInserter" title="Add a block (/)">
            <i class="fa fa-plus"></i><span class="be-hide-sm"> Add</span>
        </button>
        <button type="button" class="be-tb-btn" id="beUndo" title="Undo (Ctrl+Z)" aria-label="Undo" disabled><i class="fa fa-undo"></i></button>
        <button type="button" class="be-tb-btn" id="beRedo" title="Redo (Ctrl+Shift+Z)" aria-label="Redo" disabled><i class="fa fa-repeat"></i></button>
        <div class="be-title">
            <b><?php echo $e($page['title']); ?></b>
            <span id="beStatus" class="be-status" role="status"><?php echo $unpublished ? 'Unpublished changes' : ''; ?></span>
        </div>
        <span class="be-flex"></span>
        <button type="button" class="be-tb-btn" id="beRevisions" title="Earlier versions"><i class="fa fa-history"></i><span class="be-hide-sm"> Versions</span></button>
        <button type="button" class="be-tb-btn" id="bePreview" title="Preview on desktop, tablet and phone"><i class="fa fa-eye"></i><span class="be-hide-sm"> Preview</span></button>
        <button type="button" class="be-tb-btn" id="beSave" title="Save draft (Ctrl+S)"><i class="fa fa-save"></i><span class="be-hide-sm"> Save draft</span></button>
        <button type="button" class="be-tb-btn be-tb-publish" id="bePublish">Publish</button>
        <div class="dropdown be-more">
            <button type="button" class="be-tb-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More" aria-label="More"><i class="fa fa-ellipsis-v"></i></button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li><a href="<?php echo site_url('admin/front/page_builder/staff'); ?>"><i class="fa fa-users"></i> Staff shown on the website</a></li>
                <li><a href="<?php echo site_url('admin/front/media'); ?>" target="_blank" rel="noopener"><i class="fa fa-upload"></i> Upload images (Media Manager)</a></li>
                <li><a href="#" id="beShortcuts"><i class="fa fa-keyboard-o"></i> Keyboard shortcuts</a></li>
                <?php if (empty($page['is_homepage'])) { ?>
                    <li role="separator" class="divider"></li>
                    <li>
                        <form method="post" action="<?php echo site_url('admin/front/page_builder/classic/' . (int) $page['id']); ?>" onsubmit="return confirm('Stop using the block editor for this page? Its plain text content is shown again, and these blocks are deleted.');">
                            <input type="hidden" name="form_token" value="<?php echo $e($token); ?>">
                            <button type="submit" class="be-menu-btn text-danger"><i class="fa fa-file-text-o"></i> Use plain text instead</button>
                        </form>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div id="beNotice" class="be-notice" role="alert" hidden></div>
    <?php if ($flash) { ?><div class="be-notice be-notice-ok"><?php echo $e($flash); ?></div><?php } ?>
    <?php if (empty($page['is_homepage']) && $page['is_active'] !== 'yes') { ?>
        <div class="be-notice">This page is switched off in Front CMS &gt; Pages, so visitors can't see it even after publishing.</div>
    <?php } ?>

    <div class="be-body">
        <aside class="be-inserter" id="beInserter" aria-label="Add blocks">
            <div class="be-tabs" role="tablist">
                <button type="button" role="tab" class="be-tab active" data-tab="blocks" aria-selected="true">Blocks</button>
                <button type="button" role="tab" class="be-tab" data-tab="patterns" aria-selected="false">Patterns</button>
            </div>
            <input type="search" class="form-control be-search" id="beSearch" placeholder="Search" aria-label="Search blocks and patterns">
            <div id="beBlocksList" class="be-panel-list"></div>
            <div id="bePatternsList" class="be-panel-list" hidden></div>
        </aside>

        <main class="be-main" id="beMain">
            <div class="be-canvas" id="beCanvas" aria-label="Page content"></div>
        </main>

        <aside class="be-inspector" id="beInspector" aria-label="Block settings">
            <div id="beInspectorBody"></div>
        </aside>
    </div>
</div>

<div class="modal fade" id="beMedia" tabindex="-1" role="dialog" aria-labelledby="beMediaTitle">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title modal-media-title" id="beMediaTitle">Choose an image</h4>
            </div>
            <div class="modal-body modal-media-body pupscroll"></div>
            <div class="modal-footer">
                <span class="text-muted pull-left" style="margin-top:7px">New images: Front CMS &gt; Media Manager.</span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="beMediaUse">Use image</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bePreviewModal" tabindex="-1" role="dialog" aria-labelledby="bePreviewTitle">
    <div class="modal-dialog be-preview-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="bePreviewTitle">Preview of the draft</h4>
                <div class="btn-group btn-group-sm be-devices" role="group" aria-label="Screen size">
                    <button type="button" class="btn btn-default active" data-width="100%"><i class="fa fa-desktop"></i> Desktop</button>
                    <button type="button" class="btn btn-default" data-width="768px"><i class="fa fa-tablet"></i> Tablet</button>
                    <button type="button" class="btn btn-default" data-width="390px"><i class="fa fa-mobile"></i> Phone</button>
                </div>
                <a class="btn btn-link btn-sm" id="bePreviewTab" href="#" target="_blank" rel="noopener">Open in a new tab</a>
            </div>
            <div class="modal-body be-preview-body"><iframe id="bePreviewFrame" title="Page preview"></iframe></div>
        </div>
    </div>
</div>

<div class="modal fade" id="beRevisionsModal" tabindex="-1" role="dialog" aria-labelledby="beRevisionsTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="beRevisionsTitle">Earlier versions</h4>
            </div>
            <div class="modal-body" id="beRevisionsBody"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="beShortcutsModal" tabindex="-1" role="dialog" aria-labelledby="beShortcutsTitle">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="beShortcutsTitle">Keyboard shortcuts</h4>
            </div>
            <div class="modal-body">
                <table class="table table-condensed be-keys">
                    <tr><td><kbd>/</kbd> in an empty paragraph</td><td>Choose a block</td></tr>
                    <tr><td><kbd>Enter</kbd></td><td>New paragraph</td></tr>
                    <tr><td><kbd>Shift</kbd>+<kbd>Enter</kbd></td><td>New line</td></tr>
                    <tr><td><kbd>Ctrl</kbd>+<kbd>B</kbd> / <kbd>I</kbd> / <kbd>K</kbd></td><td>Bold / italic / link</td></tr>
                    <tr><td><kbd>Ctrl</kbd>+<kbd>Z</kbd></td><td>Undo</td></tr>
                    <tr><td><kbd>Ctrl</kbd>+<kbd>Shift</kbd>+<kbd>Z</kbd></td><td>Redo</td></tr>
                    <tr><td><kbd>Alt</kbd>+<kbd>&uarr;</kbd> / <kbd>&darr;</kbd></td><td>Move the block</td></tr>
                    <tr><td><kbd>Ctrl</kbd>+<kbd>Shift</kbd>+<kbd>D</kbd></td><td>Duplicate the block</td></tr>
                    <tr><td><kbd>Ctrl</kbd>+<kbd>S</kbd></td><td>Save draft</td></tr>
                    <tr><td><kbd>Esc</kbd></td><td>Deselect</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>backend/dist/js/jquery-ui.min.js"></script>
<script>
    window.BE = <?php echo json_encode(array(
        'catalogue' => $catalogue,
        'layouts' => $layouts,
        'layout' => $layout,
        'patterns' => $patterns,
        'savedPatterns' => $saved_patterns,
        'formsAllowed' => $forms_allowed,
        'formTypes' => array_keys(Page_widgets::FORMS),
        'token' => $token,
        'baseUrl' => base_url(),
        'saveUrl' => site_url('admin/front/page_builder/save/' . (int) $page['id']),
        'renderUrl' => site_url('admin/front/page_builder/render'),
        'revisionsUrl' => site_url('admin/front/page_builder/revisions/' . (int) $page['id']),
        'revisionUrl' => site_url('admin/front/page_builder/revision/' . (int) $page['id']),
        'patternSaveUrl' => site_url('admin/front/page_builder/save_pattern'),
        'patternDeleteUrl' => site_url('admin/front/page_builder/delete_pattern'),
        'previewUrl' => $preview_url,
        'mediaUrl' => site_url('admin/front/media/getMedia'),
    ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<script src="<?php echo base_url($js_file) . '?v=' . (int) @filemtime(FCPATH . $js_file); ?>"></script>
