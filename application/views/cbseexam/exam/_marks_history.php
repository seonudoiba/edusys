<?php
/* Change log of Report Cards scores (Exam::marks_history): who changed what, when, before → after. */
$mh_fmt = function ($m, $abs) {
    if ($abs) {
        return '<span class="label label-danger">ABS</span>';
    }
    return $m === null ? '<span class="text-muted">—</span>' : rtrim(rtrim(number_format((float) $m, 2, '.', ''), '0'), '.');
};
$mh_actions = array(
    'submit' => array('Submitted for review', 'label-info', 'fa-paper-plane'),
    'approve' => array('Approved & locked', 'label-success', 'fa-lock'),
    'unlock' => array('Unlocked', 'label-warning', 'fa-unlock'),
    'return' => array('Returned to teacher', 'label-warning', 'fa-reply'),
);
$mh_sources = array('entry' => 'Marks entry', 'import' => 'Import', 'cbt' => 'CBT');
?>
<style>
    .mh-table td,.mh-table th{vertical-align:middle!important;font-size:13px}
    .mh-arrow{color:#94a3b8;margin:0 6px}
    .mh-new{font-weight:700;color:#0f172a}
    .mh-when{white-space:nowrap;color:#475569}
    .mh-empty{padding:30px;text-align:center;color:#64748b}
</style>
<?php if (empty($rows)) { ?>
    <div class="mh-empty"><i class="fa fa-history fa-2x"></i><p>No changes recorded yet. Every score change and every review step is listed here from now on.</p></div>
<?php } else { ?>
    <div class="table-responsive">
        <table class="table table-striped mh-table">
            <thead>
                <tr>
                    <th>When</th>
                    <th>By</th>
                    <?php if (!empty($whole_exam)) { ?><th>Subject</th><?php } ?>
                    <th>Student</th>
                    <th>Assessment</th>
                    <th>Change</th>
                    <th>From</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r) { ?>
                    <tr>
                        <td class="mh-when"><?php echo date('j M Y, H:i', strtotime($r['created_at'])); ?></td>
                        <td><?php echo html_escape(trim($r['staff_name']) ?: '—'); ?></td>
                        <?php if (!empty($whole_exam)) { ?><td><?php echo html_escape($r['subject_name']); ?></td><?php } ?>
                        <?php if ($r['action'] !== 'marks') {
                            $a = isset($mh_actions[$r['action']]) ? $mh_actions[$r['action']] : array(ucfirst($r['action']), 'label-default', 'fa-info'); ?>
                            <td colspan="2"><span class="label <?php echo $a[1]; ?>"><i class="fa <?php echo $a[2]; ?>"></i> <?php echo $a[0]; ?></span></td>
                            <td colspan="2"><?php echo html_escape($r['note']); ?></td>
                        <?php } else { ?>
                            <td><?php echo html_escape(trim($r['student_name'])); ?> <small class="text-muted"><?php echo html_escape($r['admission_no']); ?></small></td>
                            <td><?php echo html_escape($r['assessment_name']); ?></td>
                            <td><?php echo $mh_fmt($r['old_marks'], (int) $r['old_absent'] === 1); ?><i class="fa fa-long-arrow-right mh-arrow"></i><span class="mh-new"><?php echo $mh_fmt($r['new_marks'], (int) $r['new_absent'] === 1); ?></span></td>
                            <td><?php echo isset($mh_sources[$r['source']]) ? $mh_sources[$r['source']] : html_escape($r['source']); ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>
