<?php
$groups = array(); // label => array of array(key, text)
foreach ($cbse_targets as $t) {
    $groups['CBSE Examination · ' . $t['group']][] = array($t['key'], $t['label']);
}
foreach ($targets as $t) {
    $groups['Examinations · ' . $t['exam_group'] . ' · ' . $t['exam']][] = array((string) (int) $t['id'], $t['subject'] . ' (max ' . (float) $t['max_marks'] . ')');
}
$has_targets = (bool) $groups;
$sat = (int) $summary['sat'];
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-share-square-o"></i> Send scores to report card</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <?php if ($error) { ?><div class="alert alert-danger"><?php echo html_escape($error); ?></div><?php } ?>
                <?php if ($result) { ?>
                    <div class="alert alert-success">
                        <b><?php echo (int) $result['sent']; ?></b> student<?php echo $result['sent'] === 1 ? '\'s mark was' : 's\' marks were'; ?> entered.
                        <?php
                        $notes = array();
                        if ($result['kept']) { $notes[] = $result['kept'] . ' already had a mark, which was kept'; }
                        if ($result['pending']) { $notes[] = $result['pending'] . ' still have written answers to mark'; }
                        if ($result['not_in_exam']) { $notes[] = $result['not_in_exam'] . ' are not in that exam (add them under Examinations first)'; }
                        if ($result['not_sat']) { $notes[] = $result['not_sat'] . ' did not sit the CBT'; }
                        if ($notes) { echo '<br>Not sent: ' . html_escape(implode('; ', $notes)) . '.'; }
                        ?>
                    </div>
                <?php } ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo html_escape($exam['exam']); ?></h3>
                    </div>
                    <form method="post" action="<?php echo site_url('admin/onlineexam/send_marks/' . (int) $exam['id']); ?>">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="box-body">
                            <p><?php echo $sat; ?> of <?php echo (int) $summary['assigned']; ?> students sat this CBT<?php echo $summary['max_score'] !== null ? ', out of ' . (float) $summary['max_score'] . ' marks' : ''; ?>.
                                <?php if ((int) $summary['pending']) { ?><span class="text-warning"><?php echo (int) $summary['pending']; ?> still have written answers to mark (<a href="<?php echo site_url('admin/onlineexam/evalution/' . (int) $exam['id']); ?>">mark them</a>); they are skipped until then.</span><?php } ?></p>
                            <?php if (!$has_targets) { ?>
                                <div class="alert alert-info">There are no exams for this session yet. Create one under CBSE Examination &gt; Exam (or Examinations &gt; Exam Group), add its subjects and students, then come back.</div>
                            <?php } else { ?>
                                <div class="form-group">
                                    <label for="target_id">Put the scores into</label>
                                    <p class="help-block" style="margin-top:0">A CBSE Examination assessment (e.g. First CA) or an Examinations subject.</p>
                                    <select class="form-control" id="target_id" name="target_id" required>
                                        <option value="">Choose an exam subject</option>
                                        <?php foreach ($groups as $label => $items) { ?>
                                            <optgroup label="<?php echo html_escape($label); ?>">
                                                <?php foreach ($items as $t) { ?>
                                                    <option value="<?php echo html_escape($t[0]); ?>" <?php echo (string) $exam['marks_target'] === $t[0] ? 'selected' : ''; ?>><?php echo html_escape($t[1]); ?></option>
                                                <?php } ?>
                                            </optgroup>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>How to enter the score</label>
                                    <div class="radio"><label><input type="radio" name="mode" value="scale" checked> As a share of that subject's maximum marks (e.g. 75% of the CBT = 15 out of 20)</label></div>
                                    <div class="radio"><label><input type="radio" name="mode" value="raw"> The CBT score as it is (never more than the maximum)</label></div>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="replace" value="1"> Replace marks already entered for that subject</label>
                                    <p class="help-block">Left unticked, students who already have a mark keep it.</p>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="box-footer">
                            <?php if ($has_targets) { ?><button type="submit" class="btn btn-primary">Send scores</button><?php } ?>
                            <a href="<?php echo site_url('admin/onlineexam'); ?>" class="btn btn-default">Back to online exams</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box box-solid">
                    <div class="box-body">
                        <p>Only students who are in the chosen exam (Assign / View Student on that exam) get a mark.</p>
                        <p class="text-muted small">Sending again is safe: it updates the same marks. Teachers can still change any mark under Examinations afterwards.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
