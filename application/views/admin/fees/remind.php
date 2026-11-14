<?php
$e = function ($v) { return html_escape((string) $v); };
$m = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . amountFormat($v); };
$tail = (int) $charge['fsg_id'] . '/' . (int) $charge['class_id'] . '/' . (int) $charge['section_id'];
$title = $charge['is_system'] ? 'Balance brought forward' : $charge['group'];
$sendable = array_filter($rows, function ($r) { return $r['skip'] === ''; });
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-commenting-o"></i> Remind guardians <small><?php echo $e($title); ?> · <?php echo $e($charge['class'] . ' ' . $charge['section']); ?></small></h1>
    </section>
    <section class="content">
        <?php $this->load->view('admin/fees/_nav', array('tab' => $tab)); ?>
        <p><a href="<?php echo site_url('admin/fees/charge/' . $tail); ?>"><i class="fa fa-angle-left"></i> Back to <?php echo $e($charge['class'] . ' ' . $charge['section']); ?></a></p>
        <?php if (!$gateway) { ?>
            <div class="alert alert-warning"><b>No SMS gateway is set up.</b> Set up Termii (or another provider) in System Settings &gt; SMS Setting to send reminders. You can still remind guardians one by one on WhatsApp from the class page.
                <?php if ($this->rbac->hasPrivilege('sms_setting', 'can_view')) { ?><a href="<?php echo site_url('smsconfig'); ?>">Open SMS settings</a><?php } ?></div>
        <?php } ?>
        <?php if (!$rows) { ?>
            <div class="box box-primary"><div class="box-body text-center fw-muted" style="padding:30px">Nobody in <?php echo $e($charge['class'] . ' ' . $charge['section']); ?> owes these fees.</div></div>
        <?php } else { ?>
            <form method="post" action="<?php echo site_url('admin/fees/remind/' . $tail); ?>" id="rmd">
                <?php echo $this->customlib->getCSRF(); ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Each guardian gets their own child's balance</h3>
                        <p class="fw-muted" style="margin:4px 0 0">Untick anyone you do not want to remind. Guardians already reminded about these fees today are left out.</p>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table fw-table">
                            <thead><tr><th style="width:30px"><input type="checkbox" id="rmd_all" checked aria-label="All"></th><th>Student</th><th>Guardian</th><th class="n">Owes</th><th>Message</th></tr></thead>
                            <tbody>
                            <?php foreach ($rows as $r) { ?>
                                <tr<?php echo $r['skip'] ? ' class="fw-muted"' : ''; ?>>
                                    <td><?php if ($r['skip'] === '') { ?><input type="checkbox" class="rmd-pick" name="pick[]" value="<?php echo (int) $r['student_session_id']; ?>" data-pages="<?php echo (int) $r['pages']; ?>" checked aria-label="<?php echo $e($r['name']); ?>"><?php } ?></td>
                                    <td><b><?php echo $e($r['name']); ?></b></td>
                                    <td><?php echo $e($r['guardian']); ?><br><span class="fw-muted"><?php echo $r['phone'] !== '' ? $e($r['phone']) : 'no phone number'; ?></span></td>
                                    <td class="n"><b><?php echo $m($r['balance']); ?></b><?php echo $r['overdue'] > 0 ? '<br><span class="fw-tag overdue">overdue</span>' : ''; ?></td>
                                    <td style="font-size:12px;max-width:420px">
                                        <?php if ($r['skip'] === 'no phone') { ?><span class="fw-tag overdue">No phone number</span> <span class="fw-muted">Add the guardian's phone on the student's profile.</span>
                                        <?php } elseif ($r['skip'] === 'reminded today') { ?><span class="fw-tag paid">Reminded today</span>
                                        <?php } else { ?><?php echo $e($r['message']); ?> <span class="fw-muted">(<?php echo (int) $r['pages']; ?> SMS)</span><?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="fw-sum">
                    <span id="rmd_text"></span>
                    <button type="submit" class="btn btn-success" id="rmd_go" <?php echo $gateway ? '' : 'disabled'; ?>><i class="fa fa-paper-plane"></i> Send reminders</button>
                </div>
            </form>
            <script>
                (function () {
                    var picks = [].slice.call(document.querySelectorAll('.rmd-pick'));
                    var ready = <?php echo $gateway ? 'true' : 'false'; ?>;
                    function update() {
                        var n = 0, pages = 0;
                        picks.forEach(function (c) { if (c.checked) { n++; pages += parseInt(c.getAttribute('data-pages'), 10) || 1; } });
                        document.getElementById('rmd_text').innerHTML = n ? 'Send to <b>' + n + '</b> guardian' + (n === 1 ? '' : 's') + ': <b>' + pages + '</b> SMS page' + (pages === 1 ? '' : 's') + ' from your SMS balance.' : 'Nobody is ticked.';
                        document.getElementById('rmd_go').disabled = !ready || n === 0;
                        document.getElementById('rmd_all').checked = picks.length > 0 && picks.every(function (c) { return c.checked; });
                    }
                    document.getElementById('rmd_all').addEventListener('change', function () { var on = this.checked; picks.forEach(function (c) { c.checked = on; }); update(); });
                    picks.forEach(function (c) { c.addEventListener('change', update); });
                    document.getElementById('rmd').addEventListener('submit', function (ev) {
                        var b = document.getElementById('rmd_go');
                        if (b.dataset.sending) { ev.preventDefault(); return; }
                        b.dataset.sending = '1'; b.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending…';
                    });
                    update();
                })();
            </script>
            <?php if (count($sendable) < count($rows)) { ?><p class="fw-muted" style="margin-top:8px"><?php echo count($rows) - count($sendable); ?> guardian<?php echo count($rows) - count($sendable) === 1 ? ' is' : 's are'; ?> left out (no phone number, or already reminded today).</p><?php } ?>
        <?php } ?>
    </section>
</div>
