<?php
/* CBT > Invigilate (Onlineexam::invigilate): live state of each student, tab switches, extra time. */
$iv_msg = $this->session->flashdata('inv_msg');
$iv_now = time();
$iv_counts = array('not' => 0, 'running' => 0, 'done' => 0);
foreach ($rows as &$r) {
    if (!$r['attempt_id']) {
        $r['state'] = 'not';
    } elseif (!$r['submitted_at'] && strtotime($r['ends_at']) > $iv_now) {
        $r['state'] = 'running';
    } else {
        $r['state'] = 'done';
    }
    $iv_counts[$r['state']]++;
}
unset($r);
$iv_max = (int) $exam['max_tab_switches'];
?>
<style>
    .iv-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:14px}
    .iv-kpi{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:12px 16px}
    .iv-kpi small{display:block;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.04em}
    .iv-kpi b{font-size:24px}
    .iv-b{display:inline-flex;align-items:center;gap:5px;border-radius:999px;padding:3px 10px;font-size:12px;font-weight:700;white-space:nowrap}
    .iv-not{background:#f1f5f9;color:#64748b}.iv-running{background:#dbeafe;color:#1d4ed8}.iv-done{background:#dcfce7;color:#15803d}.iv-forced{background:#fee2e2;color:#b91c1c}
    .iv-warn{color:#b91c1c;font-weight:700}
    .iv-table td{vertical-align:middle!important}
    .iv-extra{display:flex;gap:6px;align-items:center}
    .iv-extra input{border-radius:8px}
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-eye"></i> Invigilate: <?php echo html_escape($exam['exam']); ?></h3>
                <div class="box-tools pull-right">
                    <a class="btn btn-sm btn-default" href="<?php echo site_url('admin/onlineexam/results/' . (int) $exam['id']); ?>"><i class="fa fa-bar-chart"></i> Results</a>
                    <a class="btn btn-sm btn-default" href="<?php echo site_url('admin/onlineexam'); ?>"><i class="fa fa-arrow-left"></i> Exams</a>
                </div>
            </div>
            <div class="box-body">
                <?php if ($iv_msg) { ?><div class="alert alert-<?php echo $iv_msg[0]; ?>"><?php echo html_escape($iv_msg[1]); ?></div><?php } ?>
                <div class="iv-kpis">
                    <div class="iv-kpi"><small>Students</small><b><?php echo count($rows); ?></b></div>
                    <div class="iv-kpi"><small>Writing now</small><b style="color:#1d4ed8"><?php echo $iv_counts['running']; ?></b></div>
                    <div class="iv-kpi"><small>Submitted</small><b style="color:#15803d"><?php echo $iv_counts['done']; ?></b></div>
                    <div class="iv-kpi"><small>Not started</small><b style="color:#64748b"><?php echo $iv_counts['not']; ?></b></div>
                </div>
                <p class="text-muted small"><i class="fa fa-shield"></i> Leaving the exam page: <?php echo $iv_max ? 'submitted after ' . $iv_max . ' times' : 'recorded only'; ?> · copy and paste <?php echo (int) $exam['block_copy'] ? 'blocked' : 'allowed'; ?>. This page refreshes every 30 seconds (paused while you type).</p>
                <div class="table-responsive">
                    <table class="table table-striped iv-table">
                        <thead><tr><th>Student</th><th>Status</th><th>Time left / submitted</th><th class="text-center">Left the page</th><th>Extra time</th><?php if ($can_edit) { ?><th>Give extra time</th><?php } ?></tr></thead>
                        <tbody>
                            <?php foreach ($rows as $r) { ?>
                                <tr>
                                    <td><b><?php echo html_escape(trim($r['name'])); ?></b><br><small class="text-muted"><?php echo html_escape($r['admission_no'] . ' · ' . $r['class'] . ' (' . $r['section'] . ')'); ?></small></td>
                                    <td>
                                        <?php if ($r['state'] === 'not') { ?><span class="iv-b iv-not">Not started</span>
                                        <?php } elseif ($r['state'] === 'running') { ?><span class="iv-b iv-running"><i class="fa fa-pencil"></i> Writing</span>
                                        <?php } elseif ($r['forced_submit']) { ?><span class="iv-b iv-forced"><i class="fa fa-ban"></i> Submitted (left the page)</span>
                                        <?php } else { ?><span class="iv-b iv-done"><i class="fa fa-check"></i> Submitted</span><?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($r['state'] === 'running') { $left = strtotime($r['ends_at']) - $iv_now; echo floor($left / 60) . ' min ' . ($left % 60) . ' s left';
                                        } elseif ($r['state'] === 'done') { echo $r['submitted_at'] ? date('j M, g:i a', strtotime($r['submitted_at'])) : 'time up'; echo $r['percentage'] !== null ? ' · <b>' . (float) $r['percentage'] . '%</b>' : '';
                                        } else { echo '<span class="text-muted">–</span>'; } ?>
                                    </td>
                                    <td class="text-center <?php echo (int) $r['tab_switches'] > 0 ? 'iv-warn' : ''; ?>"><?php echo $r['attempt_id'] ? (int) $r['tab_switches'] . ($iv_max ? ' / ' . $iv_max : '') : '–'; ?></td>
                                    <td><?php echo (int) $r['extra_minutes'] ? '<b>+' . (int) $r['extra_minutes'] . ' min</b><br><small class="text-muted">' . html_escape($r['extra_reason']) . '</small>' : '<span class="text-muted">–</span>'; ?></td>
                                    <?php if ($can_edit) { ?>
                                        <td>
                                            <?php if ($r['attempt_id']) { ?>
                                                <?php echo form_open('admin/onlineexam/extra_time', array('class' => 'iv-extra')); ?>
                                                <input type="hidden" name="os_id" value="<?php echo (int) $r['os_id']; ?>">
                                                <input type="number" name="minutes" min="1" max="300" value="10" class="form-control input-sm" style="width:70px" title="Minutes">
                                                <input type="text" name="reason" maxlength="150" class="form-control input-sm" placeholder="Reason (e.g. power cut)" required>
                                                <button class="btn btn-sm btn-primary" title="<?php echo $r['state'] === 'done' ? 'Opens the exam again for this student' : 'Adds time'; ?>"><i class="fa fa-clock-o"></i> <?php echo $r['state'] === 'done' ? 'Reopen' : 'Add'; ?></button>
                                                <?php echo form_close(); ?>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
(function () {
    var typing = 0;
    document.addEventListener('input', function () { typing = Date.now(); });
    setInterval(function () { if (Date.now() - typing > 30000 && !document.querySelector('.iv-extra input:focus')) { window.location.reload(); } }, 30000);
})();
</script>
