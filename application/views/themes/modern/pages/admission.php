<?php
/*
 * The online admission form, built from the school's settings (libraries/Admission_form.php):
 * the fields it shows, which are required, their labels, help and order, the classes taking
 * applications, and when admission is open. Field names are the ones Welcome::admission reads.
 * pages/editadmission.php shows the same form filled in ($edit_values), for changes before sending.
 */
$e = function ($v) { return html_escape((string) $v); };
$CI = get_instance();
$CI->load->library('Admission_form');
$adm = $CI->admission_form;
$editing = isset($edit_values) && is_array($edit_values);
$state = $editing ? array('open' => true) : $admission_state;
$is_captcha = !$editing && !empty($is_captcha);
?>
<script src="<?php echo base_url('backend/dist/js/moment.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/datepicker/css/bootstrap-datetimepicker.css">
<script src="<?php echo base_url(); ?>backend/datepicker/js/bootstrap-datetimepicker.js"></script>

<?php if ($this->session->flashdata('msg')) {
    echo $this->session->flashdata('msg');
    $this->session->unset_userdata('msg');
} ?>

<div class="st-adm-head">
    <div>
        <h2 class="st-adm-title"><?php echo $editing ? 'Change your application' : $this->lang->line('online_admission'); ?></h2>
        <?php if ($editing) { ?>
            <p class="st-adm-sub">Reference number <b><?php echo $e($reference_no); ?></b>. Save your changes, then review and send the application.</p>
        <?php } elseif ($state['open'] && $adm->closes() !== '') { ?>
            <p class="st-adm-sub">Applications close on <b><?php echo $e(date('j F Y', strtotime($adm->closes()))); ?></b>.</p>
        <?php } ?>
    </div>
    <div class="st-adm-actions">
        <?php if ($editing) { ?>
            <a href="<?php echo site_url('welcome/online_admission_review/' . $reference_no); ?>" class="st-btn st-btn-ghost">Back to my application</a>
        <?php } else { ?>
        <a href="#checkOnlineAdmissionStatus" class="st-btn st-btn-ghost" onclick="openStatusFormmodal();" data-toggle="modal" data-target="#checkOnlineAdmissionStatus"><?php echo $this->lang->line('check_your_form_status'); ?></a>
        <?php if ($state['open'] && !empty($online_admission_application_form)) { ?>
            <a href="<?php echo base_url(); ?>welcome/download/<?php echo (int) $sch_setting->id; ?>" class="st-btn st-btn-ghost"><?php echo $this->lang->line('download_application_form'); ?></a>
        <?php } ?>
        <?php } ?>
    </div>
</div>

