<?php
/*
 * Report cards (Results::card / pdf / email, student and parent portal, website result checker).
 * One page per student, laid out like a classic school report: letterhead with the school logo and
 * the student's photo; student details; attendance of every term; every subject with this term's
 * assessments (and, on the last term, each term with the cumulative); averages; grading scale;
 * comments, promoted / retained; signatures; a footer strip. Tables only, so the PDF (mPDF) looks
 * the same. $exam, $class, $r (Class_results::results), $brand, $term_name, $session_name;
 * optional $teachers [section_id => names], $decisions [student_session_id => promotion], $print, $pdf, $embed.
 */
$n = function ($v) { return $v === null || $v === '' ? '–' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
$e = function ($v) { return html_escape((string) $v); };
$hex = preg_match('/^#?([0-9a-f]{6})$/i', (string) $brand['color'], $m) ? '#' . $m[1] : '#1e4f8c';
$mix = function ($w) use ($hex) { // the school colour mixed with white: $w = share of white (0..1)
    $c = array_map('hexdec', str_split(substr($hex, 1), 2));
    return sprintf('#%02x%02x%02x', $c[0] + (255 - $c[0]) * $w, $c[1] + (255 - $c[1]) * $w, $c[2] + (255 - $c[2]) * $w);
};
$C = $hex;
$C2 = $mix(.82); // light tint for header rows
$C3 = $mix(.9);  // lighter, for term columns
$DARK = '#111827';
$pdf = !empty($pdf);
$wrap = !$pdf && empty($embed);
$teachers = isset($teachers) ? $teachers : array();
$decisions = isset($decisions) ? $decisions : array();
$all_terms = isset($r['all_terms']) ? $r['all_terms'] : array((int) $exam['cbse_term_id'] => $term_name);
$cur = (int) $exam['cbse_term_id'];
$cum = !empty($r['cumulative']);
$prev_terms = array();
if ($cum) {
    foreach ($r['terms'] as $tid => $tn) {
        if ((int) $tid !== $cur) {
            $prev_terms[(int) $tid] = $tn;
        }
    }
}
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
$img = function ($url) use ($pdf) { // a local upload: a file path for the PDF, the address for the page
    if (!empty($pdf) && $url && strpos($url, base_url()) === 0) {
        $path = FCPATH . substr($url, strlen(base_url()));
        return is_file($path) ? $path : $url;
    }
    return $url;
};
$logo = $img($brand['logo']);
$short = function ($name) { // "First Term" → "1st Term"
    return preg_replace(array('/^First\b/i', '/^Second\b/i', '/^Third\b/i'), array('1st', '2nd', '3rd'), $name);
};
$grade_of = function ($pct) use ($r) { return Class_results::grade_of($r['grades'], $pct)[0]; };
?>
<?php if ($wrap) { ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $e($class['class'] . ' ' . $term_name . ' report cards'); ?></title>
<?php } ?>
<style>
    <?php echo $wrap ? 'body' : '.rc-page'; ?>{font-family:<?php echo $pdf ? 'dejavusanscondensed,sans-serif' : 'Arial,Helvetica,sans-serif'; ?>;color:#111;margin:0;background:<?php echo $wrap ? '#e5e7eb' : '#fff'; ?>}
    <?php if ($pdf) { /* mPDF: no @page or page-break CSS (they produce blank pages); <pagebreak /> between students */ ?>
    .rc-page{background:#fff}
    .rc-page, .rc-page table, .rc-page td, .rc-page th, .rc-page div{font-family:dejavusanscondensed,sans-serif}
    <?php } else { ?>
    .rc-page{background:#fff;max-width:210mm;margin:14px auto;padding:10mm 10mm 8mm;box-sizing:border-box;page-break-after:always}
    .rc-page:last-child{page-break-after:auto}
    <?php } ?>
    .rc-page table{border-collapse:collapse;width:100%}
    .rc-name{font-size:22px;font-weight:bold;line-height:1.15}
    .rc-addr{font-size:12.5px;line-height:1.35;color:#222}
    .rc-term{font-size:15px;margin-top:6px}
    .rc-rule{border-bottom:2px solid #333;margin:6px 0 10px}
    .rc-who td{font-size:12px;vertical-align:top;padding:1px 0}
    .rc-who b{font-weight:bold}
    .rc-grid{margin-top:10px;border:2px solid #222}
    .rc-grid th{background:<?php echo $C; ?>;color:#fff;font-size:12px;font-weight:bold;padding:5px 4px;border:1px solid #222;text-align:center}
    .rc-grid th.l{text-align:left;padding-left:6px}
    .rc-grid th small{display:block;font-weight:normal;font-size:9.5px;opacity:.9}
    .rc-grid td{font-size:11.5px;padding:3px 4px;border:1px solid #444;text-align:center}
    .rc-grid td.l{text-align:left;padding-left:6px}
    .rc-grid td.g{background:<?php echo $C2; ?>}
    .rc-grid td.t{background:<?php echo $C3; ?>}
    .rc-grid td.f,.rc-grid th.f{background:<?php echo $DARK; ?>;color:#fff;font-weight:bold}
    .rc-grid tr.sum td{font-weight:bold;background:<?php echo $C2; ?>}
    .rc-grid td.low{color:#b91c1c}
    .rc-scale td{font-size:10.5px;font-style:italic;padding:2px 4px;text-align:center;border:0}
    .rc-box{border:2px solid #222;margin-top:10px}
    .rc-box td{font-size:12px;padding:5px 7px;vertical-align:top}
    .rc-bar td{background:<?php echo $C; ?>;color:#fff;font-size:15px;font-weight:bold;font-style:italic;padding:6px 8px;border:1px solid #222}
    .rc-sign td{font-size:12px;font-weight:bold;padding:14px 4px 0;vertical-align:bottom}
    .rc-sign td.line{border-bottom:1px solid #222}
    .rc-foot{margin-top:12px;border:2px solid #222}
    .rc-foot td{font-size:12px;padding:4px 7px;border:1px solid #222;vertical-align:top}
    .rc-photo{width:88px;height:104px;object-fit:cover;border:2px solid #222}
    .rc-print{position:sticky;top:0;background:#0f172a;color:#fff;padding:10px 16px;font-size:14px;z-index:5}
    .rc-print button{background:<?php echo $C; ?>;color:#fff;border:0;border-radius:8px;padding:8px 14px;font-weight:bold;cursor:pointer;margin-right:10px}
    <?php if (!$pdf) { ?>
    @media print{body{background:#fff}.rc-print{display:none}.rc-page{margin:0;max-width:none;padding:6mm}.rc-grid th,.rc-grid td,.rc-bar td{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
    @page{size:A4;margin:6mm}
    <?php } ?>
</style>
<?php if ($wrap) { ?></head><body>
    <?php if (!empty($print)) { ?><div class="rc-print"><button onclick="window.print()">Print</button><?php echo count($r['students']); ?> report card<?php echo count($r['students']) === 1 ? '' : 's'; ?> · <?php echo $e($class['class'] . ' · ' . $term_name); ?></div><?php } ?>
<?php } ?>
<?php if (!$r['students']) { ?><div class="rc-page">No report card to show.</div><?php } ?>
<?php $first_page = true; foreach ($r['students'] as $es => $st) {
    if ($pdf && !$first_page) { echo '<pagebreak />'; }
    $first_page = false;
    $stats = isset($r['stats'][(int) $st['section_id']]) ? $r['stats'][(int) $st['section_id']] : array('size' => 0);
    $photo = $st['image'] ? base_url() . ltrim($st['image'], '/') : base_url() . 'uploads/student_images/' . ($st['gender'] === 'Female' ? 'default_female.jpg' : 'default_male.jpg');
    $photo = $img($photo);
    $teacher = isset($teachers[(int) $st['section_id']]) ? implode(', ', $teachers[(int) $st['section_id']]) : '';
    $dec = isset($decisions[(int) $st['student_session_id']]) ? $decisions[(int) $st['student_session_id']] : null;
    $att_tot = array(0, 0);
    ?>
<div class="rc-page">
    <!-- letterhead: logo, school, term; the student's photo -->
    <table><tr>
        <?php if ($logo) { ?><td style="width:120px;vertical-align:top"><img src="<?php echo $e($logo); ?>" style="width:110px;height:auto;max-height:120px" alt=""></td><?php } ?>
        <td style="vertical-align:top;padding-left:8px">
            <div class="rc-name"><?php echo $e($brand['name']); ?></div>
            <div class="rc-addr"><?php echo nl2br($e($brand['address'])); ?></div>
            <div class="rc-addr"><?php echo $e(implode('  ·  ', array_filter(array($brand['phone'] ? 'Tel: ' . $brand['phone'] : '', $brand['email'])))); ?></div>
            <div class="rc-term"><?php echo $e($term_name); ?> Report Card</div>
        </td>
        <td style="width:96px;vertical-align:top;text-align:right"><img class="rc-photo" src="<?php echo $e($photo); ?>" style="width:88px;height:104px" alt=""></td>
    </tr></table>
    <div class="rc-rule"></div>

    <!-- student -->
    <table class="rc-who"><tr>
        <td style="width:52%">
            <b>Student:</b><br>
            <?php echo $e($st['name']); ?><br>
            Admission no: <?php echo $e($st['admission_no']); ?><br>
            <?php echo $st['current_address'] ? nl2br($e($st['current_address'])) : ''; ?>
        </td>
        <td style="width:48%">
            <table>
                <tr><td style="text-align:right;width:46%"><b>Class:&nbsp;</b></td><td><?php echo $e($class['class'] . ' ' . $st['section']); ?></td></tr>
                <?php if ($teacher) { ?><tr><td style="text-align:right"><b>Class teacher:&nbsp;</b></td><td><?php echo $e($teacher); ?></td></tr><?php } ?>
                <tr><td style="text-align:right"><b>Term:&nbsp;</b></td><td><?php echo $e($term_name . ' ' . $session_name); ?></td></tr>
                <tr><td style="text-align:right"><b>No. in class:&nbsp;</b></td><td><?php echo (int) $stats['size']; ?></td></tr>
                <?php if (!empty($exam['next_term_begins'])) { ?><tr><td style="text-align:right"><b>Next term begins:&nbsp;</b></td><td><?php echo date('j F Y', strtotime($exam['next_term_begins'])); ?></td></tr><?php } ?>
            </table>
        </td>
    </tr></table>

    <!-- attendance, every term -->
    <table class="rc-grid">
        <tr><th class="l" style="width:34%">Attendance</th><?php foreach ($all_terms as $tid => $tn) { ?><th><?php echo $e($short($tn)); ?></th><?php } ?><th>Total</th></tr>
        <?php foreach (array('Days school opened' => 0, 'Days present' => 1, 'Days absent' => 2) as $label => $k) { $tot = 0; $any = false; ?>
            <tr><td class="l"><?php echo $label; ?></td>
                <?php foreach ($all_terms as $tid => $tn) {
                    $a = isset($st['attendance'][$tid]) ? $st['attendance'][$tid] : null;
                    $v = null;
                    if ($a && $a[0] > 0) {
                        $v = $k === 0 ? $a[0] : ($a[1] === null || $a[1] === '' ? null : ($k === 1 ? (int) $a[1] : max(0, $a[0] - (int) $a[1])));
                    }
                    if ($v !== null) { $tot += $v; $any = true; } ?>
                    <td><b><?php echo $v === null ? '–' : (int) $v; ?></b></td>
                <?php } ?>
                <td><b><?php echo $any ? (int) $tot : '–'; ?></b></td></tr>
        <?php } ?>
    </table>

    <!-- subjects -->
    <table class="rc-grid">
        <tr>
            <th class="l" rowspan="2">Subject</th>
            <?php foreach ($cols as $c) { ?><th rowspan="2"><?php echo $e($c['name']); ?><?php echo count($c['max']) === 1 ? '<br><small>' . $e(key($c['max'])) . '</small>' : ''; ?></th><?php } ?>
            <th colspan="2"><?php echo $e($short($term_name)); ?></th>
            <?php foreach ($prev_terms as $tid => $tn) { ?><th colspan="2"><?php echo $e($short($tn)); ?></th><?php } ?>
            <?php if ($cum) { ?><th colspan="2" class="f">Final</th><?php } ?>
            <th rowspan="2">Pos.</th><th rowspan="2">Class<br>avg</th>
        </tr>
        <tr>
            <th>Grade</th><th>Total</th>
            <?php foreach ($prev_terms as $tid => $tn) { ?><th>Grade</th><th>%</th><?php } ?>
            <?php if ($cum) { ?><th class="f">Grade</th><th class="f">%</th><?php } ?>
        </tr>
        <?php foreach ($r['subjects'] as $tt => $s) {
            $v = isset($st['subjects'][$tt]) ? $st['subjects'][$tt] : null;
            if (!$v) { continue; }
            $cs = isset($stats['subject'][$tt]) ? $stats['subject'][$tt] : null; ?>
            <tr>
                <td class="l"><b><?php echo $e($s['name']); ?></b><?php echo $s['code'] ? ' <span style="font-size:10px;color:#555">(' . $e($s['code']) . ')</span>' : ''; ?></td>
                <?php foreach ($cols as $k => $c) {
                    $cell = '';
                    foreach ($s['types'] as $tid => $t) {
                        if (mb_strtolower($t['name']) === $k) {
                            $mk = isset($v['marks'][$tid]) ? $v['marks'][$tid] : null;
                            $cell = $mk === null ? '–' : ($mk === 'ABS' ? 'ABS' : $n($mk)) . (count($c['max']) > 1 ? '/' . $n($t['maximum_marks']) : '');
                        }
                    } ?>
                    <td><?php echo $e($cell ?: ''); ?></td>
                <?php } ?>
                <td class="g"><b><?php echo $e($v['grade']); ?></b></td>
                <td class="g<?php echo $v['pct'] < 40 ? ' low' : ''; ?>"><b><?php echo $n($v['total']); ?></b><?php echo abs($v['max'] - 100) > 0.01 ? '<span style="font-size:9.5px">/' . $n($v['max']) . '</span>' : ''; ?></td>
                <?php foreach ($prev_terms as $tid => $tn) { $p = isset($v['terms'][$tid]) ? $v['terms'][$tid] : null; ?>
                    <td class="t"><?php echo $p === null ? '–' : $e($grade_of($p)); ?></td><td class="t"><?php echo $n($p); ?></td>
                <?php } ?>
                <?php if ($cum) { ?><td class="f"><?php echo $e(isset($v['cumulative']) ? $grade_of($v['cumulative']) : ''); ?></td><td class="f"><?php echo $n(isset($v['cumulative']) ? $v['cumulative'] : null); ?></td><?php } ?>
                <td><?php echo isset($v['position']) ? Class_results::ordinal($v['position']) : '–'; ?></td>
                <td><?php echo $cs ? $n($cs['avg']) : '–'; ?></td>
            </tr>
        <?php } ?>
        <tr class="sum">
            <td class="l" colspan="<?php echo count($cols) + 1; ?>" style="text-align:right">Average</td>
            <td colspan="2"><?php echo $st['average'] === null ? '–' : $n($st['average']) . '% (' . $e($st['grade']) . ')'; ?></td>
            <?php foreach ($prev_terms as $tid => $tn) { $a = isset($st['term_average'][$tid]) ? $st['term_average'][$tid] : null; ?><td colspan="2"><?php echo $a === null ? '–' : $n($a) . '%'; ?></td><?php } ?>
            <?php if ($cum) { ?><td colspan="2" class="f"><?php echo $st['cumulative_average'] === null ? '–' : $n($st['cumulative_average']) . '%'; ?></td><?php } ?>
            <td colspan="2"><?php echo isset($st['position']) ? Class_results::ordinal($st['position']) . ' of ' . (int) $stats['size'] : '–'; ?></td>
        </tr>
    </table>
    <?php if ($r['grades']) { ?>
        <table class="rc-grid" style="border-top:0;margin-top:0">
            <tr><td style="width:14%;font-weight:bold;font-style:italic;text-align:left;border:0;padding-left:8px">Scale:</td>
                <td style="border:0"><table class="rc-scale"><tr>
                    <?php $i = 0; foreach ($r['grades'] as $g) { if ($i && $i % 3 === 0) { echo '</tr><tr>'; } $i++; ?>
                        <td style="white-space:nowrap"><?php echo $n($g['min']) . ' - ' . $n($g['max']) . ' = ' . $e($g['name']) . ($g['remark'] ? ' (' . $e($g['remark']) . ')' : ''); ?></td>
                    <?php } ?>
                </tr></table></td></tr>
        </table>
    <?php } ?>

    <!-- comments, promotion -->
    <table class="rc-box"><tr><td><b>Class teacher's comment:</b><br><?php echo nl2br($e($st['remark'])); ?>&nbsp;</td></tr></table>
    <table class="rc-box" style="margin-top:6px">
        <tr class="rc-bar">
            <?php if ($cum) {
                $word = $dec ? array('promote' => 'Promoted', 'probation' => 'Promoted on probation', 'repeat' => 'Retained')[$dec['decision']] : '';
                ?>
                <td style="width:30%">Promoted / Retained:</td><td style="text-align:center"><?php echo $e($word ?: '—'); ?></td>
            <?php } else { ?>
                <td style="width:30%">Position in class:</td><td style="text-align:center"><?php echo isset($st['position']) ? Class_results::ordinal($st['position']) . ' out of ' . (int) $stats['size'] : '—'; ?><?php echo isset($stats['class_average']) && $stats['class_average'] !== null ? ' &nbsp;·&nbsp; class average ' . $n($stats['class_average']) . '%' : ''; ?></td>
            <?php } ?>
        </tr>
        <tr><td colspan="2"><b>Principal's comment:</b> <?php echo nl2br($e($st['principal_remark'])); ?><?php echo $dec && $dec['reason'] ? '<br>' . $e($dec['reason']) : ''; ?>&nbsp;</td></tr>
    </table>

    <!-- signatures -->
    <table class="rc-sign">
        <?php foreach (array("Class Teacher's Signature:", "Principal's Signature:", "Parent's Signature:") as $who) { ?>
            <tr><td style="width:30%;text-align:right"><?php echo $who; ?></td><td class="line" style="width:36%">&nbsp;</td><td style="width:10%;text-align:right">Date:</td><td class="line">&nbsp;</td></tr>
        <?php } ?>
    </table>

    <!-- footer strip -->
    <table class="rc-foot"><tr>
        <td style="width:50%"><b>Student:</b><br><?php echo $e($st['name']); ?></td>
        <td style="width:20%"><b>Class:</b><br><?php echo $e($class['class'] . ' ' . $st['section']); ?></td>
        <td><b>Term:</b><br><?php echo $e($term_name . ' ' . $session_name); ?></td>
    </tr></table>
</div>
<?php } ?>
<?php if ($wrap) { ?></body></html><?php } ?>
