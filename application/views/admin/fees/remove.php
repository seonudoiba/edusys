<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
$tail = (int) $charge['fsg_id'] . '/' . (int) $charge['class_id'] . '/' . (int) $charge['section_id'];
$title = $charge['is_system'] ? 'Balance brought forward' : $charge['group'];
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-trash-o"></i> Remove fees <small><?php echo $e($title); ?> · <?php echo $e($charge['class'] . ' ' . $charge['section']); ?></small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <p><a href="<?php echo site_url('admin/fees/charge/' . $tail); ?>"><i class="fa fa-angle-left"></i> Back to <?php echo $e($charge['class'] . ' ' . $charge['section']); ?></a></p>
        <div class="box box-<?php echo $removal['paid'] ? 'warning' : 'danger'; ?>">
            <div class="box-body">
                <?php if ($removal['paid']) { ?>
                    <p><b>These fees cannot be removed:</b> some students in <?php echo $e($charge['class'] . ' ' . $charge['section']); ?> have already paid towards them.</p>
                    <p class="fw-muted">To correct an amount, use "Change amounts or due dates". To remove the fees anyway, reverse the payments first (Collections &gt; Find a receipt).</p>
                    <a class="btn btn-default" href="<?php echo site_url('admin/fees/edit/' . $tail); ?>">Change amounts instead</a>
                <?php } else { ?>
                    <p><b>"<?php echo $e($title); ?>"</b> will be taken off <b><?php echo (int) $removal['students'] . ($removal['students'] === 1 ? ' student' : ' students'); ?></b> of <?php echo $e($charge['class'] . ' ' . $charge['section']); ?>, who will no longer owe <b><?php echo $m($removal['expected']); ?></b> in total.</p>
                    <p class="fw-muted">Nobody has paid towards these fees yet. Other sections and the fee items themselves are not touched; you can charge the fees again later.</p>
                    <form method="post" action="<?php echo site_url('admin/fees/remove/' . $tail); ?>">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <input type="hidden" name="confirm" value="yes">
                        <button type="submit" class="btn btn-danger" id="rm_go"><i class="fa fa-trash-o"></i> Remove from <?php echo (int) $removal['students'] . ($removal['students'] === 1 ? ' student' : ' students'); ?></button>
                        <a class="btn btn-default" href="<?php echo site_url('admin/fees/charge/' . $tail); ?>">Cancel</a>
                    </form>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
