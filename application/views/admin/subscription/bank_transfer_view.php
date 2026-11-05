<?php
$t = $transfer;
$labels = array(
    'pending' => array('Waiting for review', 'label-warning'),
    'awaiting_receipt' => array('No receipt yet', 'label-default'),
    'approved' => array('Approved', 'label-success'),
    'rejected' => array('Rejected', 'label-danger'),
);
$l = isset($labels[$t['status']]) ? $labels[$t['status']] : array($t['status'], 'label-default');
$money = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . number_format((float) $v, 2); };
$receipt_url = site_url('admin/subscription_bank_transfers/receipt/' . (int) $t['id']);
$is_pdf = substr((string) $t['receipt_file'], -4) === '.pdf';
$short = $t['amount'] !== null && (float) $t['amount'] < (float) $t['total'];
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-bank"></i> Bank Transfer Payment</h1>
    </section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo html_escape($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo html_escape($error); ?></div><?php } ?>
        <div class="row">
            <div class="col-md-5">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo html_escape($t['school_name']); ?></h3>
                        <span class="label <?php echo $l[1]; ?> pull-right"><?php echo $l[0]; ?></span>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <tr><th>Owner</th><td><?php echo html_escape($t['owner_name']); ?></td></tr>
                            <tr><th>Email</th><td><?php echo html_escape($t['email_address']); ?></td></tr>
                            <tr><th>Phone</th><td><?php echo html_escape($t['phone_number']); ?></td></tr>
                            <tr><th>Plan</th><td><?php echo html_escape($t['plan_title']); ?> (<?php echo (int) $t['plan_days']; ?> days)</td></tr>
                            <tr><th>Amount due</th><td><strong><?php echo $money($t['total']); ?></strong></td></tr>
                            <tr><th>Amount paid</th><td class="<?php echo $short ? 'text-danger' : ''; ?>">
                                <?php echo $t['amount'] !== null ? $money($t['amount']) : ''; ?>
                                <?php if ($short) { ?><br><small>Less than the amount due.</small><?php } ?>
                            </td></tr>
                            <tr><th>Paid on</th><td><?php echo html_escape((string) $t['payment_date']); ?></td></tr>
                            <tr><th>From account</th><td><?php echo html_escape((string) $t['payer_name']); ?></td></tr>
                            <tr><th>Bank</th><td><?php echo html_escape((string) $t['payer_bank']); ?></td></tr>
                            <tr><th>Reference</th><td><?php echo html_escape((string) $t['reference']); ?></td></tr>
                            <tr><th>Submitted</th><td><?php echo html_escape((string) $t['submitted_at']); ?></td></tr>
                            <?php if ($t['reviewed_at']) { ?>
                                <tr><th>Reviewed</th><td><?php echo html_escape($t['reviewed_at']); ?>
                                    <?php if ($reviewer) { ?> by <?php echo html_escape(trim($reviewer['name'] . ' ' . $reviewer['surname'])); ?><?php } ?></td></tr>
                            <?php } ?>
                            <?php if ($t['review_note']) { ?>
                                <tr><th>Reason given</th><td><?php echo nl2br(html_escape($t['review_note'])); ?></td></tr>
                            <?php } ?>
                        </table>
                        <p class="text-muted">Check the money has arrived in the platform's account before approving.</p>
                    </div>
                    <?php if ($t['status'] === 'pending') { ?>
                        <div class="box-footer">
                            <form method="post" action="<?php echo site_url('admin/subscription_bank_transfers/approve/' . (int) $t['id']); ?>" style="margin-bottom:15px"
                                  onsubmit="return confirm('Approve this payment and activate the subscription?');">
                                <input type="hidden" name="form_token" value="<?php echo html_escape($form_token); ?>">
                                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Approve payment</button>
                            </form>
                            <form method="post" action="<?php echo site_url('admin/subscription_bank_transfers/reject/' . (int) $t['id']); ?>">
                                <input type="hidden" name="form_token" value="<?php echo html_escape($form_token); ?>">
                                <div class="form-group">
                                    <label for="review_note">Reason for rejecting (shown to the school)</label>
                                    <textarea id="review_note" name="review_note" class="form-control" rows="3" maxlength="2000" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger"><i class="fa fa-times"></i> Reject payment</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
                <a href="<?php echo site_url('admin/subscription_bank_transfers'); ?>">&larr; All bank transfer payments</a>
            </div>
            <div class="col-md-7">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Receipt</h3>
                        <?php if ($t['receipt_file']) { ?>
                            <a class="btn btn-default btn-xs pull-right" href="<?php echo $receipt_url; ?>?download=1"><i class="fa fa-download"></i> Download</a>
                        <?php } ?>
                    </div>
                    <div class="box-body">
                        <?php if (!$t['receipt_file']) { ?>
                            <p class="text-muted">The school hasn't uploaded a receipt yet.</p>
                        <?php } elseif ($is_pdf) { ?>
                            <p><a href="<?php echo $receipt_url; ?>" target="_blank" rel="noopener">Open the PDF receipt</a>
                                (<?php echo html_escape((string) $t['receipt_name']); ?>)</p>
                        <?php } else { ?>
                            <a href="<?php echo $receipt_url; ?>" target="_blank" rel="noopener">
                                <img src="<?php echo $receipt_url; ?>" alt="Receipt" style="max-width:100%;border:1px solid #ddd">
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
