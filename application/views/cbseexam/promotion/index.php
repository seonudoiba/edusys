<?php
/* Report Cards > Promotion (Promotion::index). */
$pr_msg = $this->session->flashdata('pr_msg');
$pr_badge = array('promote' => array('Promote', 'pr-ok', 'fa-arrow-up'), 'borderline' => array('Borderline', 'pr-mid', 'fa-exclamation-triangle'),
    'repeat' => array('Repeat', 'pr-bad', 'fa-repeat'), 'none' => array('No scores', 'pr-none', 'fa-minus'));
$pr_dec = array('promote' => 'Promote', 'probation' => 'Promote on probation', 'repeat' => 'Repeat the class');
?>
<style>
    .pr-filters{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;margin-bottom:14px}
    .pr-filters .form-group{margin:0;min-width:190px}
    .pr-rules{background:#f8fafc;border:1px solid #e5e9f0;border-radius:14px;padding:14px 16px;margin-bottom:16px}
    .pr-rules .row>div{margin-bottom:8px}
    .pr-sum{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px}
    .pr-b{display:inline-flex;align-items:center;gap:5px;border-radius:999px;padding:4px 11px;font-size:12px;font-weight:700;white-space:nowrap}
    .pr-ok{background:#dcfce7;color:#15803d}.pr-mid{background:#fef3c7;color:#92400e}.pr-bad{background:#fee2e2;color:#b91c1c}.pr-none{background:#f1f5f9;color:#64748b}
    .pr-table td{vertical-align:middle!important}
    .pr-why{font-size:12px;color:#64748b}
    .pr-table select,.pr-table input{border-radius:8px}
    tr.pr-row-borderline td{background:#fffbeb}
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-level-up"></i> Promotion</h3>
                <div class="box-tools pull-right"><a class="btn btn-sm btn-default" href="<?php echo site_url('admin/stdtransfer'); ?>"><i class="fa fa-exchange"></i> Promote Students (move to the new class)</a></div>
            </div>
            <div class="box-body">
                <?php if ($pr_msg) { ?><div class="alert alert-success"><?php echo html_escape($pr_msg); ?></div><?php } ?>
                <form method="get" class="pr-filters">
                    <div class="form-group"><label>Class</label>
                        <select name="cs" class="form-control" onchange="this.form.submit()">
                            <option value="">Choose a class</option>
                            <?php foreach ($class_sections as $c) { ?><option value="<?php echo $c['id']; ?>" <?php echo (int) $c['id'] === $cs ? 'selected' : ''; ?>><?php echo html_escape($c['class'] . ' (' . $c['section'] . ')'); ?></option><?php } ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Session</label>
                        <select name="session_id" class="form-control" onchange="this.form.submit()">
                            <?php foreach ($sessions as $s) { ?><option value="<?php echo $s['id']; ?>" <?php echo (int) $s['id'] === $session_id ? 'selected' : ''; ?>><?php echo html_escape($s['session']); ?></option><?php } ?>
                        </select>
                    </div>
                </form>
                <?php if (!$result) { ?>
                    <div class="text-center text-muted" style="padding:50px"><i class="fa fa-level-up fa-3x"></i><p style="margin-top:10px">Choose a class to see each student's annual average, core subjects and attendance against your promotion rules.</p></div>
                <?php } else {
                    list($students, $exams, $rule) = $result;
                    $count = array('promote' => 0, 'borderline' => 0, 'repeat' => 0, 'none' => 0);
                    foreach ($students as $st) { $count[$st['suggestion']]++; } ?>
                    <div class="pr-rules">
                        <?php echo form_open('cbseexam/promotion/rules'); ?>
                        <input type="hidden" name="cs" value="<?php echo $cs; ?>"><input type="hidden" name="session_id" value="<?php echo $session_id; ?>"><input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                        <b><i class="fa fa-sliders"></i> Rules</b> <span class="text-muted small"><?php echo $rule['own'] ? 'this class has its own rules' : 'the school\'s rules (no rules of this class)'; ?></span>
                        <div class="row" style="margin-top:8px">
                            <div class="col-sm-2"><label class="small">Minimum average %</label><input type="number" step="0.01" min="0" max="100" name="min_average" class="form-control input-sm" value="<?php echo (float) $rule['min_average']; ?>" <?php echo $can_edit ? '' : 'disabled'; ?>></div>
                            <div class="col-sm-4"><label class="small">Core subjects (must pass)</label>
                                <select name="core_subject_ids[]" class="form-control input-sm" multiple size="3" <?php echo $can_edit ? '' : 'disabled'; ?>>
                                    <?php foreach ($subjects as $s) { ?><option value="<?php echo $s['id']; ?>" <?php echo in_array((int) $s['id'], $rule['core'], true) ? 'selected' : ''; ?>><?php echo html_escape($s['name']); ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-2"><label class="small">Core pass mark %</label><input type="number" step="0.01" min="0" max="100" name="core_pass_mark" class="form-control input-sm" value="<?php echo (float) $rule['core_pass_mark']; ?>" <?php echo $can_edit ? '' : 'disabled'; ?>></div>
                            <div class="col-sm-2"><label class="small">Minimum attendance %</label><input type="number" step="0.1" min="0" max="100" name="min_attendance" class="form-control input-sm" value="<?php echo (float) $rule['min_attendance']; ?>" <?php echo $can_edit ? '' : 'disabled'; ?>></div>
                            <div class="col-sm-2"><label class="small">Borderline within</label><input type="number" step="0.1" min="0" max="50" name="borderline_margin" class="form-control input-sm" value="<?php echo (float) $rule['borderline_margin']; ?>" <?php echo $can_edit ? '' : 'disabled'; ?>></div>
                        </div>
                        <?php if ($can_edit) { ?>
                            <label class="small" style="font-weight:500;margin-right:12px"><input type="radio" name="scope" value="class" <?php echo $rule['own'] ? 'checked' : ''; ?>> For <?php echo html_escape($class_label); ?> only</label>
                            <label class="small" style="font-weight:500;margin-right:12px"><input type="radio" name="scope" value="school" <?php echo $rule['own'] ? '' : 'checked'; ?>> For every class (school rule)</label>
                            <button class="btn btn-sm btn-default"><i class="fa fa-save"></i> Save rules</button>
                        <?php } ?>
                        <?php echo form_close(); ?>
                    </div>
                    <div class="pr-sum">
                        <?php foreach ($count as $k => $n) { if ($k === 'none' && !$n) { continue; } ?><span class="pr-b <?php echo $pr_badge[$k][1]; ?>"><i class="fa <?php echo $pr_badge[$k][2]; ?>"></i> <?php echo $n . ' ' . $pr_badge[$k][0]; ?></span><?php } ?>
                        <span class="text-muted small" style="align-self:center">From <?php echo count($exams); ?> exam(s) this session<?php echo $exams ? ': ' . html_escape(implode(', ', array_column($exams, 'name'))) : ''; ?>.</span>
                    </div>
                    <?php if (!$students) { ?>
                        <div class="alert alert-info">No students in this class for the session.</div>
                    <?php } else { ?>
                    <?php echo form_open('cbseexam/promotion/decide'); ?>
                    <input type="hidden" name="cs" value="<?php echo $cs; ?>"><input type="hidden" name="session_id" value="<?php echo $session_id; ?>">
                    <div class="table-responsive">
                        <table class="table pr-table">
                            <thead><tr><th>Student</th><th class="text-center">Annual average</th><?php foreach ($rule['core_names'] as $sid => $nm) { ?><th class="text-center"><?php echo html_escape($nm); ?></th><?php } ?><th class="text-center">Attendance</th><th>Suggestion</th><th>Decision</th><th>Reason</th></tr></thead>
                            <tbody>
                                <?php foreach ($students as $ss => $st) { $b = $pr_badge[$st['suggestion']]; $dec = $st['decision']; ?>
                                    <tr class="pr-row-<?php echo $st['suggestion']; ?>">
                                        <td><b><?php echo html_escape($st['name']); ?></b><br><small class="text-muted"><?php echo html_escape($st['admission_no']); ?></small> <a href="<?php echo site_url('cbseexam/broadsheet/transcript/' . $st['student_id']); ?>" target="_blank" title="Transcript"><i class="fa fa-external-link"></i></a></td>
                                        <td class="text-center"><b><?php echo $st['average'] === null ? '–' : $st['average'] . '%'; ?></b><input type="hidden" name="average[<?php echo $ss; ?>]" value="<?php echo $st['average']; ?>"></td>
                                        <?php foreach ($rule['core_names'] as $sid => $nm) { $v = isset($st['core'][$sid]) ? $st['core'][$sid] : null; ?><td class="text-center<?php echo $v !== null && $v < $rule['core_pass_mark'] ? ' text-danger' : ''; ?>"><?php echo $v === null ? '–' : $v . '%'; ?></td><?php } ?>
                                        <td class="text-center"><?php echo $st['attendance'] === null ? '–' : $st['attendance'] . '%'; ?></td>
                                        <td><span class="pr-b <?php echo $b[1]; ?>"><i class="fa <?php echo $b[2]; ?>"></i> <?php echo $b[0]; ?></span><div class="pr-why"><?php echo html_escape($st['why']); ?></div></td>
                                        <td>
                                            <select name="d[<?php echo $ss; ?>][decision]" class="form-control input-sm" <?php echo $can_edit ? '' : 'disabled'; ?>>
                                                <option value="">Not decided</option>
                                                <?php foreach ($pr_dec as $k => $label) { ?><option value="<?php echo $k; ?>" <?php echo $dec && $dec['decision'] === $k ? 'selected' : ''; ?>><?php echo $label; ?></option><?php } ?>
                                            </select>
                                            <?php if ($dec) { ?><small class="text-muted"><?php echo date('j M Y', strtotime($dec['decided_at'])); ?></small><?php } ?>
                                        </td>
                                        <td><input type="text" name="d[<?php echo $ss; ?>][reason]" maxlength="500" class="form-control input-sm" value="<?php echo html_escape($dec ? $dec['reason'] : ''); ?>" placeholder="<?php echo $st['suggestion'] === 'borderline' ? 'Why (kept for parents\' questions)' : 'Optional'; ?>" <?php echo $can_edit ? '' : 'disabled'; ?>></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($can_edit) { ?>
                        <div style="display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap">
                            <button type="button" class="btn btn-default" id="pr_apply"><i class="fa fa-magic"></i> Use suggestions where not decided</button>
                            <button class="btn btn-primary"><i class="fa fa-save"></i> Save decisions</button>
                        </div>
                        <script>
                        document.getElementById('pr_apply').addEventListener('click', function () {
                            document.querySelectorAll('.pr-table tbody tr').forEach(function (tr) {
                                var sel = tr.querySelector('select'), s = (tr.className.match(/pr-row-(\w+)/) || [])[1];
                                if (sel && !sel.value && (s === 'promote' || s === 'repeat')) { sel.value = s; }
                            });
                        });
                        </script>
                    <?php } ?>
                    <?php echo form_close(); ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
