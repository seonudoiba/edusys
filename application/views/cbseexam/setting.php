<?php $this->load->view('layout/cbseexam_css.php'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('setting'); ?></h1>
    </section>
    <section class="content">
        <?php if ($this->session->flashdata('msg')) { echo $this->session->flashdata('msg'); } ?>
        <div class="row">
            <div class="col-md-7">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-flag"></i> Nigerian report cards</h3>
                    </div>
                    <div class="box-body">
                        <p>Terms, CA and exam scores, grade scales and affective/psychomotor ratings as Nigerian schools use them. Adding them is safe: anything already there is kept, and you can edit or delete any of them afterwards.</p>
                        <table class="table table-condensed">
                            <tbody>
                                <?php
                                $rows = array(
                                    'terms' => array('Terms', implode(', ', array_column($terms, 0))),
                                    'schemes' => array('Assessment schemes', implode('; ', array_map(function ($name, $types) {
                                        return $name;
                                    }, array_keys($schemes), $schemes))),
                                    'grades' => array('Grade scales', implode('; ', array_map(function ($name, $ranges) {
                                        return $name . ' (' . implode(', ', array_map(function ($r) { return $r[0] . ' ' . $r[1] . '-' . $r[2]; }, $ranges)) . ')';
                                    }, array_keys($grades), $grades))),
                                    'ratings' => array('Ratings (1-5)', implode('; ', array_map(function ($name, $items) {
                                        return $name . ': ' . implode(', ', $items);
                                    }, array_keys($ratings), $ratings))),
                                );
                                foreach ($rows as $key => $row) {
                                    list($have, $total) = $status[$key]; ?>
                                    <tr>
                                        <th style="width:150px"><?php echo $row[0]; ?></th>
                                        <td><?php echo html_escape($row[1]); ?></td>
                                        <td style="width:90px" class="text-right">
                                            <?php if ($have >= $total) { ?><span class="label label-success">Set up</span>
                                            <?php } elseif ($have) { ?><span class="label label-warning"><?php echo $have . ' of ' . $total; ?></span>
                                            <?php } else { ?><span class="label label-default">Not yet</span><?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($can_install) { ?>
                            <form method="post" action="<?php echo site_url('cbseexam/setting/nigeria'); ?>">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <?php if ($examinations_menu_shown && $this->rbac->hasPrivilege('general_setting', 'can_edit')) { ?>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="hide_examinations" value="1"> Also hide the other <b>Examinations</b> menu (its results stay; System Settings &gt; Sidebar Menu shows it again)</label>
                                    </div>
                                <?php } ?>
                                <button type="submit" class="btn btn-primary">Add the Nigerian defaults</button>
                            </form>
                        <?php } else { ?>
                            <p class="text-muted">Ask your super admin to add these: it needs permission to add terms, assessments, grades and observations.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="box box-solid">
                    <div class="box-header with-border"><h3 class="box-title">A termly report, step by step</h3></div>
                    <div class="box-body">
                        <ol style="padding-left:18px">
                            <li><b>Exam</b>: create e.g. "First Term Examination" in First Term, with a CA + Exam scheme and the WAEC or Primary grade scale; add the classes.</li>
                            <li><b>Subjects</b>: add the subjects and their dates; <b>Assign / View Student</b>: add the students.</li>
                            <li><b>Marks</b>: enter each CA and the exam (online exam scores can be sent in: Online Exam &gt; Send scores to report card).</li>
                            <li><b>Attendance and remarks</b>: days present, the class teacher's and the principal's remarks.</li>
                            <li><b>Ratings</b>: Assign Observation for the term, then rate each student 1-5.</li>
                            <li><b>Template</b>: a <b>Term Wise</b> template with the term and its exam (set the next term's start date there); <b>All Terms</b> for the end-of-session report.</li>
                            <li><b>Generate rank</b>, then <b>Print Marksheet</b>.</li>
                        </ol>
                    </div>
                    <div class="box-footer text-muted small"><?php echo $this->lang->line('version') . ' ' . $version; ?></div>
                </div>
            </div>
        </div>
    </section>
</div>
