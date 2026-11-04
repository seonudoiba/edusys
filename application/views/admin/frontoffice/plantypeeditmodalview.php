<form action="<?php echo site_url('admin/enquiry') ?>" id="myForm1" method="post" class="ptt10">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="pwd"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                <input type="text" id="name_add" autocomplete="off" class="form-control"
                    value="<?php echo set_value('name', $plan_type_data['name']); ?>" name="name">
                <span id="name_add_error" class="text-danger"></span>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="exampleInputEmail1">
                    <?php echo $this->lang->line('status'); ?>
                </label><small class="req"> *</small>
                <select id="status" name="status" class="form-control">
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <option value="1" <?php echo set_value('status', (isset($plan_type_data['status']) && $plan_type_data['status'] == '1')) ? 'selected' : ''; ?>>
                        <?php echo $this->lang->line('enable'); ?>
                    </option>
                    <option value="2" <?php echo set_value('status', (isset($plan_type_data['status']) && $plan_type_data['status'] == '2')) ? 'selected' : ''; ?>>
                        <?php echo $this->lang->line('disable'); ?>
                    </option>
                </select>
                <span class="text-danger">
                    <?php echo form_error('status'); ?>
                </span>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="pwd"><?php echo $this->lang->line('sort_order'); ?></label><small class="req"> *</small>
                <small class="req"><span id="phone_error_message"></span></small>
                <input id="number" autocomplete="off" name="sort_order" placeholder="" type="text" class="form-control"
                    value="<?php echo set_value('sort_order', $plan_type_data['sort_order']); ?>" />
            </div>
        </div>
    </div><!--./row-->
    <div class="row">
        <div class="box-footer row">
            <a onclick="postRecord(<?php echo $plan_type_data['id'] ?>)"
                class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></a>
        </div>
    </div>

</form>