<?php
/*
 * Report Cards > Results > a class > a subject (Results::subject): the terms; each term's exams (CAs,
 * tests, exam; written or CBT); every student's score in each exam of the chosen term, typed in here.
 */
$cr_msg = $this->session->flashdata('cr_msg');
$n = function ($v) { return rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$base = 'cbseexam/results';
$cid = (int) $class['id'];
$sid = (int) $subject['id'];
$cur = isset($per[$term]) ? $per[$term] : null;
$exam = $cur ? $cur['exam'] : null;
$tt = $cur ? $cur['tt'] : null;
$here = $base . '/subject/' . $cid . '/' . $sid . '?term=' . (int) $term . ($section ? '&section=' . $section : '');
$locked = $lock_reason !== '';
$can_type = $this->rbac->hasPrivilege('cbse_exam_marks', 'can_edit') && !$locked;
$status = $tt_row && isset($tt_row['marks_status']) ? $tt_row['marks_status'] : 'draft';
$labels = array('draft' => array('Entering scores', '#475569', '#f1f5f9', 'fa-pencil'), 'submitted' => array('Submitted for review', '#92400e', '#fef3c7', 'fa-hourglass-half'), 'approved' => array('Approved & locked', '#15803d', '#dcfce7', 'fa-lock'));
$this->load->view('admin/cbtexams/_style');
$u = $exam ? function ($action) use ($base, $exam, $cid) { return site_url($base . '/' . $action . '/' . (int) $exam['id'] . '/' . $cid); } : null;
?>
<style>
    .sj-top{display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;justify-content:space-between;margin-bottom:14px}
    .sj-top h3{margin:0;font-weight:800}
    .sj-muted{color:#64748b}
    .sj-pills{display:inline-flex;flex-wrap:wrap;background:#fff;border:1px solid #e5e9f0;border-radius:12px;padding:4px;gap:4px}
    .sj-pills a{padding:7px 14px;border-radius:9px;font-weight:600;color:#475569}
    .sj-pills a.on{background:var(--ui-brand,#1f4e79);color:#fff}
    .sj-terms{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-bottom:16px}
    .sj-term{display:block;background:#fff;border:2px solid #e5e9f0;border-radius:16px;padding:14px 16px;color:#0f172a}
    .sj-term:hover{text-decoration:none;color:#0f172a;border-color:#cbd5e1}
    .sj-term.on{border-color:var(--ui-brand,#1f4e79);box-shadow:0 10px 26px -18px var(--ui-brand,#1f4e79)}
    .sj-term h4{margin:0 0 6px;font-weight:800}
    .sj-term .ex{font-size:12px;color:#475569;line-height:1.7}
    .sj-term .ex span{display:inline-block;background:#f1f5f9;border-radius:6px;padding:0 6px;margin:0 3px 3px 0}
    .sj-term .ex span.c{background:#ede9fe;color:#6d28d9}
    .sj-prog{height:6px;background:#eef2f6;border-radius:99px;overflow:hidden;margin-top:8px}
    .sj-prog i{display:block;height:100%;background:#22c55e}
    .sj-box{background:#fff;border:1px solid #e5e9f0;border-radius:16px;padding:16px 18px;margin-bottom:14px}
    .sj-box h4{margin:0 0 4px;font-weight:800}
    .sj-exams{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
    .sj-chip{display:inline-flex;align-items:center;gap:8px;border:1px solid #e2e8f0;border-radius:12px;padding:8px 12px;background:#fff;font-weight:700}
    .sj-chip small{color:#64748b;font-weight:600}
    .sj-chip .k{border-radius:6px;padding:2px 7px;font-size:10px;font-weight:800;letter-spacing:.03em}
    .sj-chip .k.w{background:#f1f5f9;color:#475569}.sj-chip .k.c{background:#ede9fe;color:#6d28d9}
    details.sj-pop{display:inline-block;position:relative}
    details.sj-pop>summary{list-style:none;cursor:pointer}
    details.sj-pop>summary::-webkit-details-marker{display:none}
    details.sj-pop[open]>div{position:absolute;z-index:20;top:110%;left:0;background:#fff;border:1px solid #e5e9f0;border-radius:12px;box-shadow:0 18px 40px -18px rgba(15,23,42,.4);padding:12px;min-width:320px}
    .sj-bd{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-top:8px}
    .sj-bd .row1{display:inline-flex;gap:4px;align-items:center;background:#f8fafc;border:1px solid #e5e9f0;border-radius:10px;padding:4px 6px}
    .sj-bd input{border:0;background:transparent;outline:none;font-weight:600}
    .sj-bd input.nm{width:130px}.sj-bd input.mx{width:52px;text-align:right}
    .sj-bd .x{border:0;background:none;color:#94a3b8;cursor:pointer}
    .sj-bar{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:10px}
    .sj-bar .gap{flex:1}
    .sj-pill{display:inline-flex;gap:6px;align-items:center;border-radius:99px;padding:5px 12px;font-size:12.5px;font-weight:700;background:#f1f5f9;color:#334155}
    .sj-pill.ok{background:#dcfce7;color:#15803d}.sj-pill.warn{background:#fef3c7;color:#92400e}.sj-pill.lock{background:#fee2e2;color:#b91c1c}
    .sc-wrap{overflow:auto;max-height:66vh;border:1px solid #e5e9f0;border-radius:12px}
    .sc-table{width:100%;border-collapse:separate;border-spacing:0}
    .sc-table th{position:sticky;top:0;z-index:2;background:#f8fafc;font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:#475569;padding:9px 10px;border-bottom:2px solid #e2e8f0;white-space:nowrap;text-align:center}
    .sc-table th small{display:block;text-transform:none;letter-spacing:0;color:#94a3b8;font-weight:600}
    .sc-table td{padding:7px 10px;border-bottom:1px solid #f1f5f9;text-align:center;vertical-align:middle}
    .sc-table td.nm,.sc-table th.nm{text-align:left;position:sticky;left:0;background:#fff;z-index:1;min-width:200px}
    .sc-table th.nm{z-index:3;background:#f8fafc}
    .sc-table input.sc{width:78px;text-align:center;font-weight:700;border:1px solid #dbe2ea;border-radius:8px;padding:6px 4px}
    .sc-table input.sc:focus{outline:2px solid var(--ui-brand,#1f4e79);border-color:transparent}
    .sc-table input.bad{border-color:#dc2626;background:#fef2f2;color:#b91c1c}
    .sc-table input[readonly]{background:#f8fafc;color:#64748b}
    .sc-table tr.done td.nm{box-shadow:inset 3px 0 0 #22c55e}
    .sc-tot{font-weight:800}
    .sc-cbtv{display:inline-block;min-width:60px;padding:6px 8px;border-radius:8px;background:#f5f3ff;color:#5b21b6;font-weight:800}
    .sc-cbt{display:inline-block;margin-top:3px;border-radius:6px;padding:1px 6px;font-size:10px;font-weight:800;background:#ede9fe;color:#6d28d9}
    @media (max-width:640px){.sc-table input.sc{width:60px}.sc-table td.nm,.sc-table th.nm{min-width:140px}}
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="sj-top">
            <div>
                <a href="<?php echo site_url($base . '/klass/' . $cid . ($section ? '?section=' . $section : '')); ?>" class="sj-muted"><i class="fa fa-angle-left"></i> <?php echo html_escape($class['class']); ?> subjects</a>
                <h3><?php echo html_escape($subject['name']); ?> <small class="sj-muted"><?php echo html_escape($class['class']); ?></small></h3>
            </div>
            <?php if (count($sections) > 1) { ?>
                <div class="sj-pills">
                    <a class="<?php echo !$section ? 'on' : ''; ?>" href="<?php echo site_url($base . '/subject/' . $cid . '/' . $sid . '?term=' . (int) $term); ?>">All sections</a>
                    <?php foreach ($sections as $s) { ?><a class="<?php echo (int) $s['id'] === $section ? 'on' : ''; ?>" href="<?php echo site_url($base . '/subject/' . $cid . '/' . $sid . '?term=' . (int) $term . '&section=' . (int) $s['id']); ?>"><?php echo html_escape($class['class'] . ' ' . $s['section']); ?></a><?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php if ($cr_msg) { ?><div class="alert alert-<?php echo $cr_msg[0]; ?>"><?php echo html_escape($cr_msg[1]); ?></div><?php } ?>

        <div class="sj-terms">
            <?php foreach ($per as $tid => $row) { $x = $row['tt']; $pct = $x && $x['total'] ? round($x['done'] * 100 / $x['total']) : 0; ?>
                <a class="sj-term<?php echo (int) $tid === (int) $term ? ' on' : ''; ?>" href="<?php echo site_url($base . '/subject/' . $cid . '/' . $sid . '?term=' . (int) $tid . ($section ? '&section=' . $section : '')); ?>">
                    <h4><?php echo html_escape($row['term']['name']); ?></h4>
                    <?php if (!$row['exam']) { ?><div class="sj-muted" style="font-size:12px">Not set up</div>
                    <?php } elseif (!$x) { ?><div class="sj-muted" style="font-size:12px">Not offered this term</div>
                    <?php } else { ?>
                        <div class="ex"><?php foreach ($x['types'] as $t) { ?><span class="<?php echo $t['cbt'] ? 'c' : ''; ?>"><?php echo html_escape($t['name']) . ' ' . $n($t['maximum_marks']); ?></span><?php } ?></div>
                        <div class="sj-prog"><i style="width:<?php echo $pct; ?>%"></i></div>
                        <div class="sj-muted" style="font-size:11.5px;margin-top:3px"><?php echo (int) $x['done']; ?> of <?php echo (int) $x['total']; ?> students scored<?php echo $x['marks_status'] === 'approved' ? ' · <i class="fa fa-lock"></i> approved' : ''; ?></div>
                    <?php } ?>
                </a>
            <?php } ?>
        </div>

        <?php if (!$cur) { ?>
            <div class="alert alert-warning">Add your terms in Report Cards &gt; Terms.</div>
        <?php } elseif (!$exam) { ?>
            <div class="sj-box text-center" style="padding:30px">
                <h4><?php echo html_escape($cur['term']['name']); ?> is not set up for <?php echo html_escape($class['class']); ?></h4>
                <p class="sj-muted">Setting up gives every subject of the class its exams: First CA 10, Mid-term Test 20, Exam 70 (or what the class used last). You can change them here for <?php echo html_escape($subject['name']); ?>.</p>
                <?php if ($manager) { ?><?php echo form_open($base . '/setup_term'); ?><input type="hidden" name="class_id" value="<?php echo $cid; ?>"><input type="hidden" name="term" value="<?php echo (int) $term; ?>"><input type="hidden" name="return" value="<?php echo html_escape($here); ?>"><button class="btn btn-primary btn-lg"><i class="fa fa-magic"></i> Set up <?php echo html_escape($cur['term']['name']); ?></button><?php echo form_close(); ?><?php } ?>
            </div>
        <?php } elseif (!$tt) { ?>
            <div class="sj-box text-center" style="padding:30px">
                <h4><?php echo html_escape($subject['name']); ?> is not in <?php echo html_escape($class['class'] . ' ' . $cur['term']['name']); ?></h4>
                <?php if ($manager) { ?><?php echo form_open($u('add_subject')); ?><input type="hidden" name="subject_id[]" value="<?php echo $sid; ?>"><input type="hidden" name="return" value="<?php echo html_escape($here); ?>"><button class="btn btn-primary"><i class="fa fa-plus"></i> Add <?php echo html_escape($subject['name']); ?> to <?php echo html_escape($cur['term']['name']); ?></button><?php echo form_close(); ?><?php } ?>
            </div>
        <?php } else { $lb = $labels[isset($labels[$status]) ? $status : 'draft']; ?>
            <div class="sj-box">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between">
                    <h4><i class="fa fa-list-ol"></i> <?php echo html_escape($cur['term']['name']); ?> exams <small class="sj-muted">· total <?php echo $n($tt['max']); ?> marks</small></h4>
                    <?php if ($manager) { ?>
                        <details class="sj-pop"><summary class="btn btn-default btn-sm"><i class="fa fa-sliders"></i> Change exams</summary><div style="right:0;left:auto;min-width:460px">
                            <b><?php echo html_escape($subject['name'] . ' · ' . $cur['term']['name']); ?></b>
                            <div class="sj-muted" style="font-size:12px">Each exam and its marks (e.g. First CA 10, Mid-term Test 20, Exam 70).</div>
                            <?php echo form_open($u('breakdown'), array('id' => 'sj_bd')); ?>
                            <input type="hidden" name="tt_id" value="<?php echo (int) $tt['id']; ?>"><input type="hidden" name="return" value="<?php echo html_escape($here); ?>">
                            <div class="sj-bd">
                                <?php foreach ($tt['types'] as $t) { ?><span class="row1"><input class="nm" name="name[]" value="<?php echo html_escape($t['name']); ?>" aria-label="Exam"><input class="mx" name="max[]" type="number" step="0.5" min="0.5" value="<?php echo $n($t['maximum_marks']); ?>" aria-label="Marks"><button type="button" class="x" title="Remove">&times;</button></span><?php } ?>
                                <button type="button" class="btn btn-default btn-xs" id="sj_add"><i class="fa fa-plus"></i> Add exam</button>
                                <span class="sj-pill" id="sj_total"></span>
                            </div>
                            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px">
                                <button class="btn btn-primary btn-sm">Save for <?php echo html_escape($subject['name']); ?></button>
                                <button class="btn btn-default btn-sm" name="all" value="1" onclick="return confirm('Use these exams for every subject of <?php echo html_escape(addslashes($class['class'])); ?> in <?php echo html_escape(addslashes($cur['term']['name'])); ?>?')">Save for all <?php echo html_escape($class['class']); ?> subjects</button>
                                <?php if ((int) $tt['custom_breakdown']) { ?><button class="btn btn-link btn-sm" name="reset" value="1">Use the class's exams</button><?php } ?>
                            </div>
                            <?php echo form_close(); ?>
                        </div></details>
                    <?php } ?>
                </div>
                <div class="sj-exams">
                    <?php foreach ($tt['types'] as $tid => $t) { ?>
                        <?php if ($manager) { ?>
                            <details class="sj-pop"><summary class="sj-chip"><?php echo html_escape($t['name']); ?> <small><?php echo $n($t['maximum_marks']); ?> marks</small><span class="k <?php echo $t['cbt'] ? 'c' : 'w'; ?>"><?php echo $t['cbt'] ? 'CBT' : 'WRITTEN'; ?></span><i class="fa fa-caret-down sj-muted"></i></summary><div>
                                <b><?php echo html_escape($t['name']); ?>: how is it taken?</b>
                                <?php if ($t['cbt']) { ?>
                                    <p class="sj-muted" style="margin:6px 0">Taken on computer: the CBT "<?php echo html_escape($t['cbt']['exam']); ?>" (below). Scores come from it, scaled to <?php echo $n($t['maximum_marks']); ?>; they are not typed in.</p>
                                    <?php echo form_open($u('cbt'), array('style' => 'display:inline')); ?><input type="hidden" name="return" value="<?php echo html_escape($here); ?>"><input type="hidden" name="tt_id" value="<?php echo (int) $tt['id']; ?>"><input type="hidden" name="type_id" value="<?php echo (int) $tid; ?>"><input type="hidden" name="unlink" value="1"><button class="btn btn-default btn-xs" onclick="return confirm('Make it a written exam? Scores will be typed in; the CBT stays as practice.')">Make it written instead</button><?php echo form_close(); ?>
                                <?php } else { ?>
                                    <p class="sj-muted" style="margin:6px 0">Written: scores are typed in the table below. To take it on computer instead, make a CBT for it:</p>
                                    <?php $this->load->view('admin/cbtexams/_new', array('class' => $class, 'subject' => $subject, 'targets' => isset($targets) ? $targets : array(), 'return' => $here, 'preset' => 'cbse:' . (int) $tt['id'] . ':' . (int) $tid, 'label' => '<i class="fa fa-laptop"></i> Make it a CBT')); ?>
                                    <?php $free = array_filter($cbts, function ($o) use ($sid) { return !$o['marks_target'] && (int) $o['subject_id'] === $sid; });
                                    if ($free) { ?>
                                        <?php echo form_open($u('cbt'), array('style' => 'margin-top:8px')); ?>
                                        <input type="hidden" name="return" value="<?php echo html_escape($here); ?>"><input type="hidden" name="tt_id" value="<?php echo (int) $tt['id']; ?>"><input type="hidden" name="type_id" value="<?php echo (int) $tid; ?>">
                                        <small class="sj-muted">or use a CBT already made:</small>
                                        <select name="onlineexam_id" class="form-control input-sm"><?php foreach ($free as $o) { ?><option value="<?php echo (int) $o['id']; ?>"><?php echo html_escape($o['exam']); ?></option><?php } ?></select>
                                        <button class="btn btn-default btn-xs btn-block" style="margin-top:4px">Use it</button>
                                        <?php echo form_close(); ?>
                                    <?php } ?>
                                <?php } ?>
                            </div></details>
                        <?php } else { ?>
                            <span class="sj-chip"><?php echo html_escape($t['name']); ?> <small><?php echo $n($t['maximum_marks']); ?> marks</small><span class="k <?php echo $t['cbt'] ? 'c' : 'w'; ?>"><?php echo $t['cbt'] ? 'CBT' : 'WRITTEN'; ?></span></span>
                        <?php } ?>
                    <?php } ?>
                    <?php if (!$tt['types']) { ?><span class="text-danger">No exams yet: add them with Change exams.</span><?php } ?>
                </div>
            </div>

            <?php if (!empty($cbt_rows)) { ?>
                <div class="sj-box">
                    <h4><i class="fa fa-laptop"></i> CBT <small class="sj-muted">scores come from here when students submit</small></h4>
                    <div class="cbt-cards" style="margin-top:10px">
                        <?php foreach ($cbt_rows as $o) { $this->load->view('admin/cbtexams/_card', array('o' => $o, 'return' => $here, 'class_id' => $cid, 'subject_id' => $sid, 'targets' => array())); } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="sj-box">
                <div class="sj-bar">
                    <h4 style="margin:0"><i class="fa fa-users"></i> Students' scores</h4>
                    <span class="sj-pill" style="background:<?php echo $lb[2]; ?>;color:<?php echo $lb[1]; ?>"><i class="fa <?php echo $lb[3]; ?>"></i> <?php echo $lb[0]; ?></span>
                    <?php if ($locked) { ?><span class="sj-pill lock"><i class="fa fa-lock"></i> <?php echo html_escape($lock_reason); ?></span><?php } else { ?><span class="sj-pill" id="sc_count"></span><?php } ?>
                    <span class="gap"></span>
                    <span id="sc_state" class="sj-muted"></span>
                    <?php if ($status === 'draft' && $can_type) { ?><button type="button" class="btn btn-info btn-sm sc-flow" data-action="submit"><i class="fa fa-paper-plane"></i> Submit for review</button><?php } ?>
                    <?php if ($can_approve && $status !== 'approved') { ?><button type="button" class="btn btn-success btn-sm sc-flow" data-action="approve"><i class="fa fa-lock"></i> Approve &amp; lock</button><?php } ?>
                    <?php if ($can_approve && $status === 'submitted') { ?><button type="button" class="btn btn-warning btn-sm sc-flow" data-action="return"><i class="fa fa-reply"></i> Return</button><?php } ?>
                    <?php if ($can_approve && $status === 'approved') { ?><button type="button" class="btn btn-warning btn-sm sc-flow" data-action="unlock"><i class="fa fa-unlock"></i> Unlock</button><?php } ?>
                    <?php if ($can_type) { ?><button type="button" class="btn btn-primary btn-sm" id="sc_save"><i class="fa fa-save"></i> Save scores</button><?php } ?>
                </div>
                <?php if (!$students) { ?>
                    <div class="alert alert-info" style="margin:0">No students<?php echo $section ? ' in this section' : ''; ?>.</div>
                <?php } else { ?>
                    <p class="sj-muted" style="font-size:12.5px;margin:0 0 8px">Type each score (or ABS for absent). Enter moves to the next student. Ctrl+S saves.</p>
                    <div class="sc-wrap">
                        <table class="sc-table" id="sc_table">
                            <thead><tr><th class="nm">Student</th>
                                <?php foreach ($tt['types'] as $tid => $t) { ?><th><?php echo html_escape($t['name']); ?><small>out of <?php echo $n($t['maximum_marks']); ?></small><?php echo $t['cbt'] ? '<span class="sc-cbt">CBT</span>' : ''; ?></th><?php } ?>
                                <th>Total<small>of <?php echo $n($tt['max']); ?></small></th></tr></thead>
                            <tbody>
                            <?php $last = null; foreach ($students as $es => $st) {
                                if (!$section && count($sections) > 1 && $last !== $st['section']) { $last = $st['section']; ?><tr><td class="nm" colspan="<?php echo count($tt['types']) + 2; ?>" style="background:#f8fafc;font-weight:800;font-size:12px"><?php echo html_escape($class['class'] . ' ' . $st['section']); ?></td></tr><?php } ?>
                                <tr class="sc-row">
                                    <td class="nm"><b><?php echo html_escape($st['name']); ?></b><br><small class="sj-muted"><?php echo html_escape($st['admission_no']); ?></small></td>
                                    <?php foreach ($tt['types'] as $tid => $t) { $v = isset($marks[$es][$tid]) ? $marks[$es][$tid] : ''; ?>
                                        <?php if ($t['cbt']) { ?>
                                            <td><span class="sc-cbtv" data-v="<?php echo html_escape($v); ?>" title="From the CBT"><?php echo $v === '' ? '<span class="sj-muted">not taken</span>' : html_escape($v); ?></span></td>
                                        <?php } else { ?>
                                        <td><input class="sc" name="m[<?php echo (int) $es; ?>][<?php echo (int) $tid; ?>]" value="<?php echo html_escape($v); ?>" data-max="<?php echo (float) $t['maximum_marks']; ?>" inputmode="decimal" autocomplete="off" aria-label="<?php echo html_escape($st['name'] . ' ' . $t['name']); ?>" <?php echo $can_type ? '' : 'readonly'; ?>></td>
                                        <?php } ?>
                                    <?php } ?>
                                    <td class="sc-tot">0</td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </section>
</div>
<script>
(function ($) {
    // exams editor
    function sum() { var t = 0; $('#sj_bd input.mx').each(function () { t += parseFloat(this.value) || 0; }); $('#sj_total').text('Total ' + (Math.round(t * 100) / 100)).attr('class', 'sj-pill ' + (Math.abs(t - 100) < 0.01 ? 'ok' : 'warn')); }
    $(document).on('click', '#sj_bd .x', function () { $(this).closest('.row1').remove(); sum(); });
    $(document).on('click', '#sj_add', function () { $('<span class="row1"><input class="nm" name="name[]" placeholder="e.g. Second CA"><input class="mx" name="max[]" type="number" step="0.5" min="0.5" placeholder="Marks"><button type="button" class="x">&times;</button></span>').insertBefore(this); });
    $(document).on('input', '#sj_bd input.mx', sum);
    if ($('#sj_bd').length) { sum(); }
    $('details.sj-pop').on('toggle', function () { var me = this; if (me.open) { $('details.sj-pop[open]').each(function () { if (this !== me && !$.contains(this, me)) { this.open = false; } }); } });

    // scores
    var table = $('#sc_table');
    if (!table.length) { return; }
    var dirty = false, url = <?php echo json_encode($exam && $tt ? site_url($base . '/scores/' . (int) $exam['id'] . '/' . $cid . '/' . (int) $tt['id']) : ''); ?>,
        csrf = <?php echo json_encode(array($this->security->get_csrf_token_name(), $this->security->get_csrf_hash())); ?>;
    function fmt(x) { return String(Math.round(x * 100) / 100); }
    function row(tr) {
        var s = 0, filled = 0, all = 0;
        tr.find('.sc-cbtv').each(function () { all++; var v = this.getAttribute('data-v'); if (v !== '') { filled++; if (v !== 'ABS') { s += parseFloat(v) || 0; } } });
        tr.find('input.sc').each(function () {
            all++;
            var v = $.trim(this.value).toUpperCase(), max = parseFloat(this.getAttribute('data-max'));
            var bad = v !== '' && v !== 'ABS' && v !== 'A' && (isNaN(parseFloat(v)) || parseFloat(v) < 0 || parseFloat(v) > max);
            $(this).toggleClass('bad', bad).attr('title', bad ? 'A score from 0 to ' + max + ', or ABS' : '');
            if (v !== '') { filled++; }
            if (v !== '' && v !== 'ABS' && v !== 'A' && !bad) { s += parseFloat(v); }
        });
        tr.find('.sc-tot').text(fmt(s));
        tr.toggleClass('done', filled === all);
    }
    function count() {
        var rows = table.find('tr.sc-row'), done = rows.filter('.done').length, bad = table.find('input.bad').length;
        $('#sc_count').attr('class', 'sj-pill ' + (bad ? 'warn' : (done === rows.length ? 'ok' : ''))).html('<i class="fa ' + (bad ? 'fa-exclamation-triangle' : 'fa-check-circle') + '"></i> ' + done + ' of ' + rows.length + ' complete' + (bad ? ' · ' + bad + ' not valid' : ''));
    }
    table.find('tr.sc-row').each(function () { row($(this)); });
    count();
    table.on('input', 'input.sc', function () { row($(this).closest('tr')); count(); dirty = true; $('#sc_state').text('Not saved yet'); });
    table.on('keydown', 'input.sc', function (e) {
        var down = e.key === 'Enter' || e.key === 'ArrowDown', up = e.key === 'ArrowUp';
        if (!down && !up) { return; }
        e.preventDefault();
        var td = $(this).closest('td'), col = td.index(), tr = td.closest('tr');
        var next = down ? tr.nextAll('tr.sc-row').first() : tr.prevAll('tr.sc-row').first();
        if (next.length) { next.children().eq(col).find('input').focus().select(); }
    });
    function save() {
        if (table.find('input.bad').length) { errorMsg('Fix the scores in red first.'); return; }
        var data = table.find('input.sc').serializeArray();
        data.push({ name: csrf[0], value: csrf[1] });
        $('#sc_save').prop('disabled', true);
        $.post(url, data, null, 'json').done(function (r) {
            if (r.status === 'success') { dirty = false; $('#sc_state').text('Saved ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })); successMsg(r.message); }
            else { errorMsg(r.message); }
        }).fail(function () { errorMsg('Could not save. Check your connection and try again.'); }).always(function () { $('#sc_save').prop('disabled', false); });
    }
    $('#sc_save').on('click', save);
    $(document).on('keydown', function (e) { if ((e.ctrlKey || e.metaKey) && e.key === 's' && $('#sc_save').length) { e.preventDefault(); save(); } });
    window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
    $('.sc-flow').on('click', function () {
        var action = $(this).data('action'), note = '';
        if (dirty && (action === 'submit' || action === 'approve')) { errorMsg('Save the scores first.'); return; }
        if (action === 'unlock' || action === 'return') { note = window.prompt(action === 'unlock' ? 'Why are you unlocking these scores?' : 'What should the teacher change?', ''); if (!note) { return; } }
        else if (!window.confirm(action === 'approve' ? 'Approve and lock these scores?' : 'Submit these scores for review? You cannot change them unless they are returned.')) { return; }
        var d = { 'timetable_id[]': <?php echo $tt ? (int) $tt['id'] : 0; ?>, action: action, note: note };
        d[csrf[0]] = csrf[1];
        $.post(<?php echo json_encode(site_url('cbseexam/exam/marks_status')); ?>, d, function (r) {
            if (r.status === 'success') { successMsg(r.message); setTimeout(function () { location.reload(); }, 600); } else { errorMsg(r.message); }
        }, 'json');
    });
})(jQuery);
</script>
