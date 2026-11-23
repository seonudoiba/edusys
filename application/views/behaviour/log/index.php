<?php
/* Report Behaviour (behaviour/Log). */
$e = function ($v) {
    return html_escape((string) $v);
};
$good = array_filter($incidents, function ($i) { return (int) $i['point'] >= 0; });
$bad = array_filter($incidents, function ($i) { return (int) $i['point'] < 0; });
$msg = $this->session->flashdata('beh_msg');
?>
<style>
    .bh-grid{display:grid;grid-template-columns:minmax(300px,420px) 1fr;gap:16px;align-items:start}
    @media (max-width:991px){.bh-grid{grid-template-columns:1fr}}
    .bh-types{display:flex;flex-wrap:wrap;gap:6px;margin:4px 0 2px}
    .bh-types label{display:inline-flex;align-items:center;gap:6px;margin:0;padding:6px 10px;border:1px solid #e5e9f0;border-radius:99px;font-weight:600;font-size:12.5px;cursor:pointer;background:#fff;user-select:none}
    .bh-types input{margin:0}
    .bh-types label.good:has(input:checked){background:#e7f6ee;border-color:#16a34a;color:#166534}
    .bh-types label.bad:has(input:checked){background:#fdecea;border-color:#dc2626;color:#991b1b}
    .bh-types small{font-weight:700;opacity:.75}
    .bh-h{font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:700;margin:10px 0 2px}
    .bh-pt{display:inline-block;min-width:38px;text-align:center;border-radius:99px;padding:1px 8px;font-weight:700;font-size:12px}
    .bh-pt.g{background:#e7f6ee;color:#166534}.bh-pt.b{background:#fdecea;color:#991b1b}
    .bh-table td{vertical-align:middle!important}
</style>
<div class="content-wrapper">
    <section class="content">
        <?php if ($msg) { ?><div class="alert alert-<?php echo $e($msg[0]); ?>"><?php echo $e($msg[1]); ?></div><?php } ?>
        <div class="bh-grid">
            <?php if ($can_add) { ?>
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-flag"></i> Report behaviour</h3></div>
                <?php echo form_open('behaviour/log/save', array('id' => 'bh_form')); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label for="bh_student">Student</label>
                        <select id="bh_student" name="student_id" class="form-control" required style="width:100%">
                            <option value="">Type a name or admission number…</option>
                            <?php foreach ($students as $s) { ?><option value="<?php echo (int) $s['id']; ?>"><?php echo $e(trim($s['name']) . ' (' . $s['admission_no'] . ') – ' . $s['class'] . ' ' . $s['section']); ?></option><?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>What happened</label>
                        <?php if (!$incidents) { ?>
                            <p class="text-muted">No incident types yet. <a href="<?php echo site_url('behaviour/incidents'); ?>">Add incident types</a> first (for example "Helped a classmate" +5, "Fighting" −10).</p>
                        <?php } ?>
                        <?php if ($good) { ?><div class="bh-h">Good behaviour</div><div class="bh-types">
                            <?php foreach ($good as $i) { ?><label class="good" title="<?php echo $e($i['description']); ?>"><input type="checkbox" name="incident_id[]" value="<?php echo (int) $i['id']; ?>"> <?php echo $e($i['title']); ?> <small>+<?php echo (int) $i['point']; ?></small></label><?php } ?>
                        </div><?php } ?>
                        <?php if ($bad) { ?><div class="bh-h">Misconduct</div><div class="bh-types">
                            <?php foreach ($bad as $i) { ?><label class="bad" title="<?php echo $e($i['description']); ?>"><input type="checkbox" name="incident_id[]" value="<?php echo (int) $i['id']; ?>"> <?php echo $e($i['title']); ?> <small><?php echo (int) $i['point']; ?></small></label><?php } ?>
                        </div><?php } ?>
                    </div>
                    <div class="form-group">
                        <label for="bh_note">Note <small class="text-muted">(optional: what, where, who saw it)</small></label>
                        <textarea id="bh_note" name="note" class="form-control" rows="3" maxlength="2000" placeholder="e.g. Took a classmate's calculator during Maths and would not return it."></textarea>
                    </div>
                    <p class="text-muted small" style="margin:0">The parent is told by email/SMS if the behaviour message is switched on in Behaviour Settings.</p>
                </div>
                <div class="box-footer"><button class="btn btn-primary pull-right"><i class="fa fa-check"></i> Save report</button></div>
                <?php echo form_close(); ?>
            </div>
            <?php } ?>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list"></i> Latest reports <small>this session</small></h3>
                    <div class="box-tools pull-right">
                        <form method="get" action="<?php echo site_url('behaviour/log'); ?>" style="display:inline">
                            <select name="class_id" class="form-control input-sm" onchange="this.form.submit()" aria-label="Class" style="display:inline-block;width:auto">
                                <option value="">All classes</option>
                                <?php foreach ($classes as $c) { ?><option value="<?php echo (int) $c['id']; ?>" <?php echo (int) $class_id === (int) $c['id'] ? 'selected' : ''; ?>><?php echo $e($c['class']); ?></option><?php } ?>
                            </select>
                        </form>
                        <a class="btn btn-sm btn-default" href="<?php echo site_url('behaviour/report'); ?>"><i class="fa fa-bar-chart"></i> Reports</a>
                    </div>
                </div>
                <div class="box-body">
                    <?php if (!$recent) { ?>
                        <p class="text-muted" style="text-align:center;padding:24px 0">Nothing reported yet<?php echo $class_id ? ' for this class' : ''; ?>.</p>
                    <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-hover bh-table">
                                <thead><tr><th>When</th><th>Student</th><th>What happened</th><th class="text-center">Points</th><th>Reported by</th><?php if ($can_delete) { ?><th></th><?php } ?></tr></thead>
                                <tbody>
                                <?php foreach ($recent as $r) { ?>
                                    <tr>
                                        <td style="white-space:nowrap"><?php echo $e(date('j M Y', strtotime($r['created_at']))); ?><br><small class="text-muted"><?php echo $e(date('g:i a', strtotime($r['created_at']))); ?></small></td>
                                        <td><a href="<?php echo site_url('student/view/' . (int) $r['student_id']); ?>"><b><?php echo $e(trim($r['student'])); ?></b></a><br><small class="text-muted"><?php echo $e($r['admission_no'] . ' · ' . $r['class'] . ' ' . $r['section']); ?></small></td>
                                        <td><?php echo $e($r['title']); ?><?php if ((string) $r['note'] !== '') { ?><br><small class="text-muted"><?php echo $e(mb_strimwidth($r['note'], 0, 140, '…')); ?></small><?php } ?></td>
                                        <td class="text-center"><span class="bh-pt <?php echo (int) $r['point'] >= 0 ? 'g' : 'b'; ?>"><?php echo ((int) $r['point'] > 0 ? '+' : '') . (int) $r['point']; ?></span></td>
                                        <td><small><?php echo $e($r['staff']); ?></small></td>
                                        <?php if ($can_delete) { ?><td class="text-right">
                                            <?php echo form_open('behaviour/log/remove', array('onsubmit' => "return confirm('Remove this report?')", 'style' => 'margin:0')); ?><input type="hidden" name="id" value="<?php echo (int) $r['id']; ?>"><button class="btn btn-xs btn-default" title="Remove"><i class="fa fa-trash"></i></button><?php echo form_close(); ?>
                                        </td><?php } ?>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
$(function () {
    if ($.fn.select2) { $('#bh_student').select2({ placeholder: 'Type a name or admission number…', width: '100%' }); }
    $('#bh_form').on('submit', function (e) {
        if (!$('#bh_student').val()) { e.preventDefault(); errorMsg('Choose the student.'); return; }
        if (!$(this).find('input[name="incident_id[]"]:checked').length) { e.preventDefault(); errorMsg('Choose what happened.'); }
    });
});
</script>
