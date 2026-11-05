<?php
$labels = array(
    'pending' => array('Waiting for review', 'label-warning'),
    'awaiting_receipt' => array('No receipt yet', 'label-default'),
    'approved' => array('Approved', 'label-success'),
    'rejected' => array('Rejected', 'label-danger'),
);
$tabs = array('pending' => 'Waiting for review', 'awaiting_receipt' => 'No receipt yet', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-bank"></i> Bank Transfer Payments</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Subscription payments by bank transfer</h3>
                    </div>
                    <div class="box-body">
                        <ul class="nav nav-tabs" style="margin-bottom:15px">
                            <?php foreach ($tabs as $key => $label) { ?>
                                <li class="<?php echo $status === $key ? 'active' : ''; ?>">
                                    <a href="<?php echo site_url('admin/subscription_bank_transfers?status=' . $key); ?>">
                                        <?php echo $label; ?>
                                        <?php if ($key === 'pending' && $pending_count > 0) { ?>
                                            <span class="badge bg-yellow"><?php echo (int) $pending_count; ?></span>
                                        <?php } ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>School</th>
                                        <th>Plan</th>
                                        <th class="text-right">Amount due</th>
                                        <th class="text-right">Amount paid</th>
                                        <th>Paid on</th>
                                        <th>Reference</th>
                                        <th>Submitted</th>
                                        <th>Status</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($transfers)) { ?>
                                        <tr><td colspan="9" class="text-center text-muted">No payments here.</td></tr>
                                    <?php } ?>
                                    <?php foreach ($transfers as $t) {
                                        $l = isset($labels[$t['status']]) ? $labels[$t['status']] : array($t['status'], 'label-default'); ?>
                                        <tr>
                                            <td>
                                                <?php echo html_escape($t['school_name']); ?><br>
                                                <small class="text-muted"><?php echo html_escape($t['owner_name']); ?> · <?php echo html_escape($t['email_address']); ?></small>
                                            </td>
                                            <td><?php echo html_escape($t['plan_title']); ?></td>
                                            <td class="text-right"><?php echo html_escape($currency_symbol) . number_format((float) $t['total'], 2); ?></td>
                                            <td class="text-right">
                                                <?php if ($t['amount'] !== null) {
                                                    $short = (float) $t['amount'] < (float) $t['total']; ?>
                                                    <span class="<?php echo $short ? 'text-danger' : ''; ?>"><?php echo html_escape($currency_symbol) . number_format((float) $t['amount'], 2); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo html_escape((string) $t['payment_date']); ?></td>
                                            <td><?php echo html_escape((string) $t['reference']); ?></td>
                                            <td><?php echo html_escape((string) $t['submitted_at']); ?></td>
                                            <td><span class="label <?php echo $l[1]; ?>"><?php echo $l[0]; ?></span></td>
                                            <td class="text-right">
                                                <a class="btn btn-default btn-xs" href="<?php echo site_url('admin/subscription_bank_transfers/view/' . (int) $t['id']); ?>">
                                                    <?php echo $t['status'] === 'pending' ? 'Review' : 'View'; ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
