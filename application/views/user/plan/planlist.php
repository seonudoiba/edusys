<?php
/* The school owner's account: the subscription, renewal for the next period, and top-ups. */
$e = function ($v) { return html_escape((string) $v); };
$naira = function ($v) { return '&#8358;' . number_format((float) $v); };
$sub = $current;
$flash = $this->session->flashdata('plan_error');
$has_counts = $sub && !empty($sub['student_count']);
?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if ($flash) { ?><div class="alert alert-danger"><?php echo $e($flash); ?></div><?php } ?>
                <div class="box box-primary">
                    <div class="box-header with-border"><h3 class="box-title">Your subscription</h3></div>
                    <div class="box-body">
                        <?php if (!$sub) { ?>
                            <p class="text-muted">No subscription has been paid yet.</p>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-sm-3"><small class="text-muted">Status</small><br><b><?php echo $e($status); ?></b></div>
                                <div class="col-sm-3"><small class="text-muted">Ends</small><br><b><?php echo $e(date('j M Y', $ends)); ?></b><?php if ($days_left >= 0) { ?> <span class="text-muted">(<?php echo (int) $days_left; ?> days)</span><?php } ?></div>
                                <div class="col-sm-3"><small class="text-muted">Active students</small><br><b><?php echo number_format($active); ?></b><?php if ($has_counts) { ?> <span class="text-muted">of <?php echo number_format((int) $sub['student_count']); ?> paid for</span><?php } ?></div>
                                <?php if ($has_counts) { ?><div class="col-sm-3"><small class="text-muted">Rate</small><br><b><?php echo $naira($sub['rate_per_term']); ?></b> <span class="text-muted">per student, per term</span></div><?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php if (!$methods) { ?>
                    <div class="alert alert-warning">Online payment isn't available right now. Please contact <?php echo $e($support); ?> to renew or add students by bank transfer.</div>
                <?php } ?>
            </div>
        </div>
        <?php if ($methods) { ?>
        <div class="row">
            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Renew</h3></div>
                    <form method="get" action="<?php echo site_url('subscription/edit'); ?>">
                        <div class="box-body">
                            <p class="text-muted">The next period starts when the current one ends, so renewing early doesn't lose any days. There is no setup fee.</p>
                            <div class="form-group"><label for="r_students">Students to pay for</label>
                                <input id="r_students" name="students" type="number" min="<?php echo max(1, $active); ?>" required class="form-control" value="<?php echo max($active, $has_counts ? (int) $sub['student_count'] : 0, 1); ?>"></div>
                            <div class="form-group"><label for="r_period">Pay</label>
                                <select id="r_period" name="period" class="form-control">
                                    <?php foreach (Plan_pricing::PERIODS as $k => $l) { ?><option value="<?php echo $k; ?>" <?php echo $has_counts && $sub['billing_period'] === $k ? 'selected' : ''; ?>><?php echo $l === 'Yearly' ? 'Yearly (3 terms)' : 'Each term'; ?></option><?php } ?>
                                </select></div>
                            <div class="form-group"><label for="r_method">Payment method</label>
                                <select id="r_method" name="method" class="form-control"><?php foreach ($methods as $k => $l) { ?><option value="<?php echo $e($k); ?>"><?php echo $e($l); ?></option><?php } ?></select></div>
                            <p><b id="r_total"></b></p>
                        </div>
                        <div class="box-footer"><button class="btn btn-primary" type="submit">Continue to payment</button></div>
                    </form>
                </div>
            </div>
            <?php if ($has_counts && $days_left >= 1) { ?>
                <div class="col-md-6">
                    <div class="box box-default">
                        <div class="box-header with-border"><h3 class="box-title">Add students</h3></div>
                        <form method="get" action="<?php echo site_url('subscription/topup'); ?>">
                            <div class="box-body">
                                <p class="text-muted">Pay for the extra students at your current rate, for the <?php echo (int) $topup_terms; ?> term<?php echo $topup_terms === 1 ? '' : 's'; ?> left. Your end date stays the same.</p>
                                <div class="form-group"><label for="t_students">New total of students</label>
                                    <input id="t_students" name="students" type="number" min="<?php echo (int) $sub['student_count'] + 1; ?>" required class="form-control" value="<?php echo (int) $sub['student_count'] + 1; ?>"></div>
                                <div class="form-group"><label for="t_method">Payment method</label>
                                    <select id="t_method" name="method" class="form-control"><?php foreach ($methods as $k => $l) { ?><option value="<?php echo $e($k); ?>"><?php echo $e($l); ?></option><?php } ?></select></div>
                                <p><b id="t_total"></b></p>
                            </div>
                            <div class="box-footer"><button class="btn btn-primary" type="submit">Continue to payment</button></div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
        <script>
            (function () {
                var bands = <?php echo json_encode($bands, JSON_HEX_TAG | JSON_HEX_AMP); ?>, agreed = <?php echo $has_counts && !empty($sub['quote_id']) ? (float) $sub['rate_per_term'] : 'null'; ?>;
                var fmt = function (n) { return '₦' + Math.round(n).toLocaleString('en-NG'); };
                var rate = function (n) {
                    if (agreed) { return agreed; }
                    for (var i = 0; i < bands.length; i++) {
                        var b = bands[i];
                        if (n >= +b.min_students && (b.max_students === null || n <= +b.max_students)) { return b.rate_per_term === null ? null : +b.rate_per_term; }
                    }
                    return null;
                };
                var $ = function (id) { return document.getElementById(id); };
                function renew() {
                    var n = +$('r_students').value, r = rate(n);
                    $('r_total').textContent = r === null ? 'Please contact us for a quote at this size.' : 'Total: ' + fmt(n * r * ($('r_period').value === 'year' ? 3 : 1));
                }
                $('r_students').addEventListener('input', renew);
                $('r_period').addEventListener('change', renew);
                renew();
                if ($('t_students')) {
                    var paid = <?php echo $has_counts ? (int) $sub['student_count'] : 0; ?>, cur = <?php echo $has_counts ? (float) $sub['rate_per_term'] : 0; ?>, terms = <?php echo (int) $topup_terms; ?>;
                    var top = function () { var extra = +$('t_students').value - paid; $('t_total').textContent = extra > 0 ? 'Total: ' + fmt(extra * cur * terms) : ''; };
                    $('t_students').addEventListener('input', top);
                    top();
                }
            })();
        </script>
        <?php } ?>
    </section>
</div>
