<?php
/* Report Cards > Broadsheet (Broadsheet::index). */
$bs_n = function ($v) { return $v === null || $v === '' ? '' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$bs_ord = function ($n) {
    $v = $n % 100;
    if ($v >= 11 && $v <= 13) {
        return $n . 'th';
    }
    $end = array(1 => 'st', 2 => 'nd', 3 => 'rd');
    return $n . (isset($end[$n % 10]) ? $end[$n % 10] : 'th');
};
?>
<style>
    .bs-filters{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
    .bs-filters .form-group{margin:0;min-width:200px}
    .bs-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin:16px 0}
    .bs-kpi{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:12px 16px}
    .bs-kpi small{display:block;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.04em}
    .bs-kpi b{font-size:22px;color:#0f172a}
    .bs-wrap{overflow:auto;max-height:72vh;border:1px solid #e5e9f0;border-radius:12px}
    .bs-table{margin:0;border-collapse:separate;border-spacing:0;font-size:13px}
    .bs-table th,.bs-table td{white-space:nowrap;padding:7px 9px!important;border-bottom:1px solid #eef2f6;text-align:center;vertical-align:middle!important}
    .bs-table thead th{position:sticky;top:0;z-index:3;background:#f1f5f9!important;color:#334155!important;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.03em;border-bottom:2px solid var(--ui-brand,#1f4e79)!important}
    .bs-table thead th small{display:block;font-weight:500;text-transform:none;color:#64748b}
    .bs-table .bs-name{position:sticky;left:0;z-index:2;background:#fff;text-align:left;min-width:190px;line-height:1.35}
    .bs-table .bs-name a{display:inline}
    .bs-table thead .bs-name{z-index:4}
    .bs-table tbody tr:nth-child(even) td{background:#f8fafc}
    .bs-table tbody tr:nth-child(even) td.bs-name{background:#f8fafc}
    .bs-table tfoot td{background:#f1f5f9;font-weight:700}
    .bs-pos{display:inline-block;min-width:38px;border-radius:999px;padding:2px 8px;background:#eef2ff;color:#3730a3;font-weight:700}
    .bs-pos.p1{background:#fef3c7;color:#92400e}
    .bs-abs{color:#b91c1c;font-weight:700;font-size:11px}
    .bs-low{color:#b91c1c}
    @media print{.bs-noprint,.main-header,.main-sidebar,.main-footer{display:none!important}.content-wrapper{margin:0!important}.bs-wrap{max-height:none;overflow:visible;border:0}.bs-table{font-size:10px}.bs-table thead th{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border bs-noprint">
                <h3 class="box-title"><i class="fa fa-table"></i> Broadsheet</h3>
                <div class="box-tools pull-right">
                    <a class="btn btn-sm btn-default" href="<?php echo site_url('cbseexam/broadsheet/transcript'); ?>"><i class="fa fa-graduation-cap"></i> Student transcript</a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-database"></i> Export all scores <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="<?php echo site_url('cbseexam/broadsheet/export_all'); ?>">This session (CSV)</a></li>
                            <li><a href="<?php echo site_url('cbseexam/broadsheet/export_all?all=1'); ?>">All sessions (CSV)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <form method="get" class="bs-filters bs-noprint">
                    <div class="form-group">
                        <label>Exam</label>
                        <select name="exam_id" class="form-control" onchange="this.form.cs && (this.form.cs.value='');this.form.submit()">
                            <option value="">Choose an exam</option>
                            <?php foreach ($exams as $e) { ?>
                                <option value="<?php echo $e['id']; ?>" <?php echo (int) $e['id'] === $exam_id ? 'selected' : ''; ?>><?php echo html_escape($e['name'] . ($e['term_name'] ? ' — ' . $e['term_name'] : '')); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php if (!empty($class_sections)) { ?>
                        <div class="form-group">
                            <label>Class</label>
                            <select name="cs" class="form-control" onchange="this.form.submit()">
                                <option value="0">All classes of the exam</option>
                                <?php foreach ($class_sections as $id => $label) { ?>
                                    <option value="<?php echo $id; ?>" <?php echo $id === $cs ? 'selected' : ''; ?>><?php echo html_escape($label); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                    <?php if ($sheet) { ?>
                        <a class="btn btn-default" href="<?php echo current_url() . '?' . http_build_query(array('exam_id' => $exam_id, 'cs' => $cs, 'export' => 'csv')); ?>"><i class="fa fa-file-excel-o"></i> Excel (CSV)</a>
                        <button type="button" class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print / PDF</button>
                    <?php } ?>
                </form>

                <?php if (!$exam_id) { ?>
                    <div class="text-center text-muted" style="padding:50px"><i class="fa fa-table fa-3x"></i><p style="margin-top:10px">Choose an exam to see every student's score in every subject, with totals, averages, grades and positions.</p></div>
                <?php } elseif ($sheet) {
                    list($subjects, $students, $stats, $grades) = $sheet;
                    $pcts = array_filter(array_column($students, 'percentage'), function ($v) { return $v !== null; });
                    uasort($students, function ($a, $b) { return (isset($a['position']) ? $a['position'] : 1e9) <=> (isset($b['position']) ? $b['position'] : 1e9) ?: strcmp($a['name'], $b['name']); });
                    $title = $exam['name'] . (isset($class_sections[$cs]) ? ' — ' . $class_sections[$cs] : ''); ?>
                    <h4 style="margin:18px 0 0;font-weight:700"><?php echo html_escape($title); ?></h4>
                    <div class="bs-kpis">
                        <div class="bs-kpi"><small>Students</small><b><?php echo count($students); ?></b></div>
                        <div class="bs-kpi"><small>Subjects</small><b><?php echo count($subjects); ?></b></div>
                        <div class="bs-kpi"><small>Class average</small><b><?php echo $pcts ? $bs_n(round(array_sum($pcts) / count($pcts), 2)) . '%' : '—'; ?></b></div>
                        <div class="bs-kpi"><small>Highest</small><b><?php echo $pcts ? $bs_n(max($pcts)) . '%' : '—'; ?></b></div>
                        <div class="bs-kpi"><small>Lowest</small><b><?php echo $pcts ? $bs_n(min($pcts)) . '%' : '—'; ?></b></div>
                    </div>
                    <?php if (!$students || !$subjects) { ?>
                        <div class="alert alert-info">No <?php echo !$subjects ? 'subjects' : 'students'; ?> in this exam yet.</div>
                    <?php } else { ?>
                    <div class="bs-wrap">
                        <table class="table bs-table" id="broadsheet">
                            <thead>
                                <tr>
                                    <th>Pos.</th>
                                    <th class="bs-name">Student<small>Admission no · class</small></th>
                                    <?php foreach ($subjects as $s) { ?><th title="<?php echo html_escape($s['name']); ?>"><?php echo html_escape($s['code'] ?: (mb_strlen($s['name']) > 12 ? mb_substr($s['name'], 0, 11) . '.' : $s['name'])); ?><small>/<?php echo $bs_n($s['max']); ?></small></th><?php } ?>
                                    <th>Total</th>
                                    <th>Average</th>
                                    <th>%</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $st) { ?>
                                    <tr>
                                        <td><?php if (isset($st['position'])) { ?><span class="bs-pos<?php echo $st['position'] === 1 ? ' p1' : ''; ?>"><?php echo $bs_ord($st['position']); ?></span><?php } ?></td>
                                        <td class="bs-name"><a href="<?php echo site_url('cbseexam/broadsheet/transcript/' . $st['student_id']); ?>" title="Transcript"><b><?php echo html_escape($st['name']); ?></b></a><br><small class="text-muted"><?php echo html_escape($st['admission_no'] . ' · ' . $st['class'] . ' (' . $st['section'] . ')'); ?></small></td>
                                        <?php foreach ($subjects as $tt => $s) {
                                            if (isset($st['absent'][$tt])) { ?><td><span class="bs-abs">ABS</span></td><?php
                                            } elseif (isset($st['marks'][$tt])) {
                                                $low = $s['max'] > 0 && $st['marks'][$tt] * 100 / $s['max'] < 40; ?>
                                                <td class="<?php echo $low ? 'bs-low' : ''; ?>"><?php echo $bs_n($st['marks'][$tt]); ?></td>
                                            <?php } else { ?><td class="text-muted">–</td><?php }
                                        } ?>
                                        <td><b><?php echo $bs_n($st['total']); ?></b><small class="text-muted">/<?php echo $bs_n($st['max']); ?></small></td>
                                        <td><?php echo $bs_n($st['average']); ?></td>
                                        <td><b><?php echo $bs_n($st['percentage']); ?></b></td>
                                        <td><?php echo html_escape($st['grade']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <?php foreach (array('avg' => 'Subject average', 'high' => 'Highest', 'low' => 'Lowest') as $k => $label) { ?>
                                    <tr>
                                        <td></td>
                                        <td class="bs-name"><?php echo $label; ?></td>
                                        <?php foreach ($subjects as $tt => $s) { ?><td><?php echo $stats[$tt] ? $bs_n($stats[$tt][$k]) : '–'; ?></td><?php } ?>
                                        <td colspan="4"></td>
                                    </tr>
                                <?php } ?>
                            </tfoot>
                        </table>
                    </div>
                    <p class="text-muted small" style="margin-top:8px">Positions by percentage of the subjects each student sat; equal scores share a position and the next is skipped. Scores below 40% in red.<?php if ($grades) { ?> Grades: <?php echo html_escape(implode(', ', array_map(function ($g) use ($bs_n) { return $g[0] . ' ' . $bs_n($g[1]) . '–' . $bs_n($g[2]); }, $grades))); ?>.<?php } ?></p>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
