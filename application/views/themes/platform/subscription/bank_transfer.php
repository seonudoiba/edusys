<?php
$status = $transfer['status'];
$amount_due = (float) $subscription['total'];
$page_url = site_url('subscription/bank_transfer/' . $transfer['token']);
$can_upload = in_array($status, array('awaiting_receipt', 'rejected'), true);
?>
<section class="hero hero-sm has-bg">
    <?php $this->load->view('themes/platform/_bg'); ?>
    <div class="wrap"><h1>Pay by bank transfer.</h1></div>
</section>
<div class="wrap">
    <div>
        <div class="page-body narrow">
            <p class="hint">
                <?php echo html_escape($subscription['school_name']); ?> ·
                EduSYS subscription
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

            <div>
                <h2>Amount to pay</h2>
                <p class="amount"><?php echo html_escape($currency_symbol) . number_format($amount_due, 2); ?></p>
                <h2>Bank account</h2>
                <?php if (trim(strip_tags($bank_instructions)) !== '') { ?>
                    <div class="prose"><?php echo $bank_instructions; ?></div>
                <?php } else { ?>
                    <p class="text-danger">The bank account details are not available yet. Please contact us before paying.</p>
                <?php } ?>
                <p class="hint">Use your school name as the transfer description.</p>
            </div>

            <div>
                <h2>Keep this link</h2>
                <p class="hint">Come back to this page to upload your receipt or check its status:</p>
                <input type="text" class="form-control" readonly value="<?php echo html_escape($page_url); ?>" onclick="this.select()">
            </div>

            <?php if ($can_upload) { ?>
                <div>
                    <h2>Upload your receipt</h2>
                    <?php if ($upload_error) { ?>
                        <div class="alert alert-danger"><?php echo html_escape($upload_error); ?></div>
                    <?php } ?>
                    <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                    <form action="<?php echo site_url('subscription/submit_receipt/' . $transfer['token']); ?>" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group">
                                <label for="payer_name">Account name the payment came from</label>
                                <input id="payer_name" name="payer_name" type="text" class="form-control" maxlength="200" required
                                       value="<?php echo html_escape(set_value('payer_name', (string) $transfer['payer_name'])); ?>">
                            </div>
                            <div class="form-group">
                                <label for="payer_bank">Bank</label>
                                <input id="payer_bank" name="payer_bank" type="text" class="form-control" maxlength="200" required
                                       value="<?php echo html_escape(set_value('payer_bank', (string) $transfer['payer_bank'])); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="payment_date">Payment date</label>
                                <input id="payment_date" name="payment_date" type="date" class="form-control" required max="<?php echo date('Y-m-d'); ?>"
                                       value="<?php echo html_escape(set_value('payment_date', (string) $transfer['payment_date'])); ?>">
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount paid</label>
                                <input id="amount" name="amount" type="number" step="0.01" min="0.01" class="form-control" required
                                       value="<?php echo html_escape(set_value('amount', $transfer['amount'] !== null ? (string) $transfer['amount'] : (string) $amount_due)); ?>">
                            </div>
                            <div class="form-group">
                                <label for="reference">Transaction reference</label>
                                <input id="reference" name="reference" type="text" class="form-control" maxlength="200"
                                       value="<?php echo html_escape(set_value('reference', (string) $transfer['reference'])); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="receipt">Receipt (JPG, PNG or PDF, up to 5 MB)</label>
                            <input id="receipt" name="receipt" type="file" class="form-control" required accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf">
                        </div>
                        <button type="submit" class="btn btn-info">Submit receipt for review</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
