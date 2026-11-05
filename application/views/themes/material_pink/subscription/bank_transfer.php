<?php
$status = $transfer['status'];
$amount_due = (float) $subscription['total'];
$page_url = site_url('subscription/bank_transfer/' . $transfer['token']);
$can_upload = in_array($status, array('awaiting_receipt', 'rejected'), true);
?>
<div class="container spacet40 spaceb40">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h2 class="bt-title">Pay by bank transfer</h2>
            <p class="bt-muted">
                <?php echo html_escape($subscription['school_name']); ?> ·
                <?php echo html_escape($plan ? $plan['title'] : ''); ?>
            </p>

            <?php if ($status === 'pending') { ?>
                <div class="alert alert-info">
                    <strong>Receipt received.</strong> We are reviewing your payment. Once it is approved,
                    you will receive an email at <?php echo html_escape($subscription['email_address']); ?>
                    and your school will be set up.
                </div>
            <?php } elseif ($status === 'approved') { ?>
                <div class="alert alert-success">
                    <strong>Payment approved.</strong> Thank you. Your school is being set up; check
                    <?php echo html_escape($subscription['email_address']); ?> for the next steps.
                </div>
            <?php } elseif ($status === 'rejected') { ?>
                <div class="alert alert-danger">
                    <strong>Your receipt was not accepted.</strong>
                    <?php if (!empty($transfer['review_note'])) { ?>
                        <br>Reason: <?php echo nl2br(html_escape($transfer['review_note'])); ?>
                    <?php } ?>
                    <br>Please upload a new receipt below.
                </div>
            <?php } ?>

            <div class="bt-box">
                <h4>Amount to pay</h4>
                <p class="bt-amount"><?php echo html_escape($currency_symbol) . number_format($amount_due, 2); ?></p>
                <h4>Bank account</h4>
                <?php if (trim(strip_tags($bank_instructions)) !== '') { ?>
                    <div class="bt-bank"><?php echo $bank_instructions; ?></div>
                <?php } else { ?>
                    <p class="text-danger">The bank account details are not available yet. Please contact us before paying.</p>
                <?php } ?>
                <p class="bt-muted">Use your school name as the transfer description.</p>
            </div>

            <div class="bt-box">
                <h4>Keep this link</h4>
                <p class="bt-muted">Come back to this page to upload your receipt or check its status:</p>
                <input type="text" class="form-control" readonly value="<?php echo html_escape($page_url); ?>" onclick="this.select()">
            </div>

            <?php if ($can_upload) { ?>
                <div class="bt-box">
                    <h4>Upload your receipt</h4>
                    <?php if ($upload_error) { ?>
                        <div class="alert alert-danger"><?php echo html_escape($upload_error); ?></div>
                    <?php } ?>
                    <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                    <form action="<?php echo site_url('subscription/submit_receipt/' . $transfer['token']); ?>" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="payer_name">Account name the payment came from</label><small class="req"> *</small>
                                <input id="payer_name" name="payer_name" type="text" class="form-control" maxlength="200" required
                                       value="<?php echo html_escape(set_value('payer_name', (string) $transfer['payer_name'])); ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="payer_bank">Bank</label><small class="req"> *</small>
                                <input id="payer_bank" name="payer_bank" type="text" class="form-control" maxlength="200" required
                                       value="<?php echo html_escape(set_value('payer_bank', (string) $transfer['payer_bank'])); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="payment_date">Payment date</label><small class="req"> *</small>
                                <input id="payment_date" name="payment_date" type="date" class="form-control" required max="<?php echo date('Y-m-d'); ?>"
                                       value="<?php echo html_escape(set_value('payment_date', (string) $transfer['payment_date'])); ?>">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="amount">Amount paid</label><small class="req"> *</small>
                                <input id="amount" name="amount" type="number" step="0.01" min="0.01" class="form-control" required
                                       value="<?php echo html_escape(set_value('amount', $transfer['amount'] !== null ? (string) $transfer['amount'] : (string) $amount_due)); ?>">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="reference">Transaction reference</label>
                                <input id="reference" name="reference" type="text" class="form-control" maxlength="200"
                                       value="<?php echo html_escape(set_value('reference', (string) $transfer['reference'])); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="receipt">Receipt (JPG, PNG or PDF, up to 5 MB)</label><small class="req"> *</small>
                            <input id="receipt" name="receipt" type="file" class="form-control" required accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf">
                        </div>
                        <button type="submit" class="btn btn-info">Submit receipt for review</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<style>
    .bt-title { margin-top: 0; }
    .bt-muted { color: #666; }
    .bt-box { border: 1px solid #e3e3e3; border-radius: 6px; padding: 16px 20px; margin: 16px 0; background: #fff; }
    .bt-box h4 { margin-top: 0; }
    .bt-amount { font-size: 26px; font-weight: 600; margin-bottom: 14px; }
    .bt-bank { white-space: normal; margin-bottom: 8px; }
</style>
