<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
$link = function ($d, $b) { return site_url('admin/fees/takings') . '?date=' . rawurlencode($d) . ($b !== '' ? '&by=' . rawurlencode($b) : ''); };
$day_label = date('l j F Y', strtotime($date));
$prev = date('Y-m-d', strtotime($date . ' -1 day'));
$next = date('Y-m-d', strtotime($date . ' +1 day'));
$is_today = $date === date('Y-m-d');
$can_receipt = $this->rbac->hasPrivilege('search_fees_payment', 'can_view');
$who = $by !== '' && isset($day['collectors'][$by]) ? $day['collectors'][$by]['name'] : '';
?>
<style>
    .fw-tk-modes { display: flex; flex-wrap: wrap; gap: 6px 14px; margin-top: 4px; }
    .fw-tk-modes span { white-space: nowrap; }
    .fw-tk-sign { display: none; }
    @media print {
        .main-header, .main-sidebar, .main-footer, .fw-nav, .fw-no-print, .content-header { display: none !important; }
        .content-wrapper { margin: 0 !important; padding: 0 !important; min-height: 0 !important; }
        .fw-tk-sign { display: block; margin-top: 40px; }
        .fw-tk-sign div { display: inline-block; width: 45%; border-top: 1px solid #000; padding-top: 4px; margin-right: 4%; }
        a[href]:after { content: none !important; }
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-calculator"></i> Takings <small><?php echo $e($day_label); ?></small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <p class="fw-no-print"><a href="<?php echo site_url('admin/fees/collections'); ?>"><i class="fa fa-angle-left"></i> Back to Collections</a></p>

        <form method="get" action="<?php echo site_url('admin/fees/takings'); ?>" class="fw-filters fw-no-print">
            <a class="btn btn-default" href="<?php echo $e($link($prev, $by)); ?>" aria-label="Previous day"><i class="fa fa-angle-left"></i></a>
            <input type="date" name="date" value="<?php echo $e($date); ?>" max="<?php echo date('Y-m-d'); ?>" class="form-control" style="width:auto" aria-label="Day">
            <?php if (!$is_today) { ?><a class="btn btn-default" href="<?php echo $e($link($next, $by)); ?>" aria-label="Next day"><i class="fa fa-angle-right"></i></a><?php } ?>
            <select name="by" class="form-control" aria-label="Collected by">
                <option value="">Everyone</option>
                <?php foreach ($day['collectors'] as $k => $c) { ?>
                    <option value="<?php echo $e($k); ?>"<?php echo $k === $by ? ' selected' : ''; ?>><?php echo $e($c['name']); ?></option>
                <?php } ?>
            </select>
            <button type="submit" class="btn btn-primary">Show</button>
            <?php if (!$is_today) { ?><a class="btn btn-link" href="<?php echo $e($link(date('Y-m-d'), '')); ?>">Today</a><?php } ?>
            <button type="button" class="btn btn-default" onclick="window.print()" style="margin-left:auto"><i class="fa fa-print"></i> Print</button>
        </form>

        <h4 class="visible-print-block"><?php echo $e($this->customlib->getSchoolName()); ?> — fee takings, <?php echo $e($day_label); ?><?php echo $who !== '' ? ' — ' . $e($who) : ''; ?></h4>

        <?php if (!$day['collectors']) { ?>
            <div class="box box-primary"><div class="box-body text-center fw-muted" style="padding:30px">No fee payments were recorded on <?php echo $e($day_label); ?>.</div></div>
        <?php } else { ?>
            <div class="fw-kpis">
                <div class="fw-kpi teal"><span><?php echo $who !== '' ? $e($who) : 'Total taken'; ?></span><b id="tk_total"><?php echo $m($day['total']); ?></b><small><?php echo (int) $day['count'] . ($day['count'] === 1 ? ' payment' : ' payments'); ?></small></div>
                <?php foreach ($day['modes'] as $label => $amt) { ?>
                    <div class="fw-kpi"><span><?php echo $e($label); ?></span><b><?php echo $m($amt); ?></b></div>
                <?php } ?>
            </div>

            <?php if ($by === '') { ?>
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">By collector</h3></div>
                <div class="box-body table-responsive">
                    <table class="table table-hover fw-table" id="tk_collectors">
                        <thead><tr><th>Collected by</th><th class="n">Payments</th><th>By payment mode</th><th class="n">Total</th><th class="fw-no-print"></th></tr></thead>
                        <tbody>
                        <?php foreach ($day['collectors'] as $k => $c) { ?>
                            <tr>
                                <td><b><?php echo $e($c['name']); ?></b></td>
                                <td class="n"><?php echo (int) $c['count']; ?></td>
                                <td><div class="fw-tk-modes"><?php foreach ($c['modes'] as $label => $amt) { ?><span><?php echo $e($label); ?>: <b><?php echo $m($amt); ?></b></span><?php } ?></div></td>
                                <td class="n"><b><?php echo $m($c['total']); ?></b></td>
                                <td class="fw-no-print"><a class="btn btn-default btn-xs" href="<?php echo $e($link($date, $k)); ?>">Receipts</a></td>
                            </tr>
                        <?php } ?>
                        <tr class="active"><td><b>Total</b></td><td class="n"><b><?php echo (int) $day['count']; ?></b></td><td></td><td class="n"><b><?php echo $m($day['total']); ?></b></td><td class="fw-no-print"></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php } ?>

            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Receipts<?php echo $who !== '' ? ' taken by ' . $e($who) : ''; ?></h3></div>
                <div class="box-body table-responsive">
                    <table class="table table-hover fw-table" id="tk_receipts">
                        <thead><tr><th>Receipt</th><th>Student</th><th>Class</th><th>Fee</th><th>Mode</th><?php echo $by === '' ? '<th>Collected by</th>' : ''; ?><th class="n">Amount</th></tr></thead>
                        <tbody>
                        <?php foreach ($day['rows'] as $r) {
                            $s = $r['student'];
                            $name = $this->customlib->getFullName($s['firstname'], $s['middlename'], $s['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?>
                            <tr>
                                <td><?php echo $can_receipt ? '<a href="' . $e(site_url('studentfee/searchpayment') . '?paymentid=' . rawurlencode($r['receipt'])) . '">' . $e($r['receipt']) . '</a>' : $e($r['receipt']); ?></td>
                                <td><?php echo $e($name); ?><?php echo $s['admission_no'] !== '' ? '<br><span class="fw-muted">' . $e($s['admission_no']) . '</span>' : ''; ?></td>
                                <td><?php echo $e(trim($s['class'] . ' ' . $s['section'])); ?></td>
                                <td><?php echo $e($r['fee']); ?><?php echo $r['note'] !== '' ? '<br><span class="fw-muted">' . $e($r['note']) . '</span>' : ''; ?></td>
                                <td><?php echo $e($r['mode']); ?></td>
                                <?php if ($by === '') { ?><td><?php echo $e($r['collector']); ?></td><?php } ?>
                                <td class="n"><?php echo $m($r['paid']); ?><?php echo $r['fine'] > 0 ? '<br><span class="fw-muted">incl. ' . $m($r['fine']) . ' fine</span>' : ''; ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="active"><td colspan="<?php echo $by === '' ? 6 : 5; ?>"><b>Total</b></td><td class="n"><b><?php echo $m($day['total']); ?></b></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="fw-tk-sign"><div>Handed in by</div><div>Received by (bursar)</div></div>
        <?php } ?>
    </section>
</div>
