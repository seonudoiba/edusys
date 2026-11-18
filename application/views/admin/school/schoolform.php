<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<link href="<?php echo base_url(); ?>backend/multiselect/css/jquery.multiselect.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>backend/multiselect/js/jquery.multiselect.js"></script>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <form id="form1"
                        action="<?php echo site_url('admin/schools/save/' . (isset($school['id']) ? $school['id'] : '')) ?>"
                        id="employeeform" name="employeeform" method="post" accept-charset="utf-8"
                        enctype="multipart/form-data">
                        <div class="">
                            <div class="bozero">
                                <h4 class="pagetitleh-whitebg">
                                    <?php echo $this->lang->line('school_register'); ?>
                                </h4>
                                <div class="around10">
                                    <?php if ($this->session->flashdata('msg')) {
                                        ?>
                                        <?php
                                        echo $this->session->flashdata('msg');
                                        $this->session->unset_userdata('msg');
                                        ?>
                                    <?php } ?>

                                    <?php //if (isset($error_message)) { ?>
                                    <!--<div class="alert alert-warning"><?php //echo $error_message; ?></div> -->
                                    <?php //} ?>
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <!-- <input type="hidden" name="sibling_name"
                                        value="<?php // echo set_value('sibling_name'); ?>" id="sibling_name_next">
                                    <input type="hidden" name="sibling_id"
                                        value="<?php //echo set_value('sibling_id', 0); ?>" id="sibling_id"> -->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('school_name'); ?>
                                                </label><small class="req"> *</small>
                                                <input id="school_name" name="school_name" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('school_name', isset($school['school_name']) ? $school['school_name'] : ''); ?>" />
                                                <span class="text-danger">
                                                    <?php echo form_error('school_name'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('school_code'); ?>
                                                </label>
                                                <input id="school_code" name="school_code" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('school_code', isset($school['school_code']) ? $school['school_code'] : ''); ?>" />
                                                <span class="text-danger">
                                                    <?php echo form_error('school_code'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="subdomain">Subdomain</label>
                                                <div class="input-group">
                                                    <input id="subdomain" name="subdomain" placeholder="e.g. greenfield" type="text"
                                                        class="form-control" maxlength="63"
                                                        value="<?php echo set_value('subdomain', isset($school['subdomain']) ? $school['subdomain'] : ''); ?>" />
                                                    <span class="input-group-addon">.<?php echo html_escape($this->config->item('tenancy_base_domain')); ?></span>
                                                </div>
                                                <small class="text-muted">Required before the school can be set up. Lowercase letters, digits and hyphens.</small>
                                                <span class="text-danger">
                                                    <?php echo form_error('subdomain'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('school_type'); ?>
                                                </label>
                                                <select id="school_type" name="school_type" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <option value="1" <?php echo set_value('school_type', (isset($school['school_type']) && $school['school_type'] == '1')) ? 'selected' : ''; ?>><?php echo $this->lang->line('public'); ?>
                                                    </option>
                                                    <option value="2" <?php echo set_value('school_type', (isset($school['school_type']) && $school['school_type'] == '2')) ? 'selected' : ''; ?>>
                                                        <?php echo $this->lang->line('private'); ?>
                                                    </option>
                                                    <option value="3" <?php echo set_value('school_type', (isset($school['school_type']) && $school['school_type'] == '3')) ? 'selected' : ''; ?>><?php echo $this->lang->line('other'); ?>
                                                    </option>
                                                </select>
                                                <span class="text-danger">
                                                    <?php echo form_error('school_type'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('school_category'); ?>
                                                </label>
                                                <select id="school_category" name="school_category"
                                                    class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <option value="1" <?php echo set_value('school_category', (isset($school['school_category']) && $school['school_category'] == '1')) ? 'selected' : ''; ?>>
                                                        <?php echo $this->lang->line('primary'); ?>
                                                    </option>
                                                    <option value="2" <?php echo set_value('school_category', (isset($school['school_category']) && $school['school_category'] == '2')) ? 'selected' : ''; ?>>
                                                        <?php echo $this->lang->line('secondary'); ?>
                                                    </option>
                                                    <option value="3" <?php echo set_value('school_category', (isset($school['school_category']) && $school['school_category'] == '3')) ? 'selected' : ''; ?>>
                                                        <?php echo $this->lang->line('high-secondary'); ?>
                                                    </option>
                                                </select>
                                                <span class="text-danger">
                                                    <?php echo form_error('school_category'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('school_address'); ?>
                                                </label><small class="req"> *</small>
                                                <textarea id="school_address" name="school_address"
                                                    class="form-control"><?php echo set_value('school_address', isset($school['school_address']) ? $school['school_address'] : ''); ?></textarea>

                                                <span class="text-danger">
                                                    <?php echo form_error('school_address'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('phone_number'); ?>
                                                </label><small class="req"> *</small>
                                                <input id="phone_number" name="phone_number" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('phone_number', isset($school['phone_number']) ? $school['phone_number'] : ''); ?>" />
                                                <span class="text-danger">
                                                    <?php echo form_error('phone_number'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('email_address'); ?>
                                                </label><small class="req"> *</small>
                                                <input id="email_address" name="email_address" placeholder=""
                                                    type="text" class="form-control"
                                                    value="<?php echo set_value('email_address', isset($school['email_address']) ? $school['email_address'] : ''); ?>" />
                                                <span class="text-danger">
                                                    <?php echo form_error('email_address'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('website'); ?>
                                                </label>
                                                <input id="website" name="website" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('website', isset($school['website']) ? $school['website'] : ''); ?>" />
                                                <span class="text-danger">
                                                    <?php echo form_error('website'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('principal_name'); ?>
                                                </label>
                                                <input id="principal_name" name="principal_name" placeholder=""
                                                    type="text" class="form-control"
                                                    value="<?php echo set_value('principal_name', isset($school['principal_name']) ? $school['principal_name'] : ''); ?>" />
                                                <span class="text-danger">
                                                    <?php echo form_error('principal_name'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('owner_name'); ?>
                                                </label><small class="req"> *</small>
                                                <input id="owner_name" name="owner_name" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('owner_name', isset($school['owner_name']) ? $school['owner_name'] : ''); ?>" />
                                                <span class="text-danger">
                                                    <?php echo form_error('owner_name'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!isset($school['id']) && $this->rbac->hasPrivilege('school_billing', 'can_add')) { ?>
                            <div class="around10" style="border-top:1px solid #eee;padding-top:12px">
                                <div class="checkbox" style="margin-top:0">
                                    <label style="font-size:15px;font-weight:600">
                                        <input type="checkbox" name="bill_on" id="bill_on" value="1" <?php echo set_checkbox('bill_on', '1'); ?>>
                                        Bill this school now
                                    </label>
                                    <span class="text-muted" style="margin-left:6px">creates an invoice (unpaid until the school pays) and can email it to the owner</span>
                                </div>
                                <?php if (!empty($bill_error)) { ?><div class="alert alert-danger" style="margin:8px 0"><?php echo html_escape($bill_error); ?></div><?php } ?>
                                <div id="bill_box" style="<?php echo set_value('bill_on') ? '' : 'display:none'; ?>;margin-top:8px">
                                    <?php $this->load->view('admin/school/_billing_fields', array(
                                        'bands' => isset($bands) ? $bands : array(),
                                        'preview_url' => site_url('admin/schools/bill_preview/0'),
                                        'owner_email' => '',
                                        'currency_symbol' => $currency_symbol,
                                    )); ?>
                                </div>
                            </div>
                            <script>
                                document.getElementById('bill_on').addEventListener('change', function () {
                                    document.getElementById('bill_box').style.display = this.checked ? '' : 'none';
                                });
                            </script>
                        <?php } ?>

                        <div class="mainstudent">
                            <div id="fade"></div>
                            <div id="modal">

                                <i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span>
                                <img id="loader" src="<?php //echo base_url('backend/images/chatloading.gif'); ?>">
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right" id="addloader">
                                <?php echo $this->lang->line('save'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>
</section>
</div>