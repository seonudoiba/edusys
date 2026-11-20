<form method="post" action="<?php echo site_url('cbseexam/observation/add_observation_term_marks') ?>" id="allot_exam_student">
    <input type="hidden" name="cbse_observation_term_id" value="<?php echo $cbse_observation_term_id; ?>">
    <?php
if (isset($observationParamsList) && (!empty($observationParamsList) && !empty($studentlist))) {
    ?>
        <div class="ob-fill" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;background:#f8fafc;border:1px solid #e5e9f0;border-radius:12px;padding:10px 14px;margin-top:10px">
            <b><i class="fa fa-magic"></i> Fill ratings</b>
            <select class="form-control input-sm" id="ob_fill_col" style="width:auto">
                <option value="">Every rating</option>
                <?php foreach ($observationParamsList as $param_value) { ?><option value="<?php echo (int) $param_value->cbse_observation_subparameter_id; ?>"><?php echo html_escape($param_value->cbse_observation_parameter_name); ?></option><?php } ?>
            </select>
            <span>with</span>
            <input type="number" class="form-control input-sm" id="ob_fill_val" style="width:80px" min="0" step="any" value="3">
            <label style="margin:0;font-weight:500;font-size:13px"><input type="checkbox" id="ob_fill_all"> also replace ratings already entered</label>
            <button type="button" class="btn btn-primary btn-sm" id="ob_fill_go">Fill</button>
            <span class="text-muted small">Then change any student who differs, and Save.</span>
        </div>
        <script>
        (function ($) {
            $('#ob_fill_go').on('click', function () {
                var col = $('#ob_fill_col').val(), v = parseFloat($('#ob_fill_val').val()), all = $('#ob_fill_all').prop('checked'), n = 0, over = 0;
                if (isNaN(v) || v < 0) { errorMsg('Enter the rating to fill in.'); return; }
                $('#allot_exam_student input.marksssss').each(function () {
                    if (col && !new RegExp('_' + col + '$').test(this.name)) { return; }
                    if (!all && this.value !== '') { return; }
                    var max = parseFloat(this.getAttribute('data-marks'));
                    if (!isNaN(max) && v > max) { over++; return; }
                    this.value = v; n++;
                });
                if (over) { errorMsg(over + ' rating(s) not filled: ' + v + ' is above their maximum.'); }
                if (n) { successMsg(n + ' rating(s) filled. Check them, then Save.'); }
            });
        })(jQuery);
        </script>
        <div class="row">
            <div class="col-md-12">
                <div class=" table-responsive ptt10">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('admission_no'); ?></th>
                                <th><?php echo $this->lang->line('student_name'); ?></th>
                                <th><?php echo $this->lang->line('class'); ?></th>
                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <?php

    foreach ($observationParamsList as $param_key => $param_value) {

        ?>
        <input type="hidden" name="cbse_observation_parameters[]" value="<?php echo  $param_value->cbse_observation_subparameter_id;?>">
 <th><?php echo $param_value->cbse_observation_parameter_name . " (" . $this->lang->line('max_marks') . $param_value->maximum_marks . ")" ?></th>
                                 	<?php
}
    ?>


                            </tr>
                        </thead>
                        <tbody>
                        <?php

    if (!empty($studentlist)) {
$row=1;
        foreach ($studentlist as $student_key => $student_value) {        	

            ?>
	<tr>
		<input type="hidden" name="row[]" value="<?php echo $row; ?>">
		<input type="hidden" name="student_session_<?php echo $row;?>" value="<?php echo $student_value->student_session_id; ?>">
  <td>
  	<?php echo $student_value->admission_no; ?></td>
  <td><?php echo $this->customlib->getFullName($student_value->firstname, $student_value->middlename, $student_value->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></td>
                                        <td><?php echo $student_value->class." (".$student_value->section.")"; ?></td>
                                        <td><?php echo $student_value->father_name; ?></td>

                                        <td><?php echo $this->lang->line(strtolower($student_value->gender)); ?></td>
                                                       <?php

    foreach ($observationParamsList as $param_key => $param_value) {
            ?>
 <td>
 	<input type="hidden" name="old_cbse_observation_term_student_subparameter_id_<?php echo $student_value->student_session_id."_".$param_value->cbse_observation_subparameter_id ?>" value="<?php echo $student_value->params[$param_value->cbse_observation_parameter_id]['cbse_observation_term_student_subparameter_id']; ?>">
 	
 <input type="number" data-marks="<?php echo $param_value->maximum_marks;?>" class="form-control marksssss" name="param_value_<?php echo $student_value->student_session_id."_".$param_value->cbse_observation_subparameter_id ?>" value="<?php echo $student_value->params[$param_value->cbse_observation_parameter_id]['obtain_marks']; ?>">
 </td>
                                 	<?php
}
    ?>

</tr>
	<?php
	$row++;
}
    }
    ?>
                        </tbody>
                    </table>
                </div>
              
                    <button type="submit" class="btn btn-primary btn-sm pull-right" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait.."><?php echo $this->lang->line('save'); ?>
                    </button>            
            </div>
        </div>
        <?php
} else {
    ?>
        <div class="alert alert-danger mt10 mb0"><?php echo $this->lang->line('no_student_assigned_in_examination_on_this_term'); ?> </div>
        <?php
}
?>
</form>