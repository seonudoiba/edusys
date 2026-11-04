<style type="text/css">
    .info-box {
        padding: 20px 10px;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row" style="padding:10px;">
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <a href="<?php echo site_url('admin/schools') ?>">
                        <span class="info-box-icon bg-green"><i class="fa fa-graduation-cap"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?php echo $this->lang->line('dash_school'); ?></span>
                            <span class="info-box-number"><?php echo $school_count; ?></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <a href="<?php echo site_url('admin/subscription_list') 
                                ?>">
                        <span class="info-box-icon bg-red"><i class="fa fa-credit-card"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?php echo $this->lang->line('dash_payment'); ?></span>
                            <span class="info-box-number"><?php echo $payment_count; ?></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <a href="<?php echo site_url('admin/schools') ?>">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?php echo $this->lang->line('dash_active_school'); ?></span>
                            <span class="info-box-number"><?php echo $active_school_count ? $active_school_count : 0; ?></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <a href="<?php echo site_url('admin/schools') ?>">
                        <span class="info-box-icon bg-olive"><i class="fa fa-times-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?php echo $this->lang->line('dash_unactive_school'); ?></span>
                            <span class="info-box-number"><?php echo $unactive_school_count ? $unactive_school_count : 0; ?></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
</div>