<?php
/*
 * One CBT as a card (CBT Exams > subject, Report Cards > Results > subject): state, window, questions,
 * who took it, the report card exam it sends scores to, and its actions. $o (Cbt_admin::rows),
 * $return (page to come back to), $class_id, $subject_id, $targets (optional: Cbtexams::targets).
 */
$st = array('draft' => array('Not open', '#64748b', '#f1f5f9'), 'upcoming' => array('Opens soon', '#1d4ed8', '#dbeafe'), 'open' => array('Open now', '#15803d', '#dcfce7'), 'closed' => array('Closed', '#92400e', '#fef3c7'));
$s = $st[$o['state']];
$mins = (int) substr($o['duration'], 0, 2) * 60 + (int) substr($o['duration'], 3, 2);
$can_edit = $this->rbac->hasPrivilege('online_examination', 'can_edit');
$can_q = $this->rbac->hasPrivilege('add_questions_in_exam', 'can_edit');
$hidden = function () use ($o, $return, $class_id) {
    return '<input type="hidden" name="id" value="' . (int) $o['id'] . '"><input type="hidden" name="return" value="' . html_escape($return) . '"><input type="hidden" name="class_id" value="' . (int) $class_id . '">';
};
$dt = function ($v) { return date('Y-m-d\TH:i', strtotime($v)); };
?>
<div class="cbt-card">
    <div class="cbt-head">
        <div>
            <b class="cbt-title"><?php echo html_escape($o['exam']); ?></b>
            <div class="cbt-sub"><?php echo date('D j M, g:i a', strtotime($o['exam_from'])); ?> – <?php echo date('D j M, g:i a', strtotime($o['exam_to'])); ?> · <?php echo $mins; ?> min · <?php echo (int) $o['attempt']; ?> attempt<?php echo (int) $o['attempt'] === 1 ? '' : 's'; ?></div>
        </div>
        <span class="cbt-state" style="color:<?php echo $s[1]; ?>;background:<?php echo $s[2]; ?>"><?php echo $s[0]; ?></span>
    </div>
    <div class="cbt-figs">
        <div><b><?php echo (int) $o['questions']; ?></b><small>questions</small></div>
        <div><b><?php echo rtrim(rtrim(number_format((float) $o['total_marks'], 2, '.', ''), '0'), '.') ?: 0; ?></b><small>marks</small></div>
        <div><b><?php echo (int) $o['taken']; ?>/<?php echo (int) $o['students']; ?></b><small>taken</small></div>
        <div><b><?php echo $o['average'] === null ? '–' : $o['average'] . '%'; ?></b><small>average</small></div>
    </div>
    <div class="cbt-link"><i class="fa fa-file-text-o"></i>
        <?php echo $o['link'] ? 'Scores go to the report card: <b>' . html_escape($o['link']['term_name'] . ' — ' . $o['link']['assessment']) . '</b> (scaled to ' . (float) $o['link']['maximum_marks'] . ')' : 'Practice: not on a report card'; ?>
    </div>
    <div class="cbt-actions">
        <?php if ($can_q) { ?>
            <a class="btn btn-default btn-xs" href="<?php echo site_url('admin/question/bulk?exam=' . (int) $o['id'] . '&subject=' . (int) $o['subject_id'] . '&return=' . rawurlencode($return)); ?>"><i class="fa fa-pencil"></i> Write questions</a>
            <details class="cbt-pop"><summary class="btn btn-default btn-xs"><i class="fa fa-random"></i> From question bank</summary><div>
                <?php echo form_open('admin/cbtexams/draw'); echo $hidden(); ?>
                <b>Pick questions at random</b>
                <div class="cbt-row"><label>Easy<input type="number" name="low" min="0" max="200" value="0" class="form-control input-sm"></label><label>Medium<input type="number" name="medium" min="0" max="200" value="10" class="form-control input-sm"></label><label>Hard<input type="number" name="high" min="0" max="200" value="0" class="form-control input-sm"></label><label>Marks each<input type="number" name="marks" min="0.25" step="0.25" value="1" class="form-control input-sm"></label></div>
                <button class="btn btn-primary btn-xs btn-block">Add questions</button>
                <?php echo form_close(); ?>
            </div></details>
        <?php } ?>
        <?php if ($can_edit) { ?>
            <details class="cbt-pop"><summary class="btn btn-default btn-xs"><i class="fa fa-cog"></i> Settings</summary><div>
                <?php echo form_open('admin/cbtexams/update'); echo $hidden(); ?>
                <label class="cbt-l">Title<input name="title" class="form-control input-sm" value="<?php echo html_escape($o['exam']); ?>"></label>
                <div class="cbt-row"><label>Opens<input type="datetime-local" name="from" class="form-control input-sm" value="<?php echo $dt($o['exam_from']); ?>"></label><label>Closes<input type="datetime-local" name="to" class="form-control input-sm" value="<?php echo $dt($o['exam_to']); ?>"></label></div>
                <div class="cbt-row"><label>Minutes<input type="number" name="minutes" min="1" max="600" class="form-control input-sm" value="<?php echo $mins; ?>"></label><label>Attempts<input type="number" name="attempts" min="1" max="10" class="form-control input-sm" value="<?php echo (int) $o['attempt']; ?>"></label></div>
                <?php if (!empty($targets)) { ?>
                    <label class="cbt-l">Scores go to
                        <select name="target" class="form-control input-sm">
                            <option value="">Practice (not on a report card)</option>
                            <?php foreach ($targets as $k => $t) { if ($t['taken'] && $k !== $o['marks_target']) { continue; } ?><option value="<?php echo html_escape($k); ?>" <?php echo $k === $o['marks_target'] ? 'selected' : ''; ?>><?php echo html_escape($t['label']); ?></option><?php } ?>
                        </select>
                    </label>
                <?php } ?>
                <button class="btn btn-primary btn-xs btn-block">Save</button>
                <?php echo form_close(); ?>
            </div></details>
            <?php echo form_open('admin/cbtexams/toggle', array('style' => 'display:inline')); echo $hidden(); ?><input type="hidden" name="on" value="<?php echo (int) $o['is_active'] ? 0 : 1; ?>"><button class="btn btn-xs <?php echo (int) $o['is_active'] ? 'btn-default' : 'btn-success'; ?>"><i class="fa <?php echo (int) $o['is_active'] ? 'fa-stop' : 'fa-play'; ?>"></i> <?php echo (int) $o['is_active'] ? 'Close' : 'Open'; ?></button><?php echo form_close(); ?>
            <?php if ($o['link'] && $o['taken']) { ?><?php echo form_open('admin/cbtexams/pull', array('style' => 'display:inline')); echo $hidden(); ?><button class="btn btn-default btn-xs" title="Send every finished attempt's score to the report card now"><i class="fa fa-download"></i> Pull scores</button><?php echo form_close(); ?><?php } ?>
        <?php } ?>
        <?php if ($o['to_mark']) { ?><a class="btn btn-warning btn-xs" href="<?php echo site_url('admin/onlineexam/evalution/' . (int) $o['id']); ?>" title="Written answers wait for a teacher's mark; their scores go to the report card once marked"><i class="fa fa-check-square-o"></i> Mark written answers (<?php echo (int) $o['to_mark']; ?>)</a><?php } ?>
        <a class="btn btn-default btn-xs" href="<?php echo site_url('admin/onlineexam/results/' . (int) $o['id']); ?>"><i class="fa fa-bar-chart"></i> Results</a>
        <a class="btn btn-default btn-xs" href="<?php echo site_url('admin/onlineexam/invigilate/' . (int) $o['id']); ?>"><i class="fa fa-eye"></i> Invigilate</a>
    </div>
</div>
