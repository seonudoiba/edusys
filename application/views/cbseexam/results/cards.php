<?php
/*
 * Report cards (Results::card / pdf / email, portal, result checker). One page per student: school
 * letterhead, student details, every subject with each assessment, total, grade, remark, position and
 * class figures (and each term with the cumulative on the last term), overall average and position,
 * attendance, comments, next term, grading key. Tables only, so the PDF (mPDF) looks the same.
 * $exam, $class, $r (Class_results::results), $brand, $term_name, $session_name, $print, $pdf.
 */
$n = function ($v) { return $v === null || $v === '' ? '–' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$e = function ($v) { return html_escape((string) $v); };
$color = $brand['color'] ?: '#1f4e79';
$cols = array();
foreach ($r['subjects'] as $s) {
    foreach ($s['types'] as $t) {
        $k = mb_strtolower($t['name']);
        if (!isset($cols[$k])) {
            $cols[$k] = array('name' => $t['name'], 'max' => array());
        }
        $cols[$k]['max'][(string) (float) $t['maximum_marks']] = true;
    }
}
$cum = !empty($r['cumulative']);
$logo = $brand['logo'];
if (!empty($pdf) && $logo && strpos($logo, base_url()) === 0) {
    $path = FCPATH . substr($logo, strlen(base_url()));
    $logo = is_file($path) ? $path : $logo;
}
?>
<?php $wrap = empty($pdf) && empty($embed); ?>
<?php if ($wrap) { ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $e($class['class'] . ' ' . $term_name . ' report cards'); ?></title>
<?php } ?>
<style>
    <?php echo $wrap ? 'body' : '.rc-page'; ?>{font-family:"Segoe UI",Roboto,Arial,sans-serif;color:#0f172a;margin:0;background:<?php echo $wrap ? '#eef2f6' : '#fff'; ?>}
    .rc-page{background:#fff;max-width:210mm;margin:14px auto;padding:12mm 11mm;box-sizing:border-box;page-break-after:always}
    .rc-page:last-child{page-break-after:auto}
    table{border-collapse:collapse;width:100%}
    .rc-head td{vertical-align:middle}
    .rc-school{font-size:20px;font-weight:800;color:<?php echo $e($color); ?>;letter-spacing:.2px}
    .rc-addr{font-size:10.5px;color:#475569}
    .rc-title{background:<?php echo $e($color); ?>;color:#fff;text-align:center;font-weight:800;letter-spacing:.12em;font-size:12px;padding:6px;margin:8px 0 10px}
    .rc-info td{font-size:11px;padding:3px 6px;border:1px solid #e2e8f0}
    .rc-info td.l{color:#64748b;width:16%;background:#f8fafc}
    .rc-info td.v{font-weight:700}
    .rc-sub{margin-top:10px}
    .rc-sub th{background:<?php echo $e($color); ?>;color:#fff;font-size:9.5px;padding:5px 3px;border:1px solid <?php echo $e($color); ?>;text-transform:uppercase;letter-spacing:.03em}
    .rc-sub th small{display:block;font-weight:400;text-transform:none;opacity:.85}
    .rc-sub td{font-size:10.5px;padding:4px 3px;border:1px solid #e2e8f0;text-align:center}
    .rc-sub td.s{text-align:left;font-weight:700;padding-left:6px}
    .rc-sub tr:nth-child(even) td{background:#f8fafc}
    .rc-sub td.low{color:#b91c1c}
    .rc-sum td{font-size:11px;padding:5px 6px;border:1px solid #e2e8f0}
    .rc-sum td b{font-size:13px}
    .rc-com td{font-size:11px;padding:6px;border:1px solid #e2e8f0;vertical-align:top}
    .rc-com td.l{width:22%;color:#64748b;background:#f8fafc}
    .rc-key td{font-size:9px;padding:3px;border:1px solid #e2e8f0;text-align:center}
    .rc-sign td{font-size:10.5px;padding-top:26px;text-align:center;color:#475569}
    .rc-sign span{display:inline-block;border-top:1px solid #94a3b8;padding-top:3px;min-width:150px}
    .rc-bar{position:sticky;top:0;background:#0f172a;color:#fff;padding:10px 16px;font-size:14px;z-index:5}
    .rc-bar button{background:<?php echo $e($color); ?>;color:#fff;border:0;border-radius:8px;padding:8px 14px;font-weight:700;cursor:pointer;margin-right:10px}
    @media print{body{background:#fff}.rc-bar{display:none}.rc-page{margin:0;max-width:none;padding:8mm}th,.rc-title{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
    @page{size:A4;margin:6mm}
</style>
<?php if ($wrap) { ?></head><body>
    <?php if (!empty($print)) { ?><div class="rc-bar"><button onclick="window.print()">Print</button><?php echo count($r['students']); ?> report card<?php echo count($r['students']) === 1 ? '' : 's'; ?> · <?php echo $e($class['class'] . ' · ' . $term_name); ?></div><?php } ?>
<?php } ?>
<?php if (!$r['students']) { ?><div class="rc-page">No report card to show.</div><?php } ?>
<?php foreach ($r['students'] as $es => $st) {
    $stats = isset($r['stats'][(int) $st['section_id']]) ? $r['stats'][(int) $st['section_id']] : array('size' => 0); ?>
<div class="rc-page">
    <table class="rc-head"><tr>
        <?php if ($logo) { ?><td style="width:80px"><img src="<?php echo $e($logo); ?>" style="height:70px;width:auto;max-width:80px" alt=""></td><?php } ?>
        <td style="<?php echo $logo ? '' : 'text-align:center'; ?>">
            <div class="rc-school"><?php echo $e($brand['name']); ?></div>
            <div class="rc-addr"><?php echo $e($brand['address']); ?></div>
            <div class="rc-addr"><?php echo $e(implode('  ·  ', array_filter(array($brand['phone'] ? 'Tel: ' . $brand['phone'] : '', $brand['email'])))); ?></div>
        </td>
    </tr></table>
    <div class="rc-title"><?php echo $e(strtoupper($term_name)); ?> REPORT CARD · <?php echo $e($session_name); ?> SESSION</div>
    <table class="rc-info">
        <tr><td class="l">Name</td><td class="v" colspan="3"><?php echo $e($st['name']); ?></td><td class="l">Admission no</td><td class="v"><?php echo $e($st['admission_no']); ?></td></tr>
        <tr><td class="l">Class</td><td class="v"><?php echo $e($class['class'] . ' ' . $st['section']); ?></td><td class="l">Gender</td><td class="v"><?php echo $e($st['gender']); ?></td><td class="l">No. in class</td><td class="v"><?php echo (int) $stats['size']; ?></td></tr>
        <tr><td class="l">Days school opened</td><td class="v"><?php echo (int) $exam['total_working_days'] ?: '–'; ?></td><td class="l">Days present</td><td class="v"><?php echo $st['total_present_days'] !== null && $st['total_present_days'] !== '' ? (int) $st['total_present_days'] : '–'; ?></td>
            <td class="l">Next term begins</td><td class="v"><?php echo !empty($exam['next_term_begins']) ? date('j M Y', strtotime($exam['next_term_begins'])) : '–'; ?></td></tr>
    </table>

    <table class="rc-sub">
        <thead><tr>
            <th style="text-align:left;padding-left:6px">Subject</th>
            <?php foreach ($cols as $c) { ?><th><?php echo $e($c['name']); ?><?php echo count($c['max']) === 1 ? '<small>(' . $e(key($c['max'])) . ')</small>' : ''; ?></th><?php } ?>
            <th>Total<small>(%)</small></th>
            <?php if ($cum) { foreach ($r['terms'] as $tid => $tn) { if ((int) $tid === (int) $exam['cbse_term_id']) { continue; } ?><th><?php echo $e($tn); ?><small>(%)</small></th><?php } ?><th>Cumu&shy;lative</th><?php } ?>
            <th>Grade</th><th>Remark</th><th>Pos.</th><th>Class avg</th><th>High</th><th>Low</th>
        </tr></thead>
        <tbody>
        <?php foreach ($r['subjects'] as $tt => $s) {
            $v = isset($st['subjects'][$tt]) ? $st['subjects'][$tt] : null;
            if (!$v) { continue; }
            $cs = isset($stats['subject'][$tt]) ? $stats['subject'][$tt] : null; ?>
            <tr>
                <td class="s"><?php echo $e($s['name']); ?></td>
                <?php foreach ($cols as $k => $c) {
                    $cell = '–';
                    foreach ($s['types'] as $tid => $t) {
                        if (mb_strtolower($t['name']) === $k) {
                            $m = isset($v['marks'][$tid]) ? $v['marks'][$tid] : null;
                            $cell = $m === null ? '–' : ($m === 'ABS' ? 'ABS' : $n($m)) . (count($c['max']) > 1 ? '/' . $n($t['maximum_marks']) : '');
                        }
                    } ?>
                    <td><?php echo $e($cell); ?></td>
                <?php } ?>
                <td class="<?php echo $v['pct'] < 40 ? 'low' : ''; ?>"><b><?php echo $n($v['total']); ?></b><?php echo abs($v['max'] - 100) > 0.01 ? ' <small>(' . $n($v['pct']) . ')</small>' : ''; ?></td>
                <?php if ($cum) { foreach ($r['terms'] as $tid => $tn) { if ((int) $tid === (int) $exam['cbse_term_id']) { continue; } ?><td><?php echo isset($v['terms'][$tid]) ? $n($v['terms'][$tid]) : '–'; ?></td><?php } ?><td><b><?php echo $n(isset($v['cumulative']) ? $v['cumulative'] : null); ?></b></td><?php } ?>
                <td><b><?php echo $e($v['grade']); ?></b></td>
                <td style="font-size:9.5px"><?php echo $e($v['remark']); ?></td>
                <td><?php echo isset($v['position']) ? Class_results::ordinal($v['position']) : '–'; ?></td>
                <td><?php echo $cs ? $n($cs['avg']) : '–'; ?></td>
                <td><?php echo $cs ? $n($cs['high']) : '–'; ?></td>
                <td><?php echo $cs ? $n($cs['low']) : '–'; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <table class="rc-sum" style="margin-top:8px"><tr>
        <td>Total score: <b><?php echo $n($st['total']); ?></b> / <?php echo $n($st['max']); ?></td>
        <td>Average: <b><?php echo $st['average'] === null ? '–' : $n($st['average']) . '%'; ?></b></td>
        <td>Grade: <b><?php echo $e($st['grade']); ?></b><?php echo $st['grade_remark'] ? ' (' . $e($st['grade_remark']) . ')' : ''; ?></td>
        <td>Position: <b><?php echo isset($st['position']) ? Class_results::ordinal($st['position']) : '–'; ?></b> out of <?php echo (int) $stats['size']; ?></td>
        <td>Class average: <b><?php echo isset($stats['class_average']) && $stats['class_average'] !== null ? $n($stats['class_average']) . '%' : '–'; ?></b></td>
    </tr>
    <?php if ($cum) { ?><tr><td colspan="5">Cumulative average (all terms): <b><?php echo $st['cumulative_average'] === null ? '–' : $n($st['cumulative_average']) . '%'; ?></b> · Cumulative position: <b><?php echo isset($st['cumulative_position']) ? Class_results::ordinal($st['cumulative_position']) : '–'; ?></b></td></tr><?php } ?>
    </table>

    <table class="rc-com" style="margin-top:8px">
        <tr><td class="l">Class teacher's comment</td><td><?php echo $e($st['remark'] ?: ''); ?></td></tr>
        <tr><td class="l">Principal's comment</td><td><?php echo $e($st['principal_remark'] ?: ''); ?></td></tr>
    </table>

    <?php if ($r['grades']) { ?>
        <table class="rc-key" style="margin-top:8px"><tr>
            <td style="font-weight:700;background:#f8fafc">Grading</td>
            <?php foreach ($r['grades'] as $g) { ?><td><b><?php echo $e($g['name']); ?></b><br><?php echo $n($g['min']) . '–' . $n($g['max']); ?><?php echo $g['remark'] ? '<br>' . $e($g['remark']) : ''; ?></td><?php } ?>
        </tr></table>
    <?php } ?>

    <table class="rc-sign"><tr><td><span>Class teacher</span></td><td><span>Principal</span></td><td><span>Parent / Guardian</span></td></tr></table>
</div>
<?php } ?>
<?php if ($wrap) { ?></body></html><?php } ?>
