<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
$label = $this->fee_adjustments->discount_label($discount, $currency_symbol);
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-tag"></i> <?php echo $e($discount['name']); ?> <small><?php echo $e($label); ?><?php echo $discount['id'] ? '' : ' · new'; ?></small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <ul class="fw-steps"><li>1 Discount and classes</li><li class="on">2 Students and preview</li></ul>
        <?php if ($errors) { ?>
            <div class="alert alert-danger"><ul style="margin:0"><?php foreach ($errors as $err) { ?><li><?php echo $e($err); ?></li><?php } ?></ul></div>
        <?php } ?>

        <form method="post" action="<?php echo site_url('admin/fees/discounts'); ?>" id="ds">
            <?php echo $this->customlib->getCSRF(); ?>
            <input type="hidden" name="step" value="apply">
            <input type="hidden" name="discount_id" value="<?php echo (int) $discount['id']; ?>">
            <?php if (!$discount['id']) { ?>
                <input type="hidden" name="new_name" value="<?php echo $e($discount['name']); ?>">
                <input type="hidden" name="new_type" value="<?php echo $e($discount['type']); ?>">
                <input type="hidden" name="new_value" value="<?php echo $e($discount['type'] === 'percentage' ? $discount['percentage'] : $discount['amount']); ?>">
            <?php } ?>
            <?php foreach ($sections as $s) { ?><input type="hidden" name="sections[]" value="<?php echo $e($s); ?>"><?php } ?>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Tick the students who get this discount</h3>
                    <p class="fw-muted" style="margin:4px 0 0">It is taken off their fees when their next payment is collected. The amount shown is an estimate against what they owe now.<?php echo $discount['type'] === 'percentage' ? ' A percentage applies to the payment it is used on.' : ''; ?></p>
                </div>
                <div class="box-body table-responsive">
                    <?php if (!$students) { ?>
                        <p class="fw-muted text-center" style="padding:20px">There are no active students in these classes.</p>
                    <?php } else { ?>
                        <table class="table table-hover fw-table">
                            <thead><tr><th style="width:30px"></th><th>Student</th><th>Class</th><th class="n">Owes now</th><th class="n">Discount</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php foreach ($students as $s) {
                                $name = $this->customlib->getFullName($s['firstname'], $s['middlename'], $s['lastname'], $sch_setting->middlename, $sch_setting->lastname);
                                $value = $this->fee_adjustments->discount_value($discount, $s['balance']); ?>
                                <tr>
                                    <td>
                                        <?php if ($s['state'] === 'used') { ?>
                                            <input type="checkbox" checked disabled aria-label="Used">
                                        <?php } else { ?>
                                            <input type="checkbox" class="ds-pick" value="<?php echo (int) $s['student_session_id']; ?>" data-was="<?php echo $s['state'] === 'assigned' ? 1 : 0; ?>" data-value="<?php echo $e($value); ?>" <?php echo $s['state'] === 'assigned' ? 'checked' : ''; ?> aria-label="<?php echo $e($name); ?>">
                                        <?php } ?>
                                    </td>
                                    <td><b><?php echo $e($name); ?></b><br><span class="fw-muted"><?php echo $e($s['admission_no']); ?></span></td>
                                    <td><?php echo $e($s['class'] . ' ' . $s['section']); ?></td>
                                    <td class="n"><?php echo $m($s['balance']); ?></td>
                                    <td class="n"><?php echo $value > 0 ? $m($value) : '<span class="fw-muted">nothing owed</span>'; ?></td>
                                    <td class="ds-state"><?php echo $s['state'] === 'used' ? '<span class="fw-tag paid">Used at a payment</span>' : ($s['state'] === 'assigned' ? '<span class="fw-tag owing">Has it</span>' : ''); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
            <div id="ds_hidden"></div>
            <div class="fw-sum">
                <span id="ds_text">No changes yet.</span>
                <span>
                    <button type="submit" class="btn btn-success" id="ds_go" disabled><i class="fa fa-check"></i> Apply</button>
                    <a class="btn btn-default" href="<?php echo site_url('admin/fees/discounts'); ?>">Cancel</a>
                </span>
            </div>
        </form>
        <script>
            (function () {
                var sym = <?php echo json_encode((string) $currency_symbol); ?>;
                var picks = [].slice.call(document.querySelectorAll('.ds-pick'));
                var money = function (v) { return sym + v.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}); };
                function update() {
                    var add = [], remove = [], total = 0;
                    picks.forEach(function (c) {
                        var was = c.getAttribute('data-was') === '1';
                        if (c.checked && !was) { add.push(c.value); total += parseFloat(c.getAttribute('data-value')) || 0; }
                        if (!c.checked && was) { remove.push(c.value); }
                        var st = c.closest('tr').querySelector('.ds-state');
                        st.innerHTML = c.checked && !was ? '<span class="fw-tag paid">Will get it</span>' : (!c.checked && was ? '<span class="fw-tag overdue">Will lose it</span>' : (was ? '<span class="fw-tag owing">Has it</span>' : ''));
                    });
                    var parts = [];
                    if (add.length) { parts.push('Give it to <b>' + add.length + '</b> student' + (add.length === 1 ? '' : 's') + ', about <b>' + money(total) + '</b> off'); }
                    if (remove.length) { parts.push('take it back from <b>' + remove.length + '</b>'); }
                    document.getElementById('ds_text').innerHTML = parts.length ? parts.join('; ') + '.' : 'No changes yet.';
                    document.getElementById('ds_go').disabled = !parts.length;
                    var h = '';
                    add.forEach(function (v) { h += '<input type="hidden" name="add[]" value="' + v + '">'; });
                    remove.forEach(function (v) { h += '<input type="hidden" name="remove[]" value="' + v + '">'; });
                    document.getElementById('ds_hidden').innerHTML = h;
                }
                picks.forEach(function (c) { c.addEventListener('change', update); });
                update();
            })();
        </script>
    </section>
</div>
