<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-user-plus"></i> Students not charged yet <small><?php echo $e($session_name); ?> session</small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <p><a href="<?php echo site_url('admin/fees/adjust'); ?>"><i class="fa fa-angle-left"></i> Discounts and balances</a></p>

        <?php if (!$students) { ?>
            <div class="box box-primary"><div class="box-body text-center fw-muted" style="padding:30px">Every student is charged the fees of their class. Nothing to do.</div></div>
        <?php } else { ?>
            <form method="post" action="<?php echo site_url('admin/fees/catchup'); ?>" id="cu">
                <?php echo $this->customlib->getCSRF(); ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">These students are in classes that have fees, but are not charged them</h3>
                        <p class="fw-muted" style="margin:4px 0 0">Usually they were admitted after the fees were set. Untick anyone who should not pay a set of fees (for example a full scholarship).</p>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-hover fw-table">
                            <thead><tr><th style="width:30px"><input type="checkbox" id="cu_all" checked aria-label="All"></th><th>Student</th><th>Class</th><th>Fees to charge</th><th class="n">Amount</th></tr></thead>
                            <tbody>
                            <?php foreach ($students as $s) {
                                $name = $this->customlib->getFullName($s['firstname'], $s['middlename'], $s['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?>
                                <tr>
                                    <td></td>
                                    <td><b><?php echo $e($name); ?></b><br><span class="fw-muted"><?php echo $e($s['admission_no']); ?><?php echo $s['admission_date'] && $s['admission_date'] !== '0000-00-00' ? ' · admitted ' . $e(date($this->customlib->getSchoolDateFormat(), strtotime($s['admission_date']))) : ''; ?></span></td>
                                    <td><?php echo $e($s['class'] . ' ' . $s['section']); ?></td>
                                    <td>
                                        <?php foreach ($s['missing'] as $f) { ?>
                                            <label style="display:block;font-weight:normal;margin:0 0 3px"><input type="checkbox" class="cu-pick" name="pick[]" value="<?php echo (int) $s['student_session_id'] . '-' . (int) $f['fsg_id']; ?>" data-amount="<?php echo $e($f['amount']); ?>" data-student="<?php echo (int) $s['student_session_id']; ?>" checked> <?php echo $e($f['group']); ?> <span class="fw-muted">(<?php echo $m($f['amount']); ?>)</span></label>
                                        <?php } ?>
                                    </td>
                                    <td class="n"><b><?php echo $m($s['amount']); ?></b></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="fw-sum">
                    <span>Charge <b id="cu_n">0</b> students, <b id="cu_total"></b> in total.</span>
                    <button type="submit" class="btn btn-success" id="cu_go"><i class="fa fa-check"></i> Charge them</button>
                </div>
            </form>
            <script>
                (function () {
                    var sym = <?php echo json_encode((string) $currency_symbol); ?>;
                    var picks = [].slice.call(document.querySelectorAll('.cu-pick'));
                    function update() {
                        var total = 0, who = {};
                        picks.forEach(function (c) { if (c.checked) { total += parseFloat(c.getAttribute('data-amount')) || 0; who[c.getAttribute('data-student')] = 1; } });
                        var n = Object.keys(who).length;
                        document.getElementById('cu_n').textContent = n;
                        document.getElementById('cu_total').textContent = sym + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        document.getElementById('cu_go').disabled = n === 0;
                        document.getElementById('cu_all').checked = picks.every(function (c) { return c.checked; });
                    }
                    document.getElementById('cu_all').addEventListener('change', function () { var on = this.checked; picks.forEach(function (c) { c.checked = on; }); update(); });
                    picks.forEach(function (c) { c.addEventListener('change', update); });
                    update();
                })();
            </script>
        <?php } ?>
    </section>
</div>
