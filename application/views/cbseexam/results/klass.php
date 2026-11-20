<?php
/* Report Cards > Classes & Results > a class (Results::klass). Tabs: subjects, comments, results. */
$cr_msg = $this->session->flashdata('cr_msg');
$n = function ($v) { return $v === null || $v === '' ? '–' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$term_name = '';
foreach ($terms as $t) {
    if ((int) $t['id'] === (int) $term) {
        $term_name = $t['name'];
    }
}
$base = 'cbseexam/results';
$q = '?term=' . (int) $term;
$status = array('draft' => array('Entering', 'cr-s-draft', 'fa-pencil'), 'submitted' => array('Submitted', 'cr-s-sub', 'fa-hourglass-half'), 'approved' => array('Approved', 'cr-s-ok', 'fa-lock'));
?>
<style>
    .cr-top{display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;margin-bottom:14px}
    .cr-top h3{margin:0;font-weight:800}
    .cr-terms{display:inline-flex;background:#fff;border:1px solid #e5e9f0;border-radius:12px;padding:4px;gap:4px}
    .cr-terms a{padding:7px 14px;border-radius:9px;font-weight:600;color:#475569}
    .cr-terms a.on{background:var(--ui-brand,#1f4e79);color:#fff}
    .cr-tabs{display:flex;flex-wrap:wrap;gap:6px;margin:0 0 16px;padding:0;list-style:none}
    .cr-tabs a{display:inline-flex;align-items:center;gap:7px;padding:10px 16px;border-radius:12px;background:#fff;border:1px solid #e5e9f0;color:#475569;font-weight:700}
    .cr-tabs a.on{background:var(--ui-brand,#1f4e79);border-color:var(--ui-brand,#1f4e79);color:#fff}
    .cr-tabs a b{display:inline-grid;place-items:center;width:22px;height:22px;border-radius:50%;background:rgba(0,0,0,.08);font-size:12px}
    .cr-box{background:#fff;border:1px solid #e5e9f0;border-radius:16px;padding:18px;margin-bottom:16px}
    .cr-box h4{margin:0 0 4px;font-weight:800}
    .cr-muted{color:#64748b}
    .cr-bd{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-top:10px}
    .cr-bd .cr-row{display:inline-flex;gap:4px;align-items:center;background:#f8fafc;border:1px solid #e5e9f0;border-radius:10px;padding:4px 6px}
    .cr-bd input{border:0;background:transparent;outline:none;font-weight:600}
    .cr-bd input.nm{width:130px}.cr-bd input.mx{width:52px;text-align:right}
    .cr-bd .x{border:0;background:none;color:#94a3b8;cursor:pointer}
    .cr-sum{font-weight:700;border-radius:99px;padding:4px 11px;font-size:12px}
    .cr-sum.ok{background:#dcfce7;color:#15803d}.cr-sum.warn{background:#fef3c7;color:#92400e}
    .cr-table{width:100%;border-collapse:separate;border-spacing:0}
    .cr-table th{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:#64748b;padding:8px 10px;border-bottom:2px solid #eef2f6;text-align:left;white-space:nowrap}
    .cr-table td{padding:10px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    .cr-chip{display:inline-flex;align-items:center;gap:6px;border:1px solid #e2e8f0;border-radius:10px;padding:4px 9px;margin:2px 4px 2px 0;font-size:12.5px;font-weight:600;background:#fff}
    .cr-chip small{color:#64748b;font-weight:500}
    .cr-chip .k{border-radius:6px;padding:1px 6px;font-size:10px;font-weight:800;letter-spacing:.03em}
    .cr-chip .k.w{background:#f1f5f9;color:#475569}.cr-chip .k.c{background:#ede9fe;color:#6d28d9}
    details.cr-pop{display:inline-block;position:relative}
    details.cr-pop>summary{list-style:none;cursor:pointer}
    details.cr-pop>summary::-webkit-details-marker{display:none}
    details.cr-pop[open]>div{position:absolute;z-index:20;top:110%;left:0;background:#fff;border:1px solid #e5e9f0;border-radius:12px;box-shadow:0 18px 40px -18px rgba(15,23,42,.4);padding:12px;min-width:300px}
    .cr-prog{height:7px;background:#eef2f6;border-radius:99px;overflow:hidden;min-width:90px}
    .cr-prog i{display:block;height:100%;background:#22c55e}
    .cr-st{display:inline-flex;gap:5px;align-items:center;border-radius:99px;padding:3px 10px;font-size:11px;font-weight:700;white-space:nowrap}
    .cr-s-draft{background:#f1f5f9;color:#475569}.cr-s-sub{background:#fef3c7;color:#92400e}.cr-s-ok{background:#dcfce7;color:#15803d}
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
                <a href="<?php echo site_url($base . $q); ?>" class="cr-muted"><i class="fa fa-angle-left"></i> All classes</a>
                <h3><?php echo html_escape($class['class']); ?> <small class="cr-muted"><?php echo html_escape($term_name); ?></small></h3>
            </div>
            <div class="cr-terms">
                <?php foreach ($terms as $t) { ?><a class="<?php echo (int) $t['id'] === (int) $term ? 'on' : ''; ?>" href="<?php echo site_url($base . '/klass/' . (int) $class['id'] . '?term=' . (int) $t['id'] . '&tab=' . $tab); ?>"><?php echo html_escape($t['name']); ?></a><?php } ?>
            </div>
        </div>
        <?php if ($cr_msg) { ?><div class="alert alert-<?php echo $cr_msg[0]; ?>"><?php echo html_escape($cr_msg[1]); ?></div><?php } ?>

        <?php if (!$exam) { ?>
            <div class="cr-box text-center" style="padding:40px">
                <i class="fa fa-file-text-o fa-3x" style="color:var(--ui-brand,#1f4e79)"></i>
                <h4 style="margin-top:12px"><?php echo html_escape($term_name); ?> results for <?php echo html_escape($class['class']); ?> are not set up yet</h4>
                <p class="cr-muted" style="max-width:560px;margin:8px auto 18px">Setting up adds the class's subjects (from its subject groups) and students, with a score breakdown for every subject:
                    First CA 10, Mid-term Test 20, Exam 70 (or what this class used last). You can change it for the class or for any subject.</p>
                <?php if ($manager) { ?>
                    <?php echo form_open($base . '/klass/' . (int) $class['id'] . $q); ?><input type="hidden" name="setup" value="1"><button class="btn btn-primary btn-lg"><i class="fa fa-magic"></i> Set up <?php echo html_escape($term_name); ?></button><?php echo form_close(); ?>
                <?php } else { ?><p>Ask an exam manager to set it up.</p><?php } ?>
            </div>
        <?php } else {
            $eid = (int) $exam['id'];
            $cid = (int) $class['id'];
            $u = function ($action) use ($base, $eid, $cid) { return site_url($base . '/' . $action . '/' . $eid . '/' . $cid); }; ?>
            <?php if (!empty($exam['shared'])) { ?>
                <div class="alert alert-info" style="border-radius:12px"><i class="fa fa-info-circle"></i> This term's exam "<?php echo html_escape($exam['name']); ?>" was made for several classes. Its subjects, score breakdown and publishing apply to all of them; positions and report cards here are for <?php echo html_escape($class['class']); ?> only.</div>
            <?php } ?>
            <ul class="cr-tabs">
                <li><a class="<?php echo $tab === 'subjects' ? 'on' : ''; ?>" href="<?php echo site_url($base . '/klass/' . $cid . $q . '&tab=subjects'); ?>"><b>1</b> Subjects &amp; scores</a></li>
                <li><a class="<?php echo $tab === 'comments' ? 'on' : ''; ?>" href="<?php echo site_url($base . '/klass/' . $cid . $q . '&tab=comments'); ?>"><b>2</b> Attendance &amp; comments</a></li>
                <li><a class="<?php echo $tab === 'results' ? 'on' : ''; ?>" href="<?php echo site_url($base . '/klass/' . $cid . $q . '&tab=results'); ?>"><b>3</b> Results &amp; report cards</a></li>
            </ul>

            <?php if ($tab === 'subjects') { ?>
                <div class="cr-box">
                    <h4><i class="fa fa-sliders"></i> Score breakdown for the class</h4>
                    <div class="cr-muted">Every subject is scored out of these, unless a subject has its own. A subject's scores add up to its total.</div>
                    <?php if ($manager) { ?>
                        <?php echo form_open($u('breakdown'), array('id' => 'cr_default')); ?>
                        <div class="cr-bd" id="cr_rows">
                            <?php foreach ($breakdown as $t) { ?>
                                <span class="cr-row"><input class="nm" name="name[]" value="<?php echo html_escape($t['name']); ?>" aria-label="Assessment"><input class="mx" name="max[]" type="number" step="0.5" min="0.5" value="<?php echo $n($t['maximum_marks']); ?>" aria-label="Marks"><button type="button" class="x" title="Remove">&times;</button></span>
                            <?php } ?>
                            <button type="button" class="btn btn-default btn-xs" id="cr_add"><i class="fa fa-plus"></i> Add assessment</button>
                            <span class="cr-sum" id="cr_total"></span>
                            <button class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Save for all subjects</button>
                        </div>
                        <?php echo form_close(); ?>
                    <?php } else { ?>
                        <div class="cr-bd"><?php foreach ($breakdown as $t) { ?><span class="cr-chip"><?php echo html_escape($t['name']); ?> <small><?php echo $n($t['maximum_marks']); ?></small></span><?php } ?></div>
                    <?php } ?>
                </div>

                <div class="cr-box">
                    <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between">
                        <h4><i class="fa fa-book"></i> Subjects <small class="cr-muted"><?php echo count($subjects); ?></small></h4>
                        <div class="cr-actions">
                            <a class="btn btn-default btn-sm" href="<?php echo site_url('cbseexam/exam/marks_export/' . $eid); ?>"><i class="fa fa-file-excel-o"></i> Score sheet (Excel)</a>
                            <?php if ($manager) { ?>
                                <details class="cr-pop"><summary class="btn btn-default btn-sm"><i class="fa fa-plus"></i> Add subjects</summary><div>
                                    <?php echo form_open($u('add_subject')); ?>
                                    <select name="subject_id[]" class="form-control" multiple size="8">
                                        <?php $have = array_map('intval', array_column($subjects, 'subject_id'));
                                        foreach ($all_subjects as $s) { if (in_array((int) $s['id'], $have, true)) { continue; } ?><option value="<?php echo (int) $s['id']; ?>"><?php echo html_escape($s['name'] . ($s['code'] ? ' (' . $s['code'] . ')' : '')); ?></option><?php } ?>
                                    </select>
                                    <small class="cr-muted">Hold Ctrl to pick several.</small>
                                    <button class="btn btn-primary btn-sm btn-block" style="margin-top:8px">Add</button>
                                    <?php echo form_close(); ?>
                                </div></details>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if (!$subjects) { ?>
                        <p class="cr-muted" style="margin:14px 0 0">No subjects yet. Give the class a subject group (Classes &amp; Timetable &gt; Subject Groups) or add subjects here.</p>
                    <?php } else { ?>
                        <div class="table-responsive" style="margin-top:10px;overflow:visible">
                            <table class="cr-table">
                                <thead><tr><th>Subject</th><th>Assessments (marks)</th><th>Scores in</th><th>Status</th><th></th></tr></thead>
                                <tbody>
                                <?php foreach ($subjects as $tt => $s) { $st = isset($status[$s['marks_status']]) ? $status[$s['marks_status']] : $status['draft']; $pct = $s['total'] ? round($s['done'] * 100 / $s['total']) : 0; ?>
                                    <tr>
                                        <td><b><?php echo html_escape($s['name']); ?></b><?php echo $s['code'] ? '<br><small class="cr-muted">' . html_escape($s['code']) . '</small>' : ''; ?><?php echo (int) $s['custom_breakdown'] ? '<br><small class="cr-muted"><i class="fa fa-star-o"></i> own breakdown</small>' : ''; ?></td>
                                        <td>
                                            <?php foreach ($s['types'] as $tid => $t) { ?>
                                                <?php if ($manager) { ?>
                                                    <details class="cr-pop"><summary class="cr-chip"><?php echo html_escape($t['name']); ?> <small><?php echo $n($t['maximum_marks']); ?></small><span class="k <?php echo $t['cbt'] ? 'c' : 'w'; ?>"><?php echo $t['cbt'] ? 'CBT' : 'WRITTEN'; ?></span></summary><div>
                                                        <b><?php echo html_escape($s['name'] . ' — ' . $t['name']); ?></b>
                                                        <?php if ($t['cbt']) { ?>
                                                            <p class="cr-muted" style="margin:6px 0">Scores come from the CBT "<?php echo html_escape($t['cbt']['exam']); ?>" when students submit (scaled to <?php echo $n($t['maximum_marks']); ?>).</p>
                                                            <a class="btn btn-default btn-xs" href="<?php echo site_url('admin/onlineexam'); ?>"><i class="fa fa-laptop"></i> Open CBT Exams</a>
                                                            <?php echo form_open($u('cbt'), array('style' => 'display:inline')); ?><input type="hidden" name="tt_id" value="<?php echo (int) $tt; ?>"><input type="hidden" name="type_id" value="<?php echo (int) $tid; ?>"><input type="hidden" name="unlink" value="1"><button class="btn btn-default btn-xs">Type scores in instead</button><?php echo form_close(); ?>
                                                        <?php } else { ?>
                                                            <p class="cr-muted" style="margin:6px 0">Teachers type these scores in. To use a CBT instead, its scores come here automatically:</p>
                                                            <?php echo form_open($u('cbt')); ?>
                                                            <input type="hidden" name="tt_id" value="<?php echo (int) $tt; ?>"><input type="hidden" name="type_id" value="<?php echo (int) $tid; ?>">
                                                            <select name="onlineexam_id" class="form-control input-sm">
                                                                <option value="0">A new CBT for this (the class's students)</option>
                                                                <?php foreach ($cbts as $o) { if ($o['marks_target'] && $o['marks_target'] !== 'cbse:' . $tt . ':' . $tid) { continue; } ?><option value="<?php echo (int) $o['id']; ?>"><?php echo html_escape($o['exam']); ?></option><?php } ?>
                                                            </select>
                                                            <button class="btn btn-primary btn-xs btn-block" style="margin-top:6px"><i class="fa fa-laptop"></i> Use a CBT</button>
                                                            <?php echo form_close(); ?>
                                                        <?php } ?>
                                                    </div></details>
                                                <?php } else { ?>
                                                    <span class="cr-chip"><?php echo html_escape($t['name']); ?> <small><?php echo $n($t['maximum_marks']); ?></small><span class="k <?php echo $t['cbt'] ? 'c' : 'w'; ?>"><?php echo $t['cbt'] ? 'CBT' : 'WRITTEN'; ?></span></span>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if (!$s['types']) { ?><span class="text-danger">No assessments</span><?php } ?>
                                        </td>
                                        <td style="min-width:120px"><div class="cr-prog"><i style="width:<?php echo $pct; ?>%"></i></div><small class="cr-muted"><?php echo (int) $s['done']; ?> of <?php echo (int) $s['total']; ?> students</small></td>
                                        <td><span class="cr-st <?php echo $st[1]; ?>"><i class="fa <?php echo $st[2]; ?>"></i> <?php echo $st[0]; ?></span></td>
                                        <td style="white-space:nowrap;text-align:right">
                                            <a class="btn btn-primary btn-sm" href="<?php echo site_url($base . '/scores/' . $eid . '/' . $cid . '/' . (int) $tt); ?>"><i class="fa fa-pencil-square-o"></i> Scores</a>
                                            <?php if ($manager) { ?>
                                                <details class="cr-pop" style="text-align:left"><summary class="btn btn-default btn-sm" title="This subject's own breakdown"><i class="fa fa-sliders"></i></summary><div style="right:0;left:auto">
                                                    <b><?php echo html_escape($s['name']); ?>: own breakdown</b>
                                                    <?php echo form_open($u('breakdown'), array('class' => 'cr-subj')); ?>
                                                    <input type="hidden" name="tt_id" value="<?php echo (int) $tt; ?>">
                                                    <div class="cr-bd">
                                                        <?php foreach ($s['types'] as $t) { ?><span class="cr-row"><input class="nm" name="name[]" value="<?php echo html_escape($t['name']); ?>"><input class="mx" name="max[]" type="number" step="0.5" min="0.5" value="<?php echo $n($t['maximum_marks']); ?>"><button type="button" class="x">&times;</button></span><?php } ?>
                                                        <button type="button" class="btn btn-default btn-xs cr-add1"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                    <div style="display:flex;gap:6px;margin-top:8px">
                                                        <button class="btn btn-primary btn-xs">Save for <?php echo html_escape($s['name']); ?></button>
                                                        <?php if ((int) $s['custom_breakdown']) { ?><button class="btn btn-default btn-xs" name="reset" value="1">Use the class breakdown</button><?php } ?>
                                                    </div>
                                                    <?php echo form_close(); ?>
                                                    <?php if (!$s['done']) { ?>
                                                        <?php echo form_open($u('remove_subject'), array('style' => 'margin-top:10px')); ?><input type="hidden" name="tt_id" value="<?php echo (int) $tt; ?>"><button class="btn btn-link btn-xs text-danger" onclick="return confirm('Remove <?php echo html_escape(addslashes($s['name'])); ?> from this term?')"><i class="fa fa-trash"></i> Remove subject from this term</button><?php echo form_close(); ?>
                                                    <?php } ?>
                                                </div></details>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
                <script>
                (function () {
                    function total(box, out) {
                        var t = 0; box.querySelectorAll('input.mx').forEach(function (i) { t += parseFloat(i.value) || 0; });
                        if (out) { out.textContent = 'Total ' + (Math.round(t * 100) / 100); out.className = 'cr-sum ' + (Math.abs(t - 100) < 0.01 ? 'ok' : 'warn'); }
                    }
                    function row() {
                        var s = document.createElement('span'); s.className = 'cr-row';
                        s.innerHTML = '<input class="nm" name="name[]" placeholder="e.g. Second CA"><input class="mx" name="max[]" type="number" step="0.5" min="0.5" placeholder="Marks"><button type="button" class="x">&times;</button>';
                        return s;
                    }
                    document.addEventListener('click', function (e) {
                        if (e.target.classList.contains('x')) { var f = e.target.closest('form'); e.target.closest('.cr-row').remove(); total(f, f.querySelector('.cr-sum')); }
                        if (e.target.closest('#cr_add')) { var a = document.getElementById('cr_add'); a.parentNode.insertBefore(row(), a); }
                        if (e.target.closest('.cr-add1')) { var b = e.target.closest('.cr-add1'); b.parentNode.insertBefore(row(), b); }
                    });
                    document.addEventListener('input', function (e) { if (e.target.classList.contains('mx')) { var f = e.target.closest('form'); total(f, f.querySelector('.cr-sum')); } });
                    var d = document.getElementById('cr_default'); if (d) { total(d, document.getElementById('cr_total')); }
                    document.querySelectorAll('details.cr-pop').forEach(function (x) { x.addEventListener('toggle', function () { if (x.open) { document.querySelectorAll('details.cr-pop[open]').forEach(function (o) { if (o !== x && !o.contains(x)) { o.open = false; } }); } }); });
                })();
                </script>

            <?php } elseif ($tab === 'comments') {
                $r = $results; ?>
                <?php if ($manager) { ?>
                    <div class="cr-box">
                        <?php echo form_open($u('settings'), array('class' => 'form-inline', 'style' => 'display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end')); ?>
                        <input type="hidden" name="tab" value="comments">
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
                        <h4><i class="fa fa-commenting-o"></i> Attendance &amp; comments</h4>
                        <div class="cr-actions">
                            <button type="button" class="btn btn-default btn-sm" id="cm_fill"><i class="fa fa-magic"></i> Fill empty comments</button>
                            <a class="btn btn-default btn-sm" href="<?php echo site_url('cbseexam/exam/comments'); ?>" target="_blank"><i class="fa fa-list"></i> Comment bank</a>
                        </div>
                    </div>
                    <?php echo form_open($u('comments')); ?>
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
                        var n = 0;
                        document.querySelectorAll('tr[data-avg]').forEach(function (tr) {
                            var a = tr.getAttribute('data-avg'); if (a === '') { return; } a = parseFloat(a);
                            var t = tr.querySelector('.cm-t'), p = tr.querySelector('.cm-p'), w = tr.getAttribute('data-weak');
                            if (!t.value.trim()) { var c = pick(bank.teacher, a); if (c) { t.value = c + (w ? ' Needs to improve in ' + w + '.' : ''); n++; } }
                            if (!p.value.trim()) { var d = pick(bank.principal, a); if (d) { p.value = d; n++; } }
                        });
                        if (window.successMsg) { successMsg(n ? n + ' comment(s) filled in. Check them, then Save.' : 'Every comment is already written.'); }
                    });
                })();
                </script>

            <?php } else {
                $r = $results;
                $avgs = array_filter(array_column($r['students'], 'average'), function ($v) { return $v !== null; });
                $approved = count(array_filter($subjects, function ($s) { return $s['marks_status'] === 'approved'; }));
                $sections = array();
                foreach ($r['students'] as $st) { $sections[(int) $st['section_id']] = $st['section']; }
                uasort($r['students'], function ($a, $b) { return strcmp($a['section'], $b['section']) ?: ((isset($a['position']) ? $a['position'] : 1e9) <=> (isset($b['position']) ? $b['position'] : 1e9)) ?: strcmp($a['name'], $b['name']); }); ?>
                <div class="cr-kpis">
                    <div class="cr-kpi"><small>Students</small><b><?php echo count($r['students']); ?></b></div>
                    <div class="cr-kpi"><small>Class average</small><b><?php echo $avgs ? $n(round(array_sum($avgs) / count($avgs), 2)) . '%' : '–'; ?></b></div>
                    <div class="cr-kpi"><small>Subjects approved</small><b><?php echo $approved . ' / ' . count($subjects); ?></b></div>
                    <div class="cr-kpi"><small>Report cards</small><b style="font-size:16px;color:<?php echo (int) $exam['is_publish'] ? '#15803d' : '#92400e'; ?>"><?php echo (int) $exam['is_publish'] ? 'Published' : 'Not published'; ?></b></div>
                </div>
                <div class="cr-box">
                    <div class="cr-actions">
                        <a class="btn btn-default" target="_blank" href="<?php echo $u('card'); ?>"><i class="fa fa-print"></i> Print all</a>
                        <a class="btn btn-default" target="_blank" href="<?php echo $u('pdf'); ?>"><i class="fa fa-file-pdf-o"></i> PDF of all</a>
                        <?php if ($manager) { ?>
                            <?php if ($approved < count($subjects)) { ?><?php echo form_open($u('approve_all'), array('style' => 'display:inline')); ?><button class="btn btn-success" onclick="return confirm('Approve and lock the scores of every subject?')"><i class="fa fa-lock"></i> Approve all subjects</button><?php echo form_close(); ?><?php } ?>
                            <?php echo form_open($u('publish'), array('style' => 'display:inline')); ?><input type="hidden" name="publish" value="<?php echo (int) $exam['is_publish'] ? 0 : 1; ?>">
                            <button class="btn <?php echo (int) $exam['is_publish'] ? 'btn-default' : 'btn-primary'; ?>" <?php echo !(int) $exam['is_publish'] && $approved < count($subjects) ? 'onclick="return confirm(\'Some subjects are not approved yet. Publish anyway?\')"' : ''; ?>><i class="fa fa-globe"></i> <?php echo (int) $exam['is_publish'] ? 'Withdraw from portal' : 'Publish to students & parents'; ?></button><?php echo form_close(); ?>
                            <?php echo form_open($u('email'), array('style' => 'display:inline')); ?><button class="btn btn-default" <?php echo (int) $exam['is_publish'] ? 'onclick="return confirm(\'Email each parent their child\\\'s report card (PDF)?\')"' : 'disabled title="Publish first"'; ?>><i class="fa fa-envelope-o"></i> Email to parents</button><?php echo form_close(); ?>
                        <?php } ?>
                    </div>
                    <p class="cr-muted" style="margin:10px 0 0;font-size:12.5px">Positions are within each arm; equal averages share a position. <?php echo $r['cumulative'] ? 'This is the last term: report cards also show each term and the cumulative average.' : ''; ?></p>
                </div>
                <?php if (!$r['students'] || !$subjects) { ?>
                    <div class="alert alert-info">No <?php echo !$subjects ? 'subjects' : 'students'; ?> yet.</div>
                <?php } else { ?>
                <div class="cr-box" style="overflow:auto">
                    <table class="cr-table">
                        <thead><tr><th>Pos.</th><th>Student</th>
                            <?php foreach ($r['subjects'] as $tt => $s) { ?><th title="<?php echo html_escape($s['name']); ?>" style="text-align:center"><?php echo html_escape($s['code'] ?: (mb_strlen($s['name']) > 10 ? mb_substr($s['name'], 0, 9) . '.' : $s['name'])); ?></th><?php } ?>
                            <th style="text-align:center">Total</th><th style="text-align:center">Average</th><th style="text-align:center">Grade</th><?php echo $r['cumulative'] ? '<th style="text-align:center">Cumulative</th>' : ''; ?><th></th></tr></thead>
                        <tbody>
                        <?php $last = null; foreach ($r['students'] as $es => $st) {
                            if (count($sections) > 1 && $last !== $st['section']) { $last = $st['section']; ?><tr><td colspan="<?php echo count($r['subjects']) + 7; ?>" style="background:#f8fafc;font-weight:800"><?php echo html_escape($class['class'] . ' ' . $st['section']); ?></td></tr><?php } ?>
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
    </section>
</div>
