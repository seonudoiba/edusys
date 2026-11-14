<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
$date = function ($d) { return $d ? date($this->customlib->getSchoolDateFormat(), strtotime($d)) : ''; };
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-pie-chart"></i> Fees <small><?php echo $e($session_name); ?> session</small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>

        <div class="fw-kpis">
            <div class="fw-kpi"><span>Expected</span><b><?php echo $m($totals['expected']); ?></b><small><?php echo (int) $totals['students'] . ((int) $totals['students'] === 1 ? ' student' : ' students'); ?> charged</small></div>
            <div class="fw-kpi teal"><span>Collected</span><b><?php echo $m($totals['paid']); ?></b><small><?php echo (int) $totals['progress']; ?>% of expected<?php echo $totals['discount'] > 0 ? ', plus ' . $m($totals['discount']) . ' discounts' : ''; ?></small></div>
            <div class="fw-kpi gold"><span>Outstanding</span><b><?php echo $m($totals['balance']); ?></b><small><a href="<?php echo site_url('admin/fees?status=owing'); ?>"><?php echo (int) $totals['owing'] . ((int) $totals['owing'] === 1 ? ' student owes' : ' students owe'); ?></a></small></div>
            <div class="fw-kpi red"><span>Overdue</span><b><?php echo $m($totals['overdue']); ?></b><small><a href="<?php echo site_url('admin/fees?status=overdue'); ?>">past the due date</a></small></div>
            <?php if ($pending_transfers[0] > 0) { ?>
                <div class="fw-kpi"><span>Transfers to review</span><b><?php echo (int) $pending_transfers[0]; ?></b><small><a href="<?php echo site_url('admin/offlinepayment'); ?>"><?php echo $m($pending_transfers[1]); ?> waiting</a></small></div>
            <?php } ?>
        </div>

        <?php if ($uncharged > 0 && $this->rbac->hasPrivilege('fees_master', 'can_add')) { ?>
            <div class="alert alert-warning" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between">
                <span><i class="fa fa-user-plus"></i> <b><?php echo (int) $uncharged . ((int) $uncharged === 1 ? ' student is' : ' students are'); ?> not charged</b> the fees of their class, usually because they were admitted after the fees were set.</span>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/fees/catchup'); ?>">Review and charge</a>
            </div>
        <?php } ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <?php if ($charges && $this->rbac->hasPrivilege('fees_master', 'can_add')) { ?><a class="btn btn-default btn-sm pull-right" href="<?php echo site_url('admin/fees/repeat'); ?>"><i class="fa fa-files-o"></i> Repeat fees for next term</a><?php } ?>
                <h3 class="box-title">Fees by class</h3>
                <p class="fw-muted" style="margin:4px 0 0">Each row is one set of fees charged to a class section. Open a row to see every student's balance.</p>
            </div>
            <div class="box-body">
                <form class="fw-filters" method="get" action="<?php echo site_url('admin/fees'); ?>">
                    <select name="fsg_id" class="form-control" aria-label="Fees">
                        <option value="">All fees</option>
                        <?php foreach ($group_list as $id => $name) { ?><option value="<?php echo (int) $id; ?>" <?php echo $filters['fsg_id'] === (int) $id ? 'selected' : ''; ?>><?php echo $e($name); ?></option><?php } ?>
                    </select>
                    <?php if ($term_list) { ?>
                        <select name="term" class="form-control" aria-label="Term">
                            <option value="">All terms</option>
                            <?php foreach ($term_list as $k => $label) { ?><option value="<?php echo $k; ?>" <?php echo $filters['term'] === $k ? 'selected' : ''; ?>><?php echo $label; ?></option><?php } ?>
                        </select>
                    <?php } ?>
                    <select name="class_id" class="form-control" aria-label="Class">
                        <option value="">All classes</option>
                        <?php foreach ($class_list as $id => $name) { ?><option value="<?php echo (int) $id; ?>" <?php echo $filters['class_id'] === (int) $id ? 'selected' : ''; ?>><?php echo $e($name); ?></option><?php } ?>
                    </select>
                    <select name="status" class="form-control" aria-label="Status">
                        <option value="">Any status</option>
                        <option value="owing" <?php echo $filters['status'] === 'owing' ? 'selected' : ''; ?>>Not fully paid</option>
                        <option value="overdue" <?php echo $filters['status'] === 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                        <option value="paid" <?php echo $filters['status'] === 'paid' ? 'selected' : ''; ?>>Fully paid</option>
                    </select>
                    <button type="submit" class="btn btn-default"><i class="fa fa-filter"></i> Show</button>
                    <?php if ($filters['fsg_id'] || $filters['class_id'] || $filters['status'] || $filters['term']) { ?><a href="<?php echo site_url('admin/fees'); ?>">Clear</a><?php } ?>
                </form>

                <?php if (!$charges) { ?>
                    <div class="text-center fw-muted" style="padding:30px">
                        <?php if ($totals['students'] === 0) { ?>
                            No fees are charged to students in this session yet.
                            <?php if ($this->rbac->hasPrivilege('fees_master', 'can_add')) { ?><br><a class="btn btn-primary" style="margin-top:10px" href="<?php echo site_url('admin/fees/create'); ?>"><i class="fa fa-plus"></i> Create fees</a><?php } ?>
                        <?php } else { ?>
                            Nothing matches these filters.
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="table-responsive">
                        <table class="table table-hover fw-table">
                            <thead><tr><th>Fees</th><th>Class</th><th class="n">Students</th><th class="n">Expected</th><th class="n">Collected</th><th class="n">Outstanding</th><th>Progress</th><th>Due</th><th></th></tr></thead>
                            <tbody>
                            <?php foreach ($charges as $c) { $t = $c['totals'];
                                $url = site_url('admin/fees/charge/' . $c['fsg_id'] . '/' . $c['class_id'] . '/' . $c['section_id']); ?>
                                <tr>
                                    <td><a href="<?php echo $url; ?>"><b><?php echo $e($c['is_system'] ? 'Balance brought forward' : $c['group']); ?></b></a><?php echo $c['term'] ? '<br><span class="fw-muted" style="font-size:12px">' . Fees_overview::TERMS[$c['term']] . '</span>' : ''; ?></td>
                                    <td><?php echo $e($c['class'] . ' ' . $c['section']); ?></td>
                                    <td class="n"><?php echo (int) $t['students']; ?><?php echo $t['owing'] ? ' <span class="fw-muted">(' . (int) $t['owing'] . ' owe)</span>' : ''; ?></td>
                                    <td class="n"><?php echo $m($t['expected']); ?></td>
                                    <td class="n"><?php echo $m($t['paid']); ?></td>
                                    <td class="n"><?php echo $m($t['balance']); ?><?php echo $t['overdue'] > 0 ? '<br><span class="fw-tag overdue">' . $m($t['overdue']) . ' overdue</span>' : ''; ?></td>
                                    <td><div class="fw-bar" title="<?php echo (int) $t['progress']; ?>%"><i style="width:<?php echo (int) $t['progress']; ?>%"></i></div><small class="fw-muted"><?php echo (int) $t['progress']; ?>%</small></td>
                                    <td class="fw-muted"><?php echo $e($date($c['due'])); ?></td>
                                    <td><a class="btn btn-default btn-xs" href="<?php echo $url; ?>">Open</a></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
