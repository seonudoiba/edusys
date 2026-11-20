<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/cbse-exam.css">
<form method="post" role="form" id="addTeacherRemark">
    <?php
    if (isset($resultlist) && !empty($resultlist)) {        
        ?>
        <style>
            .cm-bar{display:flex;flex-wrap:wrap;gap:10px;align-items:center;background:#f8fafc;border:1px solid #e5e9f0;border-radius:12px;padding:10px 14px;margin-bottom:12px}
            .cm-bar label{margin:0;font-weight:500;font-size:13px;display:inline-flex;gap:6px;align-items:center}
            .cm-bar .cm-gap{flex:1}
            .cm-pct{display:block;font-size:11px;color:#64748b}
        </style>
        <?php if ($this->rbac->hasPrivilege('cbse_exam_teacher_remark', 'can_edit')) { ?>
        <div class="cm-bar">
            <button type="button" class="btn btn-primary btn-sm" id="cm_fill"><i class="fa fa-magic"></i> Fill comments</button>
            <label><input type="checkbox" id="cm_replace"> Replace comments already written</label>
            <label><input type="checkbox" id="cm_weak" checked> Name the weakest subject</label>
            <span class="cm-gap"></span>
            <a href="<?php echo site_url('cbseexam/exam/comments'); ?>" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-commenting-o"></i> Comment bank</a>
        </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <div class="scroll-area overflow-auto">
                        <table class="table table-striped">
                            <thead class="heading-sticky">
                                <tr class="active">
                                    <th><?php echo $this->lang->line('admission_no'); ?></th>
                                    <th><?php echo $this->lang->line('roll_no'); ?></th>
                                    <th><?php echo $this->lang->line('class'); ?></th>
                                    <th><?php echo $this->lang->line('section'); ?></th>
                                    <th><?php echo $this->lang->line('student_name'); ?></th>                                
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('marks'); ?><br/><?php echo "(".two_digit_float($total_exam_marks).")"; ?></th>
                                    <th><?php echo $this->lang->line('class_teacher_remark') ?: $this->lang->line('remark'); ?></th>
                                    <th>Principal's remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($resultlist)) {
                                    ?>
                                    <tr>
                                        <td colspan="9" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>
                                    </tr>
                                    <?php
                                } else {
                                    
                                    foreach ($resultlist as $student) {

                                    ?>
                                    <?php $cm_pct = (!is_null($student['gain_total_marks']) && $total_exam_marks > 0) ? round($student['gain_total_marks'] * 100 / $total_exam_marks, 1) : null;
                                    $cm_subs = isset($subject_scores[(int) $student['exam_student_id']]) ? $subject_scores[(int) $student['exam_student_id']] : array(); ?>
                                    <tr class="cbse_exam_student_id_<?php echo $student['exam_student_id']; ?> cm-row" data-pct="<?php echo $cm_pct === null ? '' : $cm_pct; ?>" data-weak="<?php echo html_escape($cm_subs && $cm_subs[0][1] < 50 ? $cm_subs[0][0] : ''); ?>">
                                        <td><?php echo $student['admission_no']; ?></td>
                                        <td><?php echo $student['roll_no']; ?></td>
                                        <td><?php echo $student['class_name']; ?></td>
                                        <td><?php echo $student['section_name']; ?></td>                                
                                        <td><?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname);?></td>
                                                                 
                                        <td><?php echo $this->lang->line(strtolower($student['gender'])); ?></td>     
                                        <td><?php echo is_null($student['gain_total_marks']) ? '<span class="text text-danger">'.$this->lang->line('n_a').'</span>' : $student['gain_total_marks'] . ($cm_pct !== null ? '<span class="cm-pct">' . $cm_pct . '%</span>' : ''); ?></td>   
                                        <td class="white-space-nowrap"> 
                                            <input type="hidden" class="marksssss form-control w-sm-150" name="exam_student_id[]" value="<?php echo $student['exam_student_id']; ?>">
                                           
                                            <input type="text" class="marksssss form-control w-sm-150" name="teacher_remark[<?php echo $student['exam_student_id']; ?>]" value="<?php echo html_escape((string) $student['remark']); ?>">
                                        </td>
                                        <td class="white-space-nowrap">
                                            <input type="text" class="form-control w-sm-150" maxlength="1000" name="principal_remark[<?php echo $student['exam_student_id']; ?>]" value="<?php echo html_escape((string) $student['principal_remark']); ?>">
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>    
                </div>

                <?php if ($this->rbac->hasPrivilege('cbse_exam_teacher_remark', 'can_edit')) { ?>
                    <div class="modal-footer clearboth mx-nt-lr-15 pb0">
                        <button type="submit" class="allot-fees btn btn-primary pull-right" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait.."><?php echo $this->lang->line('save'); ?>
                        </button>
                    </div>    
                <?php } ?>
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

<script>
(function ($){
    "use strict";
    
    var bank = <?php echo json_encode(isset($comment_bank) ? $comment_bank : array('teacher' => array(), 'principal' => array())); ?>;
    function pick(list, pct) {
        for (var i = 0; i < list.length; i++) { if (pct >= list[i][0] - 0.0001 && pct <= list[i][1] + 0.0099) { return list[i][2]; } }
        return '';
    }
    $('#cm_fill').on('click', function () {
        var replace = $('#cm_replace').prop('checked'), weak = $('#cm_weak').prop('checked'), n = 0;
        $('#addTeacherRemark tr.cm-row').each(function () {
            var pct = this.getAttribute('data-pct');
            if (pct === '') { return; }
            pct = parseFloat(pct);
            var t = $(this).find('input[name^="teacher_remark"]'), p = $(this).find('input[name^="principal_remark"]');
            var tc = pick(bank.teacher, pct), pc = pick(bank.principal, pct), w = this.getAttribute('data-weak');
            if (tc && weak && w) { tc += ' Needs to improve in ' + w + '.'; }
            if (tc && (replace || !$.trim(t.val()))) { t.val(tc); n++; }
            if (pc && (replace || !$.trim(p.val()))) { p.val(pc); n++; }
        });
        if (window.successMsg) { successMsg(n ? n + ' comment(s) filled in. Check them, then Save.' : 'Nothing to fill: every comment is written (tick Replace to rewrite them) or no scores yet.'); }
    });

    $("#addTeacherRemark").on('submit', (function (e) {
        e.preventDefault();
        var $this = $(this).find("button[type=submit]:focus");
       
        $.ajax({
            url: base_url+"cbseexam/exam/addteacherremark",
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $this.button('loading');
            },
            success: function (res)
            {
                window.location.reload(true);
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }
        });
    }));
    
})(jQuery);
</script>