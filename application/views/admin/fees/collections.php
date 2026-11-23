<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
$can_collect = $this->rbac->hasPrivilege('collect_fees', 'can_add');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> Record payments <small><?php echo $e($session_name); ?> session</small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Take a payment</h3></div>
            <div class="box-body">
                <form method="get" action="<?php echo site_url('admin/fees/collections'); ?>" class="fw-filters" style="margin:0">
                    <input type="search" name="q" value="<?php echo $e($q); ?>" class="form-control" style="max-width:340px" placeholder="Student name or admission number" <?php echo $class_id ? '' : 'autofocus'; ?> aria-label="Find a student">
                    <span class="fw-muted">or</span>
                    <select name="class_id" class="form-control" aria-label="Class" onchange="this.form.section_id.value='';this.form.submit()">
                        <option value="">Class</option>
                        <?php foreach ($classes as $cid => $c) { ?><option value="<?php echo (int) $cid; ?>" <?php echo $class_id === (int) $cid ? 'selected' : ''; ?>><?php echo $e($c['name']); ?></option><?php } ?>
                    </select>
                    <select name="section_id" class="form-control" aria-label="Section" onchange="this.form.submit()">
                        <option value="">All sections</option>
                        <?php if ($class_id && isset($classes[$class_id])) { foreach ($classes[$class_id]['sections'] as $sid => $sn) { ?><option value="<?php echo (int) $sid; ?>" <?php echo $section_id === (int) $sid ? 'selected' : ''; ?>><?php echo $e($sn); ?></option><?php } } ?>
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Find</button>
                </form>
                <?php if ($q !== '' && mb_strlen($q) < 2 && !$class_id) { ?><p class="fw-muted" style="margin-top:10px">Type at least 2 letters.</p><?php } ?>
                <?php if (mb_strlen($q) >= 2 || $class_id) { ?>
                    <?php if (!$found) { ?>
                        <p class="fw-muted" style="margin-top:12px">No student in this session matches.</p>
                    <?php } else { ?>
                        <div class="table-responsive" style="margin-top:12px">
                            <table class="table table-hover fw-table">
                                <thead><tr><th>Student</th><th>Class</th><th>Guardian</th><th class="n">Balance</th><th></th></tr></thead>
                                <tbody>
                                <?php foreach ($found as $s) {
                                    $name = $this->customlib->getFullName($s['firstname'], $s['middlename'], $s['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?>
                                    <tr>
                                        <td><b><?php echo $e($name); ?></b><br><span class="fw-muted"><?php echo $e($s['admission_no']); ?></span></td>
                                        <td><?php echo $e($s['class'] . ' ' . $s['section']); ?></td>
                                        <td><?php echo $e($s['guardian_name']); ?><br><span class="fw-muted"><?php echo $e($s['guardian_phone']); ?></span></td>
                                        <td class="n"><?php echo $s['balance'] > 0 ? '<b>' . $m($s['balance']) . '</b>' : '<span class="fw-tag paid">Paid up</span>'; ?></td>
                                        <td style="white-space:nowrap">
                                            <?php if ($can_collect) { ?><a class="btn btn-primary btn-xs" href="<?php echo site_url('studentfee/addfee/' . (int) $s['student_session_id']); ?>"><i class="fa fa-money"></i> Record payment</a><?php } ?>
                                            <a class="btn btn-default btn-xs" target="_blank" rel="noopener" href="<?php echo site_url('studentfee/bill/' . (int) $s['student_session_id']); ?>"><i class="fa fa-file-text-o"></i> Fee bill</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>

        <div class="fw-kpis">
            <div class="fw-kpi teal"><span>Collected today</span><b><?php echo $m($collected_today[1]); ?></b><small><?php echo (int) $collected_today[0] . ((int) $collected_today[0] === 1 ? ' payment' : ' payments'); ?> · <a href="<?php echo site_url('admin/fees/takings'); ?>">By collector</a></small></div>
            <div class="fw-kpi"><span>Last 7 days</span><b><?php echo $m($collected_week[1]); ?></b><small><?php echo (int) $collected_week[0] . ((int) $collected_week[0] === 1 ? ' payment' : ' payments'); ?></small></div>
            <div class="fw-kpi gold"><span>Still owed</span><b><?php echo $m($totals['balance']); ?></b><small><?php echo (int) $totals['owing'] . ((int) $totals['owing'] === 1 ? ' student' : ' students'); ?></small></div>
        </div>

        <div class="fw-cards">
            <div class="fw-card">
                <h4><i class="fa fa-university"></i> Bank transfers to review</h4>
                <p><?php echo $pending_transfers[0] ? (int) $pending_transfers[0] . ' transfer' . ($pending_transfers[0] === 1 ? '' : 's') . ' (' . $m($pending_transfers[1]) . ') uploaded by parents, waiting for you to approve or reject.' : 'No transfers are waiting. Parents upload their receipts from the parent portal.'; ?></p>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/offlinepayment'); ?>">Review transfers</a>
            </div>
            <div class="fw-card">
                <h4><i class="fa fa-list-ul"></i> Who still owes</h4>
                <p>Students with unpaid fees by class, with a WhatsApp reminder to each guardian.</p>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/fees?status=owing'); ?>">By class</a>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('studentfee/feesearch'); ?>">By fee type</a>
            </div>
            <div class="fw-card">
                <h4><i class="fa fa-calculator"></i> End-of-day takings</h4>
                <p>What each collector received today, by cash, transfer and POS, with every receipt. Print it to reconcile the money handed in.</p>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('admin/fees/takings'); ?>">Today's takings</a>
            </div>
            <div class="fw-card">
                <h4><i class="fa fa-search"></i> Find a receipt</h4>
                <p>Look up a payment by its receipt number to view, reprint or reverse it.</p>
                <a class="btn btn-default btn-sm" href="<?php echo site_url('studentfee/searchpayment'); ?>">Find a receipt</a>
            </div>
        </div>
    </section>
</div>
