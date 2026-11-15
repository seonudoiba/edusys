<?php
$exam = $report['exam'];
$sum = $report['summary'];
$pct = function ($v) { return $v === null ? '&ndash;' : rtrim(rtrim(number_format((float) $v, 1), '0'), '.') . '%'; };
$status_label = array('done' => '<span class="label label-default">Submitted</span>', 'pending' => '<span class="label label-warning">Waiting for marking</span>', 'not_taken' => '<span class="label label-danger">Not taken</span>');
?>
<style>
    .rs-kpis { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; }
    .rs-kpi { flex: 1 1 120px; background: #fff; border: 1px solid #e3e6ea; border-radius: 4px; padding: 10px 12px; }
    .rs-kpi b { display: block; font-size: 22px; line-height: 1.2; }
    .rs-kpi span { color: #6b7280; font-size: 12px; text-transform: uppercase; }
    .rs-bar { background: #eef0f3; height: 8px; border-radius: 4px; overflow: hidden; min-width: 80px; }
    .rs-bar i { display: block; height: 100%; background: #2e9e5b; }
    .rs-bar i.low { background: #d9534f; } .rs-bar i.mid { background: #f0ad4e; }
    .rs-missed { display: none; background: #fafbfc; }
    .rs-missed td { padding: 6px 12px !important; }
    .rs-q { max-width: 420px; }
    @media print { .no-print, .main-sidebar, .main-header, .main-footer { display: none !important; } .content-wrapper { margin: 0 !important; } .tab-pane { display: block !important; opacity: 1 !important; } .rs-missed { display: table-row !important; } }
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bar-chart"></i> Results: <?php echo html_escape($exam['exam']); ?></h3>
                <div class="box-tools pull-right no-print">
                    <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/onlineexam'); ?>"><i class="fa fa-arrow-left"></i> Online exams</a>
                    <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/onlineexam/results_export/' . (int) $exam['id']); ?>"><i class="fa fa-file-excel-o"></i> Excel</a>
                    <button type="button" class="btn btn-default btn-sm" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
                    <?php if ($can_send) { ?>
                        <a class="btn btn-primary btn-sm" href="<?php echo site_url('admin/onlineexam/send_marks/' . (int) $exam['id']); ?>"><i class="fa fa-share-square-o"></i> Send scores to report card / CA</a>
                    <?php } ?>
                </div>
            </div>
            <div class="box-body">
                <p class="text-muted">
                    <?php echo html_escape(date('j M Y', strtotime($exam['exam_from']))); ?><?php if (substr($exam['exam_to'], 0, 10) !== substr($exam['exam_from'], 0, 10)) { echo ' &ndash; ' . html_escape(date('j M Y', strtotime($exam['exam_to']))); } ?>
                    &middot; <?php echo count($report['questions']); ?> questions &middot; pass mark <?php echo $pct($exam['passing_percentage']); ?>
                </p>
                <div class="rs-kpis" id="rs_kpis">
                    <div class="rs-kpi"><b><?php echo $sum['submitted']; ?> / <?php echo $sum['assigned']; ?></b><span>Submitted</span></div>
                    <div class="rs-kpi"><b><?php echo $sum['not_taken']; ?></b><span>Not taken</span></div>
                    <div class="rs-kpi"><b><?php echo $sum['passed']; ?></b><span>Passed</span></div>
                    <div class="rs-kpi"><b><?php echo $sum['failed']; ?></b><span>Failed</span></div>
                    <?php if ($sum['pending']) { ?><div class="rs-kpi"><b><?php echo $sum['pending']; ?></b><span>Waiting for marking</span></div><?php } ?>
                    <div class="rs-kpi"><b><?php echo $pct($sum['average']); ?></b><span>Average</span></div>
                    <div class="rs-kpi"><b><?php echo $pct($sum['highest']); ?></b><span>Highest</span></div>
                    <div class="rs-kpi"><b><?php echo $pct($sum['lowest']); ?></b><span>Lowest</span></div>
                </div>
                <?php if ($sum['pending']) { ?>
                    <div class="alert alert-warning">Some written answers are not marked yet, so those students have no final score. Mark them under the exam's <a href="<?php echo site_url('admin/onlineexam/evalution/' . (int) $exam['id']); ?>">Evaluation</a>.</div>
                <?php } ?>

                <ul class="nav nav-tabs no-print">
                    <li class="active"><a href="#rs_students" data-toggle="tab">Students (<?php echo $sum['assigned']; ?>)</a></li>
                    <li><a href="#rs_questions" data-toggle="tab">Questions (<?php echo count($report['questions']); ?>)</a></li>
                </ul>
                <div class="tab-content" style="padding-top:10px">
                    <div class="tab-pane active" id="rs_students">
                        <div class="table-responsive">
                            <table class="table table-hover table-condensed" id="rs_student_table">
                                <thead><tr><th>#</th><th>Adm. no</th><th>Student</th><th>Class</th><th>Status</th><th class="text-right">Score</th><th class="text-right">%</th><th>Result</th><th class="text-right">Right</th><th class="text-right">Wrong</th><th class="text-right">Left out</th><th>Missed</th></tr></thead>
                                <tbody>
                                <?php $i = 0; foreach ($report['students'] as $st) { $i++; $done = $st['status'] !== 'not_taken'; ?>
                                    <tr data-status="<?php echo $st['status']; ?>">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo html_escape($st['admission_no']); ?></td>
                                        <td><a href="<?php echo site_url('student/view/' . (int) $st['student_id']); ?>"><?php echo html_escape($st['name']); ?></a></td>
                                        <td><?php echo html_escape(trim($st['class'] . ' ' . $st['section'])); ?></td>
                                        <td><?php echo $status_label[$st['status']]; ?></td>
                                        <td class="text-right"><?php echo $done ? (float) $st['score'] . ' / ' . (float) $st['max_score'] : ''; ?></td>
                                        <td class="text-right"><?php echo $done ? $pct($st['percentage']) : ''; ?></td>
                                        <td><?php if ($st['status'] === 'done' && $st['passed'] !== null) { echo (int) $st['passed'] ? '<span class="label label-success">Pass</span>' : '<span class="label label-danger">Fail</span>'; } ?></td>
                                        <td class="text-right"><?php echo $done ? (int) $st['correct_count'] : ''; ?></td>
                                        <td class="text-right"><?php echo $done ? (int) $st['wrong_count'] : ''; ?></td>
                                        <td class="text-right"><?php echo $done ? (int) $st['unanswered_count'] : ''; ?></td>
                                        <td><?php if ($st['missed']) { ?>
                                            <a href="#" class="rs-toggle no-print"><?php echo count($st['missed']); ?> question<?php echo count($st['missed']) == 1 ? '' : 's'; ?> <i class="fa fa-caret-down"></i></a>
                                        <?php } elseif ($st['status'] === 'done') { ?><span class="text-success">None</span><?php } ?></td>
                                    </tr>
                                    <?php if ($st['missed']) { ?>
                                    <tr class="rs-missed"><td></td><td colspan="11">
                                        <?php foreach ($st['missed'] as $m) { $q = $report['questions'][$m['no'] - 1]; ?>
                                            <div><b>Q<?php echo $m['no']; ?>.</b> <?php echo html_escape(mb_substr($q['text'], 0, 120)); ?>
                                                &mdash; <?php echo $m['verdict'] === 'unanswered' ? '<span class="text-muted">left out</span>' : 'answered <span class="text-danger">' . html_escape($m['given']) . '</span>'; ?>,
                                                right answer <span class="text-success"><?php echo html_escape($q['answer_text']); ?></span></div>
                                        <?php } ?>
                                    </td></tr>
                                    <?php } ?>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="rs_questions">
                        <p class="text-muted no-print">Of the students who submitted. <a href="#" id="rs_sort">Show the hardest first</a></p>
                        <div class="table-responsive">
                            <table class="table table-hover table-condensed" id="rs_question_table">
                                <thead><tr><th>#</th><th>Question</th><th>Right answer</th><th class="text-right">Right</th><th class="text-right">Wrong</th><th class="text-right">Left out</th><th>% right</th><th>Wrong answer chosen most</th></tr></thead>
                                <tbody>
                                <?php foreach ($report['questions'] as $q) { $p = $q['stats']['correct_pct']; ?>
                                    <tr data-no="<?php echo $q['no']; ?>" data-pct="<?php echo $p === null ? 101 : $p; ?>">
                                        <td>Q<?php echo $q['no']; ?></td>
                                        <td class="rs-q" title="<?php echo html_escape($q['text']); ?>"><?php echo html_escape(mb_substr($q['text'], 0, 110)) . (mb_strlen($q['text']) > 110 ? '&hellip;' : ''); ?>
                                            <?php if ($q['question_type'] === 'descriptive') { ?><span class="label label-info">written</span><?php } ?></td>
                                        <td><?php echo html_escape($q['answer_text']); ?></td>
                                        <td class="text-right"><?php echo $q['stats']['correct']; ?></td>
                                        <td class="text-right"><?php echo $q['stats']['wrong']; ?></td>
                                        <td class="text-right"><?php echo $q['stats']['unanswered']; ?></td>
                                        <td><?php if ($p === null) { echo $q['question_type'] === 'descriptive' ? '<span class="text-muted">marked by teacher</span>' : '&ndash;'; } else { ?>
                                            <div class="rs-bar" title="<?php echo $p; ?>%"><i class="<?php echo $p < 40 ? 'low' : ($p < 60 ? 'mid' : ''); ?>" style="width:<?php echo (int) $p; ?>%"></i></div> <?php echo $p; ?>%
                                        <?php } ?></td>
                                        <td><?php echo $q['stats']['top_wrong'] !== '' ? html_escape($q['stats']['top_wrong']) . ' <span class="text-muted">(' . (int) $q['stats']['top_wrong_count'] . ')</span>' : ''; ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    $(function () {
        $('#rs_student_table').on('click', '.rs-toggle', function (e) {
            e.preventDefault();
            $(this).closest('tr').next('.rs-missed').toggle();
        });
        var hardest = false;
        $('#rs_sort').on('click', function (e) {
            e.preventDefault();
            hardest = !hardest;
            var body = $('#rs_question_table tbody');
            body.children('tr').sort(function (a, b) {
                return hardest ? $(a).data('pct') - $(b).data('pct') : $(a).data('no') - $(b).data('no');
            }).appendTo(body);
            $(this).text(hardest ? 'Show in question order' : 'Show the hardest first');
        });
    });
</script>