<?php if (!$state['open']) { ?>
    <div class="st-adm-closed" role="status">
        <h3>Admission is closed</h3>
        <p><?php echo nl2br($e($state['message'])); ?></p>
        <p class="st-adm-muted">Already applied? Use <b><?php echo $this->lang->line('check_your_form_status'); ?></b> above with your reference number.</p>
    </div>
<?php } else {

    $selected = function ($a, $b) { return (string) $a === (string) $b ? ' selected' : ''; };
    $checked = function ($a, $b) { return (string) $a === (string) $b ? ' checked' : ''; };
    $wide = array('textarea', 'guardian_is', 'rte');

    // One field: label, control, help and error.
    $field = function ($key) use ($adm, $e, $selected, $checked, $wide, $classlist, $genderList, $categorylist, $houses, $bloodgroup, $editing, $edit_values) {
        $f = Admission_form::fields()[$key];
        $name = $f['post'];
        $label = $e($adm->label($key));
        $req = $adm->required($key) ? ' <small class="req">*</small>' : '';
        $value = set_value($name, $editing && isset($edit_values[$name]) ? (string) $edit_values[$name] : '');
        $id = $name === 'file' ? 'student_photo' : $name;
        $help = $adm->help($key);
        $cls = 'st-adm-field' . (in_array($f['type'], $wide, true) ? ' st-adm-wide' : '');
        if ($key === 'section') {
            $cls .= ' st-adm-section displaynone';
            $req = ' <small class="st-adm-muted">(optional)</small>';
        }
        ob_start(); ?>
        <div class="<?php echo $cls; ?>" data-field="<?php echo $e($key); ?>">
            <?php if ($f['type'] === 'guardian_is' || $f['type'] === 'rte') { ?>
                <span class="st-adm-label"><?php echo $label . $req; ?></span>
                <div class="st-adm-choices">
                    <?php if ($f['type'] === 'guardian_is') {
                        foreach (array('father', 'mother', 'other') as $v) { ?>
                            <label><input type="radio" name="guardian_is" value="<?php echo $v; ?>"<?php echo $checked($value, $v); ?>> <?php echo $this->lang->line($v); ?></label>
                        <?php }
                    } else {
                        $value = $value === '' ? 'No' : $value;
                        foreach (array('Yes' => 'yes', 'No' => 'no') as $v => $l) { ?>
                            <label><input type="radio" name="rte" value="<?php echo $v; ?>"<?php echo $checked($value, $v); ?>> <?php echo $this->lang->line($l); ?></label>
                        <?php }
                    } ?>
                </div>
            <?php } else { ?>
                <label for="<?php echo $e($id); ?>"><?php echo $label . $req; ?></label>
                <?php switch ($f['type']) {
                    case 'class': ?>
                        <select id="class_id" name="class_id" class="form-control">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($classlist as $class) { ?>
                                <option value="<?php echo (int) $class['id']; ?>"<?php echo $selected($value, $class['id']); ?>><?php echo $e($class['class']); ?><?php if (isset($class['places_left']) && $class['places_left'] !== null) { echo ' (' . (int) $class['places_left'] . ' place' . ($class['places_left'] == 1 ? '' : 's') . ' left)'; } ?></option>
                            <?php } ?>
                        </select>
                        <?php break;
                    case 'section': ?>
                        <select id="section_id" name="section_id" class="form-control"><option value=""><?php echo $this->lang->line('select'); ?></option></select>
                        <?php break;
                    case 'gender': ?>
                        <select id="gender" name="gender" class="form-control">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($genderList as $k => $v) { ?><option value="<?php echo $e($k); ?>"<?php echo $selected($value, $k); ?>><?php echo $e($v); ?></option><?php } ?>
                        </select>
                        <?php break;
                    case 'category': ?>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($categorylist as $c) { ?><option value="<?php echo (int) $c['id']; ?>"<?php echo $selected($value, $c['id']); ?>><?php echo $e($c['category']); ?></option><?php } ?>
                        </select>
                        <?php break;
                    case 'house': ?>
                        <select id="house" name="house" class="form-control">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($houses as $h) { ?><option value="<?php echo (int) $h['id']; ?>"<?php echo $selected($value, $h['id']); ?>><?php echo $e($h['house_name']); ?></option><?php } ?>
                        </select>
                        <?php break;
                    case 'blood_group': ?>
                        <select id="blood_group" name="blood_group" class="form-control">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($bloodgroup as $b) { ?><option value="<?php echo $e($b); ?>"<?php echo $selected($value, $b); ?>><?php echo $e($b); ?></option><?php } ?>
                        </select>
                        <?php break;
                    case 'date': ?>
                        <input type="text" id="<?php echo $e($id); ?>" name="<?php echo $e($name); ?>" class="form-control date2" value="<?php echo $e($value); ?>" readonly autocomplete="off">
                        <?php break;
                    case 'textarea': ?>
                        <?php if ($key === 'current_address' && $adm->shown('guardian_address')) { ?>
                            <label class="st-adm-check"><input type="checkbox" id="autofill_current_address" onclick="return auto_fill_guardian_address();"> <?php echo $this->lang->line('if_guardian_address_is_current_address'); ?></label>
                        <?php } elseif ($key === 'permanent_address' && $adm->shown('current_address')) { ?>
                            <label class="st-adm-check"><input type="checkbox" id="autofill_address" onclick="return auto_fill_address();"> <?php echo $this->lang->line('if_permanent_address_is_current_address'); ?></label>
                        <?php } ?>
                        <textarea id="<?php echo $e($id); ?>" name="<?php echo $e($name); ?>" class="form-control" rows="2" autocomplete="off"><?php echo $e($value); ?></textarea>
                        <?php break;
                    case 'file': ?>
                        <input type="file" id="<?php echo $e($id); ?>" name="<?php echo $e($name); ?>" class="form-control"<?php echo $key === 'upload_documents' ? '' : ' accept="image/*"'; ?>>
                        <?php if ($key === 'upload_documents') { ?><small class="st-adm-help"><?php echo $this->lang->line('if_upload_multiple_document'); ?></small><?php } ?>
                        <?php if ($editing) { ?><small class="st-adm-help">Leave empty to keep the file you sent.</small><?php } ?>
                        <?php break;
                    default: ?>
                        <input type="<?php echo $f['type'] === 'email' ? 'email' : 'text'; ?>" id="<?php echo $e($id); ?>" name="<?php echo $e($name); ?>" class="form-control" value="<?php echo $e($value); ?>" autocomplete="off">
                <?php } ?>
            <?php } ?>
            <?php if ($help !== '') { ?><small class="st-adm-help"><?php echo $e($help); ?></small><?php } ?>
            <?php $err = form_error($name); if ($err) { ?><span class="text-danger"><?php echo strip_tags($err); ?></span><?php } ?>
        </div>
        <?php return ob_get_clean();
    };
    $questions = display_onlineadmission_custom_fields('students', $editing ? $id : false);
    ?>
    <form id="form1" class="st-adm-form onlineform" action="<?php echo $editing ? site_url('welcome/editonlineadmission/' . $reference_no) : current_url(); ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data" novalidate>
        <?php if (validation_errors()) { ?>
            <div class="alert alert-danger" role="alert">Please check the fields marked below.</div>
        <?php } ?>

        <?php if ($editing) { ?><input type="hidden" name="admission_id" value="<?php echo (int) $id; ?>"><?php } ?>
        <?php if (!$editing && !empty($online_admission_instruction) && trim(strip_tags((string) $online_admission_instruction)) !== '') { ?>
            <section class="st-adm-card st-adm-intro">
                <h3><?php echo $this->lang->line('instructions'); ?></h3>
                <div class="st-prose"><?php echo $online_admission_instruction; ?></div>
            </section>
        <?php } ?>

        <?php $n = 0; foreach (Admission_form::SECTIONS as $section => $heading) {
            $keys = $adm->section_fields($section);
            if (!$keys) { continue; }
            $n++; ?>
            <section class="st-adm-card" data-section="<?php echo $section; ?>">
                <h3><span class="st-adm-step"><?php echo $n; ?></span><?php echo $this->lang->line($heading); ?></h3>
                <div class="st-adm-grid">
                    <?php foreach ($keys as $key) { echo $field($key); } ?>
                </div>
            </section>
            <?php if ($section === 'student' && trim($questions) !== '') { $n++; ?>
                <section class="st-adm-card" data-section="questions">
                    <h3><span class="st-adm-step"><?php echo $n; ?></span><?php echo $e($adm->questions_title()); ?></h3>
                    <div class="row st-adm-questions"><?php echo $questions; ?></div>
                </section>
            <?php } ?>
        <?php } ?>

        <div class="st-adm-submit">
            <?php if ($is_captcha) { ?>
                <div class="st-adm-captcha">
                    <span id="captcha_image"><?php echo $this->captchalib->generate_captcha()['image']; ?></span>
                    <button type="button" class="st-adm-icon" title="Refresh captcha" aria-label="Refresh captcha" onclick="refreshCaptcha()"><span class="fa fa-refresh"></span></button>
                    <input type="text" name="captcha" placeholder="<?php echo $this->lang->line('captcha'); ?>" class="form-control" id="captcha" autocomplete="off">
                    <?php $err = form_error('captcha'); if ($err) { ?><span class="text-danger"><?php echo strip_tags($err); ?></span><?php } ?>
                </div>
            <?php } ?>
            <?php $has_terms = !$editing && trim(strip_tags((string) (isset($online_admission_conditions) ? $online_admission_conditions : ''))) !== '';
            $fee = !empty($admission_fee) ? (float) $admission_fee : 0; ?>
            <div class="st-adm-final">
                <?php if ($has_terms) { ?>
                    <label class="st-adm-terms"><input type="checkbox" name="checkterm" value="1"<?php echo set_checkbox('checkterm', '1'); ?>>
                        <span>I agree to the <a href="#admissionTerms" data-toggle="modal" data-target="#admissionTerms">terms and conditions</a></span></label>
                    <?php $err = form_error('checkterm'); if ($err) { ?><span class="text-danger"><?php echo strip_tags($err); ?></span><?php } ?>
                <?php } ?>
                <button type="submit" class="onlineformbtn st-adm-go"><?php echo $editing ? 'Save changes' : ($fee > 0 ? 'Continue to payment' : 'Submit application'); ?></button>
            </div>
        </div>
        <p class="st-adm-muted st-adm-note">Fields marked <span class="req">*</span> are required.<?php if (!$editing && $fee > 0) { ?> Your application is sent once the form fee of <?php echo $e($this->customlib->getSchoolCurrencyFormat() . amountFormat($fee)); ?> is paid.<?php } ?></p>
        <?php if ($has_terms) { ?>
            <div id="admissionTerms" class="modal fade" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-lg"><div class="modal-content">
                    <div class="modal-header modal-header-small"><button type="button" class="close closebtnmodal" data-dismiss="modal">&times;</button><h4><?php echo $this->lang->line('online_admission_terms_conditions'); ?></h4></div>
                    <div class="modal-body st-prose"><?php echo $online_admission_conditions; ?></div>
                    <div class="modal-footer"><button type="button" class="modalclosebtn btn mdbtn" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button></div>
                </div></div>
            </div>
        <?php } ?>
    </form>
<?php } ?>

