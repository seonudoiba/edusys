<?php
$e = function ($v) { return html_escape((string) $v); };
$today = date('Y-m-d');
?>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-ticket"></i> Coupons <small>discount codes for sign-ups</small></h1></section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <p><a class="btn btn-primary" href="<?php echo site_url('admin/billing/coupon'); ?>"><i class="fa fa-plus"></i> Add coupon</a></p>
        <div class="box box-primary">
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead><tr><th>Code</th><th>Discount</th><th>Plans</th><th>Used</th><th>Valid</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                        <?php if (!$coupons) { ?><tr><td colspan="7" class="text-center text-muted" style="padding:24px">No coupons yet.</td></tr><?php } ?>
                        <?php foreach ($coupons as $c) {
                            $plan_ids = json_decode((string) $c['plan_ids'], true);
                            $expired = $c['ends_on'] && $today > $c['ends_on'];
                            $used_up = (int) $c['max_uses'] > 0 && (int) $c['used_count'] >= (int) $c['max_uses'];
                            $live = (int) $c['is_active'] && !$expired && !$used_up && (!$c['starts_on'] || $today >= $c['starts_on']); ?>
                            <tr>
                                <td><b style="font-family:monospace"><?php echo $e($c['code']); ?></b><div class="text-muted" style="font-size:12px"><?php echo $e($c['description']); ?></div></td>
                                <td><?php echo $c['discount_type'] === 'percent' ? (float) $c['value'] . '%' : 'NGN ' . number_format((float) $c['value'], 2); ?></td>
                                <td style="font-size:12px"><?php echo is_array($plan_ids) ? $e(implode(', ', array_map(function ($id) use ($plans) { return isset($plans[$id]) ? $plans[$id] : '#' . $id; }, $plan_ids))) : 'All plans'; ?></td>
                                <td><?php echo (int) $c['used_count']; ?><?php echo (int) $c['max_uses'] > 0 ? ' / ' . (int) $c['max_uses'] : ''; ?></td>
                                <td style="font-size:12px"><?php echo $c['starts_on'] ? $e(date('d M Y', strtotime($c['starts_on']))) : 'now'; ?> → <?php echo $c['ends_on'] ? $e(date('d M Y', strtotime($c['ends_on']))) : 'no end'; ?></td>
                                <td><span class="label <?php echo $live ? 'label-success' : 'label-default'; ?>"><?php echo $live ? 'Usable' : (!(int) $c['is_active'] ? 'Off' : ($expired ? 'Expired' : ($used_up ? 'Used up' : 'Not started'))); ?></span></td>
                                <td class="text-right" style="white-space:nowrap">
                                    <a class="btn btn-xs btn-default" href="<?php echo site_url('admin/billing/coupon/' . (int) $c['id']); ?>"><i class="fa fa-pencil"></i> Edit</a>
                                    <form method="post" action="<?php echo site_url('admin/billing/toggle_coupon/' . (int) $c['id']); ?>" style="display:inline"><?php echo $token; ?>
                                        <button class="btn btn-xs btn-default" type="submit"><?php echo (int) $c['is_active'] ? 'Switch off' : 'Switch on'; ?></button></form>
                                    <form method="post" action="<?php echo site_url('admin/billing/delete_coupon/' . (int) $c['id']); ?>" style="display:inline" onsubmit="return confirm('Delete this coupon?');"><?php echo $token; ?>
                                        <button class="btn btn-xs btn-default text-danger" type="submit"><i class="fa fa-trash"></i></button></form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
