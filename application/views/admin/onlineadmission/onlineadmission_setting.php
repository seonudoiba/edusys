<?php
/*
 * Online admission settings: General (open or closed, dates, fee, instructions), Form fields
 * (show, require, rename, explain and order the built-in fields), Questions (the school's own) and
 * Classes (which take applications and how many places). libraries/Admission_form.php
 */
$admin_session   = $this->session->userdata('admin');
$currency_symbol = $admin_session['currency_symbol'];
$e = function ($v) { return html_escape((string) $v); };
$state = $adm->state();
$config = $adm->config();
$on = function ($name) use ($inserted_fields) {
    foreach ($inserted_fields as $row) {
        if ($row->name === $name) {
            return (bool) $row->status;
        }
    }
    return false;
};
$sch_setting_array = json_decode(json_encode($sch_setting_detail), true);
$all_fields = Admission_form::fields();
$section_names = array('student' => 'Student', 'parents' => 'Parents', 'guardian' => 'Guardian', 'address' => 'Address', 'other' => 'Other details', 'documents' => 'Documents');
$types = array('input' => 'Short answer', 'textarea' => 'Paragraph', 'number' => 'Number', 'date_picker' => 'Date', 'select' => 'Drop-down (one choice)', 'multiselect' => 'Drop-down (several choices)', 'checkbox' => 'Tick boxes');
$reasons = array('off' => 'switched off', 'not_yet' => 'not open yet', 'ended' => 'the closing date has passed', 'full' => 'no class has places left');
$class_settings = $adm->class_settings($classes);
$taken = $adm->applications_by_class();
?>
<script src="<?php echo base_url(); ?>backend/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>backend/js/ckeditor_config.js"></script>
<style>
.oa-state { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; padding: 12px 16px; margin: 0 0 16px; border-radius: 6px; border: 1px solid #d6e9c6; background: #f3faf0; }
.oa-state.is-closed { border-color: #ebccd1; background: #fdf3f3; }
.oa-state b { font-size: 15px; }
.oa-state .oa-dot { width: 10px; height: 10px; border-radius: 50%; background: #3c9a3c; }
.oa-state.is-closed .oa-dot { background: #c9302c; }
.oa-state .btn { margin-left: auto; }
.oa-table td, .oa-table th { vertical-align: middle !important; }
.oa-table .form-control { height: 30px; padding: 4px 8px; }
.oa-handle { cursor: move; color: #999; width: 24px; text-align: center; }
.oa-section-title { margin: 22px 0 8px; font-size: 15px; font-weight: 600; }
.oa-muted { color: #777; }
.oa-locked { color: #777; font-size: 12px; }
.oa-sortable tr.ui-sortable-helper { display: table; background: #fff; box-shadow: 0 4px 14px rgba(0,0,0,.15); }
.oa-qform { padding: 14px; border: 1px solid #e3e6ea; border-radius: 6px; background: #fafbfc; }
.oa-help { color: #777; margin: 4px 0 14px; }
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom theme-shadow">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('online_admission'); ?></h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo site_url('admin/onlinestudent'); ?>" class="btn btn-default btn-sm"><i class="fa fa-list"></i> Applications</a>
                            <a href="<?php echo base_url('online_admission'); ?>" target="_blank" rel="noopener" class="btn btn-default btn-sm"><i class="fa fa-external-link"></i> View the form</a>
                        </div>
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_general" data-toggle="tab">General</a></li>
                        <li><a href="#tab_fields" data-toggle="tab">Form fields</a></li>
                        <li><a href="#tab_questions" data-toggle="tab">Questions</a></li>
                        <li><a href="#tab_classes" data-toggle="tab">Classes and places</a></li>
                    </ul>
                    <div class="tab-content">

                        <!-- ============================== General -->
                        <div class="tab-pane active" id="tab_general">
                            <?php if ($this->session->flashdata('msg')) { echo $this->session->flashdata('msg'); $this->session->unset_userdata('msg'); } ?>
                            <div class="oa-state<?php echo $state['open'] ? '' : ' is-closed'; ?>" id="oa_state">
                                <span class="oa-dot"></span>
                                <?php if ($state['open']) { ?>
                                    <b>Admission is open.</b>
                                    <span class="oa-muted"><?php echo $adm->closes() !== '' ? 'Closes on ' . $e(date('j F Y', strtotime($adm->closes()))) . '.' : 'No closing date.'; ?></span>
                                <?php } else { ?>
                                    <b>Admission is closed</b>
                                    <span class="oa-muted">(<?php echo $e($reasons[$state['reason']]); ?>). Visitors see: “<?php echo $e($state['message']); ?>”</span>
                                <?php } ?>
                            </div>
                            <form action="<?php echo site_url('admin/onlineadmission/admissionsetting'); ?>" method="post" id="form1" accept-charset="utf-8" enctype="multipart/form-data">
                                <div class="row">
                                    <label class="col-lg-3 col-md-4 col-sm-5 control-label" for="online_admission">Take applications online</label>
                                    <div class="col-lg-9 col-md-8 col-sm-7">
                                        <div class="form-group">
                                            <div class="material-switch">
                                                <input id="online_admission" name="online_admission" type="checkbox" class="enableexam" value="1" <?php echo set_checkbox('online_admission', '1', (set_value('online_admission', $result->online_admission) == 1) ? true : false); ?> />
                                                <label for="online_admission" class="label-success"></label>
                                            </div>
                                            <p class="oa-help">Switch off to close admission straight away. Leave on and set dates to open and close it automatically.</p>
                                        </div>
                                    </div>
                                    <div id="settingclm">
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="online_admission_opens">Opens on</label>
                                                        <input type="date" class="form-control" id="online_admission_opens" name="online_admission_opens" value="<?php echo $e($adm->opens()); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="online_admission_closes">Closes after</label>
                                                        <input type="date" class="form-control" id="online_admission_closes" name="online_admission_closes" value="<?php echo $e($adm->closes()); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="oa-help" style="margin-top:28px">Both are optional. The form takes applications from the opening day to the end of the closing day.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="online_admission_closed_message">Message when admission is closed</label>
                                            <textarea class="form-control" id="online_admission_closed_message" name="online_admission_closed_message" rows="2" maxlength="1000" placeholder="For example: Admission for the 2027/2028 session opens in March. Call the school office for enquiries."><?php echo $e($config['closed_message']); ?></textarea>
                                            <p class="oa-help">Optional. Without it, visitors see a short notice with the opening or closing date.</p>
                                        </div>
                                    </div>
                                    <div id="settingclm2">
                                        <div id="paymentclm">
                                            <label class="col-lg-3 col-md-4 col-sm-5" for="chk_yes"><?php echo $this->lang->line('online_admission_payment_option'); ?></label>
                                            <div class="col-lg-9 col-md-8 col-sm-7">
                                                <div class="form-group">
                                                    <div class="material-switch">
                                                        <input id="chk_yes" name="online_admission_payment" type="checkbox" class="amountenable" value="yes" <?php echo set_checkbox('online_admission_payment', 'yes', (set_value('online_admission_payment', $result->online_admission_payment) == 'yes') ? true : false); ?> />
                                                        <label for="chk_yes" class="label-success"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="amountclm">
                                            <label class="col-lg-3 col-md-4 col-sm-5 control-label" for="online_admission_amount"><?php echo $this->lang->line('online_admission_form_fees') . ' (' . $currency_symbol . ')'; ?></label>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <input type="text" name="online_admission_amount" id="online_admission_amount" class="form-control" value="<?php echo convertBaseAmountCurrencyFormat($result->online_admission_amount); ?>">
                                                    <span class="text-danger"><?php echo form_error('online_admission_amount'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-md-4 col-sm-5"><?php echo $this->lang->line('upload_admission_application_form'); ?></label>
                                                <div class="col-lg-6 col-md-8 col-sm-7">
                                                    <div class="row">
                                                        <div class="col-md-8 col-xs-10">
                                                            <div class="form-group">
                                                                <input type="file" data-height="26" name="file" class="form-control filestyle">
                                                                <span class="text-danger"><?php echo form_error('file'); ?></span>
                                                            </div>
                                                        </div>
                                                        <?php if (!empty($sch_setting_detail->online_admission_application_form)) { ?>
                                                        <div class="col-md-1 col-xs-2">
                                                            <a href="<?php echo base_url(); ?>admin/onlineadmission/download/<?php echo (int) $sch_setting_detail->id; ?>" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('download'); ?>" class="btn btn-default btn-xs"><i class="fa fa-download"></i></a>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <label class="control-label"><?php echo $this->lang->line('online_admission_instructions'); ?></label>
                                            <div class="form-group">
                                                <textarea name="online_admission_instruction" id="online_admission_instruction" class="form-control ckeditor"><?php echo $result->online_admission_instruction; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <label class="control-label"><?php echo $this->lang->line('terms_conditions'); ?></label>
                                            <div class="form-group">
                                                <textarea name="online_admission_conditions" id="online_admission_conditions" class="form-control ckeditor"><?php echo $result->online_admission_conditions; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="box-footer pull-right">
                                        <button type="submit" name="submitbtn" id="submitbtn" value="submitbtn" class="btn btn-primary"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- ============================== Form fields -->
                        <div class="tab-pane" id="tab_fields">
                            <p class="oa-help">Switch a field on to add it to the form (it saves straight away). Tick <b>Required</b> to make applicants fill it,
                                change its label or add a hint, and drag rows to change the order. Class, first name, gender and date of birth are always asked.</p>
                            <form id="fields_form">
                                <?php foreach (Admission_form::SECTIONS as $section => $lang) { ?>
                                    <div class="oa-section-title"><?php echo $e($section_names[$section]); ?></div>
                                    <div class="table-responsive">
                                    <table class="table table-bordered oa-table">
                                        <thead><tr><th class="oa-handle"></th><th style="width:22%">Field</th><th style="width:90px">On form</th><th style="width:90px">Required</th><th>Label on the form</th><th>Hint for applicants</th></tr></thead>
                                        <tbody class="oa-sortable" data-section="<?php echo $section; ?>">
                                        <?php foreach ($adm->ordered($section) as $key) {
                                            $f = $all_fields[$key];
                                            // Fields switched off for the whole school (System Settings > Student fields) aren't offered.
                                            if (!$f['locked'] && array_key_exists($key, $sch_setting_array) && !$sch_setting_detail->$key) { continue; }
                                            $c = isset($config['fields'][$key]) ? $config['fields'][$key] : array('label' => '', 'help' => ''); ?>
                                            <tr data-key="<?php echo $e($key); ?>">
                                                <td class="oa-handle" title="Drag to reorder"><i class="fa fa-arrows-v"></i><input type="hidden" name="order[<?php echo $section; ?>][]" value="<?php echo $e($key); ?>"></td>
                                                <td><?php echo $e($adm->default_label($key)); ?></td>
                                                <td>
                                                    <?php if ($f['locked']) { ?><span class="oa-locked">Always</span><?php } else { ?>
                                                    <div class="material-switch">
                                                        <input id="field_<?php echo $e($key); ?>" name="<?php echo $e($key); ?>" type="checkbox" class="chk" value="1"<?php echo $on($key) ? ' checked' : ''; ?>>
                                                        <label for="field_<?php echo $e($key); ?>" class="label-success"></label>
                                                    </div>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($f['locked']) { ?><span class="oa-locked"><?php echo $f['required'] ? 'Always' : 'Optional'; ?></span><?php } else { ?>
                                                    <input type="checkbox" name="fields[<?php echo $e($key); ?>][required]" value="1" aria-label="Required"<?php echo $adm->required($key) ? ' checked' : ''; ?>>
                                                    <?php } ?>
                                                </td>
                                                <td><input type="text" class="form-control" name="fields[<?php echo $e($key); ?>][label]" maxlength="80" value="<?php echo $e($c['label']); ?>" placeholder="<?php echo $e($adm->default_label($key)); ?>" aria-label="Label"></td>
                                                <td><input type="text" class="form-control" name="fields[<?php echo $e($key); ?>][help]" maxlength="200" value="<?php echo $e($c['help']); ?>" aria-label="Hint"></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    </div>
                                <?php } ?>
                                <div class="text-right"><button type="submit" class="btn btn-primary" id="save_fields">Save form fields</button></div>
                            </form>
                        </div>

                        <!-- ============================== Questions -->
                        <div class="tab-pane" id="tab_questions">
                            <p class="oa-help">Ask anything the built-in fields don't cover. Answers are kept with the application and on the student's record once you enrol them.</p>
                            <form id="qtitle_form" class="form-inline" style="margin-bottom:14px">
                                <label for="questions_title">Heading on the form</label>
                                <input type="text" class="form-control" id="questions_title" name="questions_title" maxlength="80" value="<?php echo $e($config['questions_title']); ?>" placeholder="Additional information">
                                <button type="submit" class="btn btn-default">Save heading</button>
                            </form>
                            <div class="table-responsive">
                            <table class="table table-bordered oa-table" id="questions_table">
                                <thead><tr><th>Question</th><th>Answer</th><th style="width:90px">Required</th><th style="width:90px">On form</th><th style="width:80px"></th></tr></thead>
                                <tbody>
                                <?php if (!$questions) { ?><tr><td colspan="5" class="oa-muted">No questions yet. Add one below.</td></tr><?php } ?>
                                <?php foreach ($questions as $q) { ?>
                                    <tr>
                                        <td><?php echo $e($q['name']); ?><?php if ($q['field_values'] !== '' && $q['field_values'] !== null) { ?><br><small class="oa-muted"><?php echo $e(str_replace(',', ', ', $q['field_values'])); ?></small><?php } ?></td>
                                        <td><?php echo $e(isset($types[$q['type']]) ? $types[$q['type']] : $q['type']); ?></td>
                                        <td><?php echo $q['validation'] ? 'Yes' : 'No'; ?></td>
                                        <td>
                                            <div class="material-switch">
                                                <input id="field_q<?php echo (int) $q['id']; ?>" name="<?php echo $e($q['name']); ?>" type="checkbox" class="chk" value="1"<?php echo $on($q['name']) ? ' checked' : ''; ?>>
                                                <label for="field_q<?php echo (int) $q['id']; ?>" class="label-success"></label>
                                            </div>
                                        </td>
                                        <td><?php if (isset($types[$q['type']])) { ?><button type="button" class="btn btn-default btn-xs oa-edit-q" data-q="<?php echo $e(json_encode(array('id' => (int) $q['id'], 'name' => $q['name'], 'type' => $q['type'], 'options' => str_replace(',', "\n", (string) $q['field_values']), 'required' => (int) $q['validation']))); ?>"><i class="fa fa-pencil"></i> Edit</button><?php } ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            </div>
                            <form id="question_form" class="oa-qform">
                                <h4 style="margin-top:0" id="question_form_title">Add a question</h4>
                                <input type="hidden" name="id" value="0">
                                <div class="row">
                                    <div class="col-md-6"><div class="form-group">
                                        <label for="q_name">Question</label><small class="req"> *</small>
                                        <input type="text" class="form-control" id="q_name" name="name" maxlength="100" placeholder="For example: Does the child have any allergies?">
                                        <span class="text-danger" data-error="name"></span>
                                    </div></div>
                                    <div class="col-md-3"><div class="form-group">
                                        <label for="q_type">Answer</label>
                                        <select class="form-control" id="q_type" name="type">
                                            <?php foreach ($types as $k => $v) { ?><option value="<?php echo $k; ?>"><?php echo $e($v); ?></option><?php } ?>
                                        </select>
                                        <span class="text-danger" data-error="type"></span>
                                    </div></div>
                                    <div class="col-md-3"><div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="checkbox" style="margin-top:4px"><label><input type="checkbox" name="required" value="1"> Required</label></div>
                                    </div></div>
                                    <div class="col-md-12 oa-options" style="display:none"><div class="form-group">
                                        <label for="q_options">Choices, one per line</label>
                                        <textarea class="form-control" id="q_options" name="options" rows="3"></textarea>
                                        <span class="text-danger" data-error="options"></span>
                                    </div></div>
                                </div>
                                <button type="submit" class="btn btn-primary">Save question</button>
                                <button type="button" class="btn btn-link" id="question_cancel" style="display:none">Cancel</button>
                                <span class="oa-muted" style="margin-left:8px">To delete a question, use <a href="<?php echo site_url('admin/customfield'); ?>">Custom fields</a>.</span>
                            </form>
                        </div>

                        <!-- ============================== Classes -->
                        <div class="tab-pane" id="tab_classes">
                            <p class="oa-help">Choose the classes applicants can apply for and, if you like, how many places each has. When a class's places are taken it disappears from the form;
                                when every class is full, admission shows as closed.
                                <?php if ($adm->counted_since() !== '') { ?>Applications are counted from <b><?php echo $e(date('j F Y, g:ia', strtotime($adm->counted_since()))); ?></b>.<?php } ?></p>
                            <form id="classes_form">
                                <div class="table-responsive">
                                <table class="table table-bordered oa-table">
                                    <thead><tr><th>Class</th><th style="width:150px">Takes applications</th><th style="width:150px">Places</th><th style="width:130px">Applications</th><th style="width:110px">Places left</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($classes as $class) {
                                        $id = (int) $class['id'];
                                        $s = $class_settings[$id];
                                        $n = isset($taken[$id]) ? $taken[$id] : 0; ?>
                                        <tr>
                                            <td><?php echo $e($class['class']); ?></td>
                                            <td><input type="checkbox" name="classes[<?php echo $id; ?>][open]" value="1" aria-label="Takes applications"<?php echo $s['open'] ? ' checked' : ''; ?>></td>
                                            <td><input type="number" min="0" class="form-control" name="classes[<?php echo $id; ?>][places]" value="<?php echo $s['places'] === null ? '' : (int) $s['places']; ?>" placeholder="No limit" aria-label="Places"></td>
                                            <td><?php echo $n; ?></td>
                                            <td><?php echo $s['places'] === null ? '<span class="oa-muted">No limit</span>' : max(0, $s['places'] - $n); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                </div>
                                <div class="checkbox"><label><input type="checkbox" name="restart_count" value="1"> Start counting again from now (for a new admission round)</label></div>
                                <div class="text-right"><button type="submit" class="btn btn-primary">Save classes</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(".ckeditor").each(function (_, ckeditor) {
    CKEDITOR.env.isCompatible = true;
    CKEDITOR.replace(ckeditor, { toolbar: 'Ques', customConfig: '<?php echo base_url('backend/js/ckeditor_config.js'); ?>' });
});

jQuery(function ($) { // after the footer scripts (Bootstrap tabs)
    var url = '<?php echo site_url('admin/onlineadmission'); ?>/';

    // Keep the tab across saves and links.
    if (location.hash && $('a[href="' + location.hash + '"]').length) { $('a[href="' + location.hash + '"]').tab('show'); }
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (ev) { history.replaceState(null, '', $(ev.target).attr('href')); });

    function toggleFee() { $('#amountclm').toggle($('#chk_yes').prop('checked')); }
    toggleFee();
    $('#chk_yes').on('click', toggleFee);
    $('#form1').on('submit', function () { $('#submitbtn').button('loading'); });

    function post(action, data, $btn, done) {
        $btn && $btn.prop('disabled', true);
        $.post(url + action, data, function (r) {
            if (r.status == 1) { successMsg(r.msg); done && done(r); } else { errorMsg(r.msg || 'Something went wrong'); }
        }, 'json').fail(function () { errorMsg('Could not save. Please try again.'); }).always(function () { $btn && $btn.prop('disabled', false); });
    }

    // On-form switches save straight away (fields and questions).
    $(document).on('click', '.chk', function (ev) {
        var name = $(this).attr('name'), status = this.checked ? 1 : 0;
        $.post(url + 'changeformfieldsetting', { name: name, status: status }, function (r) {
            successMsg(r.msg);
            if (name === 'if_guardian_is') { $('#tab_fields tbody[data-section="guardian"] .chk').not('#field_if_guardian_is').prop('checked', !!status); }
        }, 'json');
    });

    $('.oa-sortable').sortable({ handle: '.oa-handle', axis: 'y', helper: function (e, tr) { var $o = tr.children(), $h = tr.clone(); $h.children().each(function (i) { $(this).width($o.eq(i).width()); }); return $h; } });
    $('#fields_form').on('submit', function (ev) { ev.preventDefault(); post('saveform', $(this).serialize(), $('#save_fields')); });
    $('#classes_form').on('submit', function (ev) { ev.preventDefault(); post('saveclasses', $(this).serialize(), $(this).find('[type=submit]'), function () { location.hash = '#tab_classes'; location.reload(); }); });
    $('#qtitle_form').on('submit', function (ev) { ev.preventDefault(); post('savequestionstitle', $(this).serialize(), $(this).find('[type=submit]')); });

    var $q = $('#question_form');
    function showOptions() { $q.find('.oa-options').toggle(/select|multiselect|checkbox/.test($('#q_type').val())); }
    $('#q_type').on('change', showOptions);
    function resetQuestion() {
        $q[0].reset(); $q.find('[name=id]').val(0); $q.find('[data-error]').html('');
        $('#question_form_title').text('Add a question'); $('#question_cancel').hide(); showOptions();
    }
    $(document).on('click', '.oa-edit-q', function () {
        var d = $(this).data('q');
        resetQuestion();
        $q.find('[name=id]').val(d.id); $q.find('[name=name]').val(d.name); $q.find('[name=type]').val(d.type);
        $q.find('[name=options]').val(d.options); $q.find('[name=required]').prop('checked', d.required == 1);
        $('#question_form_title').text('Change question'); $('#question_cancel').show(); showOptions();
        $('html, body').animate({ scrollTop: $q.offset().top - 80 }, 200); $('#q_name').focus();
    });
    $('#question_cancel').on('click', resetQuestion);
    $q.on('submit', function (ev) {
        ev.preventDefault();
        $q.find('[data-error]').html('');
        var $btn = $q.find('[type=submit]').prop('disabled', true);
        $.post(url + 'savequestion', $q.serialize(), function (r) {
            if (r.status == 1) { location.hash = '#tab_questions'; location.reload(); return; }
            $.each(r.error || {}, function (k, v) { $q.find('[data-error="' + k + '"]').text(v); });
        }, 'json').always(function () { $btn.prop('disabled', false); });
    });
});
</script>