<div id="checkOnlineAdmissionStatus" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-small">
                <button type="button" class="close closebtnmodal" data-dismiss="modal">&times;</button>
                <h4><?php echo $this->lang->line('check_your_form_status'); ?></h4>
            </div>
            <form action="<?php echo base_url() . 'welcome/checkadmissionstatus'; ?>" method="post" class="onlineform" id="checkstatusform">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="refno"><?php echo $this->lang->line('enter_your_reference_number'); ?></label><small class="req"> *</small>
                        <input type="text" class="form-control" name="refno" id="refno" autocomplete="off">
                        <span class="text-danger" id="error_status_refno"></span>
                    </div>
                    <div class="form-group mb10">
                        <label for="student_dob"><?php echo $this->lang->line('select_your_date_of_birth'); ?></label><small class="req"> *</small>
                        <input type="text" class="form-control date2" name="student_dob" id="student_dob" autocomplete="off" readonly>
                        <span class="text-danger" id="error_status_dob"></span>
                    </div>
                    <span class="text-danger" id="invaliderror"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modalclosebtn btn mdbtn" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                    <button type="submit" class="onlineformbtn mdbtn"><?php echo $this->lang->line('submit'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var date_format = '<?php echo strtr($this->customlib->getSchoolDateFormat(), array('d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy')); ?>';
