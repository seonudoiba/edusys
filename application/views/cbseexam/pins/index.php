<?php
/* Report Cards > Result checker PINs (Pins::index). */
$pm = $this->session->flashdata('pin_msg');
?>
<style>
    .pn-grid{display:grid;grid-template-columns:320px 1fr;gap:18px}
    @media (max-width:991px){.pn-grid{grid-template-columns:1fr}}
    .pn-card{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:18px}
    .pn-card h4{margin:0 0 12px;font-weight:700}
    .pn-batch{display:block;border:1px solid #e5e9f0;border-radius:10px;padding:10px 12px;margin-bottom:8px;color:#0f172a}
    .pn-batch.on{border-color:var(--ui-brand,#1f4e79);box-shadow:0 0 0 3px color-mix(in srgb,var(--ui-brand,#1f4e79) 15%,transparent)}
    .pn-batch small{display:block;color:#64748b}
    .pn-bar{height:6px;background:#eef2f6;border-radius:99px;margin-top:6px;overflow:hidden}
    .pn-bar i{display:block;height:100%;background:var(--ui-brand,#1f4e79)}
    .pn-pin{font-family:Consolas,monospace;font-size:14px;letter-spacing:.06em;font-weight:700}
    .pn-how{background:#f8fafc;border-radius:10px;padding:10px 12px;font-size:13px;color:#334155}
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-key"></i> Result checker PINs</h3>
            </div>
            <div class="box-body">
                <?php if ($pm) { ?><div class="alert alert-<?php echo $pm[0]; ?>"><?php echo html_escape($pm[1]); ?></div><?php } ?>
                <div class="pn-how" style="margin-bottom:16px">
                    <i class="fa fa-info-circle"></i> Parents and students check results on your website at <a href="<?php echo html_escape($checker_url); ?>" target="_blank"><?php echo html_escape($checker_url); ?></a>.
                    <?php if ($batches) { ?>Because you have PINs, the page asks for <b>admission no + PIN</b>. A PIN works for one student only (the first it is used for) and a set number of times.<?php } else { ?>Right now it only asks for the admission no. Once you make PINs, a PIN is required too.<?php } ?>
                    Only published exams are shown.
                </div>
                <div class="pn-grid">
                    <div>
                        <div class="pn-card">
                            <h4><i class="fa fa-plus-circle"></i> Make PINs</h4>
                            <?php echo form_open('cbseexam/pins/generate'); ?>
                            <div class="form-group"><label>How many</label><input type="number" name="count" class="form-control" min="1" max="2000" value="50" required></div>
                            <div class="form-group"><label>Uses per PIN</label><input type="number" name="max_uses" class="form-control" min="1" max="100" value="5" required><small class="text-muted">How many times it can be used to view results.</small></div>
                            <div class="form-group"><label>Session</label>
                                <select name="session_id" class="form-control">
                                    <option value="0">Any session</option>
                                    <?php foreach ($sessions as $s) { ?><option value="<?php echo $s['id']; ?>" <?php echo (int) $s['id'] === (int) $current_session ? 'selected' : ''; ?>><?php echo html_escape($s['session']); ?></option><?php } ?>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-block"><i class="fa fa-magic"></i> Make PINs</button>
                            <?php echo form_close(); ?>
                        </div>
                        <?php if ($batches) { ?>
                            <div class="pn-card" style="margin-top:16px">
                                <h4><i class="fa fa-th-list"></i> Batches</h4>
                                <?php foreach ($batches as $b) { $pct = $b['total'] ? round($b['used'] * 100 / $b['total']) : 0; ?>
                                    <a class="pn-batch<?php echo $b['batch'] === $batch ? ' on' : ''; ?>" href="<?php echo site_url('cbseexam/pins?batch=' . rawurlencode($b['batch'])); ?>">
                                        <b><?php echo html_escape($b['batch']); ?></b> <span class="pull-right text-muted"><?php echo (int) $b['total']; ?> PINs</span>
                                        <small><?php echo date('j M Y, H:i', strtotime($b['created_at'])); ?> · <?php echo html_escape($b['session'] ?: 'any session'); ?> · <?php echo (int) $b['used']; ?> used<?php echo $b['disabled'] ? ' · ' . (int) $b['disabled'] . ' disabled' : ''; ?></small>
                                        <span class="pn-bar"><i style="width:<?php echo $pct; ?>%"></i></span>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="pn-card">
                        <?php if (!$pins) { ?>
                            <div class="text-center text-muted" style="padding:50px"><i class="fa fa-key fa-3x"></i><p style="margin-top:10px">No PINs yet. Make a batch, then print the cards and give one to each student with the report card.</p></div>
                        <?php } else { ?>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-bottom:12px">
                                <h4 style="flex:1;margin:0">Batch <?php echo html_escape($batch); ?></h4>
                                <a class="btn btn-sm btn-primary" target="_blank" href="<?php echo site_url('cbseexam/pins/cards/' . rawurlencode($batch)); ?>"><i class="fa fa-print"></i> Print cards</a>
                                <a class="btn btn-sm btn-default" href="<?php echo site_url('cbseexam/pins/export/' . rawurlencode($batch)); ?>"><i class="fa fa-file-excel-o"></i> Export</a>
                                <?php echo form_open('cbseexam/pins/toggle', array('style' => 'margin:0')); ?><input type="hidden" name="batch" value="<?php echo html_escape($batch); ?>"><input type="hidden" name="active" value="0"><button class="btn btn-sm btn-default" onclick="return confirm('Disable every PIN of this batch?')"><i class="fa fa-ban"></i> Disable batch</button><?php echo form_close(); ?>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped example">
                                    <thead><tr><th>Serial</th><th>PIN</th><th>Used</th><th>Student</th><th>Last used</th><th>Status</th><th class="noExport"></th></tr></thead>
                                    <tbody>
                                        <?php foreach ($pins as $p) { ?>
                                            <tr>
                                                <td><?php echo html_escape($p['serial_no']); ?></td>
                                                <td class="pn-pin"><?php echo html_escape(implode(' ', str_split($p['pin'], 4))); ?></td>
                                                <td><?php echo (int) $p['used_count'] . ' / ' . (int) $p['max_uses']; ?></td>
                                                <td><?php echo $p['student_name'] ? html_escape($p['student_name'] . ' (' . $p['admission_no'] . ')') : '<span class="text-muted">–</span>'; ?></td>
                                                <td><?php echo $p['last_used_at'] ? date('j M Y, H:i', strtotime($p['last_used_at'])) : '<span class="text-muted">–</span>'; ?></td>
                                                <td><?php echo $p['is_active'] ? ((int) $p['used_count'] >= (int) $p['max_uses'] ? '<span class="label label-default">Used up</span>' : '<span class="label label-success">Active</span>') : '<span class="label label-danger">Disabled</span>'; ?></td>
                                                <td class="text-right">
                                                    <?php echo form_open('cbseexam/pins/toggle', array('style' => 'margin:0')); ?>
                                                    <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>"><input type="hidden" name="batch" value="<?php echo html_escape($batch); ?>"><input type="hidden" name="active" value="<?php echo $p['is_active'] ? 0 : 1; ?>">
                                                    <button class="btn btn-xs btn-default"><?php echo $p['is_active'] ? '<i class="fa fa-ban"></i> Disable' : '<i class="fa fa-check"></i> Enable'; ?></button>
                                                    <?php echo form_close(); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
