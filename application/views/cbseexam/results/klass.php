<?php
/*
 * Report Cards > Results > a class (Results::klass).
 *   tab subjects     the subjects the class offers (filter by section), each with its terms
 *   tab reportcards  per term: attendance & comments, results, approve, publish, print, PDF, email
 */
$cr_msg = $this->session->flashdata('cr_msg');
$n = function ($v) { return $v === null || $v === '' ? '–' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$base = 'cbseexam/results';
$cid = (int) $class['id'];
$here = $base . '/klass/' . $cid;
?>
<style>
    .cr-top{display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;justify-content:space-between;margin-bottom:14px}
    .cr-top h3{margin:0;font-weight:800}
    .cr-muted{color:#64748b}
    .cr-tabs{display:flex;flex-wrap:wrap;gap:6px;margin:0 0 16px;padding:0;list-style:none}
    .cr-tabs a{display:inline-flex;align-items:center;gap:7px;padding:10px 16px;border-radius:12px;background:#fff;border:1px solid #e5e9f0;color:#475569;font-weight:700}
    .cr-tabs a.on{background:var(--ui-brand,#1f4e79);border-color:var(--ui-brand,#1f4e79);color:#fff}
    .cr-pills{display:inline-flex;flex-wrap:wrap;background:#fff;border:1px solid #e5e9f0;border-radius:12px;padding:4px;gap:4px}
    .cr-pills a{padding:7px 14px;border-radius:9px;font-weight:600;color:#475569}
    .cr-pills a.on{background:var(--ui-brand,#1f4e79);color:#fff}
    .cr-box{background:#fff;border:1px solid #e5e9f0;border-radius:16px;padding:18px;margin-bottom:16px}
    .cr-box h4{margin:0 0 4px;font-weight:800}
    .cr-subjects{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px}
    .cr-sub{display:block;background:#fff;border:1px solid #e5e9f0;border-radius:16px;padding:16px 18px;color:#0f172a;transition:transform .15s,box-shadow .15s}
    .cr-sub:hover{transform:translateY(-2px);box-shadow:0 16px 32px -18px rgba(15,23,42,.35);color:#0f172a;text-decoration:none}
    .cr-sub h4{margin:0 0 2px;font-weight:800}
    .cr-sub .t{display:flex;align-items:center;gap:8px;font-size:12px;color:#475569;margin-top:7px}
    .cr-sub .t b{min-width:86px;font-weight:600}
    .cr-prog{flex:1;height:7px;background:#eef2f6;border-radius:99px;overflow:hidden}
    .cr-prog i{display:block;height:100%;background:#22c55e}
    .cr-table{width:100%;border-collapse:separate;border-spacing:0}
    .cr-table th{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:#64748b;padding:8px 10px;border-bottom:2px solid #eef2f6;text-align:left;white-space:nowrap}
    .cr-table td{padding:10px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    .cr-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:14px}
    .cr-kpi{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:12px 16px}
    .cr-kpi small{display:block;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:.05em}
    .cr-kpi b{font-size:22px}
    .cr-pos{display:inline-block;min-width:40px;text-align:center;border-radius:99px;padding:2px 8px;background:#eef2ff;color:#3730a3;font-weight:800}
    .cr-actions{display:flex;flex-wrap:wrap;gap:8px}
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="cr-top">
            <div>
                <a href="<?php echo site_url($base); ?>" class="cr-muted"><i class="fa fa-angle-left"></i> All classes</a>
                <h3><?php echo html_escape($class['class']); ?></h3>
            </div>
            <?php if ($tab === 'subjects' && count($sections) > 1) { ?>
                <div class="cr-pills">
                    <a class="<?php echo !$section ? 'on' : ''; ?>" href="<?php echo site_url($here); ?>">All sections</a>
                    <?php foreach ($sections as $s) { ?><a class="<?php echo (int) $s['id'] === $section ? 'on' : ''; ?>" href="<?php echo site_url($here . '?section=' . (int) $s['id']); ?>"><?php echo html_escape($class['class'] . ' ' . $s['section']); ?></a><?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php if ($cr_msg) { ?><div class="alert alert-<?php echo $cr_msg[0]; ?>"><?php echo html_escape($cr_msg[1]); ?></div><?php } ?>
        <ul class="cr-tabs">
            <li><a class="<?php echo $tab === 'subjects' ? 'on' : ''; ?>" href="<?php echo site_url($here); ?>"><i class="fa fa-book"></i> Subjects</a></li>
            <li><a class="<?php echo $tab === 'reportcards' ? 'on' : ''; ?>" href="<?php echo site_url($here . '?tab=reportcards'); ?>"><i class="fa fa-file-text-o"></i> Report cards</a></li>
        </ul>

        <?php if ($tab === 'subjects') { ?>
            <?php if (!$subjects) { ?>
                <div class="cr-box text-center" style="padding:36px">
                    <i class="fa fa-book fa-3x" style="color:var(--ui-brand,#1f4e79)"></i>
                    <h4 style="margin-top:12px">No subjects for <?php echo html_escape($class['class']); ?> yet</h4>
                    <?php $sg_ci = get_instance(); $sg_groups = $sg_ci->class_results->subject_groups($sg_ci->current_session); ?>
                    <?php if ($sg_groups) { ?>
                        <p class="cr-muted">Choose the subject group <?php echo html_escape($class['class']); ?> takes: its subjects appear here, ready for scores.</p>
                        <?php echo form_open('cbseexam/results/subject_group', array('style' => 'display:inline-flex;gap:8px;align-items:center;flex-wrap:wrap;justify-content:center;margin-top:6px')); ?>
                            <input type="hidden" name="class_id" value="<?php echo (int) $cid; ?>">
                            <select name="subject_group_id" class="form-control" required style="width:auto">
                                <option value="">Choose a subject group</option>
                                <?php foreach ($sg_groups as $gid => $g) { ?><option value="<?php echo (int) $gid; ?>"><?php echo html_escape($g['name'] . ' (' . count($g['subjects']) . ' subjects)'); ?></option><?php } ?>
                            </select>
                            <button class="btn btn-primary"><i class="fa fa-check"></i> Use it for <?php echo html_escape($class['class']); ?></button>
                            <a class="btn btn-default" href="<?php echo site_url('admin/subjectgroup'); ?>" target="_blank" rel="noopener"><i class="fa fa-plus"></i> New subject group</a>
                        <?php echo form_close(); ?>
                    <?php } else { ?>
                        <p class="cr-muted">There are no subject groups yet. Make one in <a href="<?php echo site_url('admin/subjectgroup'); ?>" target="_blank" rel="noopener">Classes &amp; Timetable &gt; Subject Groups <i class="fa fa-external-link"></i></a> (its name and subjects), then come back here.</p>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="cr-subjects">
                    <?php foreach ($subjects as $sid => $s) { ?>
                        <a class="cr-sub" href="<?php echo site_url($base . '/subject/' . $cid . '/' . (int) $sid . ($section ? '?section=' . $section : '')); ?>">
                            <h4><?php echo html_escape($s['name']); ?></h4>
                            <small class="cr-muted"><?php echo html_escape($s['code'] ?: ' '); ?></small>
                            <?php foreach ($terms as $t) {
                                $p = isset($progress[(int) $sid][(int) $t['id']]) ? $progress[(int) $sid][(int) $t['id']] : null;
                                $pct = $p && $p['total'] ? round($p['done'] * 100 / $p['total']) : 0; ?>
                                <div class="t"><b><?php echo html_escape($t['name']); ?></b>
                                    <?php if ($p) { ?><span class="cr-prog"><i style="width:<?php echo $pct; ?>%"></i></span><span><?php echo count($p['types']); ?> exams · <?php echo $pct; ?>%</span><?php echo $p['marks_status'] === 'approved' ? ' <i class="fa fa-lock" title="Approved"></i>' : ''; ?>
                                    <?php } else { ?><span class="cr-muted">not set up</span><?php } ?>
                                </div>
                            <?php } ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>

        <?php } else { // report cards
            $term_name = '';
            foreach ($terms as $t) { if ((int) $t['id'] === (int) $term) { $term_name = $t['name']; } }
            $tq = $here . '?tab=reportcards&term=' . (int) $term; ?>
            <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:14px">
                <div class="cr-pills"><?php foreach ($terms as $t) { ?><a class="<?php echo (int) $t['id'] === (int) $term ? 'on' : ''; ?>" href="<?php echo site_url($here . '?tab=reportcards&term=' . (int) $t['id'] . '&step=' . $step); ?>"><?php echo html_escape($t['name']); ?></a><?php } ?></div>
                <?php if ($exam) { ?>
                    <div class="cr-pills">
                        <a class="<?php echo $step === 'comments' ? 'on' : ''; ?>" href="<?php echo site_url($tq . '&step=comments'); ?>"><i class="fa fa-commenting-o"></i> Attendance &amp; comments</a>
                        <a class="<?php echo $step === 'results' ? 'on' : ''; ?>" href="<?php echo site_url($tq . '&step=results'); ?>"><i class="fa fa-trophy"></i> Results &amp; report cards</a>
                    </div>
                <?php } ?>
            </div>
            <?php if (!$exam) { ?>
                <div class="cr-box text-center" style="padding:36px">
                    <h4><?php echo html_escape($term_name); ?> is not set up for <?php echo html_escape($class['class']); ?></h4>
                    <p class="cr-muted">Setting up gives every subject of the class its exams (First CA 10, Mid-term Test 20, Exam 70, or what the class used last). Change any subject's exams on its page.</p>
                    <?php if ($manager) { ?><?php echo form_open($base . '/setup_term'); ?><input type="hidden" name="class_id" value="<?php echo $cid; ?>"><input type="hidden" name="term" value="<?php echo (int) $term; ?>"><input type="hidden" name="return" value="<?php echo html_escape($tq); ?>"><button class="btn btn-primary btn-lg"><i class="fa fa-magic"></i> Set up <?php echo html_escape($term_name); ?></button><?php echo form_close(); ?><?php } ?>
                </div>
            <?php } else {
                $eid = (int) $exam['id'];
                $u = function ($action) use ($base, $eid, $cid) { return site_url($base . '/' . $action . '/' . $eid . '/' . $cid); };
                $r = $results;
                if (!empty($exam['shared'])) { ?>
                    <div class="alert alert-info" style="border-radius:12px"><i class="fa fa-info-circle"></i> "<?php echo html_escape($exam['name']); ?>" was made for several classes: publishing applies to all of them; positions and report cards here are for <?php echo html_escape($class['class']); ?>.</div>
                <?php }
                if ($step === 'comments') { ?>
                    <?php if ($manager) { ?>
                        <div class="cr-box">
                            <?php echo form_open($u('settings'), array('style' => 'display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end')); ?>
                            <input type="hidden" name="return" value="<?php echo html_escape($tq . '&step=comments'); ?>">
                            <div><label class="cr-muted" style="display:block;font-size:12px">Days school opened this term</label><input type="number" min="0" name="total_working_days" class="form-control" style="width:120px" value="<?php echo (int) $exam['total_working_days']; ?>"></div>
                            <div><label class="cr-muted" style="display:block;font-size:12px">Next term begins</label><input type="date" name="next_term_begins" class="form-control" value="<?php echo html_escape($exam['next_term_begins']); ?>"></div>
                            <div><label class="cr-muted" style="display:block;font-size:12px">Grading scale</label>
                                <select name="grade_id" class="form-control"><?php foreach ($grade_scales as $g) { ?><option value="<?php echo (int) $g['id']; ?>" <?php echo (int) $g['id'] === (int) $exam['cbse_exam_grade_id'] ? 'selected' : ''; ?>><?php echo html_escape($g['name']); ?></option><?php } ?></select></div>
                            <button class="btn btn-default"><i class="fa fa-save"></i> Save</button>
                            <?php echo form_close(); ?>
                        </div>
                    <?php } ?>
                    <div class="cr-box">
                        <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;margin-bottom:10px">
                            <h4><i class="fa fa-commenting-o"></i> Attendance &amp; comments · <?php echo html_escape($term_name); ?></h4>
                            <div class="cr-actions">
                                <button type="button" class="btn btn-default btn-sm" id="cm_fill"><i class="fa fa-magic"></i> Fill empty comments</button>
                                <a class="btn btn-default btn-sm" href="<?php echo site_url('cbseexam/exam/comments'); ?>" target="_blank"><i class="fa fa-list"></i> Comment bank</a>
                            </div>
                        </div>
                        <?php echo form_open($u('comments')); ?>
                        <input type="hidden" name="return" value="<?php echo html_escape($tq . '&step=comments'); ?>">
                        <div class="table-responsive">
                            <table class="cr-table">
                                <thead><tr><th>Student</th><th>Average</th><th>Days present<?php echo (int) $exam['total_working_days'] ? ' / ' . (int) $exam['total_working_days'] : ''; ?></th><th>Class teacher's comment</th><th>Principal's comment</th></tr></thead>
                                <tbody>
                                <?php foreach ($r['students'] as $es => $st) {
                                    $weak = '';
                                    $low = null;
                                    foreach ($st['subjects'] as $tt => $sub) { if ($sub['pct'] < 50 && ($low === null || $sub['pct'] < $low)) { $low = $sub['pct']; $weak = $r['subjects'][$tt]['name']; } } ?>
                                    <tr data-avg="<?php echo $st['average'] === null ? '' : $st['average']; ?>" data-weak="<?php echo html_escape($weak); ?>">
                                        <td><b><?php echo html_escape($st['name']); ?></b><br><small class="cr-muted"><?php echo html_escape($st['admission_no'] . ' · ' . $st['section']); ?></small></td>
                                        <td><?php echo $st['average'] === null ? '–' : $n($st['average']) . '%'; ?></td>
                                        <td><input type="number" min="0" name="present[<?php echo (int) $es; ?>]" class="form-control input-sm" style="width:90px" value="<?php echo html_escape((string) $st['total_present_days']); ?>"></td>
                                        <td><input type="text" maxlength="1000" name="teacher[<?php echo (int) $es; ?>]" class="form-control input-sm cm-t" value="<?php echo html_escape((string) $st['remark']); ?>"></td>
                                        <td><input type="text" maxlength="1000" name="principal[<?php echo (int) $es; ?>]" class="form-control input-sm cm-p" value="<?php echo html_escape((string) $st['principal_remark']); ?>"></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="text-align:right;margin-top:12px"><button class="btn btn-primary"><i class="fa fa-save"></i> Save attendance &amp; comments</button></div>
                        <?php echo form_close(); ?>
                    </div>
                    <script>
                    (function () {
                        var bank = <?php echo json_encode($bank); ?>;
                        function pick(list, v) { for (var i = 0; i < list.length; i++) { if (v >= list[i][0] - 0.0001 && v <= list[i][1] + 0.0099) { return list[i][2]; } } return ''; }
                        document.getElementById('cm_fill').addEventListener('click', function () {
                            var c = 0;
                            document.querySelectorAll('tr[data-avg]').forEach(function (tr) {
                                var a = tr.getAttribute('data-avg'); if (a === '') { return; } a = parseFloat(a);
                                var t = tr.querySelector('.cm-t'), p = tr.querySelector('.cm-p'), w = tr.getAttribute('data-weak');
                                if (!t.value.trim()) { var x = pick(bank.teacher, a); if (x) { t.value = x + (w ? ' Needs to improve in ' + w + '.' : ''); c++; } }
                                if (!p.value.trim()) { var y = pick(bank.principal, a); if (y) { p.value = y; c++; } }
                            });
                            if (window.successMsg) { successMsg(c ? c + ' comment(s) filled in. Check them, then Save.' : 'Every comment is already written.'); }
                        });
                    })();
                    </script>
                <?php } else {
                    $avgs = array_filter(array_column($r['students'], 'average'), function ($v) { return $v !== null; });
                    $approved = count(array_filter($subjects_t, function ($s) { return $s['marks_status'] === 'approved'; }));
                    $secs = array();
                    foreach ($r['students'] as $st) { $secs[(int) $st['section_id']] = $st['section']; }
                    uasort($r['students'], function ($a, $b) { return strcmp($a['section'], $b['section']) ?: ((isset($a['position']) ? $a['position'] : 1e9) <=> (isset($b['position']) ? $b['position'] : 1e9)) ?: strcmp($a['name'], $b['name']); }); ?>
                    <div class="cr-kpis">
                        <div class="cr-kpi"><small>Students</small><b><?php echo count($r['students']); ?></b></div>
                        <div class="cr-kpi"><small>Class average</small><b><?php echo $avgs ? $n(round(array_sum($avgs) / count($avgs), 2)) . '%' : '–'; ?></b></div>
                        <div class="cr-kpi"><small>Subjects approved</small><b><?php echo $approved . ' / ' . count($subjects_t); ?></b></div>
                        <div class="cr-kpi"><small>Report cards</small><b style="font-size:16px;color:<?php echo (int) $exam['is_publish'] ? '#15803d' : '#92400e'; ?>"><?php echo (int) $exam['is_publish'] ? 'Published' : 'Not published'; ?></b></div>
                    </div>
                    <div class="cr-box">
                        <div class="cr-actions">
                            <a class="btn btn-default" target="_blank" href="<?php echo $u('card'); ?>"><i class="fa fa-print"></i> Print all</a>
                            <a class="btn btn-default" target="_blank" href="<?php echo $u('pdf'); ?>"><i class="fa fa-file-pdf-o"></i> PDF of all</a>
                            <?php if ($manager) { ?>
                                <?php if ($approved < count($subjects_t)) { ?><?php echo form_open($u('approve_all'), array('style' => 'display:inline')); ?><input type="hidden" name="return" value="<?php echo html_escape($tq . '&step=results'); ?>"><button class="btn btn-success" onclick="return confirm('Approve and lock the scores of every subject?')"><i class="fa fa-lock"></i> Approve all subjects</button><?php echo form_close(); ?><?php } ?>
                                <?php echo form_open($u('publish'), array('style' => 'display:inline')); ?><input type="hidden" name="return" value="<?php echo html_escape($tq . '&step=results'); ?>"><input type="hidden" name="publish" value="<?php echo (int) $exam['is_publish'] ? 0 : 1; ?>">
                                <button class="btn <?php echo (int) $exam['is_publish'] ? 'btn-default' : 'btn-primary'; ?>" <?php echo !(int) $exam['is_publish'] && $approved < count($subjects_t) ? 'onclick="return confirm(\'Some subjects are not approved yet. Publish anyway?\')"' : ''; ?>><i class="fa fa-globe"></i> <?php echo (int) $exam['is_publish'] ? 'Withdraw from portal' : 'Publish to students & parents'; ?></button><?php echo form_close(); ?>
                                <?php echo form_open($u('email'), array('style' => 'display:inline')); ?><input type="hidden" name="return" value="<?php echo html_escape($tq . '&step=results'); ?>"><button class="btn btn-default" <?php echo (int) $exam['is_publish'] ? 'onclick="return confirm(\'Email each parent their child\\\'s report card (PDF)?\')"' : 'disabled title="Publish first"'; ?>><i class="fa fa-envelope-o"></i> Email to parents</button><?php echo form_close(); ?>
                            <?php } ?>
                        </div>
                        <p class="cr-muted" style="margin:10px 0 0;font-size:12.5px">Positions are within each section; equal averages share a position. <?php echo $r['cumulative'] ? 'Last term: report cards also show each term and the cumulative average.' : ''; ?></p>
                    </div>
                    <?php if (!$r['students'] || !$subjects_t) { ?>
                        <div class="alert alert-info">No <?php echo !$subjects_t ? 'subjects' : 'students'; ?> yet.</div>
                    <?php } else { ?>
                    <div class="cr-box" style="overflow:auto">
                        <table class="cr-table">
                            <thead><tr><th>Pos.</th><th>Student</th>
                                <?php foreach ($r['subjects'] as $tt => $s) { ?><th title="<?php echo html_escape($s['name']); ?>" style="text-align:center"><?php echo html_escape($s['code'] ?: (mb_strlen($s['name']) > 10 ? mb_substr($s['name'], 0, 9) . '.' : $s['name'])); ?></th><?php } ?>
                                <th style="text-align:center">Total</th><th style="text-align:center">Average</th><th style="text-align:center">Grade</th><?php echo $r['cumulative'] ? '<th style="text-align:center">Cumulative</th>' : ''; ?><th></th></tr></thead>
                            <tbody>
                            <?php $last = null; foreach ($r['students'] as $es => $st) {
                                if (count($secs) > 1 && $last !== $st['section']) { $last = $st['section']; ?><tr><td colspan="<?php echo count($r['subjects']) + 7; ?>" style="background:#f8fafc;font-weight:800"><?php echo html_escape($class['class'] . ' ' . $st['section']); ?></td></tr><?php } ?>
                                <tr>
                                    <td><?php echo isset($st['position']) ? '<span class="cr-pos">' . Class_results::ordinal($st['position']) . '</span>' : ''; ?></td>
                                    <td style="white-space:nowrap"><b><?php echo html_escape($st['name']); ?></b><br><small class="cr-muted"><?php echo html_escape($st['admission_no']); ?></small></td>
                                    <?php foreach ($r['subjects'] as $tt => $s) { $v = isset($st['subjects'][$tt]) ? $st['subjects'][$tt] : null; ?><td style="text-align:center;<?php echo $v && $v['pct'] < 40 ? 'color:#b91c1c' : ''; ?>"><?php echo $v ? $n($v['total']) : '<span class="cr-muted">–</span>'; ?></td><?php } ?>
                                    <td style="text-align:center"><b><?php echo $n($st['total']); ?></b></td>
                                    <td style="text-align:center"><b><?php echo $st['average'] === null ? '–' : $n($st['average']) . '%'; ?></b></td>
                                    <td style="text-align:center"><?php echo html_escape($st['grade']); ?></td>
                                    <?php echo $r['cumulative'] ? '<td style="text-align:center">' . ($st['cumulative_average'] === null ? '–' : $n($st['cumulative_average']) . '%') . '</td>' : ''; ?>
                                    <td style="white-space:nowrap"><a class="btn btn-default btn-xs" target="_blank" href="<?php echo $u('card') . '/' . (int) $es; ?>"><i class="fa fa-eye"></i> Report card</a> <a class="btn btn-default btn-xs" target="_blank" href="<?php echo $u('pdf') . '/' . (int) $es; ?>" title="PDF"><i class="fa fa-file-pdf-o"></i></a></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </section>
</div>
