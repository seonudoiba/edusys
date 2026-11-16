<?php
/* Online Admission > an application: details, form-fee payment and the decision (Onlinestudent::review). */
$e = function ($v) { return html_escape((string) $v); };
$cur = $this->customlib->getSchoolCurrencyFormat();
$date = function ($v) { return $v && strpos((string) $v, '0000') !== 0 ? html_escape($this->customlib->showDate(date('Y-m-d', strtotime($v)))) : ''; };
$name = trim($a['firstname'] . ' ' . $a['middlename'] . ' ' . $a['lastname']);
$fee_on = $sch_setting->online_admission_payment == 'yes';
$paid = (int) $a['paid_status'];
$status = $review ? $review['review_status'] : null;
$img = function ($path) { return $path && strpos($path, 'no_image') === false ? base_url($path) : ''; };
// label => value; empty values are left out
$sections = array(
    'Applicant' => array(
        'Class' => $a['class'] ? $a['class'] . ' (' . $a['section'] . ')' : '',
        'Date of birth' => $date($a['dob']),
        'Gender' => $a['gender'] ? $this->lang->line(strtolower($a['gender'])) : '',
        'Category' => $a['category'],
        'Religion' => $a['religion'],
        'Blood group' => $a['blood_group'],
        'Phone' => $a['mobileno'],
        'Email' => $a['email'],
        'Previous school' => $a['previous_school'],
        'Current address' => $a['current_address'],
        'Permanent address' => $a['permanent_address'],
        'State / city' => trim($a['state'] . ($a['state'] && $a['city'] ? ', ' : '') . $a['city']),
        'House' => $a['house_name'],
        'Height / weight' => trim(($a['height'] ? $a['height'] : '') . ($a['height'] && $a['weight'] ? ' / ' : '') . ($a['weight'] ? $a['weight'] : '')),
        'Note' => $a['note'],
    ),
    'Parents and guardian' => array(
        'Father' => trim($a['father_name'] . ($a['father_phone'] ? ', ' . $a['father_phone'] : '') . ($a['father_occupation'] ? ' (' . $a['father_occupation'] . ')' : '')),
        'Mother' => trim($a['mother_name'] . ($a['mother_phone'] ? ', ' . $a['mother_phone'] : '') . ($a['mother_occupation'] ? ' (' . $a['mother_occupation'] . ')' : '')),
        'Guardian' => trim($a['guardian_name'] . ($a['guardian_relation'] ? ' (' . $a['guardian_relation'] . ')' : '')),
        'Guardian phone' => $a['guardian_phone'],
        'Guardian email' => $a['guardian_email'],
        'Guardian address' => $a['guardian_address'],
    ),
);
?>
<style>
    .ar-head { display: flex; flex-wrap: wrap; gap: 16px; align-items: center; }
    .ar-photo { width: 84px; height: 84px; border-radius: 6px; object-fit: cover; border: 1px solid #ddd; background: #f4f4f4; }
    .ar-head h2 { margin: 0 0 4px; font-size: 22px; }
    .ar-tags .label { display: inline-block; margin: 2px 4px 2px 0; font-size: 12px; }
    .ar-table th { width: 170px; color: #666; font-weight: 600; }
    .ar-people { display: flex; gap: 12px; flex-wrap: wrap; }
    .ar-people figure { margin: 0; text-align: center; font-size: 12px; color: #666; }
    .ar-people img { width: 72px; height: 72px; border-radius: 6px; object-fit: cover; border: 1px solid #ddd; display: block; margin-bottom: 4px; }
    .ar-decide .btn { margin-bottom: 8px; }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-user-plus"></i> Online admission <small>Review</small></h1>
    </section>
    <section class="content">
        <?php if ($this->session->flashdata('msg')) { echo $this->session->flashdata('msg'); $this->session->unset_userdata('msg'); } ?>
        <p><a href="<?php echo site_url('admin/onlinestudent'); ?>"><i class="fa fa-arrow-left"></i> All applications</a></p>
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="ar-head">
                            <?php if ($img($a['image'])) { ?><img class="ar-photo" src="<?php echo $e($img($a['image'])); ?>" alt=""><?php } else { ?><span class="ar-photo" style="display:flex;align-items:center;justify-content:center;color:#aaa"><i class="fa fa-user fa-2x"></i></span><?php } ?>
                            <div>
                                <h2 id="ar_name"><?php echo $e($name); ?></h2>
                                <div class="ar-tags">
                                    <span class="label label-default">Ref <?php echo $e($a['reference_no']); ?></span>
                                    <?php if ((int) $a['form_status'] === 1) { ?><span class="label label-success">Submitted <?php echo $date($review['submit_date']); ?></span><?php } else { ?><span class="label label-danger">Not submitted</span><?php } ?>
                                    <?php if ($fee_on) { ?>
                                        <?php if ($paid === 1) { ?><span class="label label-success" id="ar_paid">Form fee paid</span>
                                        <?php } elseif ($paid === 2) { ?><span class="label label-warning" id="ar_paid">Payment to confirm</span>
                                        <?php } else { ?><span class="label label-danger" id="ar_paid">Form fee unpaid</span><?php } ?>
                                    <?php } ?>
                                    <?php if ($a['is_enroll']) { ?><span class="label label-primary">Enrolled</span>
                                    <?php } elseif ($status === 'approved') { ?><span class="label label-success" id="ar_status">Approved, not enrolled yet</span>
                                    <?php } elseif ($status === 'rejected') { ?><span class="label label-danger" id="ar_status">Rejected</span>
                                    <?php } else { ?><span class="label label-warning" id="ar_status">Waiting for review</span><?php } ?>
                                </div>
                                <div class="text-muted" style="margin-top:4px">Applied <?php echo $date($a['created_at']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php foreach ($sections as $title => $rows) {
                    $rows = array_filter($rows, function ($v) { return trim((string) $v) !== ''; });
                    if (!$rows) { continue; } ?>
                    <div class="box box-default">
                        <div class="box-header with-border"><h3 class="box-title"><?php echo $e($title); ?></h3></div>
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-condensed ar-table">
                                <?php foreach ($rows as $label => $value) { ?><tr><th><?php echo $e($label); ?></th><td><?php echo $e($value); ?></td></tr><?php } ?>
                            </table>
                        </div>
                    </div>
                <?php } ?>

                <?php $pics = array_filter(array('Father' => $img($a['father_pic']), 'Mother' => $img($a['mother_pic']), 'Guardian' => $img($a['guardian_pic']))); ?>
                <?php if ($pics || $a['document']) { ?>
                    <div class="box box-default">
                        <div class="box-header with-border"><h3 class="box-title">Photos and documents</h3></div>
                        <div class="box-body">
                            <?php if ($pics) { ?>
                                <div class="ar-people"><?php foreach ($pics as $who => $src) { ?><figure><img src="<?php echo $e($src); ?>" alt=""><?php echo $e($who); ?></figure><?php } ?></div>
                            <?php } ?>
                            <?php if ($a['document']) { ?>
                                <p style="margin:10px 0 0"><a class="btn btn-default btn-sm" href="<?php echo site_url('admin/onlinestudent/onlineadmission_download/' . (int) $a['id']); ?>"><i class="fa fa-download"></i> Download the uploaded document</a></p>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="col-md-4">
                <div class="box box-<?php echo $paid === 1 ? 'success' : ($paid === 2 ? 'warning' : 'default'); ?>" id="ar_payment">
                    <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-money"></i> Form fee payment</h3></div>
                    <div class="box-body">
                        <?php if (!$fee_on && !$payments && !$bank_transfer) { ?>
                            <p class="text-muted">The admission form has no fee.</p>
                        <?php } ?>
                        <?php foreach ($payments as $p) { ?>
                            <table class="table table-condensed" style="margin-bottom:10px">
                                <tr><th>Amount</th><td><b><?php echo $e($cur . amountFormat($p['paid_amount'])); ?></b></td></tr>
                                <tr><th>Paid by</th><td><?php echo $e(ucwords(str_replace('_', ' ', $p['payment_mode']))); ?><?php echo $p['payment_type'] === 'offline' ? '' : ' (online)'; ?></td></tr>
                                <tr><th>Date</th><td><?php echo $date($p['date']); ?></td></tr>
                                <?php if ($p['transaction_id']) { ?><tr><th>Reference</th><td style="word-break:break-all"><?php echo $e($p['transaction_id']); ?></td></tr><?php } ?>
                                <?php if ($p['note']) { ?><tr><th>Note</th><td><?php echo $e($p['note']); ?></td></tr><?php } ?>
                            </table>
                        <?php } ?>
                        <?php if ($bank_transfer && $bank_transfer['status'] !== 'approved') { $t = $bank_transfer; ?>
                            <h4 style="margin-top:0;font-size:15px">Bank transfer
                                <?php $labels = array('pending' => array('warning', 'To confirm'), 'rejected' => array('danger', 'Rejected')); ?>
                                <span class="label label-<?php echo $labels[$t['status']][0]; ?>"><?php echo $labels[$t['status']][1]; ?></span></h4>
                            <table class="table table-condensed" style="margin-bottom:8px">
                                <tr><th>Amount</th><td><?php echo $e($cur . amountFormat($t['amount'])); ?></td></tr>
                                <tr><th>From</th><td><?php echo $e($t['payer_name']); ?>, <?php echo $e($t['payer_bank']); ?></td></tr>
                                <tr><th>Date</th><td><?php echo $date($t['payment_date']); ?></td></tr>
                                <?php if ($t['reference']) { ?><tr><th>Reference</th><td><?php echo $e($t['reference']); ?></td></tr><?php } ?>
                                <tr><th>Receipt</th><td><a href="<?php echo site_url('admin/onlinestudent/transfer_receipt/' . (int) $t['id']); ?>" target="_blank" rel="noopener"><i class="fa fa-file-o"></i> Open receipt</a></td></tr>
                                <?php if ($t['review_note']) { ?><tr><th>Note</th><td><?php echo $e($t['review_note']); ?></td></tr><?php } ?>
                            </table>
                            <?php if ($t['status'] === 'pending' && $can_edit) { ?>
                                <p class="text-muted small">Check the money is in the school's account first.</p>
                                <form method="post" action="<?php echo site_url('admin/onlinestudent/transfer_review'); ?>" style="margin-bottom:8px">
                                    <input type="hidden" name="from" value="review"><input type="hidden" name="transfer_id" value="<?php echo (int) $t['id']; ?>"><input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success btn-block" id="ar_transfer_approve" onclick="return confirm('Mark the form fee as paid?');"><i class="fa fa-check"></i> Confirm payment received</button>
                                </form>
                                <form method="post" action="<?php echo site_url('admin/onlinestudent/transfer_review'); ?>">
                                    <input type="hidden" name="from" value="review"><input type="hidden" name="transfer_id" value="<?php echo (int) $t['id']; ?>"><input type="hidden" name="action" value="reject">
                                    <textarea name="note" class="form-control" rows="2" maxlength="500" required placeholder="Reason, shown to the applicant" style="margin-bottom:6px"></textarea>
                                    <button type="submit" class="btn btn-default btn-block"><i class="fa fa-times"></i> Payment not received</button>
                                </form>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($fee_on && !$payments && (!$bank_transfer || $bank_transfer['status'] === 'rejected')) { ?>
                            <p class="text-muted" style="margin:0">No payment yet.</p>
                        <?php } ?>
                    </div>
                </div>

                <div class="box box-primary ar-decide" id="ar_decide">
                    <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-gavel"></i> Decision</h3></div>
                    <div class="box-body">
                        <?php if ($status) { ?>
                            <p><b><?php echo $status === 'approved' ? 'Approved' : 'Rejected'; ?></b><?php if ($reviewer) { ?> by <?php echo $e(trim($reviewer['name'] . ' ' . $reviewer['surname'])); ?><?php } ?> on <?php echo $date($review['reviewed_at']); ?>.
                                <?php if ($review['review_note']) { ?><br><span class="text-muted"><?php echo $e($review['review_note']); ?></span><?php } ?></p>
                        <?php } ?>
                        <?php if ($a['is_enroll']) { ?>
                            <p>This applicant is a student now.</p>
                            <?php if ($enrolled_student_id) { ?><a class="btn btn-primary btn-block" href="<?php echo site_url('student/view/' . $enrolled_student_id); ?>"><i class="fa fa-user"></i> Open the student's profile</a><?php } ?>
                        <?php } elseif ($can_edit) { ?>
                            <?php if ($fee_on && $paid !== 1) { ?>
                                <div class="alert alert-warning" style="padding:8px 10px">The form fee isn't confirmed as paid yet.</div>
                            <?php } ?>
                            <?php if ($status !== 'rejected') { ?>
                                <form method="post" action="<?php echo site_url('admin/onlinestudent/decide/' . (int) $a['id']); ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success btn-block" id="ar_approve"><i class="fa fa-check"></i> <?php echo $status === 'approved' ? 'Continue to enroll' : 'Approve and enroll'; ?></button>
                                </form>
                                <p class="text-muted small">Next you check the details, choose the fees and click Save and Enroll; the applicant becomes a student with a login.</p>
                                <form method="post" action="<?php echo site_url('admin/onlinestudent/decide/' . (int) $a['id']); ?>" style="border-top:1px solid #eee;padding-top:10px">
                                    <input type="hidden" name="action" value="reject">
                                    <textarea name="note" class="form-control" rows="2" maxlength="500" required placeholder="Reason, shown to the applicant" style="margin-bottom:6px"></textarea>
                                    <button type="submit" class="btn btn-default btn-block" id="ar_reject"><i class="fa fa-times"></i> Reject application</button>
                                </form>
                            <?php } else { ?>
                                <form method="post" action="<?php echo site_url('admin/onlinestudent/decide/' . (int) $a['id']); ?>">
                                    <input type="hidden" name="action" value="reopen">
                                    <button type="submit" class="btn btn-default btn-block"><i class="fa fa-undo"></i> Review again</button>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
