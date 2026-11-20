<div class="divider"></div>
<?php if ($this->rbac->hasPrivilege('marks_import', 'can_view')) {
    ?>
    <div class="row">   
        <div class="col-md-9"> 
            <form method="POST" enctype="multipart/form-data" id="fileUploadForm">

                <div class="input-group mb10">
                    <input id="my-file-selector" data-height="34" class="dropify" type="file" accept=".xlsx,.csv">
                    <div class="input-group-btn">
                        <input type="submit" class="btn btn-primary" value="<?php echo $this->lang->line('submit') ?>" id="btnSubmit"/>
                    </div>
                </div>
        </div>  
    </form>

    <div class="col-md-3"> 
        <a class="btn btn-primary pull-right" href="<?php echo site_url('admin/examgroup/exportformat') ?>" target="_blank"><i class="fa fa-download"></i> <?php echo $this->lang->line('export_sample'); ?></a>
    </div>
    </div>    
    <?php
}
?>

<form method="post" action="<?php echo site_url('admin/examgroup/entrymarks') ?>" id="assign_form1111">
    <input type="hidden" id="max_mark" value="<?php echo $subject_detail->max_marks; ?>">
    <?php
    if (isset($resultlist) && !empty($resultlist)) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" name="exam_group_class_batch_exam_subject_id" value="<?php echo $exam_group_class_batch_exam_subject_id; ?>">
                <div class="eg-bar">
                    <input type="search" class="form-control input-sm" id="eg_search" placeholder="Find a student..." aria-label="Find a student" style="max-width:240px">
                    <span class="eg-pill" id="eg_count"></span>
                    <span class="eg-help"><i class="fa fa-keyboard-o"></i> Enter: next student &nbsp;·&nbsp; max <?php echo (float) $subject_detail->max_marks; ?> marks</span>
                </div>
                <div class="table-responsive eg-scroll">
                    <table class="table table-striped eg-table">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('admission_no'); ?></th>
                                <th><?php echo $this->lang->line('roll_number'); ?></th>
                                <th><?php echo $this->lang->line('student_name'); ?></th> 
                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                <th><?php echo $this->lang->line('category'); ?></th>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <th><?php echo $this->lang->line('attendance'); ?></th>
                                <th><?php echo $this->lang->line('marks') ?></th>
                                <th><?php echo $this->lang->line('note') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (empty($resultlist)) {
                                ?>
                                <tr>
                                    <td colspan="7" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>
                                </tr>
                                <?php
                            } else {

                                foreach ($resultlist as $student) {

                                    ?>
                                    <tr class="std_adm_<?php echo $student['admission_no']; ?> eg-row" data-text="<?php echo html_escape(strtolower($student['firstname'] . ' ' . $student['lastname'] . ' ' . $student['admission_no'])); ?>">
                                <input type="hidden" name="prev_id[<?php echo $student['exam_group_class_batch_exam_students_id'] ?>]" value="<?php echo $student['exam_group_exam_result_id'] ?>">
                                <input type="hidden" name="exam_group_student_id[]" value="<?php echo $student['exam_group_class_batch_exam_students_id'] ?>">
                                <td><?php echo $student['admission_no']; ?></td>
                                <td><?php  $roll_no=($student['use_exam_roll_no'])?$student['exam_roll_no']:$student['roll_no']; 
                                echo ($roll_no != 0) ? $roll_no : '-'; ?></td>
                                <td><?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname);?></td>
                                <td><?php echo $student['father_name']; ?></td>
                                <td><?php echo $student['category']; ?></td>
                                <td><?php echo $this->lang->line(strtolower($student['gender'])); ?></td>
                                <td>
                                    <div>
                                        <?php
                                      $attendance_status=0;
                                        foreach ($attendence_exam as $attendence_key => $attendence_value) {
                                            $chk = ($student['exam_group_exam_result_attendance'] == $attendence_value) ? "checked='checked'" : "";
                                            $attendance_status = ($student['exam_group_exam_result_attendance'] == $attendence_value) ? 1 : 0;
                                            ?>
                                            <label class="checkbox-inline"><input type="checkbox" class="attendance_chk" name="exam_group_student_attendance_<?php echo $student['exam_group_class_batch_exam_students_id']; ?>" value="<?php echo $attendence_value; ?>" <?php echo $chk; ?>><?php echo $this->lang->line($attendence_value); ?></label>
                                            <?php
                                        }
                                        ?>

                                    </div>
                                </td>
                                <td> 
                                    <input type="number" class="marksssss form-control" name="exam_group_student_mark_<?php echo $student['exam_group_class_batch_exam_students_id']; ?>" value="<?php echo $student['exam_group_exam_result_get_marks']; ?>" step="any" min="0" placeholder="/<?php echo (float) $subject_detail->max_marks; ?>" <?php echo ($attendance_status) ? "disabled":"" ?>>
                                </td>
                                <td> <input type="text" class="form-control note" name="exam_group_student_note_<?php echo $student['exam_group_class_batch_exam_students_id']; ?>" value="<?php echo $student['exam_group_exam_result_note']; ?>"></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody></table>
                </div>

                <?php if ($this->rbac->hasPrivilege('exam_marks', 'can_edit')) { ?>
                    <button type="submit" class="allot-fees btn btn-primary btn-sm pull-right" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait'); ?>"><?php echo $this->lang->line('save'); ?>
                    </button>
                <?php } ?>
                <br/>
                <br/>
            </div>
        </div>
        <?php
    } else {
        ?>

        <div class="alert alert-info">
            <?php echo $this->lang->line('no_record_found'); ?>
        </div>
        <?php
    }
    ?>
