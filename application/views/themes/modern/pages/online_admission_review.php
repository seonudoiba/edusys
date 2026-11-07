<?php
/*
 * An application after it's been filled in: its status (sent, awaiting payment), the reference
 * number to check it later, and everything entered, under the school's own labels and order
 * (libraries/Admission_form.php). Applicants can change it until it's sent, pay the form fee, print it.
 */
$e = function ($v) { return html_escape((string) $v); };
$CI = get_instance();
$CI->load->library('Admission_form');
$adm = $CI->admission_form;
$a = $application;
$fee_on = $online_admission_payment == 'yes';
$sent = (int) $form_status === 1;
$is_admin = $status !== '';
$fmt = function ($d) { return ($d && $d !== '0000-00-00' && $d !== '1970-01-01') ? date($this->customlib->getSchoolDateFormat(), strtotime($d)) : ''; };
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$has_terms = trim(strip_tags((string) $online_admission_conditions)) !== '';

// What each field shows.
$column = array('category_id' => 'category', 'house' => 'house_name', 'measure_date' => 'measurement_date', 'file' => 'image', 'upload_documents' => 'document');
$value = function ($key) use ($a, $adm, $fmt, $column, $section_name) {
    $f = Admission_form::fields()[$key];
    $name = $f['post'];
    switch ($key) {
        case 'class': return $a['class'] . ($section_name !== '' && strcasecmp($section_name, 'General') !== 0 ? ' (' . $section_name . ')' : '');
        case 'dob': return $fmt($a['dob']);
        case 'measurement_date': return $fmt($a['measurement_date']);
        case 'if_guardian_is': return ucfirst((string) $a['guardian_is']);
        case 'upload_documents': return !empty($a['document']) ? 'Received' : '';
    }
    $col = isset($column[$name]) ? $column[$name] : $name;
    return isset($a[$col]) ? (string) $a[$col] : '';
};
$photos = array();
foreach (array('student_photo' => array('image', 'student'), 'father_pic' => array('father_pic', 'father'), 'mother_pic' => array('mother_pic', 'mother'), 'guardian_photo' => array('guardian_pic', 'guardian')) as $key => $p) {
    if ($adm->shown($key) && !empty($a[$p[0]])) {
        $photos[] = array(base_url($a[$p[0]]), $this->lang->line($p[1]));
    }
}
if ($sent) {
    list($state_class, $state_text, $state_note) = array('is-sent', 'Application submitted', 'The school has your application. Keep your reference number and the date of birth you entered to check its progress.');
} elseif ($fee_on && (int) $paid_status === 2) {
    list($state_class, $state_text, $state_note) = array('is-wait', 'Payment being confirmed', 'Your payment is being confirmed. Your application is sent as soon as it is.');
} elseif ($fee_on) {
    list($state_class, $state_text, $state_note) = array('is-wait', 'Awaiting payment', 'Pay the form fee of ' . $currency_symbol . amountFormat($online_admission_amount) . ' below to send your application. You can still change it until then.');
} else {
    list($state_class, $state_text, $state_note) = array('is-wait', 'Not sent yet', 'Check your details, then accept the terms and send your application.');
}
if ($this->session->flashdata('msg')) { $this->session->unset_userdata('msg'); } // the status panel says it
?>
<style>
#fade { display: none; position: fixed; inset: 0; background: rgba(255,255,255,.7); z-index: 1001; }
#modal { display: none; position: fixed; top: 45%; left: 50%; z-index: 1002; font-size: 28px; }
@media print {
    body.st * { visibility: hidden; }
    #divtoprint, #divtoprint * { visibility: visible; }
    #divtoprint { position: absolute; left: 0; top: 0; width: 100%; }
    .st-rev-noprint { display: none !important; }
    .st-rev-printonly { display: block !important; }
    .st-adm-card { box-shadow: none; break-inside: avoid; }
}
</style>

