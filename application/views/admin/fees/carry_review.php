<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-share"></i> Carry balances forward <small>review</small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <ul class="fw-steps"><li>1 Classes and due date</li><li class="on">2 Review each student</li></ul>
        <?php if ($errors) { ?>
            <div class="alert alert-danger"><ul style="margin:0"><?php foreach ($errors as $err) { ?><li><?php echo $e($err); ?></li><?php } ?></ul></div>
        <?php } ?>
        <form method="post" action="<?php echo site_url('admin/fees/carryforward'); ?>" id="cr">
            <?php echo $this->customlib->getCSRF(); ?>
            <input type="hidden" name="step" value="apply">
            <input type="hidden" name="due_date" value="<?php echo $e($due); ?>">
            <?php foreach ($sections as $s) { ?><input type="hidden" name="sections[]" value="<?php echo $e($s); ?>"><?php } ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Balances from <?php echo $e($previous->session); ?>, due <?php echo $e($due); ?></h3>
                    <p class="fw-muted" style="margin:4px 0 0">Each amount starts at what the student still owed. Change it if part was settled another way, or set 0 to bring nothing. Students already brought forward are updated to the new amount.</p>
                </div>
                <div class="box-body table-responsive">
                    <?php if (!$students) { ?>
                        <p class="fw-muted text-center" style="padding:20px">There are no active students in these classes.</p>
                    <?php } else { ?>
                        <table class="table table-hover fw-table">
                            <thead><tr><th>Student</th><th>Class</th><th class="n">Owed last session</th><th class="n">Already brought forward</th><th class="n" style="width:170px">Bring forward</th><th>Change</th></tr></thead>
                            <tbody>
                            <?php foreach ($students as $s) {
                                $name = $this->customlib->getFullName($s['firstname'], $s['middlename'], $s['lastname'], $sch_setting->middlename, $sch_setting->lastname);
                                $start = $s['previous']; ?>
                                <tr<?php echo $start <= 0 && $s['carried'] === null ? ' class="fw-muted"' : ''; ?>>
                                    <td><b><?php echo $e($name); ?></b><br><span class="fw-muted"><?php echo $e($s['admission_no']); ?></span></td>
                                    <td><?php echo $e($s['class'] . ' ' . $s['section']); ?></td>
                                    <td class="n"><?php echo $m($s['previous']); ?></td>
                                    <td class="n"><?php echo $s['carried'] === null ? '<span class="fw-muted">—</span>' : $m($s['carried']); ?></td>
                                    <td class="n"><input type="text" inputmode="decimal" class="form-control input-sm text-right cr-amt" name="amount[<?php echo (int) $s['student_session_id']; ?>]" value="<?php echo $e(number_format($start, 2, '.', '')); ?>" data-carried="<?php echo $s['carried'] === null ? '' : $e($s['carried']); ?>" aria-label="Amount for <?php echo $e($name); ?>"></td>
                                    <td class="cr-state"></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
            <div class="fw-sum">
                <span id="cr_text"></span>
                <span>
                    <button type="submit" class="btn btn-success" id="cr_go"><i class="fa fa-check"></i> Bring forward</button>
                    <a class="btn btn-default" href="<?php echo site_url('admin/fees/carryforward'); ?>">Back</a>
                </span>
            </div>
        </form>
        <script>
            (function () {
                var sym = <?php echo json_encode((string) $currency_symbol); ?>;
                var inputs = [].slice.call(document.querySelectorAll('.cr-amt'));
                var money = function (v) { return sym + v.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}); };
                function update() {
                    var add = 0, upd = 0, total = 0, bad = false;
                    inputs.forEach(function (i) {
                        var raw = i.value.replace(/,/g, '').trim(), v = parseFloat(raw), carried = i.getAttribute('data-carried'), st = i.closest('tr').querySelector('.cr-state');
                        if (raw === '' || isNaN(v) || v < 0) { bad = true; i.parentNode.classList.add('has-error'); st.innerHTML = '<span class="fw-tag overdue">Enter an amount</span>'; return; }
                        i.parentNode.classList.remove('has-error');
                        if (carried === '') {
                            if (v > 0) { add++; total += v; st.innerHTML = '<span class="fw-tag owing">Will be added</span>'; } else { st.innerHTML = ''; }
                        } else {
                            total += v;
                            if (Math.abs(v - parseFloat(carried)) > 0.004) { upd++; st.innerHTML = '<span class="fw-tag owing">Will change</span>'; } else { st.innerHTML = '<span class="fw-tag paid">No change</span>'; }
                        }
                    });
                    document.getElementById('cr_text').innerHTML = bad ? 'Some amounts are not valid.' : 'Add <b>' + add + '</b> student' + (add === 1 ? '' : 's') + (upd ? ', change <b>' + upd + '</b>' : '') + '. Brought forward in these classes: <b>' + money(total) + '</b>.';
                    document.getElementById('cr_go').disabled = bad || (add + upd === 0);
                }
                inputs.forEach(function (i) { i.addEventListener('input', update); });
                update();
            })();
        </script>
    </section>
</div>
