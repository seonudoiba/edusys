<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-10">
                <div class="nav-tabs-custom box box-primary theme-shadow">
                     <div class="box-header with-border">
                       <h3 class="box-title titlefix"><?php echo $this->lang->line('payment_methods'); ?></h3>
                    </div>
                    <ul class="nav nav-tabs nav-tabs2">
                        <li><a href="#tab_bank" data-toggle="tab"><i class="fa fa-university"></i> Bank transfer</a></li>
                        <li class="active"><a href="#tab_1" data-toggle="tab"><?php echo $this->lang->line('paypal'); ?></a></li>
                        <li><a href="#tab_2" data-toggle="tab"><?php echo $this->lang->line('stripe'); ?></a></li>
                        <li><a href="#tab_3" data-toggle="tab"><?php echo $this->lang->line('payu'); ?></a></li>
                        <li><a href="#tab_4" data-toggle="tab"><?php echo $this->lang->line('ccavenue'); ?></a></li>
                        <li><a href="#tab_5" data-toggle="tab"><?php echo $this->lang->line('instamojo'); ?></a></li>
                        <li><a href="#tab_6" data-toggle="tab"><?php echo $this->lang->line('paystack'); ?></a></li>
                        <li><a href="#tab_25" data-toggle="tab">Monnify</a></li>
                        <li><a href="#tab_26" data-toggle="tab">Interswitch</a></li>
                        <li><a href="#tab_27" data-toggle="tab">Remita</a></li>
                        <li><a href="#tab_7" data-toggle="tab"><?php echo $this->lang->line('razorpay'); ?></a></li>
                        <li><a href="#tab_8" data-toggle="tab"><?php echo $this->lang->line('paytm'); ?></a></li>
                        <li><a href="#tab_9" data-toggle="tab"><?php echo $this->lang->line('midtrans'); ?></a></li>
                        <li><a href="#tab_10" data-toggle="tab"><?php echo $this->lang->line('pesapal'); ?></a></li>
                        <li><a href="#tab_11" data-toggle="tab"><?php echo $this->lang->line('flutter_wave'); ?> </a></li>
                        <li><a href="#tab_12" data-toggle="tab"><?php echo $this->lang->line('ipay_africa'); ?></a></li>
                        <li><a href="#tab_13" data-toggle="tab"><?php echo $this->lang->line('jazzcash'); ?></a></li>
                        <li><a href="#tab_14" data-toggle="tab"><?php echo $this->lang->line('billplz'); ?></a></li>
                        <li><a href="#tab_15" data-toggle="tab"><?php echo $this->lang->line('sslcommerz'); ?></a></li>
                        <li><a href="#tab_16" data-toggle="tab"><?php echo $this->lang->line('walkingm'); ?></a></li>
                        <li><a href="#tab_17" data-toggle="tab"><?php echo $this->lang->line('mollie'); ?></a></li>
                        <li><a href="#tab_18" data-toggle="tab"><?php echo $this->lang->line('cashfree'); ?></a></li>
                        <li><a href="#tab_19" data-toggle="tab"><?php echo $this->lang->line('payfast'); ?></a></li>
                        <li><a href="#tab_20" data-toggle="tab"><?php echo $this->lang->line('toyyibPay'); ?></a></li>
                        <li><a href="#tab_21" data-toggle="tab"><?php echo $this->lang->line('twocheckout'); ?></a></li>
                        <li><a href="#tab_22" data-toggle="tab"><?php echo $this->lang->line('skrill'); ?></a></li>
                        <li><a href="#tab_23" data-toggle="tab"><?php echo $this->lang->line('payhere'); ?></a></li>
                        <li><a href="#tab_24" data-toggle="tab"><?php echo $this->lang->line('onepay'); ?></a></li>
                    </ul>
                    <div class="tab-content pb0">
                        <div class="tab-pane" id="tab_bank">
                            <?php $bt = $bank_transfer; $bt_msg = $this->session->flashdata('bank_transfer_msg'); ?>
                            <form role="form" action="<?php echo site_url('admin/paymentsettings/bank_transfer'); ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <?php if (is_array($bt_msg)) { ?><div class="alert alert-<?php echo html_escape($bt_msg[0]); ?>"><?php echo html_escape($bt_msg[1]); ?></div><?php } ?>
                                    <p class="text-muted">Payers transfer to your account and upload the receipt; you confirm it before it counts as paid.
                                        This works alongside an online gateway, so people can choose either.</p>
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label class="control-label col-md-5" for="bt_bank_name">Bank name</label>
                                                <div class="col-md-7"><input id="bt_bank_name" name="bank_name" type="text" maxlength="120" class="form-control" value="<?php echo html_escape($bt['bank_name']); ?>" placeholder="e.g. Access Bank"></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5" for="bt_account_name">Account name</label>
                                                <div class="col-md-7"><input id="bt_account_name" name="account_name" type="text" maxlength="160" class="form-control" value="<?php echo html_escape($bt['account_name']); ?>"></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5" for="bt_account_number">Account number</label>
                                                <div class="col-md-7"><input id="bt_account_number" name="account_number" type="text" maxlength="40" class="form-control" value="<?php echo html_escape($bt['account_number']); ?>"></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5" for="bt_note">Note for payers</label>
                                                <div class="col-md-7"><textarea id="bt_note" name="note" rows="3" maxlength="1000" class="form-control" placeholder="Optional, e.g. Use the reference number as the transfer narration."><?php echo html_escape($bt['note']); ?></textarea></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5">Accept bank transfer for</label>
                                                <div class="col-md-7">
                                                    <div class="checkbox"><label><input type="checkbox" name="admission" value="1"<?php echo $bt['admission'] ? ' checked' : ''; ?>> The online admission form fee</label></div>
                                                    <div class="checkbox"><label><input type="checkbox" name="school_fees" value="1"<?php echo $bt['school_fees'] ? ' checked' : ''; ?>> School fees (students upload proof under Offline Bank Payments)</label></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="well well-sm">
                                                <b>How it works</b>
                                                <ol style="padding-left:18px;margin:8px 0 0">
                                                    <li>The payer sees these bank details and the amount.</li>
                                                    <li>They transfer the money and upload the receipt.</li>
                                                    <li>Admission: you approve or reject it on the application (Student Information &gt; Online Admission).
                                                        School fees: under Fees Collection &gt; Offline Bank Payments.</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <div class="row"><div class="col-md-6 col-md-offset-3">
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?><button type="submit" class="btn btn-primary" id="bank_transfer_save"><?php echo $this->lang->line('save'); ?></button><?php } ?>
                                    </div></div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane active" id="tab_1">
                            <form role="form" id="paypal" action="<?php echo site_url('admin/paymentsettings/paypal') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$paypal_result = check_in_array('paypal', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="col-sm-5 control-label" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('paypal_username'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-lg-7 col-md-7 col-sm-7">
                                                        <input autofocus="" id="name" name="paypal_username" placeholder="" type="text" class="form-control" value="<?php echo isset($paypal_result->api_username) ? $paypal_result->api_username : ""; ?>" />
                                                        <span class=" text text-danger paypal_username_error"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-lg-5 col-md-5 col-sm-5" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('paypal_password'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-lg-7 col-md-7 col-sm-7">
                                                        <input id="name" name="paypal_password" placeholder="" type="password" class="form-control"  value="<?php echo isset($paypal_result->api_password) ? $paypal_result->api_password : ""; ?>" />
                                                        <span class=" text text-danger paypal_password_error"></span>
                                                    </div></div>
                                                <div class="form-group">
                                                    <label class="control-label col-lg-5 col-md-5 col-sm-5 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('paypal_signature'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                                                        <input id="name" name="paypal_signature" placeholder="" type="text" class="form-control"  value="<?php echo isset($paypal_result->api_signature) ? $paypal_result->api_signature : ""; ?>" />
                                                        <span class=" text text-danger paypal_signature_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.paypal.com/in/home" target="_blank">
                                                    <h5><?php echo $this->lang->line('multinational_payment_gateway'); ?></h5>
                                                    <img src="<?php echo $this->media_storage->getImageURL('backend/images/paypal.png'); ?>" width="200"><p>https://www.paypal.com</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 pl-sm-6">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary paypal_save ml-n1 float-sm-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_2">
                            <form role="form" id="stripe" id="stripe" action="<?php echo site_url('admin/paymentsettings/stripe') ?>" class="form-horizontal" method="post">
                                <div class="box-body minheight149">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$stripe_result = check_in_array('stripe', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('stripe_api_secret_key'); ?><small class="req"> *</small></label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="api_secret_key" value="<?php echo isset($stripe_result->api_secret_key) ? $stripe_result->api_secret_key : ""; ?>">
                                                        <span class=" text text-danger api_secret_key_error"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-5 control-label">
                                                        <?php echo $this->lang->line('stripe_publishable_key'); ?><small class="req"> *</small></label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="api_publishable_key" value="<?php echo isset($stripe_result->api_publishable_key) ? $stripe_result->api_publishable_key : ""; ?>">
                                                        <span class=" text text-danger api_publishable_key_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://stripe.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('multinational_payment_gateway'); ?></h5>
                                                    <img src="<?php echo $this->media_storage->getImageURL('backend/images/stripe.png') ?>" width="200"><p>https://stripe.com</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary stripe_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_3">
                            <form role="form" id="payu" id="custom" action="<?php echo site_url('admin/paymentsettings/payu') ?>" class="form-horizontal" method="post">
                                <div class="box-body minheight149">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$payu_result = check_in_array('payu', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('payu_money_key'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="key" value="<?php echo isset($payu_result->api_secret_key) ? $payu_result->api_secret_key : ""; ?>">
                                                        <span class="text text-danger key_error"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('payu_money_salt'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="salt" value="<?php echo isset($payu_result->salt) ? $payu_result->salt : ""; ?>">
                                                        <span class="text text-danger salt_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.payumoney.com" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_india'); ?></h5>
                                                    <img src="<?php echo $this->media_storage->getImageURL('backend/images/paym.png'); ?>" width="200"><p>https://www.payumoney.com</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary payu_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_4">
                            <form role="form" id="ccavenue"  action="<?php echo site_url('admin/paymentsettings/ccavenue') ?>" class="form-horizontal" method="post">
                                <div class="box-body minheight149">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$ccavenue_result = check_in_array('ccavenue', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('ccavenue_merchant_id'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="ccavenue_secret" value="<?php echo isset($ccavenue_result->api_secret_key) ? $ccavenue_result->api_secret_key : ""; ?>">
                                                        <span class="text text-danger ccavenue_secret_error"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('ccavenue_working_key'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="ccavenue_salt" value="<?php echo isset($ccavenue_result->salt) ? $ccavenue_result->salt : ""; ?>">
                                                        <span class="text text-danger ccavenue_salt_error"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('access_code'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="ccavenue_api_publishable_key" value="<?php echo isset($ccavenue_result->api_publishable_key) ? $ccavenue_result->api_publishable_key : ""; ?>">
                                                        <span class="text text-danger ccavenue_api_publishable_key_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.ccavenue.com" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_india'); ?></h5>
                                                    
                                                    <img src="<?php echo $this->media_storage->getImageURL('backend/images/ccavenue.png'); ?>" width="200"><p>https://www.ccavenue.com</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary ccavenue_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="tab_5">
                            <form role="form" id="instamojo"  action="<?php echo site_url('admin/paymentsettings/instamojo') ?>" class="form-horizontal" method="post">
                                <div class="box-body minheight149">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$instamojo_result = check_in_array('instamojo', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('private_api_key'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="instamojo_apikey" value="<?php echo isset($instamojo_result->api_secret_key) ? $instamojo_result->api_secret_key : ""; ?>">
                                                        <span class="text text-danger instamojo_apikey_error"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('private_auth_token'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="instamojo_authtoken" value="<?php echo isset($instamojo_result->api_publishable_key) ? $instamojo_result->api_publishable_key : ""; ?>">
                                                        <span class="text text-danger instamojo_authtoken_error"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-5 control-label"><?php echo $this->lang->line('private_salt'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-sm-7">
                                                        <input type="text" class="form-control" name="instamojo_salt" value="<?php echo isset($instamojo_result->salt) ? $instamojo_result->salt : ""; ?>">
                                                        <span class="text text-danger instamojo_salt_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.instamojo.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_india'); ?></h5>
                                                    
                                                    <img src="<?php echo $this->media_storage->getImageURL('backend/images/instamojo.png'); ?>" width="200"> <p>https://www.instamojo.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary instamojo_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane " id="tab_6">
                            <form role="form" id="paystack" action="<?php echo site_url('admin/paymentsettings/paystack') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$paystack_result = check_in_array('paystack', $paymentlist);
 
?>

                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('paystack_secret_key'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="paystack_secretkey" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($paystack_result->api_secret_key) ? $paystack_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger paystack_secretkey_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://paystack.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_afirican_countries'); ?></h5>                                            
                                                    
                                                    <img src="<?php echo $this->media_storage->getImageURL('backend/images/paystack.png'); ?>" width="200"><p>https://paystack.com</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary paystack_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php // Option 2: no keys, a subaccount of the platform's Paystack account (Payment_gateways::for_school)
                            $CI_sub = &get_instance(); $CI_sub->load->library('Payment_gateways');
                            if ($CI_sub->payment_gateways->subaccounts_available('paystack')) {
                                $sub_row = check_in_array('paystack', $paymentlist); ?>
                            <div class="box-body" style="border-top:1px solid #eee;margin-top:10px">
                                <h4 style="margin-top:0">Or: receive payments straight into your bank account <small>no Paystack account or keys needed</small></h4>
                                <?php if (!empty($sub_row->api_signature) && empty($sub_row->api_secret_key)) { ?>
                                    <div class="alert alert-success" style="margin-bottom:10px">
                                        Active: payments settle to <b><?php echo html_escape($sub_row->api_email); ?></b>,
                                        account <?php echo html_escape($sub_row->account_no); ?> (subaccount <?php echo html_escape($sub_row->api_signature); ?>).
                                        Saving another account below replaces it.
                                    </div>
                                <?php } ?>
                                <p class="text-muted">Parents pay through the platform's Paystack account and Paystack pays the money into this
                                    bank account automatically (usually the next business day). Paystack's fee is deducted from each payment<?php
                                    $pct = Payment_gateways::platform_split_percent(); echo $pct > 0 ? '; the platform keeps ' . $pct . '%' : ''; ?>.
                                    Saving your own secret key above instead uses your own Paystack account.</p>
                                <form class="form-horizontal subaccount-form" data-gateway="paystack" action="<?php echo site_url('admin/paymentsettings/subaccount'); ?>" method="post">
                                    <input type="hidden" name="gateway" value="paystack">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-12 col-xs-12">Bank</label>
                                        <div class="col-md-6 col-sm-7 col-xs-12">
                                            <select name="bank" class="form-control sub-bank"><option value="">Loading banks…</option></select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-12 col-xs-12">Account number</label>
                                        <div class="col-md-6 col-sm-7 col-xs-12">
                                            <input name="account" class="form-control sub-account" maxlength="10" inputmode="numeric" autocomplete="off" placeholder="10-digit NUBAN">
                                            <span class="help-block sub-name"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-12 col-xs-12">Business name</label>
                                        <div class="col-md-6 col-sm-7 col-xs-12">
                                            <input name="business_name" class="form-control" maxlength="100" placeholder="The school's name (shown on receipts)">
                                            <span class="text text-danger subaccount_error"></span>
                                        </div>
                                    </div>
                                    <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="col-md-6 col-md-offset-3"><button type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Saving">Use this bank account</button></div>
                                    <?php } ?>
                                </form>
                            </div>
                            <?php } ?>
                        </div>

                        <div class="tab-pane " id="tab_7">
                            <form role="form" id="razorpay" action="<?php echo site_url('admin/paymentsettings/razorpay') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$razorpay_result = check_in_array('razorpay', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('razorpay_key_id'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="razorpay_keyid" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($razorpay_result->api_publishable_key) ? $razorpay_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger razorpay_keyid_error"></span>
                                                    </div>  </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('razorpay_key_secret'); ?><small class="req"> *</small>
                                                    </label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="razorpay_secretkey" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($razorpay_result->api_secret_key) ? $razorpay_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger razorpay_secretkey_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://razorpay.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_india'); ?></h5>
                                                    <img src="<?php echo base_url(); ?>/backend/images/razorpay.jpg<?php echo img_time(); ?>" width="200"><p>https://razorpay.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary razorpay_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="tab_8">
                            <form role="form" id="paytm" action="<?php echo site_url('admin/paymentsettings/paytm') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$paytm_result = check_in_array('paytm', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('merchant_id'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="paytm_merchantid" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($paytm_result->api_publishable_key) ? $paytm_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger paytm_merchantid_error"></span>
                                                    </div>  </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('merchant_key'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="paytm_merchantkey" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($paytm_result->api_secret_key) ? $paytm_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger paytm_merchantkey_error"></span>
                                                    </div>  </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('website'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="paytm_website" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($paytm_result->paytm_website) ? $paytm_result->paytm_website : ""; ?>" />
                                                        <span class=" text text-danger paytm_website_error"></span>
                                                    </div>  </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('indusrty_type'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="paytm_industrytype" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($paytm_result->paytm_industrytype) ? $paytm_result->paytm_industrytype : ""; ?>" />
                                                        <span class=" text text-danger paytm_industrytype_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://paytm.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_india'); ?></h5>
                                                    <img src="<?php echo base_url(); ?>/backend/images/paytm.jpg<?php echo img_time(); ?>" width="200"><p>https://paytm.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary paytm_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_9">
                            <form role="form" id="midtrans" action="<?php echo site_url('admin/paymentsettings/midtrans') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-7">
                                                <?php
$midtrans_result = check_in_array('midtrans', $paymentlist);
?>

                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('server_key'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="midtrans_serverkey" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($midtrans_result->api_secret_key) ? $midtrans_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger midtrans_serverkey_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://midtrans.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_indonesia'); ?></h5>
                                                    <img src="<?php echo base_url(); ?>/backend/images/midtrans.jpg<?php echo img_time(); ?>" width="200"><p>https://midtrans.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary midtrans_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane " id="tab_10">
                            <form role="form" id="pesapal" action="<?php echo site_url('admin/paymentsettings/pesapal') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$pesapal_result = check_in_array('pesapal', $paymentlist);
?>

                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                       <?php echo $this->lang->line('consumer_key'); ?>
                                                    <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="pesapal_consumer_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($pesapal_result->api_publishable_key) ? $pesapal_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger pesapal_consumer_key_error"></span>
                                                    </div>  </div>
                                                    <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                       <?php echo $this->lang->line('consumer_secret'); ?>
                                                    <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="pesapal_consumer_secret" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($pesapal_result->api_secret_key) ? $pesapal_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger pesapal_consumer_secret_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.pesapal.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_afirican_countries'); ?></h5>
                                                    <img src="<?php echo base_url(); ?>/backend/images/pesapal.jpg<?php echo img_time(); ?>" width="200"><p>https://www.pesapal.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                            <button type="submit" class="btn btn-primary midtrans_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                         <div class="tab-pane " id="tab_11">
                            <form role="form" id="flutterwave" action="<?php echo site_url('admin/paymentsettings/flutterwave') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$flutterwave_result = check_in_array('flutterwave', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                       <?php echo $this->lang->line('public_key'); ?>
                                                    <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="public_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($flutterwave_result->api_publishable_key) ? $flutterwave_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger public_key_error"></span>
                                                    </div>  </div>
                                                   <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                       <?php echo $this->lang->line('secret_key'); ?>
                                                    <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="secret_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($flutterwave_result->api_secret_key) ? $flutterwave_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger secret_key_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://flutterwave.com/us/" target="_blank">
                                                    <h5><?php echo $this->lang->line('multinational_payment_gateway'); ?></h5>
                                                    <img src="<?php echo base_url(); ?>/backend/images/flutterwave.png<?php echo img_time(); ?>" width="200"><p>https://flutterwave.com/us</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                             <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                              <button type="submit" class="btn btn-primary midtrans_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php // Option 2: no keys, a subaccount of the platform's Flutterwave account (Payment_gateways::for_school)
                            $CI_sub = &get_instance(); $CI_sub->load->library('Payment_gateways');
                            if ($CI_sub->payment_gateways->subaccounts_available('flutterwave')) {
                                $sub_row = check_in_array('flutterwave', $paymentlist); ?>
                            <div class="box-body" style="border-top:1px solid #eee;margin-top:10px">
                                <h4 style="margin-top:0">Or: receive payments straight into your bank account <small>no Flutterwave account or keys needed</small></h4>
                                <?php if (!empty($sub_row->api_signature) && empty($sub_row->api_secret_key)) { ?>
                                    <div class="alert alert-success" style="margin-bottom:10px">
                                        Active: payments settle to <b><?php echo html_escape($sub_row->api_email); ?></b>,
                                        account <?php echo html_escape($sub_row->account_no); ?> (subaccount <?php echo html_escape($sub_row->api_signature); ?>).
                                        Saving another account below replaces it.
                                    </div>
                                <?php } ?>
                                <p class="text-muted">Parents pay through the platform's Flutterwave account and Flutterwave pays the money into this
                                    bank account automatically (usually the next business day). Flutterwave's fee is deducted from each payment<?php
                                    $pct = Payment_gateways::platform_split_percent(); echo $pct > 0 ? '; the platform keeps ' . $pct . '%' : ''; ?>.
                                    Saving your own secret key above instead uses your own Flutterwave account.</p>
                                <form class="form-horizontal subaccount-form" data-gateway="flutterwave" action="<?php echo site_url('admin/paymentsettings/subaccount'); ?>" method="post">
                                    <input type="hidden" name="gateway" value="flutterwave">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-12 col-xs-12">Bank</label>
                                        <div class="col-md-6 col-sm-7 col-xs-12">
                                            <select name="bank" class="form-control sub-bank"><option value="">Loading banks…</option></select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-12 col-xs-12">Account number</label>
                                        <div class="col-md-6 col-sm-7 col-xs-12">
                                            <input name="account" class="form-control sub-account" maxlength="10" inputmode="numeric" autocomplete="off" placeholder="10-digit NUBAN">
                                            <span class="help-block sub-name"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-12 col-xs-12">Business name</label>
                                        <div class="col-md-6 col-sm-7 col-xs-12">
                                            <input name="business_name" class="form-control" maxlength="100" placeholder="The school's name (shown on receipts)">
                                            <span class="text text-danger subaccount_error"></span>
                                        </div>
                                    </div>
                                    <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="col-md-6 col-md-offset-3"><button type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Saving">Use this bank account</button></div>
                                    <?php } ?>
                                </form>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="tab-pane " id="tab_12">
                            <form role="form" id="ipayafrica" action="<?php echo site_url('admin/paymentsettings/ipayafrica') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$ipayafrica_result = check_in_array('ipayafrica', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                       <?php echo $this->lang->line('vendorid'); ?>
                                                    <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="ipayafrica_vendorid" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($ipayafrica_result->api_publishable_key) ? $ipayafrica_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger ipayafrica_vendorid_error"></span>
                                                    </div>  </div>
                                                    <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                       <?php echo $this->lang->line('hashkey'); ?>
                                                    <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="ipayafrica_hashkey" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($ipayafrica_result->api_secret_key) ? $ipayafrica_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger ipayafrica_hashkey_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://ipayafrica.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_afirican_countries'); ?></h5>
                                                    <img src="<?php echo base_url(); ?>/backend/images/ipayafrica.png<?php echo img_time(); ?>" width="200"><p>https://ipayafrica.com//</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                            <button type="submit" class="btn btn-primary midtrans_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                             <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane " id="tab_13">
                            <form role="form" id="jazzcash" action="<?php echo site_url('admin/paymentsettings/jazzcash') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$jazzcash_result = check_in_array('jazzcash', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('pp_merchantid'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="jazzcash_pp_MerchantID" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($jazzcash_result->api_secret_key) ? $jazzcash_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger jazzcash_pp_MerchantID_error"></span>
                                                    </div>  </div>
                                                     <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('pp_password'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="jazzcash_pp_Password" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($jazzcash_result->api_password) ? $jazzcash_result->api_password : ""; ?>" />
                                                        <span class=" text text-danger jazzcash_pp_Password_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.jazzcash.com.pk/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_pakistan'); ?></h5>
                                                    <img src="<?php echo base_url(); ?>/backend/images/jazzcash.jpg<?php echo img_time(); ?>" width="200"><p>https://www.jazzcash.com.pk/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary jazzcash_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane " id="tab_14">
                            <form role="form" id="billplz" action="<?php echo site_url('admin/paymentsettings/billplz') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$billplz_result = check_in_array('billplz', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('api_key'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="billplz_api_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($billplz_result->api_secret_key) ? $billplz_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger billplz_api_key_error"></span>
                                                    </div>  </div>
                                                     <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                         <?php echo $this->lang->line('customer_service_email'); ?>

                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="billplz_customer_service_email" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($billplz_result->api_email) ? $billplz_result->api_email : ""; ?>" />
                                                        <span class=" text text-danger billplz_customer_service_email_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.billplz.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_malaysia'); ?></h5>
                                                    <img src="<?php echo base_url(); ?>/backend/images/billplz.jpg<?php echo img_time(); ?>" width="200"><p>https://www.billplz.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary jazzcash_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                         <div class="tab-pane " id="tab_15">
                            <form role="form" id="sslcommerz" action="<?php echo site_url('admin/paymentsettings/sslcommerz') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$sslcommerz_result = check_in_array('sslcommerz', $paymentlist);
?>

                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('store_id'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="sslcommerz_api_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($sslcommerz_result->api_publishable_key) ? $sslcommerz_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger sslcommerz_api_key_error"></span>
                                                    </div>  </div>
                                                     <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                         <?php echo $this->lang->line('store_password'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="sslcommerz_store_password" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($sslcommerz_result->api_password) ? $sslcommerz_result->api_password : ""; ?>" />
                                                        <span class=" text text-danger sslcommerz_store_password_error"></span>
                                                    </div>  </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.sslcommerz.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_bangladesh'); ?></h5><br>
                                                    <img src="<?php echo base_url(); ?>/backend/images/sslcommerz.png<?php echo img_time(); ?>" width="200"><br><br><p>https://www.sslcommerz.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary sslcommerz_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane " id="tab_16">
                            <form role="form" id="walkingm" action="<?php echo site_url('admin/paymentsettings/walkingm') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$walkingm_result = check_in_array('walkingm', $paymentlist);
?>

                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('client_id'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="walkingm_client_id" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($walkingm_result->api_publishable_key) ? $walkingm_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger walkingm_client_id_error"></span>
                                                    </div>  </div>
                                                     <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                         <?php echo $this->lang->line('client_secret'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="walkingm_client_secret" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($walkingm_result->api_secret_key) ? $walkingm_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger walkingm_client_secret_error"></span>
                                                    </div>  </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://walkingm.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_liberia'); ?></h5><br>
                                                    <img src="<?php echo base_url(); ?>/backend/images/walkingm.png<?php echo img_time(); ?>" width="200"><br><br><p>https://walkingm.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary walkingm_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                         <div class="tab-pane " id="tab_17">
                            <form role="form" id="mollie" action="<?php echo site_url('admin/paymentsettings/mollie') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$mollie_result = check_in_array('mollie', $paymentlist);
?>

                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('api_key'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="mollie_api_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($mollie_result->api_publishable_key) ? $mollie_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger mollie_api_key_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.mollie.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_european_countries'); ?></h5><br>
                                                    <img src="<?php echo base_url(); ?>/backend/images/mollie.png<?php echo img_time(); ?>" width="200"><br><br><p>https://www.mollie.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary mollie_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane " id="tab_18">
                            <form role="form" id="cashfree" action="<?php echo site_url('admin/paymentsettings/cashfree') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$cashfree_result = check_in_array('cashfree', $paymentlist);
?>

                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('app_id'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="cashfree_app_id" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($cashfree_result->api_publishable_key) ? $cashfree_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger cashfree_app_id_error"></span>
                                                    </div>  </div>
                                                     <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('secret_key'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="cashfree_secret_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($cashfree_result->api_secret_key) ? $cashfree_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger cashfree_secret_key_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.cashfree.com/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_india'); ?></h5><br>
                                                    <img src="<?php echo base_url(); ?>/backend/images/cashfree.png<?php echo img_time(); ?>" width="200"><br><br><p>https://www.cashfree.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary cashfree_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane " id="tab_19">
                            <form role="form" id="payfast" action="<?php echo site_url('admin/paymentsettings/payfast') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$payfast_result = check_in_array('payfast', $paymentlist);
?>

                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('merchant_id'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="payfast_api_publishable_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($payfast_result->api_publishable_key) ? $payfast_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger payfast_api_publishable_key_error"></span>
                                                    </div>  </div>
                                                     <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('merchant_key'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="payfast_api_secret_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($payfast_result->api_secret_key) ? $payfast_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger payfast_api_secret_key_error"></span>
                                                    </div>  </div>
                                                     <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('security_passphrase'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="payfast_salt" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($payfast_result->salt) ? $payfast_result->salt : ""; ?>" />
                                                        <span class=" text text-danger payfast_salt_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.payfast.co.za/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_afirican_countries'); ?></h5><br>
                                                    <img src="<?php echo base_url(); ?>/backend/images/payfast.png<?php echo img_time(); ?>" width="200"><br><br><p>https://www.payfast.co.za/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary payfast_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                         <div class="tab-pane " id="tab_20">
                            <form role="form" id="toyyibpay" action="<?php echo site_url('admin/paymentsettings/toyyibPay') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$toyyibpay_result = check_in_array('toyyibpay', $paymentlist);
?>
                                                    <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('secret_key'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="toyyibpay_api_secret_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($toyyibpay_result->api_secret_key) ? $toyyibpay_result->api_secret_key : ""; ?>" />
                                                        <span class=" text text-danger toyyibpay_api_secret_key_error"></span>
                                                    </div>  </div>
                                                     <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('category_code'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="toyyibpay_category_code" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($toyyibpay_result->api_signature) ? $toyyibpay_result->api_signature : ""; ?>" />
                                                        <span class=" text text-danger toyyibpay_category_code_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://toyyibpay.com/" target="_blank">
                                                     <h5><?php echo $this->lang->line('payment_gateway_for_malaysia'); ?></h5><br>
                                                    <img src="<?php echo base_url(); ?>/backend/images/toyyibpay.png<?php echo img_time(); ?>" width="200"><br><br><p>https://toyyibpay.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary toyyibpay_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                         <div class="tab-pane " id="tab_21">
                            <form role="form" id="twocheckout" action="<?php echo site_url('admin/paymentsettings/twocheckout') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$twocheckout_result = check_in_array('twocheckout', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('merchant_code'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input  name="twocheckout_api_publishable_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($twocheckout_result->api_publishable_key) ? $twocheckout_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger twocheckout_api_publishable_key_error"></span>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                <?php echo $this->lang->line('secret_key'); ?>
                                                <small class="req"> *</small>
                                                </label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                <input  name="twocheckout_api_secret_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($twocheckout_result->api_secret_key) ? $twocheckout_result->api_secret_key : ""; ?>" />
                                                <span class=" text text-danger twocheckout_api_secret_key_error"></span>
                                                </div>
                                                </div>
                                                <div class="form-group">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                <?php echo 'IPN Endpoint for 2Checkout  (' . base_url() . 'gateway_ins/twocheckout)'; ?>
                                                </label>
                                                </div>

                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.2checkout.com/" target="_blank">
                                                     <h5><?php echo $this->lang->line('payment_gateway_for_malaysia'); ?></h5><br>
                                                    <img src="<?php echo base_url(); ?>/backend/images/twocheckout.png<?php echo img_time(); ?>" width="200"><br><br><p>https://www.2checkout.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary toyyibpay_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane " id="tab_22">
                            <form role="form" id="skrill" action="<?php echo site_url('admin/paymentsettings/skrill') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$skrill_result = check_in_array('skrill', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('merchant_account_email'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input  name="skrill_api_email" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($skrill_result->api_email) ? $skrill_result->api_email : ""; ?>" />
                                                        <span class=" text text-danger skrill_api_email_error"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                <?php echo $this->lang->line('merchant_secret_salt'); ?>
                                                <small class="req"> *</small>
                                                </label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                <input  name="skrill_salt" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($skrill_result->salt) ? $skrill_result->salt : ""; ?>" />
                                                <span class=" text text-danger skrill_salt_error"></span>
                                                </div>
                                                </div>

                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.skrill.com/" target="_blank">
                                                     <h5><?php echo $this->lang->line('payment_gateway_for_malaysia'); ?></h5>
                                                     <br>
                                                    <img src="<?php echo base_url(); ?>/backend/images/skrill.jpg<?php echo img_time(); ?>" width="200">
                                                    <br>
                                                    <br>
                                                    <p>https://www.skrill.com/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary toyyibpay_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane " id="tab_23">
                            <form role="form" id="payhere" action="<?php echo site_url('admin/paymentsettings/payhere') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$payhere_result = check_in_array('payhere', $paymentlist);
?>

                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('merchant_id'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input  name="payhere_api_publishable_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($payhere_result->api_publishable_key) ? $payhere_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger payhere_api_publishable_key_error"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                <?php echo $this->lang->line('merchant_secret'); ?>
                                                <small class="req"> *</small>
                                                </label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                <input  name="payhere_api_secret_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($payhere_result->api_secret_key) ? $payhere_result->api_secret_key : ""; ?>" />
                                                <span class=" text text-danger payhere_api_secret_key_error"></span>
                                                </div>
                                                </div>

                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.payhere.lk/" target="_blank">
                                                     <h5><?php echo $this->lang->line('payment_gateway_for_sri_lanka'); ?></h5>
                                                     <br>
                                                    <img src="<?php echo base_url(); ?>/backend/images/payhere.png<?php echo img_time(); ?>" width="200">
                                                    <br>
                                                    <br>
                                                    <p>https://www.payhere.lk/</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary toyyibpay_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="tab_25">
                            <form role="form" id="monnify" action="<?php echo site_url('admin/paymentsettings/monnify') ?>" class="form-horizontal online-gateway-form" method="post">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <?php $monnify_result = check_in_array('monnify', $paymentlist); ?>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12">API key<small class="req"> *</small></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input name="monnify_api_key" type="text" class="form-control" autocomplete="off" placeholder="MK_TEST_... or MK_PROD_..."
                                                           value="<?php echo isset($monnify_result->api_publishable_key) ? html_escape($monnify_result->api_publishable_key) : ''; ?>" />
                                                    <span class="text text-danger monnify_api_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12">Secret key<small class="req"> *</small></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input name="monnify_secret_key" type="text" class="form-control" autocomplete="off" placeholder=""
                                                           value="<?php echo isset($monnify_result->api_secret_key) ? html_escape($monnify_result->api_secret_key) : ''; ?>" />
                                                    <span class="text text-danger monnify_secret_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12">Contract code<small class="req"> *</small></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input name="monnify_contract_code" type="text" class="form-control" autocomplete="off" placeholder="e.g. 1234567890"
                                                           value="<?php echo isset($monnify_result->account_no) ? html_escape($monnify_result->account_no) : ''; ?>" />
                                                    <span class="text text-danger monnify_contract_code_error"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12">Mode</label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <select name="monnify_mode" class="form-control">
                                                        <option value="0" <?php echo (isset($monnify_result->gateway_mode) && (int) $monnify_result->gateway_mode === 1) ? '' : 'selected'; ?>>Test (sandbox)</option>
                                                        <option value="1" <?php echo (isset($monnify_result->gateway_mode) && (int) $monnify_result->gateway_mode === 1) ? 'selected' : ''; ?>>Live</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 disblock">
                                            <h5><b>Monnify</b>: Nigerian payment gateway</h5>
                                            <p class="text-muted">Card, bank transfer and USSD. Naira only. The money goes to the school's own Monnify account.</p>
                                            <p class="text-muted">Where to find the keys: Monnify dashboard > Developers > API Keys & Contracts.</p>
                                            <p><a href="https://app.monnify.com" target="_blank" rel="noopener">https://app.monnify.com</a></p>
                                            <p class="text-muted">After saving, choose Monnify under <b>Select payment gateway</b> to use it.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                                <button type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="tab_26">
                            <form role="form" id="interswitch" action="<?php echo site_url('admin/paymentsettings/interswitch') ?>" class="form-horizontal online-gateway-form" method="post">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <?php $interswitch_result = check_in_array('interswitch', $paymentlist); ?>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12">Merchant code<small class="req"> *</small></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input name="interswitch_merchant_code" type="text" class="form-control" autocomplete="off" placeholder="e.g. MX12345"
                                                           value="<?php echo isset($interswitch_result->api_username) ? html_escape($interswitch_result->api_username) : ''; ?>" />
                                                    <span class="text text-danger interswitch_merchant_code_error"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12">Pay item ID<small class="req"> *</small></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input name="interswitch_pay_item_id" type="text" class="form-control" autocomplete="off" placeholder="e.g. 1234567"
                                                           value="<?php echo isset($interswitch_result->account_no) ? html_escape($interswitch_result->account_no) : ''; ?>" />
                                                    <span class="text text-danger interswitch_pay_item_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12">Mode</label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <select name="interswitch_mode" class="form-control">
                                                        <option value="0" <?php echo (isset($interswitch_result->gateway_mode) && (int) $interswitch_result->gateway_mode === 1) ? '' : 'selected'; ?>>Test (sandbox)</option>
                                                        <option value="1" <?php echo (isset($interswitch_result->gateway_mode) && (int) $interswitch_result->gateway_mode === 1) ? 'selected' : ''; ?>>Live</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 disblock">
                                            <h5><b>Interswitch</b>: Nigerian payment gateway</h5>
                                            <p class="text-muted">Card (Verve, Visa, Mastercard), bank transfer, USSD. Naira only. The money goes to the school's own Interswitch account.</p>
                                            <p class="text-muted">Where to find the keys: Quickteller Business > Developer Tools (Web Checkout).</p>
                                            <p><a href="https://business.quickteller.com" target="_blank" rel="noopener">https://business.quickteller.com</a></p>
                                            <p class="text-muted">After saving, choose Interswitch under <b>Select payment gateway</b> to use it.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                                <button type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="tab_27">
                            <form role="form" id="remita" action="<?php echo site_url('admin/paymentsettings/remita') ?>" class="form-horizontal online-gateway-form" method="post">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <?php $remita_result = check_in_array('remita', $paymentlist); ?>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12">Merchant ID<small class="req"> *</small></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input name="remita_merchant_id" type="text" class="form-control" autocomplete="off" placeholder=""
                                                           value="<?php echo isset($remita_result->api_username) ? html_escape($remita_result->api_username) : ''; ?>" />
                                                    <span class="text text-danger remita_merchant_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12">Service type ID<small class="req"> *</small></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input name="remita_service_type_id" type="text" class="form-control" autocomplete="off" placeholder="the service (fee type) payments go to"
                                                           value="<?php echo isset($remita_result->account_no) ? html_escape($remita_result->account_no) : ''; ?>" />
                                                    <span class="text text-danger remita_service_type_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12">API key<small class="req"> *</small></label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <input name="remita_api_key" type="text" class="form-control" autocomplete="off" placeholder=""
                                                           value="<?php echo isset($remita_result->api_secret_key) ? html_escape($remita_result->api_secret_key) : ''; ?>" />
                                                    <span class="text text-danger remita_api_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-5 col-sm-12 col-xs-12">Mode</label>
                                                <div class="col-md-7 col-sm-7 col-xs-12">
                                                    <select name="remita_mode" class="form-control">
                                                        <option value="0" <?php echo (isset($remita_result->gateway_mode) && (int) $remita_result->gateway_mode === 1) ? '' : 'selected'; ?>>Test (sandbox)</option>
                                                        <option value="1" <?php echo (isset($remita_result->gateway_mode) && (int) $remita_result->gateway_mode === 1) ? 'selected' : ''; ?>>Live</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 disblock">
                                            <h5><b>Remita</b>: Nigerian payment gateway</h5>
                                            <p class="text-muted">Card, bank transfer, USSD and payment at any bank branch (RRR). Naira only. The money goes to the school's own Remita account.</p>
                                            <p class="text-muted">Where to find the keys: Remita profile > Integration (merchant ID, service type and API key).</p>
                                            <p><a href="https://remita.net" target="_blank" rel="noopener">https://remita.net</a></p>
                                            <p class="text-muted">After saving, choose Remita under <b>Select payment gateway</b> to use it.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                                <button type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane " id="tab_24">
                           <form role="form" id="onepay" action="<?php echo site_url('admin/paymentsettings/onepay') ?>" class="form-horizontal" method="post">
                                <div class="box-body">
                                    <div class="row">
                                            <div class="col-md-7">
                                                <?php
$onepay_result = check_in_array('onepay', $paymentlist);
?>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('onepay_merchant_id'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="onepay_merchant_id" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($onepay_result->api_publishable_key) ? $onepay_result->api_publishable_key : ""; ?>" />
                                                        <span class=" text text-danger onepay_merchant_id_error"></span>
                                                    </div>  </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                        <?php echo $this->lang->line('access_code'); ?>
                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="onepay_salt" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($onepay_result->salt) ? $onepay_result->salt : ""; ?>" />
                                                        <span class=" text text-danger onepay_salt_error"></span>
                                                    </div>  </div>
                                                     <div class="form-group">
                                                    <label class="control-label col-md-5 col-sm-12 col-xs-12" for="exampleInputEmail1">
                                                         <?php echo $this->lang->line('hash_key'); ?>

                                                        <small class="req"> *</small></label>
                                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                                        <input  name="onepay_api_signature" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo isset($onepay_result->api_signature) ? $onepay_result->api_signature : ""; ?>" />
                                                        <span class=" text text-danger onepay_api_signature_error"></span>
                                                    </div>  </div>
                                            </div>
                                            <div class="col-md-5 text text-center disblock">
                                                <a href="https://www.onepay.vn/" target="_blank">
                                                    <h5><?php echo $this->lang->line('payment_gateway_for_vietnam'); ?></h5><br>
                                                    <img src="<?php echo base_url(); ?>/backend/images/onepay.svg" width="200"><br><br><p>https://www.onepay.vn</p></a>
                                            </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                                <button type="submit" class="btn btn-primary sslcommerz_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.tab-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="box box-primary">
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form role="form" action="<?php echo site_url('admin/paymentsettings/setting') ?>" id="payment_gateway" method="POST">
                        <div class="box-body minheight199">
                            <div class="form-group"> <!-- Radio group !-->
                                <?php
$radio_check = check_selected($paymentlist);
?>

                                <label class="control-label"><?php echo $this->lang->line('select_payment_gateway'); ?></label>

                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_setting" value="paypal" <?php
if ($radio_check == 'paypal') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('paypal'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="stripe" <?php
if ($radio_check == 'stripe') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('stripe'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="payu" <?php
if ($radio_check == 'payu') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('payu'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="ccavenue" <?php
if ($radio_check == 'ccavenue') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('ccavenue'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="instamojo" <?php
if ($radio_check == 'instamojo') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('instamojo'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_setting" value="paystack" <?php
if ($radio_check == 'paystack') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('paystack'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="razorpay" <?php
if ($radio_check == 'razorpay') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('razorpay'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="paytm" <?php
if ($radio_check == 'paytm') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('paytm'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="midtrans" <?php
if ($radio_check == 'midtrans') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('midtrans'); ?>
                                    </label>
                                </div>
                                  <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="pesapal" <?php
if ($radio_check == 'pesapal') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('pesapal'); ?>
                                    </label>
                                </div>
                                 <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="flutterwave" <?php
if ($radio_check == 'flutterwave') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('flutter_wave'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="ipayafrica" <?php
if ($radio_check == 'ipayafrica') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('ipay_africa'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="jazzcash" <?php
if ($radio_check == 'jazzcash') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('jazzcash'); ?>
                                    </label>
                                </div>

                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="billplz" <?php
if ($radio_check == 'billplz') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('billplz'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="sslcommerz" <?php
if ($radio_check == 'sslcommerz') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('sslcommerz'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="walkingm" <?php
if ($radio_check == 'walkingm') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('walkingm'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="mollie" <?php
if ($radio_check == 'mollie') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('mollie'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="cashfree" <?php
if ($radio_check == 'cashfree') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('cashfree'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="payfast" <?php
if ($radio_check == 'payfast') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('payfast'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="toyyibpay" <?php
if ($radio_check == 'toyyibpay') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('toyyibPay'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="twocheckout" <?php
if ($radio_check == 'twocheckout') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('twocheckout'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="skrill" <?php
if ($radio_check == 'skrill') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('skrill'); ?>
                                    </label>
                                </div>
                                 <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="payhere" <?php
if ($radio_check == 'payhere') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('payhere'); ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_setting" value="monnify" <?php echo $radio_check == 'monnify' ? 'checked' : ''; ?>>
                                        Monnify
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_setting" value="interswitch" <?php echo $radio_check == 'interswitch' ? 'checked' : ''; ?>>
                                        Interswitch
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_setting" value="remita" <?php echo $radio_check == 'remita' ? 'checked' : ''; ?>>
                                        Remita
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="onepay" <?php
if ($radio_check == 'onepay') {
    echo "checked";
}
?>>
                                        <?php echo $this->lang->line('onepay'); ?>
                                    </label>
                                </div>
                                 <span class="text text-danger payment_setting_error"></span>
                                <div class="radio">
                                    <label>
                                        <input type="radio"  name="payment_setting" value="none" <?php
if ($radio_check == 'none') {
    echo "checked";
}
?>>
                                         <?php echo $this->lang->line('none'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) {?>
                                <button type="submit" class="btn btn-primary pull-right payment_gateway_save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                            <?php }?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php

function check_selected($array)
{
    $selected = "none";
    if (!empty($array)) {

        foreach ($array as $a => $element) {
            if ($element->is_active == "yes") {
                $selected = $element->payment_type;
            }
        }
    }
    return $selected;
}

function check_in_array($find, $array)
{
    if (!empty($array)) {
        foreach ($array as $element) {
            if ($find == $element->payment_type) {
                return $element;
            }
        }
    }
    $object            = new stdClass();
    $object->id        = "";
    $object->type      = "";
    $object->api_id    = "";
    $object->username  = "";
    $object->url       = "";
    $object->name      = "";
    $object->contact   = "";
    $object->password  = "";
    $object->authkey   = "";
    $object->senderid  = "";
    $object->is_active = "";
    return $object;
}
?>

<script type="text/javascript">
    $("#payment_gateway").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".payment_gateway_save");
        $this.button('loading');
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#payment_gateway").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
                //if fails
            }, complete: function () {
                $this.button('reset');
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    $("#paypal").submit(function (e) {
        $("[class$='_error']").html("");

        var $this = $(".paypal_save");
        $this.button('loading');
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#paypal").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
                //if fails
            }, complete: function () {
                $this.button('reset');
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    $("#stripe").submit(function (e) {
        $("[class$='_error']").html("");

        var $this = $(".stripe_save");
        $this.button('loading');
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#stripe").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
                //if fails
            }, complete: function () {
                $this.button('reset');
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    $("#payu").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".payu_save");
        $this.button('loading');
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#payu").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#ccavenue").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".ccavenue_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#ccavenue").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#paystack").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".paystack_save");
        $this.button('loading');
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#paystack").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#instamojo").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".instamojo_save");
        $this.button('loading');
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#instamojo").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#razorpay").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".razorpay_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#razorpay").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#paytm").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".paytm_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#paytm").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#midtrans").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".midtrans_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#midtrans").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#pesapal").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".pesapal_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#pesapal").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

  $("#ipayafrica").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".ipayafrica_save");
        $this.button('loading');
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#ipayafrica").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".ipayafrica_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

   $("#flutterwave").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".flutterwave_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#flutterwave").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".flutterwave_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

     $("#jazzcash").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".jazzcash_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#jazzcash").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

     $("#billplz").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".billplz_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#billplz").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

      $("#sslcommerz").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".sslcommerz_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#sslcommerz").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#walkingm").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".walkingm_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#walkingm").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

      $("#mollie").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".mollie_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#mollie").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

       $("#cashfree").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".cashfree_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#cashfree").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

       $("#payfast").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".payfast_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#payfast").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#toyyibpay").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".toyyibpay_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#toyyibpay").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#twocheckout").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".twocheckout_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#twocheckout").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#skrill").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".skrill_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#skrill").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            },
            complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

    $("#payhere").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".skrill_save");
        $this.button('loading');
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#payhere").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            },
            complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });

     $("#onepay").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".onepay_save");
        $this.button('loading');
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#onepay").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.button('reset');
            }
        });
        e.preventDefault();
    });
    // Monnify, Interswitch, Remita (admin/paymentsettings/<gateway>, same answers as the others)
    $(".online-gateway-form").submit(function (e) {
        e.preventDefault();
        var $form = $(this), $btn = $form.find("button[type=submit]");
        $form.find("[class$='_error']").html("");
        $btn.button('loading');
        $.ajax({
            type: "POST", dataType: 'JSON', url: $form.attr('action'), data: $form.serialize(),
            success: function (data) {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) { $form.find('.' + key + "_error").html(value); });
                } else {
                    successMsg(data.msg);
                }
            },
            complete: function () { $btn.button('reset'); }
        });
    });
    // Subaccount forms (Paystack, Flutterwave): banks, account name check, save.
    $(".subaccount-form").each(function () {
        var $form = $(this), gw = $form.data('gateway'), loaded = false;
        var $bank = $form.find('.sub-bank'), $acct = $form.find('.sub-account'), $name = $form.find('.sub-name');
        function load() {
            if (loaded) { return; }
            loaded = true;
            $.getJSON('<?php echo site_url('admin/paymentsettings/banks'); ?>/' + gw, function (d) {
                $bank.empty().append($('<option value="">').text(d.ok ? 'Choose your bank' : (d.message || 'Banks could not be loaded')));
                $.each(d.banks || {}, function (code, label) { $bank.append($('<option>').val(code).text(label)); });
            });
        }
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) { if ($($(e.target).attr('href')).find($form).length) { load(); } });
        if ($form.closest('.tab-pane').hasClass('active')) { load(); }
        function check() {
            $name.text('').css('color', '');
            if ($bank.val() && /^\d{10}$/.test($acct.val())) {
                $name.text('Checking the account…');
                $.post('<?php echo site_url('admin/paymentsettings/resolve_account'); ?>', {gateway: gw, bank: $bank.val(), account: $acct.val()}, function (d) {
                    $name.text(d.ok ? '✔ ' + d.name : (d.message || 'Account not found')).css('color', d.ok ? '#2e7d32' : '#c62828');
                }, 'json');
            }
        }
        $bank.on('change', check);
        $acct.on('input', function () { this.value = this.value.replace(/\D/g, '').slice(0, 10); check(); });
        $form.on('submit', function (e) {
            e.preventDefault();
            var $btn = $form.find('button[type=submit]');
            $form.find('.subaccount_error').html('');
            $btn.button('loading');
            $.post($form.attr('action'), $form.serialize(), function (d) {
                if (d.st === 1) { $form.find('.subaccount_error').text(d.msg.subaccount || 'Not saved'); } else { successMsg(d.msg); }
            }, 'json').always(function () { $btn.button('reset'); });
        });
    });
</script>
<script>
$(function () { if (location.hash === '#tab_bank') { $('a[href="#tab_bank"]').tab('show'); } });
</script>
