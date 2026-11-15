<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-ioxhost"></i> <?php //echo $this->lang->line('front_office'); ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('offline_bank_payments'); ?></h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <?php $bt_ci = get_instance(); $bt_ci->load->library('School_bank_transfer'); $bt = $bt_ci->school_bank_transfer->details();
                    if ($bt_ci->school_bank_transfer->complete()) { ?>
                    <div class="box-body pb0">
                        <div class="alert alert-info" style="margin-bottom:0">
                            <b>Pay into:</b> <?php echo html_escape($bt['bank_name']); ?> &middot; <?php echo html_escape($bt['account_name']); ?> &middot; <b><?php echo html_escape($bt['account_number']); ?></b>
                            <?php if ($bt['note'] !== '') { ?><br><?php echo nl2br(html_escape($bt['note'])); ?><?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if ($setting->offline_bank_payment_instruction) {?>
                    <div class="box-body pb0">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="font15" for="payment_date_error"><?php echo $this->lang->line('instructions'); ?></label>
                                <div class="unorder-list">
                                    <?php echo $setting->offline_bank_payment_instruction; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }?>

                    <form id="form1" action="<?php echo site_url('user/offlinepayment') ?>"  name="offlinepayment" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) {
    ?>
                                    <?php
echo $this->session->flashdata('msg');
    $this->session->unset_userdata('msg');
    ?>
                                <?php }?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <?php
                                $group_total = 0;
                                if (!empty($group_fees)) { ?>
                                <div class="table-responsive">
                                    <table class="table table-condensed table-bordered" id="transfer_fees">
                                        <thead><tr><th>Fees this transfer pays</th><th class="text-right">Owing (<?php echo $currency_symbol; ?>)</th></tr></thead>
                                        <tbody>
                                        <?php foreach ($group_fees as $gf) {
                                            $owes = (float) $gf['amount_balance'] + max(0, (float) $gf['fine_balance']);
                                            $group_total += $owes; ?>
                                            <tr><td><?php echo html_escape($gf['fee_group_name'] . ' (' . $gf['fee_type_code'] . ')'); ?></td><td class="text-right"><?php echo amountFormat($owes); ?></td></tr>
                                        <?php } ?>
                                        </tbody>
                                        <tfoot><tr><th>Total</th><th class="text-right"><?php echo amountFormat($group_total); ?></th></tr></tfoot>
                                    </table>
                                </div>
                                <p class="text-muted">If you paid less than the total, it is applied to the fees from the top of this list.</p>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-md-6">
                                             <div class="form-group">
                                            <label for="payment_date_error"><?php echo $this->lang->line('date_of_payment'); ?></label><small class="req"> *</small>
                                            <input id="payment_date" name="payment_date" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('payment_date'); ?>" />
                                            <span class="text-danger"><?php echo form_error('payment_date'); ?></span>
                                        </div>
                                    </div>

                                      <div class="col-md-6">
                                          <div class="form-group">
                                            <label for="bank_from"><?php echo $this->lang->line('payment_mode'); ?></label><small class="req"> *</small>
                                            <input id="bank_from" name="bank_from" placeholder="" type="text" class="form-control"  value="<?php echo set_value('bank_from'); ?>" />
                                            <span class="text-danger"><?php echo form_error('bank_from'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label for="bank_account_transferred"><?php echo $this->lang->line('payment_from'); ?></label><small class="req"> *</small>
                                            <input id="bank_account_transferred" name="bank_account_transferred" placeholder="" type="text" class="form-control"  value="<?php echo set_value('bank_account_transferred'); ?>" />
                                            <span class="text-danger"><?php echo form_error('bank_account_transferred'); ?></span>
                                        </div>
                                    </div>
                                      <div class="col-md-6">
                                           <div class="form-group">
                                            <label for="reference"><?php echo $this->lang->line('reference'); ?>   </label>
                                            <input id="reference" name="reference" placeholder="" type="text" class="form-control"  value="<?php echo set_value('reference'); ?>" />
                                            <span class="text-danger"><?php echo form_error('reference'); ?></span>
                                        </div>
                                    </div>
                                </div>
                             <div class="row">
                                 <div class="col-md-6">
                                      <div class="form-group">
                                            <label for="amount"><?php echo $this->lang->line('amount_paid'); ?> (<?php echo $currency_symbol; ?>) </label><small class="req"> *</small>
                                            <input id="amount" name="amount" placeholder="" type="number" class="form-control"  value="<?php echo set_value('amount', $group_total > 0 ? round($group_total, 2) : ''); ?>" />
                                            <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                        </div>
                                 </div>
                                   <div class="col-md-6">
                                          <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('proof_of_payment'); ?></label>
                                           <input id="attachment" name="attachment" type="file" class="filestyle form-control"  />
                                            <span class="text-danger"><?php echo form_error('attachment'); ?></span>
                                        </div>
                                 </div>
                             </div>
                            </div><!-- /.box-body -->
                    <div class="box-footer">
                   <button type="submit" class="btn btn-primary  pull-right"> <?php echo $this->lang->line('save'); ?></button>
                   </div>
               </form>
                </div>
            </div><!--/.col (left) col-8 end-->
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->