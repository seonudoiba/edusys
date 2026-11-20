<?php
/*
 * The grading key on a report card: each grade with its score range and remark, so parents can check
 * the grades. $grades: the exam's grade ranges (objects: name, minimum_percentage, maximum_percentage,
 * description), best first.
 */
$gk = array();
foreach ((array) $grades as $g) {
    $g = (object) $g;
    $gk[] = $g;
}
usort($gk, function ($a, $b) { return (float) $b->minimum_percentage <=> (float) $a->minimum_percentage; });
$gk_n = function ($v) { return rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.'); };
if ($gk) { ?>
<table cellpadding="0" cellspacing="0" width="100%" class="denifittable gradingkey" style="margin-top:4px">
    <tr>
        <td valign="middle" class="text-center" style="font-weight:bold;width:14%" rowspan="<?php echo array_filter(array_map(function ($g) { return trim((string) $g->description); }, $gk)) ? 3 : 2; ?>"><?php echo $this->lang->line('grading_scale') ?: 'Grading key'; ?></td>
        <?php foreach ($gk as $g) { ?><td class="text-center" style="font-weight:bold"><?php echo html_escape($g->name); ?></td><?php } ?>
    </tr>
    <tr>
        <?php foreach ($gk as $g) { ?><td class="text-center" style="font-size:11px"><?php echo $gk_n($g->minimum_percentage) . '–' . $gk_n($g->maximum_percentage); ?></td><?php } ?>
    </tr>
    <?php if (array_filter(array_map(function ($g) { return trim((string) $g->description); }, $gk))) { ?>
    <tr>
        <?php foreach ($gk as $g) { ?><td class="text-center" style="font-size:10px"><?php echo html_escape($g->description); ?></td><?php } ?>
    </tr>
    <?php } ?>
</table>
<?php } ?>