<div id="divtoprint" class="st-rev">
    <div class="st-rev-printonly" style="display:none">
        <?php ob_start(); $this->setting_model->get_onlineadmissionheader(); $header = trim(ob_get_clean()); // it echoes the file name
        if ($header !== '') { ?><img src="<?php echo $e(base_url('uploads/print_headerfooter/online_admission_receipt/' . $header)); ?>" alt="" style="width:100%;max-height:120px;object-fit:contain"><?php } ?>
    </div>

    <div class="st-rev-status <?php echo $state_class; ?>" role="status">
        <div class="st-rev-icon" aria-hidden="true"><i class="fa <?php echo $sent ? 'fa-check' : 'fa-clock-o'; ?>"></i></div>
        <div class="st-rev-main">
            <h2><?php echo $e($state_text); ?></h2>
            <p><?php echo $e($state_note); ?></p>
        </div>
        <div class="st-rev-ref">
            <span>Reference number</span>
            <b><?php echo $e($reference_no); ?></b>
        </div>
    </div>

    <ol class="st-rev-steps st-rev-noprint" aria-label="Progress">
        <li class="done">Details filled in</li>
        <?php if ($fee_on) { ?><li class="<?php echo (int) $paid_status === 1 ? 'done' : 'current'; ?>">Form fee paid</li><?php } ?>
        <li class="<?php echo $sent ? 'done' : ''; ?>">Application submitted</li>
        <li>School reviews it</li>
    </ol>

    <dl class="st-rev-facts">
        <div><dt><?php echo $this->lang->line('reference_no'); ?></dt><dd><?php echo $e($reference_no); ?></dd></div>
        <div><dt><?php echo $this->lang->line('form_status'); ?></dt><dd><span class="st-rev-badge <?php echo $sent ? 'ok' : 'wait'; ?>"><?php echo $sent ? 'Submitted' : $e($state_text); ?></span></dd></div>
        <?php if ($fee_on) { ?>
            <div><dt><?php echo $this->lang->line('payment_status'); ?></dt><dd>
                <?php if ((int) $paid_status === 1) { ?><span class="st-rev-badge ok"><?php echo $this->lang->line('paid'); ?></span> <?php echo $e($currency_symbol . amountFormat($transaction_paid_amount)); ?>
                <?php } elseif ((int) $paid_status === 2) { ?><span class="st-rev-badge wait"><?php echo $this->lang->line('processing'); ?></span>
                <?php } else { ?><span class="st-rev-badge wait"><?php echo $this->lang->line('unpaid'); ?></span><?php } ?>
                <?php if (!empty($transaction_id)) { ?><br><small><?php echo $this->lang->line('transaction_id'); ?>: <?php echo $e($transaction_id); ?></small><?php } ?>
            </dd></div>
        <?php } ?>
        <div><dt><?php echo $this->lang->line('application_date'); ?></dt><dd><?php echo $e($application_date); ?></dd></div>
    </dl>

    <div class="st-rev-toolbar st-rev-noprint">
        <h3><?php echo $this->lang->line('review_entered_details_and_status'); ?></h3>
        <div>
            <?php if (!$sent && !$is_admin) { ?>
                <a class="st-btn st-btn-ghost" href="<?php echo site_url('welcome/editonlineadmission/' . $reference_no); ?>" id="editbtn"><i class="fa fa-pencil"></i> Change details</a>
            <?php } ?>
            <button type="button" class="st-btn st-btn-ghost" id="printbtn" onclick="window.print()"><i class="fa fa-print"></i> <?php echo $this->lang->line('print'); ?></button>
        </div>
    </div>

    <?php if ($photos) { ?>
        <div class="st-rev-photos">
            <?php foreach ($photos as $p) { ?><figure><img src="<?php echo $e($p[0]); ?>" alt=""><figcaption><?php echo $e($p[1]); ?></figcaption></figure><?php } ?>
        </div>
    <?php } ?>

    <?php
    $answers = array();
    foreach (get_onlineadmission_custom_table_values($id, 'students') as $q) {
        if ($this->customlib->getfieldstatus($q->name) && $q->field_value !== null && $q->field_value !== '') {
            $answers[] = $q;
        }
    }
    foreach (Admission_form::SECTIONS as $section => $heading) {
        $rows = array();
        foreach ($adm->section_fields($section) as $key) {
            $f = Admission_form::fields()[$key];
            if ($key === 'section' || ($f['type'] === 'file' && $key !== 'upload_documents')) { continue; }
            $rows[$key] = $value($key);
        }
        if (array_filter($rows, 'strlen')) { ?>
            <section class="st-adm-card">
                <h3><?php echo $this->lang->line($heading); ?></h3>
                <dl class="st-rev-grid">
                    <?php foreach ($rows as $key => $v) { ?>
                        <div<?php echo Admission_form::fields()[$key]['type'] === 'textarea' ? ' class="wide"' : ''; ?>><dt><?php echo $e($adm->label($key)); ?></dt><dd><?php echo $v === '' ? '<span class="st-adm-muted">—</span>' : nl2br($e($v)); ?></dd></div>
                    <?php } ?>
                </dl>
            </section>
        <?php }
        if ($section === 'student' && $answers) { ?>
            <section class="st-adm-card">
                <h3><?php echo $e($adm->questions_title()); ?></h3>
                <dl class="st-rev-grid">
                    <?php foreach ($answers as $q) { ?>
                        <div class="wide"><dt><?php echo $e($q->name); ?></dt><dd><?php echo nl2br($e(str_replace(',', ', ', $q->field_value))); ?></dd></div>
                    <?php } ?>
                </dl>
            </section>
        <?php }
    } ?>

    <?php if (!$sent && !$is_admin) { ?>
        <div class="st-rev-action st-rev-noprint">
            <?php if ($fee_on && (int) $paid_status === 0) { ?>
                <form id="paymentform" action="<?php echo base_url(); ?>onlineadmission/checkout" method="post">
                    <input type="hidden" name="admission_id" value="<?php echo (int) $admission_id; ?>">
                    <input type="hidden" name="reference_no" value="<?php echo $e($reference_no); ?>">
                    <?php if (!empty($currencies) && count($currencies) > 1) { ?>
                        <label class="st-rev-currency"><?php echo $this->lang->line('change_currency'); ?>
                            <select class="form-control currency_list" name="currency">
                                <?php foreach ($currencies as $c) { ?><option value="<?php echo (int) $c->id; ?>"<?php echo $c->id == $this->customlib->getSchoolCurrency() ? ' selected' : ''; ?>><?php echo $e($c->short_name . ' (' . $c->symbol . ')'); ?></option><?php } ?>
                            </select>
                        </label>
                    <?php } ?>
                    <button type="submit" id="paybtn" class="onlineformbtn st-adm-go"><?php echo $this->lang->line('pay'); ?> <?php echo $e($currency_symbol . amountFormat($online_admission_amount)); ?></button>
                </form>
            <?php } elseif (!$fee_on) { ?>
                <form id="admissionform" action="<?php echo base_url(); ?>welcome/submitadmission" method="post">
                    <input type="hidden" name="admission_id" value="<?php echo (int) $admission_id; ?>">
                    <input type="hidden" name="reference_no" value="<?php echo $e($reference_no); ?>">
                    <?php if ($has_terms) { ?>
                        <label class="st-adm-terms"><input type="checkbox" id="checkterm" name="checkterm" value="1"> <span>I agree to the <a href="#openTermsAndConditionsModal" data-toggle="modal" data-target="#openTermsAndConditionsModal">terms and conditions</a></span></label>
                    <?php } else { ?><input type="hidden" name="checkterm" value="1"><?php } ?>
                    <span class="text-danger" id="termerror"></span>
                    <button type="submit" class="onlineformbtn st-adm-go" id="submitbtn">Submit application</button>
                </form>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="st-rev-printonly" style="display:none"><?php $this->setting_model->get_onlineadmissionfooter(); ?></div>
</div>

<?php if ($has_terms) { ?>
<div id="openTermsAndConditionsModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header modal-header-small"><button type="button" class="close closebtnmodal" data-dismiss="modal">&times;</button><h4><?php echo $this->lang->line('online_admission_terms_conditions'); ?></h4></div>
        <div class="modal-body st-prose"><?php echo $online_admission_conditions; ?></div>
        <div class="modal-footer"><button type="button" class="modalclosebtn btn mdbtn" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button></div>
    </div></div>
</div>
<?php } ?>
<div id="fade"></div><div id="modal"><i class="fa fa-spinner fa-spin"></i></div>

<script>
$(function () {
    $(document).on('submit', '#admissionform', function (ev) {
        ev.preventDefault();
        var form = $(this), $btn = $('#submitbtn').prop('disabled', true);
        $.post(form.attr('action'), form.serializeArray(), function (r) {
            if (r.status == 0) { $('#termerror').html(r.error); $btn.prop('disabled', false); return; }
            window.location.href = '<?php echo base_url('welcome/online_admission_review/'); ?>' + r.reference_no;
        }, 'json').fail(function () { $btn.prop('disabled', false); });
    });
    $(document).on('change', '.currency_list', function () {
        $('#fade, #modal').show();
        $.post(base_url + 'welcome/changeCurrencyFormat', { currency_id: $(this).val() }, function () { window.location.reload(); }, 'json');
    });
});
</script>
