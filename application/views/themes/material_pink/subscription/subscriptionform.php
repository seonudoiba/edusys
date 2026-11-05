<link href="<?php echo base_url(); ?>backend/multiselect/css/jquery.multiselect.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>backend/multiselect/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>backend/multiselect/js/jquery.multiselect.js"></script>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <form id="form1"
                        action="<?php echo site_url('subscription/save/' . (isset($school['id']) ? $school['id'] : '')) ?>"
                        id="employeeform" name="employeeform" method="post" accept-charset="utf-8"
                        enctype="multipart/form-data">
                        <div class="">
                            <div class="bozero">
                                <h4 class="pagetitleh-whitebg">
                                    <?php echo $this->lang->line('school_register'); ?>
                                </h4>
                                <div class="around10">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <input type="hidden" name="plan_id"
                                        value="<?php echo set_value('plan_id', isset($plan_id) ? $plan_id : ''); ?>"
                                        id="plan_id">

                                    <div class="row">
                                        <div class="col-md-6">
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
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
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
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
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
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
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
                                    
                                    <?php
                                        if(isset($amount) && $amount > 0){ ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">
                                                            <?php echo $this->lang->line('payment_method'); ?>
                                                        </label><small class="req"> *</small>
                                                        <select id="payment_method" name="payment_method" class="form-control">
                                                            <option value="">Select Payment Method</option>
                                                            <option value="paystack" <?php echo set_select('payment_method', 'paystack'); ?>>
                                                                <?php echo $this->lang->line('paystack_payment'); ?>
                                                            </option>
                                                            <option value="bank_transfer" <?php echo set_select('payment_method', 'bank_transfer'); ?>>
                                                                Bank transfer (upload receipt)
                                                            </option>
                                                        </select>
                                                        <span class="text-danger">
                                                            <?php echo form_error('payment_method'); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                    ?>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('amount'); ?>
                                                </label><small class="req"> *</small>
                                                <input id="amount" name="amount" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('amount', isset($amount) ? $amount : '') ?>"
                                                    readonly />
                                                <span class="text-danger">
                                                    <?php echo form_error('amount'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('days'); ?>
                                                </label><small class="req"> *</small>
                                                <input id="days" name="days" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('days', isset($days) ? $days : '') ?>"
                                                    readonly />
                                                <span class="text-danger">
                                                    <?php echo form_error('days'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-left" id="addloader"
                                style="margin-bottom:15px">
                                <?php echo $this->lang->line('saveandpayment'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>
</section>
</div>