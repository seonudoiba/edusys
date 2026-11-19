<form method="post" action="<?php echo site_url('cbseexam/exam/entrystudents') ?>" id="allot_exam_student">
    <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
    <?php
    if (isset($resultlist) && !empty($resultlist)) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class=" table-responsive ptt10">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th width="60"><label class="checkbox-inline bolds"><input type="checkbox" class="select_all"/> <?php echo $this->lang->line('all'); ?></label></th>
                                <th><?php echo $this->lang->line('student_name'); ?></th>
                                <th><?php echo $this->lang->line('admission_no'); ?></th>                                 
                                <th><?php echo $this->lang->line('class'); ?> (<?php echo $this->lang->line('section'); ?>)</th>
                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                <?php if ($sch_setting->category) { ?>
                                    <th><?php echo $this->lang->line('category'); ?></th>
                                <?php } ?>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                            </tr>
                            <?php
                            if (empty($resultlist)) {
                                ?>
                                <tr>
                                    <td colspan="7" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>
                                </tr>
                                <?php
                            } else {
                                $counter = 1; 
                                foreach ($resultlist as $student) {
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="all_students[]" value="<?php echo $student['student_session_id']; ?>">
                                            <input type="hidden" name="student_<?php echo $student['student_session_id']; ?>" value="<?php echo $student['id']; ?>">
                                            <input class="checkbox" type="checkbox" name="student_session_id[]"  value="<?php echo $student['student_session_id']; ?>" <?php if($student['exam_student_id']!=0){ echo "checked"; }?> />
                                        </td>
                                        <td><?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname);?></td>
                                        <td><?php echo $student['admission_no']; ?></td>                                        
                                        <td><?php echo $student['class']." (".$student['section'].")"; ?></td>
                                        <td><?php echo $student['father_name']; ?></td>
                                        <?php if ($sch_setting->category) { ?>
                                            <td><?php echo $student['category']; ?></td>
                                        <?php } ?>
                                        <td><?php echo $this->lang->line(strtolower($student['gender'])); ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($this->rbac->hasPrivilege('cbse_exam_assign_view_student', 'can_edit')) { ?>
                    <div class="modal-footer mx-nt-lr-15 pb0">
                        <button type="submit" class="btn btn-primary pull-right" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait.."><?php echo $this->lang->line('save'); ?>
                         </button>
                    </div>     
                <?php } ?>
            </div>
        </div>
        <?php
    } else {
        ?>        
        <?php
        // Why nobody was found (Cbseexam_exam_model::empty_reason), instead of a bare "No Record Found".
        $why = isset($empty_reason) && is_array($empty_reason) ? $empty_reason : null;
        ?>
        <div class="alert alert-warning">
            <b>No students to show.</b>
            <?php if (!$why) { ?>
                <?php echo $this->lang->line('no_record_found'); ?>
            <?php } elseif (!$why['classes']) { ?>
                This exam has no class or section. Edit the exam (pencil) and tick its class and sections.
            <?php } else { ?>
                The list shows active students of <b><?php echo html_escape(implode(', ', $why['classes'])); ?></b>
                in the session <b><?php echo html_escape($why['session']); ?></b>.
                <ul style="margin:6px 0 0 18px;padding:0">
                    <?php if ($why['other_sessions']) { ?>
                        <li>These classes have students in another session:
                            <?php $p = array(); foreach ($why['other_sessions'] as $name => $n) { $p[] = html_escape($name) . ' (' . (int) $n . ')'; } echo implode(', ', $p); ?>.
                            Switch the session at the top of the page, or promote the students into <?php echo html_escape($why['session']); ?> (Student Information &gt; Promote Students).</li>
                    <?php } ?>
                    <?php if ($why['inactive']) { ?>
                        <li><?php echo (int) $why['inactive']; ?> student(s) in these classes are disabled.</li>
                    <?php } ?>
                    <?php if (!$why['other_sessions'] && !$why['inactive']) { ?>
                        <li>No students are in these classes and sections. If the students are in other sections, edit the exam (pencil) and tick those sections too.</li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
        <?php
    }
    ?>
</form>