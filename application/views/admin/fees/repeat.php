<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-files-o"></i> Repeat fees <small>for the next term</small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <p class="fw-muted" style="margin:-4px 0 14px">Pick fees you charged before. They open in Create fees with the same items, classes and amounts, named for the next term; you set the due dates, change what you need, and review before anyone is charged.</p>
        <?php if (!$sets) { ?>
            <div class="box box-primary"><div class="box-body text-center fw-muted" style="padding:30px">No fees have been charged in this session or the last one yet.
                <br><a class="btn btn-primary" style="margin-top:10px" href="<?php echo site_url('admin/fees/create'); ?>"><i class="fa fa-plus"></i> Create fees</a></div></div>
        <?php } else { ?>
            <div class="box box-primary">
                <div class="box-body table-responsive">
                    <table class="table table-hover fw-table">
                        <thead><tr><th>Fees</th><th>Session</th><th>Term</th><th>Classes</th><th class="n">Per student</th><th class="n">Students</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($sets as $s) { ?>
                            <tr>
                                <td><b><?php echo $e($s['name']); ?></b></td>
                                <td><?php echo $e($s['session']); ?><?php echo $s['session_id'] === $current_session_id ? '' : ' <span class="fw-muted">(last session)</span>'; ?></td>
                                <td><?php echo $s['term'] ? Fees_overview::TERMS[$s['term']] : '<span class="fw-muted">—</span>'; ?></td>
                                <td style="max-width:280px"><?php echo $e(implode(', ', $s['classes'])); ?></td>
                                <td class="n"><?php echo $m($s['per_student'][0]); ?><?php echo $s['per_student'][1] > $s['per_student'][0] + 0.004 ? ' – ' . $m($s['per_student'][1]) : ''; ?></td>
                                <td class="n"><?php echo (int) $s['students']; ?></td>
                                <td>
                                    <form method="post" action="<?php echo site_url('admin/fees/repeat'); ?>" style="margin:0">
                                        <?php echo $this->customlib->getCSRF(); ?>
                                        <input type="hidden" name="set" value="<?php echo $e($s['key']); ?>">
                                        <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-files-o"></i> Repeat</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
    </section>
</div>
