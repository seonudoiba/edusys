<?php
$e = function ($v) { return html_escape((string) $v); };
$money = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . number_format((float) $v, 0); };
$max = max(1, max(array_column($monthly, 'revenue')));
$sum = function ($k) use ($monthly) { return array_sum(array_column($monthly, $k)); };
$methods = array('paystack' => 'Paystack', 'bank_transfer' => 'Bank transfer', 'manual' => 'Manual', 'free' => 'Free plan');
?>
<style>
    .rp-kpi { background: #fff; border: 1px solid #e5e5e5; border-radius: 4px; padding: 12px 14px; margin-bottom: 14px; }
    .rp-kpi span { color: #777; font-size: 12px; text-transform: uppercase; }
    .rp-kpi b { display: block; font-size: 22px; }
    .rp-chart { display: flex; align-items: flex-end; gap: 6px; height: 180px; padding-top: 18px; }
    .rp-bar { flex: 1; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; height: 100%; }
    .rp-bar i { display: block; width: 100%; max-width: 38px; background: #3c8dbc; border-radius: 3px 3px 0 0; min-height: 2px; }
    .rp-bar small { font-size: 10px; color: #777; margin-top: 3px; }
</style>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-line-chart"></i> Reports
        <small>
            <?php foreach (array(3, 6, 12, 24) as $m) { ?>
                <a href="<?php echo site_url('admin/billing/reports?months=' . $m); ?>" class="<?php echo $m === $months ? 'text-bold' : ''; ?>" style="margin-left:8px"><?php echo $m; ?> months</a>
            <?php } ?>
        </small></h1></section>
    <section class="content">
        <div class="row">
            <div class="col-sm-3 col-xs-6"><div class="rp-kpi"><span>Revenue</span><b><?php echo $money($sum('revenue')); ?></b></div></div>
            <div class="col-sm-3 col-xs-6"><div class="rp-kpi"><span>Paid subscriptions</span><b><?php echo (int) $sum('paid'); ?></b><small class="text-muted"><?php echo (int) $sum('renewals'); ?> renewals</small></div></div>
            <div class="col-sm-3 col-xs-6"><div class="rp-kpi"><span>Registrations</span><b><?php echo (int) $sum('registrations'); ?></b><small class="text-muted"><?php echo (int) $sum('setups'); ?> set up</small></div></div>
            <div class="col-sm-3 col-xs-6"><div class="rp-kpi"><span>Lapsed schools</span><b><?php echo count($churn); ?></b><small class="text-muted">ended, not renewed</small></div></div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">By month</h3>
                <div class="box-tools"><a class="btn btn-box-tool" href="<?php echo site_url('admin/billing/reports?months=' . $months . '&export=monthly'); ?>"><i class="fa fa-download"></i> CSV</a></div></div>
            <div class="box-body">
                <div class="rp-chart">
                    <?php foreach ($monthly as $ym => $m) { ?>
                        <div class="rp-bar" title="<?php echo $e(date('F Y', strtotime($ym . '-01')) . ': ' . number_format($m['revenue'])); ?>">
                            <i style="height:<?php echo round($m['revenue'] / $max * 100, 1); ?>%"></i><small><?php echo $e(date('M y', strtotime($ym . '-01'))); ?></small>
                        </div>
                    <?php } ?>
                </div>
                <div class="table-responsive" style="margin-top:12px">
                    <table class="table table-condensed">
                        <thead><tr><th>Month</th><th class="text-right">Revenue</th><th class="text-right">Paid</th><th class="text-right">Renewals</th><th class="text-right">Registrations</th><th class="text-right">Set up</th><th class="text-right">Expiries</th></tr></thead>
                        <tbody>
                            <?php foreach (array_reverse($monthly, true) as $ym => $m) { ?>
                                <tr><td><?php echo $e(date('F Y', strtotime($ym . '-01'))); ?></td><td class="text-right"><?php echo $money($m['revenue']); ?></td>
                                    <td class="text-right"><?php echo (int) $m['paid']; ?></td><td class="text-right"><?php echo (int) $m['renewals']; ?></td>
                                    <td class="text-right"><?php echo (int) $m['registrations']; ?></td><td class="text-right"><?php echo (int) $m['setups']; ?></td>
                                    <td class="text-right"><?php echo (int) $m['expiries']; ?></td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4"><div class="box box-default"><div class="box-header with-border"><h3 class="box-title">By plan</h3></div>
                <div class="box-body no-padding"><table class="table table-condensed">
                    <?php if (!$by_plan) { ?><tr><td class="text-muted">No payments in this period.</td></tr><?php } ?>
                    <?php foreach ($by_plan as $p => $r) { ?><tr><td><?php echo $e($p); ?></td><td class="text-right"><?php echo (int) $r['count']; ?></td><td class="text-right"><?php echo $money($r['revenue']); ?></td></tr><?php } ?>
                </table></div></div></div>
            <div class="col-md-4"><div class="box box-default"><div class="box-header with-border"><h3 class="box-title">By payment method</h3></div>
                <div class="box-body no-padding"><table class="table table-condensed">
                    <?php if (!$by_method) { ?><tr><td class="text-muted">No payments in this period.</td></tr><?php } ?>
                    <?php foreach ($by_method as $m => $r) { ?><tr><td><?php echo $e(isset($methods[$m]) ? $methods[$m] : $m); ?></td><td class="text-right"><?php echo (int) $r['count']; ?></td><td class="text-right"><?php echo $money($r['revenue']); ?></td></tr><?php } ?>
                </table></div></div></div>
            <div class="col-md-4"><div class="box box-default"><div class="box-header with-border"><h3 class="box-title">Top schools by revenue</h3></div>
                <div class="box-body no-padding"><table class="table table-condensed">
                    <?php if (!$top) { ?><tr><td class="text-muted">No payments in this period.</td></tr><?php } ?>
                    <?php foreach ($top as $id => $amount) { ?><tr><td><a href="<?php echo site_url('admin/schools/view/' . (int) $id); ?>"><?php echo $e(isset($names[$id]) ? $names[$id] : '#' . $id); ?></a></td><td class="text-right"><?php echo $money($amount); ?></td></tr><?php } ?>
                </table></div></div></div>
        </div>
        <div class="box box-default">
            <div class="box-header with-border"><h3 class="box-title">Lapsed schools</h3> <span class="text-muted">subscription ended, not renewed</span>
                <div class="box-tools"><a class="btn btn-box-tool" href="<?php echo site_url('admin/billing/reports?export=churn'); ?>"><i class="fa fa-download"></i> CSV</a></div></div>
            <div class="box-body no-padding"><table class="table table-condensed">
                <?php if (!$churn) { ?><tr><td class="text-muted">None.</td></tr><?php } ?>
                <?php foreach ($churn as $s) { ?>
                    <tr><td><a href="<?php echo site_url('admin/schools/view/' . (int) $s['id']); ?>"><?php echo $e($s['school_name']); ?></a><div class="text-muted" style="font-size:12px"><?php echo $e($s['email_address']); ?></div></td>
                        <td><?php echo $e($s['plan_title']); ?></td><td>ended <?php echo $e(date('d M Y', strtotime($s['expires_at']))); ?></td>
                        <td class="text-right"><span class="label label-danger"><?php echo abs((int) $s['days_left']); ?> days ago</span></td></tr>
                <?php } ?>
            </table></div>
        </div>
    </section>
</div>