</form>
<style>
    .eg-bar{display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;margin-bottom:10px}
    .eg-pill{display:inline-flex;align-items:center;gap:6px;background:#f1f5f9;border-radius:999px;padding:5px 12px;font-size:13px;font-weight:600;color:#334155}
    .eg-pill.ok{background:#dcfce7;color:#15803d}.eg-pill.warn{background:#fef3c7;color:#92400e}
    .eg-help{font-size:12px;color:#64748b}
    .eg-scroll{max-height:62vh;overflow:auto;border:1px solid #e5e9f0;border-radius:12px}
    .eg-table{margin:0}
    .eg-table thead th{position:sticky;top:0;z-index:2;background:#f8fafc}
    .eg-table input.marksssss{width:96px;text-align:center;font-weight:600}
    .eg-table input.marksssss.eg-bad{border-color:#dc2626;background:#fef2f2;color:#b91c1c}
    .eg-table tr.eg-done td:first-child{box-shadow:inset 3px 0 0 #22c55e}
</style>
<script>
(function ($) {
    var form = $('#assign_form1111'), MAX = parseFloat($('#max_mark').val()) || 0;
    function check() {
        var rows = form.find('tr.eg-row'), done = 0, bad = 0;
        rows.each(function () {
            var tr = $(this), inp = tr.find('input.marksssss'), v = inp.val(), away = tr.find('.attendance_chk:checked').length > 0;
            var isBad = v !== '' && !away && (isNaN(parseFloat(v)) || parseFloat(v) < 0 || (MAX && parseFloat(v) > MAX));
            inp.toggleClass('eg-bad', isBad).attr('title', isBad ? 'Must be between 0 and ' + MAX : '');
            bad += isBad ? 1 : 0;
            var ok = away || (v !== '' && !isBad); done += ok ? 1 : 0; tr.toggleClass('eg-done', ok);
        });
        $('#eg_count').attr('class', 'eg-pill ' + (bad ? 'warn' : (done === rows.length ? 'ok' : '')))
            .html('<i class="fa ' + (bad ? 'fa-exclamation-triangle' : 'fa-check-circle') + '"></i> ' + done + ' of ' + rows.length + ' students done' + (bad ? ' · ' + bad + ' out of range' : ''));
    }
    form.on('input change', 'input.marksssss, .attendance_chk', check);
    form.on('keydown', 'input.marksssss', function (e) {
        var down = e.key === 'Enter' || e.key === 'ArrowDown', up = e.key === 'ArrowUp';
        if (!down && !up) { return; }
        e.preventDefault();
        var tr = $(this).closest('tr'), next = down ? tr.nextAll('tr.eg-row:visible').first() : tr.prevAll('tr.eg-row:visible').first();
        next.find('input.marksssss:enabled').focus().select();
    });
    $('#eg_search').on('input', function () {
        var q = this.value.toLowerCase().trim();
        form.find('tr.eg-row').each(function () { this.style.display = !q || this.getAttribute('data-text').indexOf(q) > -1 ? '' : 'none'; });
    });
    check();
})(jQuery);
</script>
