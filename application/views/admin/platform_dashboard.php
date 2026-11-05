<?php
$e = function ($v) { return html_escape((string) $v); };
$money = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . number_format((float) $v, 0); };
$this_month = end($monthly_revenue);
$max = max(1, max($monthly_revenue));
$view = function ($id) { return site_url('admin/schools/view/' . (int) $id); };
?>
<style>
    .pd-kpi { display: block; background: #fff; border-radius: 4px; padding: 14px 16px; margin-bottom: 16px; border: 1px solid #e5e5e5; color: inherit; }
    .pd-kpi:hover { border-color: #3c8dbc; text-decoration: none; color: inherit; }
    .pd-kpi .pd-label { color: #777; font-size: 12px; text-transform: uppercase; letter-spacing: .03em; }
    .pd-kpi .pd-value { font-size: 26px; font-weight: 600; line-height: 1.3; }
    .pd-kpi .pd-hint { font-size: 12px; color: #888; }
    .pd-kpi.pd-attn { border-left: 4px solid #f39c12; }
    .pd-kpi.pd-bad { border-left: 4px solid #dd4b39; }
    .pd-kpi.pd-ok { border-left: 4px solid #00a65a; }
    .pd-kpi.pd-info { border-left: 4px solid #3c8dbc; }
    .pd-empty { color: #999; padding: 14px 0; }
    .pd-list td { vertical-align: middle !important; }
    .pd-sub { color: #888; font-size: 12px; }
    .pd-chart { display: flex; align-items: flex-end; gap: 10px; height: 160px; padding: 6px 4px 0; }
    .pd-bar { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; }
    .pd-bar i { display: block; width: 100%; max-width: 46px; background: #3c8dbc; border-radius: 3px 3px 0 0; min-height: 2px; }
    .pd-bar small { margin-top: 4px; color: #777; font-size: 11px; }
    .pd-bar em { font-style: normal; font-size: 11px; color: #555; margin-bottom: 2px; white-space: nowrap; }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-tachometer"></i> Platform overview</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-lg-2 col-sm-4 col-xs-6">
                <a class="pd-kpi pd-attn" href="<?php echo site_url('admin/schools?tab=waiting_approval'); ?>">
                    <div class="pd-label">Waiting for approval</div>
                    <div class="pd-value"><?php echo (int) $counts['waiting_approval']; ?></div>
                    <div class="pd-hint">Set up and approve</div>
                </a>
            </div>
            <div class="col-lg-2 col-sm-4 col-xs-6">
                <a class="pd-kpi pd-attn" href="<?php echo site_url('admin/subscription_bank_transfers'); ?>">
                    <div class="pd-label">Receipts to review</div>
                    <div class="pd-value"><?php echo (int) $pending_transfer_count; ?></div>
                    <div class="pd-hint">Bank transfers</div>
                </a>
            </div>
            <div class="col-lg-2 col-sm-4 col-xs-6">
                <a class="pd-kpi pd-ok" href="<?php echo site_url('admin/schools?tab=active'); ?>">
                    <div class="pd-label">Active schools</div>
                    <div class="pd-value"><?php echo (int) $counts['active']; ?></div>
                    <div class="pd-hint"><?php echo (int) $counts['all']; ?> registered</div>
                </a>
            </div>
            <div class="col-lg-2 col-sm-4 col-xs-6">
                <a class="pd-kpi <?php echo $counts['expiring'] ? 'pd-attn' : 'pd-info'; ?>" href="<?php echo site_url('admin/schools?tab=expiring'); ?>">
                    <div class="pd-label">Expiring in 30 days</div>
                    <div class="pd-value"><?php echo (int) $counts['expiring']; ?></div>
                    <div class="pd-hint"><?php echo (int) $counts['expired']; ?> already expired</div>
                </a>
            </div>
            <div class="col-lg-2 col-sm-4 col-xs-6">
                <a class="pd-kpi <?php echo $counts['disabled'] ? 'pd-bad' : 'pd-info'; ?>" href="<?php echo site_url('admin/schools?tab=disabled'); ?>">
                    <div class="pd-label">Disabled</div>
                    <div class="pd-value"><?php echo (int) $counts['disabled']; ?></div>
                    <div class="pd-hint">Can't log in</div>
                </a>
            </div>
            <div class="col-lg-2 col-sm-4 col-xs-6">
                <a class="pd-kpi pd-info" href="<?php echo site_url('admin/subscription_list'); ?>">
                    <div class="pd-label">Revenue this month</div>
                    <div class="pd-value"><?php echo $money($this_month); ?></div>
                    <div class="pd-hint"><?php echo $money($total_revenue); ?> in total</div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border"><h3 class="box-title">Waiting for approval</h3>
                        <div class="box-tools"><a href="<?php echo site_url('admin/schools?tab=waiting_approval'); ?>" class="btn btn-box-tool">All</a></div></div>
                    <div class="box-body no-padding">
                        <?php if (!$waiting) { ?><div class="pd-empty text-center">Nothing to approve.</div><?php } else { ?>
                        <table class="table pd-list">
                            <?php foreach ($waiting as $s) { ?>
                                <tr>
                                    <td><a href="<?php echo $view($s['id']); ?>"><b><?php echo $e($s['school_name']); ?></b></a>
                                        <div class="pd-sub"><?php echo $e($s['owner_name']); ?> · <?php echo $e($s['plan_title'] ?: 'No plan'); ?></div></td>
                                    <td class="pd-sub"><?php echo $e(date('d M', strtotime($s['created_at']))); ?></td>
                                    <td class="text-right"><a class="btn btn-xs btn-warning" href="<?php echo $view($s['id']); ?>">Review &amp; approve</a></td>
                                </tr>
                            <?php } ?>
                        </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border"><h3 class="box-title">Bank transfer receipts to review</h3>
                        <div class="box-tools"><a href="<?php echo site_url('admin/subscription_bank_transfers'); ?>" class="btn btn-box-tool">All</a></div></div>
                    <div class="box-body no-padding">
                        <?php if (!$pending_transfers) { ?><div class="pd-empty text-center">No receipts waiting.</div><?php } else { ?>
                        <table class="table pd-list">
                            <?php foreach ($pending_transfers as $t) { ?>
                                <tr>
                                    <td><b><?php echo $e($t['school_name']); ?></b><div class="pd-sub"><?php echo $e($t['plan_title']); ?></div></td>
                                    <td class="text-right"><?php echo $money($t['amount']); ?>
                                        <?php if ((float) $t['amount'] < (float) $t['total']) { ?><div class="pd-sub text-danger">due <?php echo $money($t['total']); ?></div><?php } ?></td>
                                    <td class="text-right"><a class="btn btn-xs btn-warning" href="<?php echo site_url('admin/subscription_bank_transfers/view/' . (int) $t['id']); ?>">Review</a></td>
                                </tr>
                            <?php } ?>
                        </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Subscriptions expiring soon</h3></div>
                    <div class="box-body no-padding">
                        <?php if (!$expiring) { ?><div class="pd-empty text-center">No subscription expires in the next 30 days.</div><?php } else { ?>
                        <table class="table pd-list">
                            <?php foreach ($expiring as $s) { ?>
                                <tr>
                                    <td><a href="<?php echo $view($s['id']); ?>"><b><?php echo $e($s['school_name']); ?></b></a>
                                        <div class="pd-sub"><?php echo $e($s['plan_title']); ?></div></td>
                                    <td><?php echo $e(date('d M Y', strtotime($s['expires_at']))); ?></td>
                                    <td class="text-right"><?php echo $s['days_left'] < 0
                                        ? '<span class="label label-danger">Expired</span>'
                                        : '<span class="label label-warning">' . (int) $s['days_left'] . ' days</span>'; ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Revenue, last 6 months</h3></div>
                    <div class="box-body">
                        <div class="pd-chart" role="img" aria-label="Paid subscription revenue per month">
                            <?php foreach ($monthly_revenue as $ym => $total) { ?>
                                <div class="pd-bar" title="<?php echo $e(date('F Y', strtotime($ym . '-01'))) . ': ' . $money($total); ?>">
                                    <em><?php echo $total > 0 ? $money($total) : ''; ?></em>
                                    <i style="height: <?php echo round($total / $max * 100, 1); ?>%"></i>
                                    <small><?php echo $e(date('M', strtotime($ym . '-01'))); ?></small>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-default">
            <div class="box-header with-border"><h3 class="box-title">Latest registrations</h3>
                <div class="box-tools"><a href="<?php echo site_url('admin/schools'); ?>" class="btn btn-box-tool">All schools</a></div></div>
            <div class="box-body no-padding">
                <?php if (!$recent) { ?><div class="pd-empty text-center">No schools yet.</div><?php } else { ?>
                <table class="table pd-list">
                    <?php $labels = array('waiting_approval' => 'label-warning', 'awaiting_payment' => 'label-default', 'active' => 'label-success', 'disabled' => 'label-danger');
                    foreach ($recent as $s) { ?>
                        <tr>
                            <td><a href="<?php echo $view($s['id']); ?>"><b><?php echo $e($s['school_name']); ?></b></a>
                                <div class="pd-sub"><?php echo $e($s['email_address']); ?></div></td>
                            <td><?php echo $e($s['plan_title'] ?: '—'); ?></td>
                            <td><span class="label <?php echo $labels[$s['state']]; ?>"><?php echo $e(ucwords(str_replace('_', ' ', $s['state']))); ?></span></td>
                            <td class="pd-sub text-right"><?php echo $e(date('d M Y', strtotime($s['created_at']))); ?></td>
                        </tr>
                    <?php } ?>
                </table>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
