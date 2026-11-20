<?php
/* "New CBT" (a popover form): for a class and subject; $targets (report card exams it can send to), $preset (one of them, chosen), $return, $label. */
$label = isset($label) ? $label : '<i class="fa fa-plus"></i> New CBT';
?>
<details class="cbt-pop"><summary class="btn btn-primary btn-sm"><?php echo $label; ?></summary><div style="right:0;left:auto;min-width:380px">
    <?php echo form_open('admin/cbtexams/create'); ?>
    <input type="hidden" name="class_id" value="<?php echo (int) $class['id']; ?>"><input type="hidden" name="subject_id" value="<?php echo (int) $subject['id']; ?>"><input type="hidden" name="return" value="<?php echo html_escape($return); ?>">
    <b>New <?php echo html_escape($subject['name']); ?> CBT for <?php echo html_escape($class['class']); ?></b>
    <?php if ($preset) { ?>
        <input type="hidden" name="target" value="<?php echo html_escape($preset); ?>">
        <div class="cbt-muted" style="font-size:12px;margin-top:4px">Scores go to <?php echo html_escape(isset($targets[$preset]) ? $targets[$preset]['label'] : 'the report card'); ?>.</div>
    <?php } else { ?>
        <label class="cbt-l">Scores go to
            <select name="target" class="form-control input-sm">
                <?php foreach ($targets as $k => $t) { if ($t['taken']) { continue; } ?><option value="<?php echo html_escape($k); ?>"><?php echo html_escape($t['label']); ?></option><?php } ?>
                <option value="">Practice (not on a report card)</option>
            </select>
        </label>
    <?php } ?>
    <label class="cbt-l">Title <small class="cbt-muted">(empty: named after the class, subject and exam)</small><input name="title" class="form-control input-sm"></label>
    <div class="cbt-row"><label>Opens<input type="datetime-local" name="from" class="form-control input-sm" value="<?php echo date('Y-m-d') . 'T08:00'; ?>"></label><label>Closes<input type="datetime-local" name="to" class="form-control input-sm" value="<?php echo date('Y-m-d', strtotime('+7 days')) . 'T17:00'; ?>"></label></div>
    <div class="cbt-row"><label>Minutes<input type="number" name="minutes" min="1" max="600" value="40" class="form-control input-sm"></label><label>Attempts<input type="number" name="attempts" min="1" max="10" value="1" class="form-control input-sm"></label><label>Pass %<input type="number" name="pass" min="0" max="100" value="40" class="form-control input-sm"></label></div>
    <button class="btn btn-primary btn-sm btn-block">Make the CBT</button>
    <p class="cbt-muted" style="font-size:11.5px;margin:6px 0 0">Every student of <?php echo html_escape($class['class']); ?> gets it. Then add its questions and open it.</p>
    <?php echo form_close(); ?>
</div></details>
