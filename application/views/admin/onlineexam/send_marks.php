<?php
$groups = array(); // label => array of array(key, text)
// Nothing chosen yet: the first Report Cards assessment of the exam's subject is suggested.
$suggest = '';
if ((string) $exam['marks_target'] === '' && !empty($exam['subject_id'])) {
    foreach ($cbse_targets as $t) {
        if ((int) $t['subject_id'] === (int) $exam['subject_id']) { $suggest = $t['key']; break; }
    }
}
$chosen = (string) $exam['marks_target'] !== '' ? (string) $exam['marks_target'] : $suggest;
foreach ($cbse_targets as $t) {
    $groups['Report Cards · ' . $t['group']][] = array($t['key'], $t['label']);
}
foreach ($targets as $t) {
    $groups['Examinations (Exam Group) · ' . $t['exam_group'] . ' · ' . $t['exam']][] = array((string) (int) $t['id'], $t['subject'] . ' (max ' . (float) $t['max_marks'] . ')');
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
                <?php if (!empty($auto_off)) { ?><div class="alert alert-info">Automatic sending is off. Scores already sent stay on the report card.</div><?php } ?>
                <?php if (!empty($exam['marks_auto']) && (string) $exam['marks_target'] !== '') { ?>
                    <div class="alert alert-success"><i class="fa fa-refresh"></i> <b>Automatic:</b> each student's score goes into the report card as soon as it is final
                        (submitted, written answers marked).
                        <a href="<?php echo site_url('admin/onlineexam/send_marks/' . (int) $exam['id'] . '?auto=off'); ?>" class="alert-link" style="margin-left:6px">Turn off</a></div>
                <?php } ?>
                <?php if ($error) { ?><div class="alert alert-danger"><?php echo html_escape($error); ?></div><?php } ?>
                <?php if ($result) { ?>
                    <div class="alert alert-success">
                        <b><?php echo (int) $result['sent']; ?></b> student<?php echo $result['sent'] === 1 ? '\'s mark was' : 's\' marks were'; ?> entered.
                        <?php if (!empty($result['added'])) { ?><?php echo (int) $result['added']; ?> of them were added to that exam's students (their class is part of it).<?php } ?>
                        <?php
                        $notes = array();
                        if ($result['kept']) { $notes[] = $result['kept'] . ' already had a mark, which was kept'; }
                        if ($result['pending']) { $notes[] = $result['pending'] . ' still have written answers to mark'; }
                        if ($result['not_in_exam']) {
                            $notes[] = $result['not_in_exam'] . (isset($result['kind']) && $result['kind'] === 'cbse'
                                ? ' are not in that exam and their class or section is not part of it (Report Cards > Exam: edit the exam to add the class and section, or add them under Assign / View Student)'
                                : ' are not in that exam (add them under Examinations &gt; Exam Group first)');
                        }
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
                                <div class="alert alert-info">There are no exams for this session yet. Create one under Report Cards &gt; Exam (or Examinations &gt; Exam Group), add its subjects and classes, then come back.</div>
                            <?php } else { ?>
                                <div class="form-group">
                                    <label for="target_id">Put the scores into</label>
                                    <p class="help-block" style="margin-top:0"><b>Report Cards</b>: an assessment of a term exam (e.g. Mathematics · First CA), used for the term report card.
                                        <b>Examinations (Exam Group)</b>: a subject of the older Examinations module, which has its own marksheet. Most schools use Report Cards.</p>
                                    <select class="form-control" id="target_id" name="target_id" required>
                                        <option value="">Choose where the scores go</option>
                                        <?php foreach ($groups as $label => $items) { ?>
                                            <optgroup label="<?php echo html_escape($label); ?>">
                                                <?php foreach ($items as $t) { ?>
                                                    <option value="<?php echo html_escape($t[0]); ?>" <?php echo $chosen === $t[0] ? 'selected' : ''; ?>><?php echo html_escape($t[1]); ?></option>
                                                <?php } ?>
                                            </optgroup>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>How to enter the score</label>
                                    <?php $raw = isset($exam['marks_scale']) && (int) $exam['marks_scale'] === 0; ?>
                                    <div class="radio"><label><input type="radio" name="mode" value="scale" <?php echo $raw ? '' : 'checked'; ?>> As a share of that subject's maximum marks (e.g. 75% of the CBT = 15 out of 20)</label></div>
                                    <div class="radio"><label><input type="radio" name="mode" value="raw" <?php echo $raw ? 'checked' : ''; ?>> The CBT score as it is (never more than the maximum)</label></div>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="replace" value="1" <?php echo !empty($exam['marks_replace']) ? 'checked' : ''; ?>> Replace marks already entered for that subject</label>
                                    <p class="help-block">Left unticked, students who already have a mark keep it.</p>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="auto" value="1" <?php echo !empty($exam['marks_auto']) || (string) $exam['marks_target'] === '' ? 'checked' : ''; ?>> <b>Send automatically from now on</b></label>
                                    <p class="help-block">Each student's score goes in as soon as it is final: when they submit, or when their written answers are marked. No need to come back here.</p>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="box-footer">
                            <?php if ($has_targets) { ?><button type="submit" class="btn btn-primary">Send scores now</button><?php } ?>
                            <a href="<?php echo site_url('admin/onlineexam'); ?>" class="btn btn-default">Back to online exams</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box box-solid">
                    <div class="box-body">
                        <p>Students get a mark when they are in the chosen exam. For a Report Cards exam, students of its classes and sections are added to it automatically.</p>
                        <p class="text-muted small">Sending again is safe: it updates the same marks. Teachers can still change any mark afterwards, under Report Cards (or Examinations).</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
