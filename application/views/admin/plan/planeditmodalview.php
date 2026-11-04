<form id="myForm1" method="post" class="ptt10">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="pwd"><?php echo $this->lang->line('plan_title'); ?></label><small class="req"> *</small>
                <input type="text" id="title" autocomplete="off" class="form-control"
                    value="<?php echo set_value('title', $plan_data['title']); ?>" name="title">
                <span id="name_add_error" class="text-danger"></span>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="pwd"><?php echo $this->lang->line('plan_text'); ?></label><small class="req"> *</small>
                <small class="req"><span id="phone_error_message"></span></small>
                <input id="number" autocomplete="off" name="text" placeholder="" type="text" class="form-control"
                    value="<?php echo set_value('text', $plan_data['text']); ?>" />
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="pwd"><?php echo $this->lang->line('plan_days'); ?></label><small
                    class="req"> *</small> <small class="req"><span
                        id="plan_days"></span></small>
                <input id="number" autocomplete="off" name="days" placeholder="" type="text"
                    class="form-control" value="<?php echo set_value('days', $plan_data['days']); ?>" />
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="exampleInputEmail1">
                    <?php echo $this->lang->line('status'); ?>
                </label><small class="req"> *</small>
                <select id="status" name="status" class="form-control">
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <option value="1" <?php echo set_value('status', (isset($plan_data['status']) && $plan_data['status'] == '1')) ? 'selected' : ''; ?>>
                        <?php echo $this->lang->line('enable'); ?>
                    </option>
                    <option value="2" <?php echo set_value('status', (isset($plan_data['status']) && $plan_data['status'] == '2')) ? 'selected' : ''; ?>>
                        <?php echo $this->lang->line('disable'); ?>
                    </option>
                </select>
                <span class="text-danger">
                    <?php echo form_error('status'); ?>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="plan_type_list"><?php echo $this->lang->line('plan_type_list'); ?></label><small
                        class="req"> *</small>

                    <select name="plan_type_list[]" multiple class="form-control select2" id="plan_type_list111">
                        <?php foreach ($plan_type_list as $value => $label): ?>
                            <option value="<?php echo $label['id']; ?>" <?php echo (in_array($label['id'], $list)) ? 'selected' : ''; ?>>
                                <?php echo $label['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <span id="plan_type_list_error" class="text-danger"></span>
                </div>
            </div>
        </div><!--./row-->
        <div class="col-sm-4">
            <div class="form-group">
                <label for="pwd"><?php echo $this->lang->line('plan_price'); ?></label><small class="req"> *</small>
                <input type="text" id="price" autocomplete="off" class="form-control"
                    value="<?php echo set_value('price', $plan_data['price']); ?>" name="price">
                <span id="name_add_error" class="text-danger"></span>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="pwd"><?php echo $this->lang->line('plan_o_price'); ?></label>
                <input type="text" id="plan_o_price" autocomplete="off" class="form-control"
                    value="<?php echo set_value('oprice', $plan_data['oprice']); ?>" name="oprice">
                <span id="name_add_error" class="text-danger"></span>
            </div>
        </div>
    </div><!--./row-->
    <div class="row">
        <div class="box-footer row">
            <a onclick="postRecord(<?php echo $plan_data['plan_id'] ?>)"
                class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></a>
        </div>
    </div>
</form>
<script>

    $(document).ready(function () {
        $('#plan_type_list111').select2({
            placeholder: 'Select plan types',
            width: '100%'
        });
    });

    function postRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/plan/editpost/' + id,
            type: 'POST',
            data: $("#myForm1").serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }
            },
        });
    }
</script>