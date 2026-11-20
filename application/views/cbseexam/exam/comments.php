<?php
/* Report Cards > comment bank (Exam::comments): comments by average score for "Fill comments". */
$cm_can = $this->rbac->hasPrivilege('cbse_exam_teacher_remark', 'can_edit');
$cm_n = function ($v) { return rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$cm_by = array('teacher' => array(), 'principal' => array());
foreach ($rows as $r) {
    if (isset($cm_by[$r['type']])) {
        $cm_by[$r['type']][] = $r;
    }
}
?>
<style>
    .cm-card{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:18px;margin-bottom:18px}
    .cm-card h4{margin:0 0 12px;font-weight:700}
    .cm-range{display:inline-block;min-width:92px;border-radius:999px;background:#eef2ff;color:#3730a3;font-weight:700;font-size:12px;padding:3px 10px;text-align:center}
    .cm-row{display:flex;align-items:center;gap:12px;padding:9px 0;border-top:1px solid #f1f5f9}
    .cm-row:first-of-type{border-top:0}
    .cm-row .cm-text{flex:1}
    .cm-def{color:#64748b}
    .cm-add .form-control{border-radius:8px}
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-commenting-o"></i> Report card comment bank</h3>
                <div class="box-tools pull-right"><a href="<?php echo site_url('cbseexam/exam'); ?>" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> Exams</a></div>
            </div>
            <div class="box-body">
                <p class="text-muted">In Exam &gt; Teacher Remark, <b>Fill comments</b> writes each student's comments from this list, by the student's average in the exam. Teachers can still edit any comment before saving.</p>
                <?php if ($this->session->flashdata('cm_msg')) { ?><div class="alert alert-info"><?php echo html_escape($this->session->flashdata('cm_msg')); ?></div><?php } ?>
                <?php if (!$rows) { ?>
                    <div class="alert alert-warning" style="border-radius:10px">
                        Your comment bank is empty, so the suggested comments below are used.
                        <?php if ($cm_can) { ?>
                            <?php echo form_open('cbseexam/exam/comments', array('style' => 'display:inline')); ?><input type="hidden" name="do" value="seed"><button class="btn btn-xs btn-warning" style="margin-left:8px"><i class="fa fa-download"></i> Use them and edit</button><?php echo form_close(); ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="row">
                    <?php foreach ($types as $type => $label) { ?>
                        <div class="col-md-6">
                            <div class="cm-card">
                                <h4><?php echo html_escape($label); ?></h4>
                                <?php if ($cm_by[$type]) {
                                    foreach ($cm_by[$type] as $r) { ?>
                                        <div class="cm-row">
                                            <span class="cm-range"><?php echo $cm_n($r['min_percentage']) . '–' . $cm_n($r['max_percentage']); ?>%</span>
                                            <span class="cm-text"><?php echo html_escape($r['comment']); ?></span>
                                            <?php if ($cm_can) { ?>
                                                <?php echo form_open('cbseexam/exam/comments', array('style' => 'margin:0')); ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?php echo (int) $r['id']; ?>"><button class="btn btn-xs btn-default" title="Delete" onclick="return confirm('Delete this comment?')"><i class="fa fa-trash"></i></button><?php echo form_close(); ?>
                                            <?php } ?>
                                        </div>
                                    <?php }
                                } else {
                                    foreach ($defaults[$type] as $d) { ?>
                                        <div class="cm-row cm-def"><span class="cm-range"><?php echo $cm_n($d[0]) . '–' . $cm_n($d[1]); ?>%</span><span class="cm-text"><?php echo html_escape($d[2]); ?></span><small>suggested</small></div>
                                    <?php }
                                } ?>
                                <?php if ($cm_can) { ?>
                                    <?php echo form_open('cbseexam/exam/comments', array('class' => 'cm-add', 'style' => 'margin-top:12px')); ?>
                                    <input type="hidden" name="do" value="add"><input type="hidden" name="type" value="<?php echo $type; ?>">
                                    <div class="row">
                                        <div class="col-xs-3"><input type="number" name="min" class="form-control input-sm" placeholder="From %" min="0" max="100" step="0.01" required></div>
                                        <div class="col-xs-3"><input type="number" name="max" class="form-control input-sm" placeholder="To %" min="0" max="100" step="0.01" required></div>
                                        <div class="col-xs-6"><button class="btn btn-sm btn-primary btn-block"><i class="fa fa-plus"></i> Add comment</button></div>
                                    </div>
                                    <textarea name="comment" class="form-control input-sm" rows="2" maxlength="500" placeholder="Comment" required style="margin-top:6px"></textarea>
                                    <?php echo form_close(); ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>
