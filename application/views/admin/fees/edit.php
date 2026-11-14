<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
$fmt = function ($d) { return $d ? date($this->customlib->getSchoolDateFormat(), strtotime($d)) : ''; };
$tail = (int) $charge['fsg_id'] . '/' . (int) $charge['class_id'] . '/' . (int) $charge['section_id'];
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-pencil"></i> Change fees <small><?php echo $e($charge['group']); ?></small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <p><a href="<?php echo site_url('admin/fees/charge/' . $tail); ?>"><i class="fa fa-angle-left"></i> Back to <?php echo $e($charge['class'] . ' ' . $charge['section']); ?></a></p>
        <?php if ($errors) { ?>
            <div class="alert alert-danger"><b>Please fix the following:</b><ul style="margin:6px 0 0"><?php foreach ($errors as $err) { ?><li><?php echo $e($err); ?></li><?php } ?></ul></div>
        <?php } ?>

        <form method="post" action="<?php echo site_url('admin/fees/edit/' . $tail); ?>" id="fe">
            <?php echo $this->customlib->getCSRF(); ?>
            <input type="hidden" name="step" value="review">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Amounts and due dates</h3>
                    <p class="fw-muted" style="margin:4px 0 0">A change applies to every student charged "<?php echo $e($charge['group']); ?>"<?php echo $preview && count($preview['sections']) > 1 ? ', in ' . $e(implode(', ', $preview['sections'])) : ''; ?>. Payments already made are kept.</p></div>
                <div class="box-body table-responsive">
                    <table class="table fw-table">
                        <thead><tr><th>Item</th><th class="n">Amount now</th><th style="width:170px">New amount</th><th>Due now</th><th style="width:170px">New due date</th></tr></thead>
                        <tbody>
                        <?php foreach ($items as $id => $it) {
                            $c = isset($changes[$id]) ? $changes[$id] : null; ?>
                            <tr>
                                <td><b><?php echo $e($it['item']); ?></b></td>
                                <td class="n"><?php echo $m($it['amount']); ?></td>
                                <td><input type="text" inputmode="decimal" class="form-control input-sm text-right" name="amount[<?php echo (int) $id; ?>]" value="<?php echo $e($c ? number_format($c['amount'], 2, '.', '') : number_format($it['amount'], 2, '.', '')); ?>" aria-label="New amount for <?php echo $e($it['item']); ?>"></td>
                                <td class="fw-muted"><?php echo $e($fmt($it['due_date']) ?: 'none'); ?></td>
                                <td><input type="text" class="form-control input-sm date" name="due[<?php echo (int) $id; ?>]" value="<?php echo $e($fmt($c ? $c['due_date'] : $it['due_date'])); ?>" autocomplete="off" placeholder="none" aria-label="New due date for <?php echo $e($it['item']); ?>"></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <button type="submit" class="btn btn-default"><i class="fa fa-eye"></i> Show what changes</button>
        </form>

        <?php if ($preview) { ?>
            <div class="box box-primary" style="margin-top:16px" id="fe_preview">
                <div class="box-header with-border"><h3 class="box-title">What changes</h3></div>
                <div class="box-body">
                    <?php if (!$preview['changed']) { ?>
                        <p class="fw-muted">Nothing is different from now.</p>
                    <?php } else { ?>
                        <ul>
                            <?php foreach ($preview['rows'] as $r) {
                                if (!$r['changed']) {
                                    continue;
                                } ?>
                                <li><b><?php echo $e($r['item']); ?></b>:
                                    <?php if (abs($r['new_amount'] - $r['old_amount']) > 0.004) { ?><?php echo $m($r['old_amount']); ?> → <b><?php echo $m($r['new_amount']); ?></b><?php } ?>
                                    <?php if ($r['new_due'] !== $r['old_due']) { ?><?php echo abs($r['new_amount'] - $r['old_amount']) > 0.004 ? '; ' : ''; ?>due <?php echo $e($fmt($r['old_due']) ?: 'none'); ?> → <b><?php echo $e($fmt($r['new_due']) ?: 'none'); ?></b><?php } ?>
                                    <?php if ($r['paid_more']) { ?><br><span class="text-danger"><i class="fa fa-exclamation-triangle"></i> <?php echo (int) $r['paid_more'] . ($r['paid_more'] === 1 ? ' student has' : ' students have'); ?> already paid more than the new amount; refund or adjust their payments separately.</span><?php } ?>
                                </li>
                            <?php } ?>
                        </ul>
                        <p><?php echo (int) $preview['students'] . ($preview['students'] === 1 ? ' student' : ' students'); ?> in <?php echo $e(implode(', ', $preview['sections'])); ?>. Expected in total: <?php echo $m($preview['expected_before']); ?> → <b><?php echo $m($preview['expected_after']); ?></b>.</p>
                        <form method="post" action="<?php echo site_url('admin/fees/edit/' . $tail); ?>">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <input type="hidden" name="step" value="apply">
                            <?php foreach ($changes as $id => $c) { ?>
                                <input type="hidden" name="amount[<?php echo (int) $id; ?>]" value="<?php echo $e($c['amount']); ?>">
                                <input type="hidden" name="due[<?php echo (int) $id; ?>]" value="<?php echo $e($fmt($c['due_date'])); ?>">
                            <?php } ?>
                            <button type="submit" class="btn btn-success" id="fe_apply"><i class="fa fa-check"></i> Save these changes</button>
                        </form>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </section>
</div>
