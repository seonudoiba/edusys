<script src="<?php echo base_url(); ?>backend/plugins/ckeditor/ckeditor.js"></script>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-book"></i> Reading passages</h1>
    </section>
    <section class="content">
        <?php if ($this->session->flashdata('msg')) { echo $this->session->flashdata('msg'); } ?>
        <div class="row">
            <?php if (($edit && $can_edit) || (!$edit && $can_add)) { ?>
                <div class="col-md-5">
                    <div class="box box-primary">
                        <div class="box-header with-border"><h3 class="box-title"><?php echo $edit ? 'Edit passage' : 'Add a passage'; ?></h3></div>
                        <form method="post" action="<?php echo site_url('admin/question_passage/save'); ?>">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <input type="hidden" name="id" value="<?php echo $edit ? (int) $edit['id'] : ''; ?>">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="p_title">Title</label><small class="req"> *</small>
                                    <input id="p_title" name="title" class="form-control" maxlength="255" required value="<?php echo $edit ? html_escape($edit['title']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="p_subject">Subject</label>
                                    <select id="p_subject" name="subject_id" class="form-control">
                                        <option value="">Any</option>
                                        <?php foreach ($subjects as $s) { ?>
                                            <option value="<?php echo (int) $s['id']; ?>" <?php echo $edit && (int) $edit['subject_id'] === (int) $s['id'] ? 'selected' : ''; ?>><?php echo html_escape($s['name'] . ($s['code'] ? ' (' . $s['code'] . ')' : '')); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="p_body">Passage</label><small class="req"> *</small>
                                    <textarea id="p_body" name="body" class="form-control" rows="10"><?php echo $edit ? html_escape($edit['body']) : ''; ?></textarea>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary">Save passage</button>
                                <?php if ($edit) { ?><a href="<?php echo site_url('admin/question_passage'); ?>" class="btn btn-default">Cancel</a><?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-7">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Passages</h3>
                        <div class="box-tools pull-right"><a href="<?php echo site_url('admin/question'); ?>" class="btn btn-default btn-sm">Question bank</a></div>
                    </div>
                    <div class="box-body">
                        <?php if (!$passages) { ?>
                            <p class="text-muted">No passages yet. Add one, then choose it on each question that belongs to it. You can also import passages with questions from Excel.</p>
                        <?php } else { ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead><tr><th>Title</th><th>Subject</th><th>Questions</th><th class="text-right"></th></tr></thead>
                                    <tbody>
                                        <?php foreach ($passages as $p) { ?>
                                            <tr>
                                                <td><?php echo html_escape($p['title']); ?></td>
                                                <td><?php echo html_escape((string) $p['subject']); ?></td>
                                                <td><?php echo (int) $p['questions']; ?></td>
                                                <td class="text-right" style="white-space:nowrap">
                                                    <?php if ($can_edit) { ?><a class="btn btn-default btn-xs" href="<?php echo site_url('admin/question_passage/index/' . (int) $p['id']); ?>" title="Edit"><i class="fa fa-pencil"></i></a><?php } ?>
                                                    <?php if ($can_delete) { ?>
                                                        <form method="post" action="<?php echo site_url('admin/question_passage/delete/' . (int) $p['id']); ?>" style="display:inline" onsubmit="return confirm('Delete this passage? Its questions stay in the bank without it.');">
                                                            <?php echo $this->customlib->getCSRF(); ?>
                                                            <button type="submit" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-remove"></i></button>
                                                        </form>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    if (document.getElementById('p_body') && window.CKEDITOR) {
        CKEDITOR.env.isCompatible = true;
        CKEDITOR.replace('p_body', { allowedContent: true, extraPlugins: 'ckeditor_wiris', customConfig: '<?php echo base_url(); ?>backend/js/ckeditor_config.js' });
    }
</script>
