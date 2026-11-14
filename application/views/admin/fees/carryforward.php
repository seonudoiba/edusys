<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
$picked = array_flip($sections);
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-share"></i> Carry balances forward <small><?php echo $previous ? $e($previous->session) . ' → ' : ''; ?><?php echo $e($session_name); ?></small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <p><a href="<?php echo site_url('admin/fees/adjust'); ?>"><i class="fa fa-angle-left"></i> Discounts and balances</a></p>
        <?php if (!$previous) { ?>
            <div class="box box-primary"><div class="box-body text-center fw-muted" style="padding:30px">There is no session before <?php echo $e($session_name); ?>, so there is nothing to bring forward.</div></div>
        <?php } else { ?>
            <ul class="fw-steps"><li class="on">1 Classes and due date</li><li>2 Review each student</li></ul>
            <?php if ($errors) { ?>
                <div class="alert alert-danger"><b>Please fix the following:</b><ul style="margin:6px 0 0"><?php foreach ($errors as $err) { ?><li><?php echo $e($err); ?></li><?php } ?></ul></div>
            <?php } ?>
            <form method="post" action="<?php echo site_url('admin/fees/carryforward'); ?>" id="cf_fwd">
                <?php echo $this->customlib->getCSRF(); ?>
                <input type="hidden" name="step" value="review">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">What students in each class still owe from <?php echo $e($previous->session); ?></h3>
                        <p class="fw-muted" style="margin:4px 0 0">Students are shown in their class of this session. Tick the classes to bring forward; you see every student's amount before anything is saved.</p>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-hover fw-table">
                            <thead><tr><th style="width:30px"><input type="checkbox" id="fwd_all" aria-label="All classes with balances"></th><th>Class (this session)</th><th class="n">Students</th><th class="n">Owe from last session</th><th class="n">Amount owed</th><th>Brought forward</th></tr></thead>
                            <tbody>
                            <?php foreach ($summary as $key => $s) { ?>
                                <tr<?php echo $s['owing'] ? '' : ' class="fw-muted"'; ?>>
                                    <td><input type="checkbox" class="fwd-sec" name="sections[]" value="<?php echo $e($key); ?>" data-owing="<?php echo (int) $s['owing']; ?>" <?php echo isset($picked[$key]) ? 'checked' : ''; ?> aria-label="<?php echo $e($s['class'] . ' ' . $s['section']); ?>"></td>
                                    <td><?php echo $e($s['class'] . ' ' . $s['section']); ?></td>
                                    <td class="n"><?php echo (int) $s['students']; ?></td>
                                    <td class="n"><?php echo (int) $s['owing']; ?></td>
                                    <td class="n"><?php echo $s['owed'] > 0 ? '<b>' . $m($s['owed']) . '</b>' : $m(0); ?></td>
                                    <td><?php echo $s['carried'] ? '<span class="fw-tag paid">' . (int) $s['carried'] . ' done</span>' : ''; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box box-primary">
                    <div class="box-header with-border"><h3 class="box-title">When should the brought-forward balances be paid?</h3></div>
                    <div class="box-body">
                        <input type="text" name="due_date" class="form-control date" style="max-width:200px" value="<?php echo $e($due); ?>" autocomplete="off" aria-label="Due date" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Next: review each student <i class="fa fa-angle-right"></i></button>
            </form>
            <script>
                document.getElementById('fwd_all').addEventListener('change', function () {
                    var on = this.checked;
                    document.querySelectorAll('.fwd-sec').forEach(function (c) { if (c.getAttribute('data-owing') !== '0') { c.checked = on; } });
                });
            </script>
        <?php } ?>
    </section>
</div>
