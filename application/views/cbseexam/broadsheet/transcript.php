<?php
/* Report Cards > Student transcript (Broadsheet::transcript): every exam across terms and sessions. */
$tr_n = function ($v) { return $v === null || $v === '' ? '–' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$tr_brand = $this->customlib->brand();
?>
<style>
    .tr-search{display:flex;gap:8px;max-width:520px}
    .tr-head{display:flex;align-items:center;gap:16px;border-bottom:3px solid var(--ui-brand,#1f4e79);padding-bottom:12px;margin-bottom:16px}
    .tr-head img{height:64px;width:auto;max-width:120px;object-fit:contain}
    .tr-head .tr-school{font-size:20px;font-weight:800;color:var(--ui-brand,#1f4e79)}
    .tr-head small{color:#64748b}
    .tr-student{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:10px;margin-bottom:16px}
    .tr-student div{background:#f8fafc;border-radius:10px;padding:8px 12px}
    .tr-student small{display:block;color:#64748b;font-size:11px;text-transform:uppercase}
    .tr-session{margin:22px 0 8px;font-weight:800;font-size:16px;display:flex;align-items:center;gap:10px}
    .tr-session .label{font-size:12px}
    .tr-exam{border:1px solid #e5e9f0;border-radius:12px;margin-bottom:12px;overflow:hidden;page-break-inside:avoid}
    .tr-exam-h{display:flex;flex-wrap:wrap;gap:10px;align-items:center;background:#f1f5f9;padding:8px 12px}
    .tr-exam-h b{flex:1}
    .tr-exam table{margin:0}
    .tr-exam td,.tr-exam th{padding:6px 10px!important;font-size:13px}
    @media print{.tr-noprint,.main-header,.main-sidebar,.main-footer{display:none!important}.content-wrapper{margin:0!important}.tr-exam-h{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border tr-noprint">
                <h3 class="box-title"><i class="fa fa-graduation-cap"></i> Student transcript</h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo site_url('cbseexam/broadsheet'); ?>" class="btn btn-sm btn-default"><i class="fa fa-table"></i> Broadsheet</a>
                    <?php if (!empty($student)) { ?><button type="button" class="btn btn-sm btn-primary" onclick="window.print()"><i class="fa fa-print"></i> Print / PDF</button><?php } ?>
                </div>
            </div>
            <div class="box-body">
                <form method="get" action="<?php echo site_url('cbseexam/broadsheet/transcript'); ?>" class="tr-search tr-noprint">
                    <input type="search" name="q" class="form-control" value="<?php echo html_escape($q); ?>" placeholder="Admission no or student name" required>
                    <button class="btn btn-primary"><i class="fa fa-search"></i> Find</button>
                </form>
                <?php if ($q !== '' && empty($student)) { ?>
                    <?php if (!$matches) { ?>
                        <div class="alert alert-info" style="margin-top:14px">No student matches "<?php echo html_escape($q); ?>".</div>
                    <?php } else { ?>
                        <div class="list-group" style="margin-top:14px;max-width:620px">
                            <?php foreach ($matches as $m) { ?>
                                <a class="list-group-item" href="<?php echo site_url('cbseexam/broadsheet/transcript/' . $m['id']); ?>"><b><?php echo html_escape(trim($m['firstname'] . ' ' . $m['middlename'] . ' ' . $m['lastname'])); ?></b> <span class="text-muted"><?php echo html_escape($m['admission_no'] . ($m['class'] ? ' · ' . $m['class'] . ' (' . $m['section'] . ')' : '')); ?></span></a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>

                <?php if (!empty($student)) { ?>
                    <div style="margin-top:18px">
                        <div class="tr-head">
                            <?php if (!empty($tr_brand['logo'])) { ?><img src="<?php echo html_escape($tr_brand['logo']); ?>" alt=""><?php } ?>
                            <div>
                                <div class="tr-school"><?php echo html_escape($tr_brand['name']); ?></div>
                                <small><?php echo html_escape($tr_brand['address']); ?></small>
                                <div style="font-weight:700;margin-top:4px;letter-spacing:.08em">ACADEMIC TRANSCRIPT</div>
                            </div>
                        </div>
                        <div class="tr-student">
                            <div><small>Student</small><b><?php echo html_escape(trim($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname'])); ?></b></div>
                            <div><small>Admission no</small><b><?php echo html_escape($student['admission_no']); ?></b></div>
                            <div><small>Current class</small><b><?php echo html_escape($student['class'] ? $student['class'] . ' (' . $student['section'] . ')' : '–'); ?></b></div>
                            <div><small>Date of birth</small><b><?php echo $student['dob'] && $student['dob'] !== '0000-00-00' ? date('j M Y', strtotime($student['dob'])) : '–'; ?></b></div>
                            <div><small>Gender</small><b><?php echo html_escape($student['gender'] ?: '–'); ?></b></div>
                        </div>
                        <?php if (empty($exams)) { ?>
                            <div class="alert alert-info">No Report Cards exams recorded for this student yet.</div>
                        <?php } else {
                            $tr_last = null;
                            foreach ($exams as $x) {
                                if ($x['session'] !== $tr_last) {
                                    $tr_last = $x['session']; ?>
                                    <div class="tr-session"><i class="fa fa-calendar"></i> <?php echo html_escape($x['session'] ?: 'Session'); ?>
                                        <?php if (isset($sessions[$x['session']])) { ?><span class="label label-primary">Session average <?php echo $tr_n($sessions[$x['session']]); ?>%</span><?php } ?>
                                    </div>
                                <?php } ?>
                                <div class="tr-exam">
                                    <div class="tr-exam-h">
                                        <b><?php echo html_escape($x['exam_name'] . ($x['term_name'] ? ' — ' . $x['term_name'] : '')); ?></b>
                                        <span class="text-muted"><?php echo html_escape($x['class'] . ' (' . $x['section'] . ')'); ?></span>
                                        <span class="label label-default"><?php echo $tr_n($x['percentage']); ?>%</span>
                                        <?php if ($x['grade']) { ?><span class="label label-success"><?php echo html_escape($x['grade']); ?></span><?php } ?>
                                        <?php if ($x['rank']) { ?><span class="label label-info">Position <?php echo (int) $x['rank']; ?></span><?php } ?>
                                        <?php if (!$x['is_publish']) { ?><span class="label label-warning" title="Results of this exam are not published yet">Not published</span><?php } ?>
                                    </div>
                                    <?php if ($x['subjects']) { ?>
                                        <table class="table table-striped">
                                            <thead><tr><th>Subject</th><th class="text-center">Score</th><th class="text-center">Out of</th><th class="text-center">%</th><th class="text-center">Grade</th></tr></thead>
                                            <tbody>
                                                <?php foreach ($x['subjects'] as $s) { ?>
                                                    <tr><td><?php echo html_escape($s['name']); ?></td><td class="text-center"><b><?php echo $tr_n($s['got']); ?></b></td><td class="text-center"><?php echo $tr_n($s['max']); ?></td><td class="text-center"><?php echo $tr_n($s['pct']); ?></td><td class="text-center"><?php echo html_escape($s['grade']); ?></td></tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    <?php } else { ?><p class="text-muted" style="padding:10px 12px;margin:0">No scores entered.</p><?php } ?>
                                </div>
                            <?php }
                        } ?>
                        <p class="text-muted small">Printed <?php echo date('j F Y'); ?>.</p>
                    </div>
                <?php } elseif ($q === '') { ?>
                    <div class="text-center text-muted" style="padding:50px"><i class="fa fa-graduation-cap fa-3x"></i><p style="margin-top:10px">Find a student to see every exam they sat, across terms and sessions.</p></div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
