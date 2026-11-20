<?php
/*
 * Subjects of a Report Cards exam with their score status (Cbse_marks_guard): Draft, Submitted for
 * review, Approved & locked. Marks opens the entry (#subjectModal); teachers submit, exam managers
 * approve / return / unlock (one subject or all submitted), and anyone with marks access sees the
 * change history. The JS is in cbseexam/exam/index.php (.sx-*).
 */
$sx_badge = array(
    'draft' => array('Draft', 'sx-draft', 'fa-pencil'),
    'submitted' => array('Submitted for review', 'sx-sub', 'fa-hourglass-half'),
    'approved' => array('Approved & locked', 'sx-ok', 'fa-lock'),
);
$sx_counts = array('draft' => 0, 'submitted' => 0, 'approved' => 0);
foreach ((array) $exam_subjects as $s) {
    $st = isset($s->marks_status) && isset($sx_counts[$s->marks_status]) ? $s->marks_status : 'draft';
    $sx_counts[$st]++;
}
$sx_can_enter = $this->rbac->hasPrivilege('cbse_exam_marks', 'can_edit');
?>
<style>
    .sx-badge{display:inline-flex;align-items:center;gap:5px;border-radius:999px;padding:3px 10px;font-size:12px;font-weight:600;white-space:nowrap}
    .sx-draft{background:#f1f5f9;color:#475569}
    .sx-sub{background:#fef3c7;color:#92400e}
    .sx-ok{background:#dcfce7;color:#15803d}
    .sx-sum{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:0 0 12px}
    .sx-sum .sx-badge{font-size:13px;padding:5px 12px}
    .sx-sum .sx-gap{flex:1}
    .sx-actions{display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap}
    .sx-when{display:block;font-size:11px;color:#94a3b8;margin-top:3px}
</style>
<div class="row pb10">
    <div class="col-lg-2 col-md-3 col-sm-12">
        <p class="examinfo"><span><?php echo $this->lang->line('exam') ?></span><?php echo $examDetail['name']; ?></p>
    </div>
    <div class="col-lg-10 col-md-9 col-sm-12">
        <p class="examinfo"><span><?php echo $this->lang->line('class_section'); ?></span><?php echo $examDetail['class_sections']; ?></p>
    </div>
</div><!--./row-->
<div class="divider2"></div>
<div class="sx-sum">
    <?php foreach ($sx_badge as $k => $b) { ?>
        <span class="sx-badge <?php echo $b[1]; ?>"><i class="fa <?php echo $b[2]; ?>"></i> <?php echo $sx_counts[$k] . ' ' . $b[0]; ?></span>
    <?php } ?>
    <span class="sx-gap"></span>
    <button type="button" class="btn btn-default btn-sm sx-history" data-exam_id="<?php echo $examDetail['id']; ?>"><i class="fa fa-history"></i> Change history</button>
    <?php if (!empty($can_approve) && $sx_counts['submitted'] + $sx_counts['draft'] > 0) { ?>
        <button type="button" class="btn btn-success btn-sm sx-approve-all" data-exam_id="<?php echo $examDetail['id']; ?>"
            data-ids="<?php echo html_escape(json_encode(array_values(array_map(function ($s) { return (int) $s->id; }, array_filter((array) $exam_subjects, function ($s) { return isset($s->marks_status) && $s->marks_status === 'submitted'; }))))); ?>"
            <?php echo $sx_counts['submitted'] ? '' : 'disabled title="No subject is waiting for review"'; ?>><i class="fa fa-check-circle"></i> Approve all submitted (<?php echo $sx_counts['submitted']; ?>)</button>
    <?php } ?>
</div>
<?php if (!empty($examDetail['is_publish'])) { ?>
    <div class="alert alert-info" style="border-radius:10px"><i class="fa fa-globe"></i> The results of this exam are published to students and parents. Only an exam manager can change scores now; every change is recorded.</div>
<?php } elseif ($sx_counts['approved'] < count((array) $exam_subjects) && count((array) $exam_subjects)) { ?>
    <div class="alert alert-warning" style="border-radius:10px"><i class="fa fa-info-circle"></i> Publish the results once every subject is approved and locked (<?php echo $sx_counts['approved']; ?> of <?php echo count((array) $exam_subjects); ?> so far).</div>
<?php } ?>
<div class="table-responsive row">
    <table class="table table-bordered" id="subjects_table">
        <thead>
            <tr>
                <th class="col-sm-3"><?php echo $this->lang->line('subject') ?></th>
                <th class=""><?php echo $this->lang->line('date'); ?></th>
                <th class=""><?php echo $this->lang->line('start_time'); ?></th>
                <th class=""><?php echo $this->lang->line('room_no') ?></th>
                <th>Scores</th>
                <th class="text-right"><?php echo $this->lang->line('enter_marks'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($exam_subjects)) {
                foreach ($exam_subjects as $exam_subject_key => $exam_subject_value) {
                    $exam_subject_value = (array) $exam_subject_value;
                    $st = isset($exam_subject_value['marks_status']) && isset($sx_badge[$exam_subject_value['marks_status']]) ? $exam_subject_value['marks_status'] : 'draft';
                    $b = $sx_badge[$st];
                    $when = $st === 'approved' ? $exam_subject_value['marks_approved_at'] : ($st === 'submitted' ? $exam_subject_value['marks_submitted_at'] : null);
                    $sub_code = ($exam_subject_value['subject_code'] != "") ? " (" . $exam_subject_value['subject_code'] . ")" : "";
                    ?>
                    <tr>
                        <td><?php echo html_escape($exam_subject_value['subject_name'] . $sub_code); ?></td>
                        <td><?php echo $this->customlib->dateformat($exam_subject_value['date']); ?></td>
                        <td><?php echo $exam_subject_value['time_from']; ?></td>
                        <td><?php echo $exam_subject_value['room_no']; ?></td>
                        <td>
                            <span class="sx-badge <?php echo $b[1]; ?>"><i class="fa <?php echo $b[2]; ?>"></i> <?php echo $b[0]; ?></span>
                            <?php if ($when) { ?><span class="sx-when"><?php echo date('j M Y, H:i', strtotime($when)); ?></span><?php } ?>
                        </td>
                        <td class="text-right">
                            <div class="sx-actions">
                                <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#subjectModal" data-subject_name="<?php echo html_escape($exam_subject_value['subject_name']); ?>" data-exam_id="<?php echo $examDetail['id']; ?>" data-subject_id="<?php echo $exam_subject_value['subject_id']; ?>" data-timetable_id="<?php echo $exam_subject_value['id']; ?>" title="<?php echo $st === 'draft' ? 'Enter scores' : 'View scores'; ?>"><i class="fa <?php echo $st === 'draft' ? 'fa-pencil-square-o' : 'fa-eye'; ?>" aria-hidden="true"></i> Scores</button>
                                <?php if ($st === 'draft' && $sx_can_enter) { ?>
                                    <button type="button" class="btn btn-info btn-xs sx-act" data-action="submit" data-id="<?php echo $exam_subject_value['id']; ?>" data-exam_id="<?php echo $examDetail['id']; ?>" title="Send these scores for review"><i class="fa fa-paper-plane"></i> Submit</button>
                                <?php } ?>
                                <?php if (!empty($can_approve) && $st !== 'approved') { ?>
                                    <button type="button" class="btn btn-success btn-xs sx-act" data-action="approve" data-id="<?php echo $exam_subject_value['id']; ?>" data-exam_id="<?php echo $examDetail['id']; ?>" title="Approve and lock"><i class="fa fa-lock"></i> Approve</button>
                                <?php } ?>
                                <?php if (!empty($can_approve) && $st === 'submitted') { ?>
                                    <button type="button" class="btn btn-warning btn-xs sx-act" data-action="return" data-id="<?php echo $exam_subject_value['id']; ?>" data-exam_id="<?php echo $examDetail['id']; ?>" title="Return to the teacher for changes"><i class="fa fa-reply"></i> Return</button>
                                <?php } ?>
                                <?php if (!empty($can_approve) && $st === 'approved') { ?>
                                    <button type="button" class="btn btn-warning btn-xs sx-act" data-action="unlock" data-id="<?php echo $exam_subject_value['id']; ?>" data-exam_id="<?php echo $examDetail['id']; ?>" title="Unlock to make a change"><i class="fa fa-unlock"></i> Unlock</button>
                                <?php } ?>
                                <button type="button" class="btn btn-default btn-xs sx-history" data-timetable_id="<?php echo $exam_subject_value['id']; ?>" data-name="<?php echo html_escape($exam_subject_value['subject_name']); ?>" title="Change history"><i class="fa fa-history"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>
