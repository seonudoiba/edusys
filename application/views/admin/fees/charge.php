<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
$date = function ($d) { return $d ? date($this->customlib->getSchoolDateFormat(), strtotime($d)) : ''; };
$can_collect = $this->rbac->hasPrivilege('collect_fees', 'can_add');
$title = $charge['is_system'] ? 'Balance brought forward' : $charge['group'];
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-pie-chart"></i> <?php echo $e($title); ?> <small><?php echo $e($charge['class'] . ' ' . $charge['section']); ?> · <?php echo $e($session_name); ?></small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;margin-bottom:10px">
            <a href="<?php echo site_url('admin/fees'); ?>"><i class="fa fa-angle-left"></i> All fees</a>
            <?php if (!$charge['is_system']) { ?>
                <?php if ($this->rbac->hasPrivilege('fees_master', 'can_edit')) { ?>
                    <form method="post" action="<?php echo site_url('admin/fees/term/' . (int) $charge['fsg_id']); ?>" class="fw-filters" style="margin:0">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <input type="hidden" name="back" value="<?php echo $e('admin/fees/charge/' . (int) $charge['fsg_id'] . '/' . (int) $charge['class_id'] . '/' . (int) $charge['section_id']); ?>">
                        <label for="fw_term" class="fw-muted" style="margin:0">Term</label>
                        <select name="term" id="fw_term" class="form-control input-sm" style="min-width:150px">
                            <option value="">Whole session</option>
                            <?php foreach (Fees_overview::TERMS as $k => $label) { ?><option value="<?php echo $k; ?>" <?php echo $charge['term'] === $k ? 'selected' : ''; ?>><?php echo $label; ?></option><?php } ?>
                        </select>
                        <button type="submit" class="btn btn-default btn-sm">Save</button>
                    </form>
                <?php } elseif ($charge['term']) { ?>
                    <span class="fw-muted"><?php echo Fees_overview::TERMS[$charge['term']]; ?></span>
                <?php } ?>
            <?php } ?>
        </div>
        <?php if ($uncharged && $this->rbac->hasPrivilege('fees_master', 'can_add')) { ?>
            <div class="alert alert-warning">
                <i class="fa fa-user-plus"></i> <b><?php echo count($uncharged) . (count($uncharged) === 1 ? ' student' : ' students'); ?> in <?php echo $e($charge['class'] . ' ' . $charge['section']); ?> <?php echo count($uncharged) === 1 ? 'is' : 'are'; ?> not charged these fees</b>
                (<?php echo $e(implode(', ', array_slice(array_map(function ($s) { return trim($s['firstname'] . ' ' . $s['lastname']); }, $uncharged), 0, 5))); ?><?php echo count($uncharged) > 5 ? ', …' : ''; ?>).
                <a href="<?php echo site_url('admin/fees/catchup'); ?>">Review and charge</a>
            </div>
        <?php } ?>

        <div class="fw-kpis">
            <div class="fw-kpi"><span>Expected</span><b><?php echo $m($totals['expected']); ?></b><small><?php echo (int) $totals['students'] . ((int) $totals['students'] === 1 ? ' student' : ' students'); ?></small></div>
            <div class="fw-kpi teal"><span>Collected</span><b><?php echo $m($totals['paid']); ?></b><small><?php echo (int) $totals['progress']; ?>%</small></div>
            <div class="fw-kpi gold"><span>Outstanding</span><b><?php echo $m($totals['balance']); ?></b><small><?php echo (int) $totals['owing'] . ((int) $totals['owing'] === 1 ? ' student owes' : ' students owe'); ?></small></div>
            <div class="fw-kpi red"><span>Overdue</span><b><?php echo $m($totals['overdue']); ?></b></div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border" style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-start;justify-content:space-between">
                <div>
                    <h3 class="box-title">Students</h3>
                    <p class="fw-muted" style="margin:4px 0 0">Most owed first. Collect takes a full or part payment; Fee bill prints what is owed and how to pay.</p>
                    <?php if ($last_reminder) { ?><p class="fw-muted" style="margin:4px 0 0"><i class="fa fa-commenting-o"></i> Last SMS reminder: <?php echo $e(date($this->customlib->getSchoolDateFormat(), strtotime($last_reminder[0]))); ?>, <?php echo (int) $last_reminder[1]; ?> sent<?php echo $last_reminder[2] ? ', ' . (int) $last_reminder[2] . ' failed' : ''; ?>.</p><?php } ?>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:6px">
                    <?php $tail = (int) $charge['fsg_id'] . '/' . (int) $charge['class_id'] . '/' . (int) $charge['section_id']; ?>
                    <?php if ($totals['owing'] > 0 && $this->rbac->hasPrivilege('collect_fees', 'can_add')) { ?><a class="btn btn-primary btn-sm" href="<?php echo site_url('admin/fees/remind/' . $tail); ?>"><i class="fa fa-commenting-o"></i> Remind all who owe</a><?php } ?>
                    <?php if (!$charge['is_system'] && $this->rbac->hasPrivilege('fees_master', 'can_edit')) { ?><a class="btn btn-default btn-sm" href="<?php echo site_url('admin/fees/edit/' . $tail); ?>"><i class="fa fa-pencil"></i> Change amounts or due dates</a><?php } ?>
                    <?php if ($this->rbac->hasPrivilege('fees_master', 'can_delete')) { ?><a class="btn btn-default btn-sm" href="<?php echo site_url('admin/fees/remove/' . $tail); ?>"><i class="fa fa-trash-o"></i> Remove</a><?php } ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover fw-table">
                    <thead><tr><th>Student</th><th>Fees</th><th class="n">Expected</th><th class="n">Paid</th><th class="n">Balance</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($students as $s) { $p = $s['person']; $t = $s['totals'];
                        $name = $this->customlib->getFullName(isset($p['firstname']) ? $p['firstname'] : '', isset($p['middlename']) ? $p['middlename'] : '', isset($p['lastname']) ? $p['lastname'] : '', $sch_setting->middlename, $sch_setting->lastname);
                        $status = $t['balance'] <= 0 ? 'paid' : ($t['overdue'] > 0 ? 'overdue' : 'owing'); ?>
                        <tr>
                            <td><b><?php echo $e($name); ?></b><br><span class="fw-muted"><?php echo $e(isset($p['admission_no']) ? $p['admission_no'] : ''); ?></span></td>
                            <td class="fw-muted" style="font-size:12px">
                                <?php foreach ($s['items'] as $i) { ?><?php echo $e($i['item']); ?>: <?php echo $m($i['expected']); ?><?php echo $i['due_date'] ? ' (due ' . $e($date($i['due_date'])) . ')' : ''; ?><br><?php } ?>
                            </td>
                            <td class="n"><?php echo $m($t['expected']); ?></td>
                            <td class="n"><?php echo $m($t['paid'] + $t['discount']); ?></td>
                            <td class="n"><b><?php echo $m($t['balance']); ?></b></td>
                            <td><span class="fw-tag <?php echo $status; ?>"><?php echo $status === 'paid' ? 'Paid' : ($status === 'overdue' ? 'Overdue' : 'Owing'); ?></span></td>
                            <td style="white-space:nowrap">
                                <?php if ($can_collect && $t['balance'] > 0) { ?><a class="btn btn-primary btn-xs" href="<?php echo site_url('studentfee/addfee/' . (int) $s['student_session_id']); ?>"><i class="fa fa-money"></i> Collect</a><?php } ?>
                                <a class="btn btn-default btn-xs" target="_blank" rel="noopener" href="<?php echo site_url('studentfee/bill/' . (int) $s['student_session_id']); ?>"><i class="fa fa-file-text-o"></i> Fee bill</a>
                                <?php if ($t['balance'] > 0 && !empty($p['guardian_phone'])) {
                                    $text = 'Dear ' . ($p['guardian_name'] ?: 'parent') . ', this is a reminder that ' . $name . ' (' . $charge['class'] . ' ' . $charge['section'] . ') has an outstanding balance of ' . $currency_symbol . amountFormat($t['balance']) . ' for ' . $title . '. Kindly pay at your earliest convenience. Thank you. ' . $this->customlib->getSchoolName(); ?>
                                    <a class="btn btn-default btn-xs" target="_blank" rel="noopener" title="Remind on WhatsApp" href="<?php echo html_escape($this->customlib->whatsapp_link($p['guardian_phone'], $text)); ?>"><i class="fa fa-whatsapp"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