var datetime_format = '<?php echo strtr($this->customlib->getSchoolDateFormat(), array('d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY')); ?>';
$(function () {
    $('.date2, .date').datepicker({ autoclose: true, format: date_format, todayHighlight: true });
    if ($.fn.datetimepicker) { $('.datetime').datetimepicker({ format: datetime_format + ' hh:mm a', locale: 'en' }); }

    // Sections are optional: the choice only appears when the class has more than one.
    function loadSections(classId, chosen, chosenSection) {
        var $s = $('#section_id'), $box = $s.closest('.st-adm-section');
        $s.find('option:not(:first)').remove();
        $box.addClass('displaynone');
        if (!classId) { return; }
        $.post(base_url + 'welcome/getSections', { class_id: classId }, function (rows) {
            $.each(rows || [], function (i, r) {
                $s.append($('<option>').val(r.id).text(r.section).prop('selected', String(r.id) === String(chosen) || (!Number(chosen) && String(r.section_id) === String(chosenSection))));
            });
            $box.toggleClass('displaynone', (rows || []).length < 2);
        }, 'json');
    }
    loadSections($('#class_id').val(), '<?php echo (int) set_value('section_id', 0); ?>', '<?php echo $editing ? (int) $class_section_id : 0; ?>');
    $(document).on('change', '#class_id', function () { loadSections($(this).val(), 0); });

    $('input:radio[name="guardian_is"]').on('change', function () {
        var v = $(this).val(), from = v === 'father' || v === 'mother' ? v : '';
        $('#guardian_name').val(from ? $('#' + from + '_name').val() : '');
        $('#guardian_phone').val(from ? $('#' + from + '_phone').val() : '');
        $('#guardian_occupation').val(from ? $('#' + from + '_occupation').val() : '');
        $('#guardian_relation').val(from === 'father' ? <?php echo json_encode((string) $this->lang->line('father')); ?> : from === 'mother' ? <?php echo json_encode((string) $this->lang->line('mother')); ?> : '');
    });

    $('#checkOnlineAdmissionStatus').modal({ backdrop: 'static', keyboard: false, show: false });
    $(document).on('submit', '#checkstatusform', function (ev) {
        ev.preventDefault();
        var form = $(this);
        $.ajax({ url: form.attr('action'), type: 'POST', dataType: 'JSON', data: form.serializeArray(), success: function (r) {
            if (r.status == 0) {
                $.each(r.error, function (k, v) { $('#error_status_' + k).html(v); });
            } else if (r.status == 2) {
                $('#error_status_dob, #error_status_refno').html('');
                $('#invaliderror').html(r.error);
            } else {
                window.location.href = '<?php echo base_url() . 'welcome/online_admission_review/'; ?>' + r.refno;
            }
        } });
    });
});
function auto_fill_guardian_address() { if ($('#autofill_current_address').is(':checked')) { $('#current_address').val($('#guardian_address').val()); } }
function auto_fill_address() { if ($('#autofill_address').is(':checked')) { $('#permanent_address').val($('#current_address').val()); } }
function openStatusFormmodal() { $('#error_status_dob, #error_status_refno, #invaliderror').html(''); $('#student_dob, #refno').val(''); }
function refreshCaptcha() { $.post('<?php echo base_url('site/refreshCaptcha'); ?>', {}, function (c) { $('#captcha_image').html(c); }); }
</script>
