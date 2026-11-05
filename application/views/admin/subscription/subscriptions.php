<?php
$e = function ($v) { return html_escape((string) $v); };
$money = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . number_format((float) $v, 0); };
$f = $filters;
$query = function (array $over = array()) use ($f) {
    $q = array_filter(array_merge(array('status' => $f['status'] === 'all' ? '' : $f['status'], 'method' => $f['method'], 'plan' => $f['plan'] ?: '', 'from' => $f['from'], 'to' => $f['to'], 'q' => $f['q']), $over), 'strlen');
    return http_build_query($q);
};
$link = function (array $over = array()) use ($query) { $q = $query($over); return site_url('admin/subscription_list') . ($q !== '' ? '?' . $q : ''); };
$tabs = array('all' => 'All', 'active' => 'Active', 'expiring' => 'Expiring in 30 days', 'expired' => 'Expired', 'pending' => 'Unpaid');
$state_labels = array('active' => 'label-success', 'expired' => 'label-danger', 'pending' => 'label-default');
$method_names = array('paystack' => 'Paystack', 'bank_transfer' => 'Bank transfer', 'manual' => 'Manual', 'free' => 'Free plan');
$can_delete = $this->rbac->hasPrivilege('subscription_list', 'can_delete');
$filtered = $f['method'] !== '' || $f['plan'] || $f['from'] || $f['to'] || $f['q'] !== '';
?>
<style>
    .sb-filters { display: flex; flex-wrap: wrap; gap: 8px; align-items: flex-end; margin-bottom: 12px; }
    .sb-filters .form-group { margin: 0; }
    .sb-filters label { display: block; font-size: 11px; color: #777; font-weight: normal; margin-bottom: 2px; }
    .sb-sum { display: flex; flex-wrap: wrap; gap: 24px; padding: 10px 2px 14px; }
    .sb-sum div { font-size: 12px; color: #777; }
    .sb-sum b { display: block; font-size: 18px; color: #333; }
    .sb-table td { vertical-align: middle !important; }
    .sb-sub { color: #888; font-size: 12px; }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-credit-card"></i> Subscriptions</h1>
    </section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <div class="box box-primary">
            <div class="box-body">
                <ul class="nav nav-tabs" style="margin-bottom:12px">
                    <?php foreach ($tabs as $k => $label) { ?>
                        <li class="<?php echo $f['status'] === $k ? 'active' : ''; ?>"><a href="<?php echo $link(array('status' => $k === 'all' ? '' : $k)); ?>"><?php echo $label; ?> <span class="badge"><?php echo (int) $counts[$k]; ?></span></a></li>
                    <?php } ?>
                </ul>
                <form method="get" action="<?php echo site_url('admin/subscription_list'); ?>" class="sb-filters">
                    <?php if ($f['status'] !== 'all') { ?><input type="hidden" name="status" value="<?php echo $e($f['status']); ?>"><?php } ?>
                    <div class="form-group"><label for="q">Search</label><input id="q" type="search" name="q" class="form-control" value="<?php echo $e($f['q']); ?>" placeholder="School, owner, email, phone, reference" style="min-width:240px"></div>
                    <div class="form-group"><label for="method">Payment method</label>
                        <select id="method" name="method" class="form-control"><option value="">All methods</option>
                            <?php foreach ($methods as $m) { ?><option value="<?php echo $e($m); ?>" <?php echo $f['method'] === $m ? 'selected' : ''; ?>><?php echo $e(isset($method_names[$m]) ? $method_names[$m] : ucwords(str_replace('_', ' ', $m))); ?></option><?php } ?>
                        </select></div>
                    <div class="form-group"><label for="plan">Plan</label>
                        <select id="plan" name="plan" class="form-control"><option value="">All plans</option>
                            <?php foreach ($plans as $p) { ?><option value="<?php echo (int) $p['plan_id']; ?>" <?php echo $f['plan'] === (int) $p['plan_id'] ? 'selected' : ''; ?>><?php echo $e($p['title']); ?></option><?php } ?>
                        </select></div>
                    <div class="form-group"><label for="from">From</label><input id="from" type="date" name="from" class="form-control" value="<?php echo $e($f['from']); ?>"></div>
                    <div class="form-group"><label for="to">To</label><input id="to" type="date" name="to" class="form-control" value="<?php echo $e($f['to']); ?>"></div>
                    <button class="btn btn-default" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <?php if ($filtered) { ?><a class="btn btn-link" href="<?php echo $link(array('method' => '', 'plan' => '', 'from' => '', 'to' => '', 'q' => '')); ?>">Clear</a><?php } ?>
                    <a class="btn btn-default" style="margin-left:auto" href="<?php echo $link(array('export' => 'csv')); ?>"><i class="fa fa-download"></i> Export CSV</a>
                </form>
                <div class="sb-sum">
                    <div><b><?php echo (int) $totals['count']; ?></b>subscriptions</div>
                    <div><b><?php echo $money($totals['revenue']); ?></b><?php echo (int) $totals['paid']; ?> paid</div>
                    <div><b><?php echo $money($totals['outstanding']); ?></b><?php echo (int) $totals['pending']; ?> unpaid</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover sb-table">
                        <thead><tr><th>Date</th><th>School</th><th>Plan</th><th>Method</th><th class="text-right">Amount</th><th>Period</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                        <tbody>
                            <?php if (!$rows) { ?><tr><td colspan="8" class="text-center text-muted" style="padding:30px">No subscriptions match.</td></tr><?php } ?>
                            <?php foreach ($rows as $r) { ?>
                                <tr>
                                    <td><?php echo $e(date('d M Y', strtotime($r['created_at']))); ?><div class="sb-sub">#<?php echo (int) $r['id']; ?></div></td>
                                    <td>
                                        <?php if ($r['school_id']) { ?><a href="<?php echo site_url('admin/schools/view/' . (int) $r['school_id']); ?>"><b><?php echo $e($r['name']); ?></b></a>
                                        <?php } else { ?><b><?php echo $e($r['name']); ?></b> <span class="sb-sub">(sign-up not completed)</span><?php } ?>
                                        <div class="sb-sub"><?php echo $e($r['owner_name']); ?> · <?php echo $e($r['email_address']); ?> · <?php echo $e($r['phone_number']); ?></div>
                                    </td>
                                    <td><?php echo $e($r['plan_title'] ?: 'Plan #' . $r['plan_id']); ?><div class="sb-sub"><?php echo (int) $r['days']; ?> days</div></td>
                                    <td><?php echo $e(isset($method_names[$r['method']]) ? $method_names[$r['method']] : ucwords(str_replace('_', ' ', $r['method']))); ?>
                                        <?php if ($r['reference']) { ?><div class="sb-sub" title="Payment reference"><?php echo $e(mb_strimwidth($r['reference'], 0, 18, '…')); ?></div><?php } ?></td>
                                    <td class="text-right"><?php echo $money($r['total']); ?>
                                        <?php if ((float) $r['discount_amt'] > 0) { ?><div class="sb-sub"><s><?php echo $money($r['price']); ?></s></div><?php } ?></td>
                                    <td><?php if ($r['paid']) { ?>
                                            <?php echo $e(date('d M Y', strtotime($r['starts']))); ?> → <?php echo $e(date('d M Y', strtotime($r['ends']))); ?>
                                            <div class="sb-sub"><?php echo $r['days_left'] < 0 ? 'ended ' . abs($r['days_left']) . ' days ago' : $r['days_left'] . ' days left'; ?></div>
                                        <?php } else { ?><span class="sb-sub">Starts when paid</span><?php } ?></td>
                                    <td><span class="label <?php echo $state_labels[$r['state']]; ?>"><?php echo $r['state'] === 'pending' ? 'Unpaid' : ucfirst($r['state']); ?></span>
                                        <?php if ($r['expiring']) { ?><span class="label label-warning">Expiring</span><?php } ?></td>
                                    <td class="text-right" style="white-space:nowrap">
                                        <?php if ($r['transfer_id']) { ?>
                                            <a class="btn btn-xs <?php echo $r['transfer_status'] === 'pending' ? 'btn-warning' : 'btn-default'; ?>" href="<?php echo site_url('admin/subscription_bank_transfers/view/' . (int) $r['transfer_id']); ?>">
                                                <?php echo $r['transfer_status'] === 'pending' ? 'Review receipt' : 'Bank transfer'; ?></a>
                                        <?php } ?>
                                        <?php if ($r['school_id']) { ?><a class="btn btn-xs btn-default" href="<?php echo site_url('admin/schools/view/' . (int) $r['school_id']); ?>">School</a><?php } ?>
                                        <?php if ($can_delete && !$r['paid'] && !$r['school_id']) { ?>
                                            <form method="post" action="<?php echo site_url('admin/subscription_list/delete/' . (int) $r['id']); ?>" style="display:inline"
                                                  onsubmit="return confirm('Delete this unpaid sign-up?');"><?php echo $token; ?>
                                                <input type="hidden" name="back" value="<?php echo $e($query()); ?>">
                                                <button class="btn btn-xs btn-default text-danger" type="submit" title="Delete unpaid sign-up"><i class="fa fa-trash"></i></button>
                                            </form>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
