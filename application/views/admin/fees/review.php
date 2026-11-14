<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
$date = function ($d) { return $d ? date($this->customlib->getSchoolDateFormat(), strtotime($d)) : 'No due date'; };
$json = json_encode($plan);
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-plus-circle"></i> Create fees <small>review</small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <ul class="fw-steps"><li>1 Name</li><li>2 Fee items</li><li>3 Classes</li><li>4 Amounts and due dates</li><li class="on">5 Review</li></ul>

        <div class="fw-kpis">
            <div class="fw-kpi"><span>Students to charge</span><b><?php echo (int) $preview['students']; ?></b><small>active students of the chosen sections</small></div>
            <div class="fw-kpi teal"><span>Total to collect</span><b><?php echo $m($preview['total']); ?></b></div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?php echo $e($plan['name']); ?></h3> <span class="fw-muted">· <?php echo !empty($plan['term']) && isset(Fees_overview::TERMS[$plan['term']]) ? Fees_overview::TERMS[$plan['term']] : 'Whole session'; ?></span></div>
            <div class="box-body">
                <p><b>Fee items</b></p>
                <ul>
                    <?php foreach ($plan['items'] as $it) { ?>
                        <li><?php echo $e($it['name']); ?><?php echo $it['feetype_id'] === null ? ' <span class="fw-tag owing">new</span>' : ''; ?> <span class="fw-muted">· <?php echo $e($date($it['due_date'])); ?></span></li>
                    <?php } ?>
                </ul>
                <div class="table-responsive">
                    <table class="table table-hover fw-table">
                        <thead><tr><th>Class</th><th>Created as</th><th class="n">Students</th><th class="n">Per student</th><th class="n">Total</th></tr></thead>
                        <tbody>
                        <?php foreach ($preview['rows'] as $r) { ?>
                            <tr<?php echo $r['students'] === 0 ? ' class="warning"' : ''; ?>>
                                <td><?php echo $e($r['class'] . ' ' . $r['section']); ?></td>
                                <td class="fw-muted"><?php echo $e($r['group']); ?></td>
                                <td class="n"><?php echo (int) $r['students']; ?><?php echo $r['students'] === 0 ? ' <span class="fw-muted">(none yet)</span>' : ''; ?></td>
                                <td class="n"><?php echo $m($r['per_student']); ?></td>
                                <td class="n"><b><?php echo $m($r['total']); ?></b></td>
                            </tr>
                        <?php } ?>
                        <tr class="active"><td colspan="2"><b>Total</b></td><td class="n"><b><?php echo (int) $preview['students']; ?></b></td><td></td><td class="n"><b><?php echo $m($preview['total']); ?></b></td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="fw-muted">Students admitted later can be given these fees from their class's row in the Overview, or in Fees Master &gt; Assign.</p>
            </div>
        </div>

        <form method="post" action="<?php echo site_url('admin/fees/create'); ?>" style="display:inline">
            <?php echo $this->customlib->getCSRF(); ?>
            <input type="hidden" name="step" value="create">
            <input type="hidden" name="plan" value="<?php echo $e($json); ?>">
            <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-check"></i> Charge <?php echo (int) $preview['students'] . ((int) $preview['students'] === 1 ? ' student' : ' students'); ?></button>
        </form>
        <form method="post" action="<?php echo site_url('admin/fees/create'); ?>" style="display:inline;margin-left:8px">
            <?php echo $this->customlib->getCSRF(); ?>
            <input type="hidden" name="step" value="edit">
            <input type="hidden" name="plan" value="<?php echo $e($json); ?>">
            <button type="submit" class="btn btn-default btn-lg"><i class="fa fa-pencil"></i> Back to change</button>
        </form>
    </section>
</div>
