<?php
$e = function ($v) { return html_escape((string) $v); };
$h = $health;
$size = function ($b) {
    if ($b === false || $b === null) { return '—'; }
    $u = array('B', 'KB', 'MB', 'GB', 'TB'); $i = 0;
    while ($b >= 1024 && $i < 4) { $b /= 1024; $i++; }
    return round($b, $i ? 1 : 0) . ' ' . $u[$i];
};
$ok = function ($good, $text) { return '<span class="label ' . ($good ? 'label-success' : 'label-warning') . '">' . html_escape($text) . '</span>'; };
$v = function ($k) use ($values) { return html_escape((string) $values[$k]); };
?>
<style>
    .ps-dl dt { color: #777; font-weight: normal; }
    .ps-dl dd { margin-bottom: 8px; }
    .ps-log { background: #1e1e1e; color: #ddd; font: 11px/1.5 Consolas, monospace; padding: 10px; max-height: 260px; overflow: auto; white-space: pre-wrap; word-break: break-all; border-radius: 4px; }
    .ps-muted { color: #888; }
</style>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-cogs"></i> Platform settings <small>and system health</small></h1></section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <div class="row">
            <div class="col-md-6">
                <form method="post" action="<?php echo site_url('admin/platform/save_settings'); ?>" class="box box-primary">
                    <?php echo $token; ?>
                    <div class="box-header with-border"><h3 class="box-title">Settings</h3></div>
                    <div class="box-body">
                        <div class="form-group"><label for="platform_name">Platform name</label>
                            <input id="platform_name" name="platform_name" class="form-control" maxlength="100" required value="<?php echo $v('platform_name') ?: 'Edusys'; ?>">
                            <p class="help-block">Shown as the sender and in the platform's emails.</p></div>
                        <div class="row">
                            <div class="col-sm-6 form-group"><label for="support_email">Support email</label>
                                <input id="support_email" name="support_email" type="email" class="form-control" value="<?php echo $v('support_email'); ?>" placeholder="<?php echo $e($effective['support_email']); ?>">
                                <p class="help-block">Reply-to of every email. Empty: the sending address.</p></div>
                            <div class="col-sm-6 form-group"><label for="support_phone">Support phone</label>
                                <input id="support_phone" name="support_phone" class="form-control" maxlength="50" value="<?php echo $v('support_phone'); ?>"></div>
                        </div>
                        <div class="form-group"><label for="notify_email">Platform notifications go to</label>
                            <input id="notify_email" name="notify_email" type="email" class="form-control" value="<?php echo $v('notify_email'); ?>" placeholder="<?php echo $e($effective['notify_email']); ?>">
                            <p class="help-block">New registrations and receipts to review. Empty: the support email.</p></div>
                        <hr>
                        <h4>Renewal reminders</h4>
                        <div class="checkbox"><label><input type="checkbox" name="reminders_enabled" value="1" <?php echo $values['reminders_enabled'] === '0' ? '' : 'checked'; ?>> Email the owner before the subscription ends</label></div>
                        <div class="form-group"><label for="reminder_days">Days before the end</label>
                            <input id="reminder_days" name="reminder_days" class="form-control" value="<?php echo $v('reminder_days') ?: '30,7,1'; ?>" style="max-width:220px">
                            <p class="help-block">Comma separated, e.g. 30,7,1. Sent by the daily job (<code>php index.php platform_cron reminders</code>).</p></div>
                        <div class="checkbox"><label><input type="checkbox" name="expired_notice" value="1" <?php echo $values['expired_notice'] === '0' ? '' : 'checked'; ?>> Email the owner when the subscription has ended</label></div>
                        <hr>
                        <h4>When a subscription ends</h4>
                        <div class="row">
                            <div class="col-sm-4 form-group"><label for="grace_days">Grace period (days)</label>
                                <input id="grace_days" name="grace_days" type="number" min="0" max="365" class="form-control" value="<?php echo $values['grace_days'] !== null && $values['grace_days'] !== '' ? $v('grace_days') : '7'; ?>"></div>
                            <div class="col-sm-8 form-group"><label for="expiry_action">After the grace period</label>
                                <select id="expiry_action" name="expiry_action" class="form-control">
                                    <option value="warn" <?php echo $values['expiry_action'] === 'disable' ? '' : 'selected'; ?>>Warn only (banner for the school, emails)</option>
                                    <option value="disable" <?php echo $values['expiry_action'] === 'disable' ? 'selected' : ''; ?>>Disable the school until it renews</option>
                                </select>
                                <p class="help-block">A disabled school is enabled again automatically when it renews. Its data is kept.</p></div>
                        </div>
                        <hr>
                        <h4>Invoices</h4>
                        <div class="row">
                            <div class="col-sm-6 form-group"><label for="invoice_company">Company name</label>
                                <input id="invoice_company" name="invoice_company" class="form-control" maxlength="150" value="<?php echo $v('invoice_company'); ?>" placeholder="the platform name"></div>
                            <div class="col-sm-6 form-group"><label for="invoice_tax_id">Tax ID / RC number</label>
                                <input id="invoice_tax_id" name="invoice_tax_id" class="form-control" maxlength="60" value="<?php echo $v('invoice_tax_id'); ?>"></div>
                        </div>
                        <div class="form-group"><label for="invoice_address">Address</label>
                            <textarea id="invoice_address" name="invoice_address" class="form-control" rows="2" maxlength="500"><?php echo $v('invoice_address'); ?></textarea></div>
                        <div class="form-group"><label for="invoice_footer">Footer</label>
                            <input id="invoice_footer" name="invoice_footer" class="form-control" maxlength="500" value="<?php echo $v('invoice_footer') ?: 'Thank you for your business.'; ?>"></div>
                        <hr>
                        <div class="form-group"><label for="extra_reserved_subdomains">Extra reserved subdomains</label>
                            <textarea id="extra_reserved_subdomains" name="extra_reserved_subdomains" class="form-control" rows="2" placeholder="e.g. blog, shop, careers"><?php echo $v('extra_reserved_subdomains'); ?></textarea>
                            <p class="help-block">Schools can't use these, on top of the built-in list (www, admin, api, mail ...).</p></div>
                    </div>
                    <div class="box-footer"><button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save settings</button></div>
                </form>

                <form method="post" action="<?php echo site_url('admin/platform/test_email'); ?>" class="box box-default">
                    <?php echo $token; ?>
                    <div class="box-header with-border"><h3 class="box-title">Test the mail</h3></div>
                    <div class="box-body" style="display:flex;gap:8px">
                        <input name="to" type="email" class="form-control" required placeholder="Send a test email to">
                        <button class="btn btn-default" type="submit"><i class="fa fa-paper-plane"></i> Send test</button>
                    </div>
                </form>
            </div>

            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">System health</h3></div>
                    <div class="box-body">
                        <dl class="dl-horizontal ps-dl">
                            <dt>Database</dt><dd><?php echo $ok($h['migration_current'] === $h['migration_target'], $h['migration_current'] === $h['migration_target'] ? 'Up to date' : 'Migrations pending'); ?>
                                <span class="ps-muted">version <?php echo (int) $h['migration_current']; ?> of <?php echo (int) $h['migration_target']; ?> · MySQL <?php echo $e($h['mysql']); ?></span></dd>
                            <dt>PHP</dt><dd><?php echo $e($h['php']); ?></dd>
                            <dt>Domain</dt><dd><?php echo $e($h['base_domain']); ?> <span class="ps-muted">central: <?php echo $e($h['central_hosts']); ?></span> <?php echo $h['force_https'] ? '<span class="label label-success">HTTPS</span>' : ''; ?></dd>
                            <dt>SQL guard</dt><dd><?php echo $ok($h['raw_sql_mode'] === 'throw', $h['raw_sql_mode']); ?> <span class="ps-muted">use "throw" in production</span></dd>
                            <dt>Mail</dt><dd><?php echo $e($h['smtp_host']); ?> <span class="ps-muted">login <?php echo $e($h['smtp_login']); ?> · from <?php echo $e($h['from_email']); ?></span><br>
                                <span class="ps-muted">last 7 days:</span> <?php echo (int) $h['mail_sent']; ?> sent, <?php echo $h['mail_failed'] ? '<span class="text-danger">' . (int) $h['mail_failed'] . ' failed</span>' : '0 failed'; ?>
                                · <a href="<?php echo site_url('admin/platform/email_log'); ?>">email log</a></dd>
                            <dt>Paystack</dt><dd><?php echo $ok($h['paystack'] === 'live key', $h['paystack']); ?></dd>
                            <dt>Disk</dt><dd><?php echo $size($h['disk_free']); ?> free of <?php echo $size($h['disk_total']); ?></dd>
                            <dt>Scheduled jobs</dt><dd>
                                <?php if (!$h['cron']) { ?><span class="label label-warning">Never ran</span> <span class="ps-muted">install the crontab (DEPLOYMENT.md §7)</span>
                                <?php } else { foreach ($h['cron'] as $task => $when) { $age = time() - strtotime($when); ?>
                                    <div><?php echo $e($task); ?>: <?php echo $e($when); ?> <?php echo $age > 2 * 86400 ? '<span class="label label-warning">over 2 days ago</span>' : ''; ?></div>
                                <?php } } ?></dd>
                        </dl>
                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Storage per school</h3></div>
                    <div class="box-body no-padding">
                        <table class="table table-condensed">
                            <?php if (!$h['uploads']) { ?><tr><td class="ps-muted">No school folders yet.</td></tr><?php } ?>
                            <?php foreach (array_slice($h['uploads'], 0, 12, true) as $id => $u) { ?>
                                <tr><td><a href="<?php echo site_url('admin/schools/view/' . (int) $id); ?>"><?php echo $e(isset($u['name']) ? $u['name'] : 'School #' . $id); ?></a></td>
                                    <td class="text-right"><?php echo $size($u['bytes']); ?></td><td class="text-right ps-muted"><?php echo (int) $u['files']; ?> files</td></tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Latest errors</h3> <span class="ps-muted"><?php echo $e($h['error_log']['file']); ?></span></div>
                    <div class="box-body">
                        <?php if (!$h['error_log']['lines']) { ?><p class="ps-muted">No errors logged.</p>
                        <?php } else { ?><div class="ps-log"><?php echo $e(implode("\n", $h['error_log']['lines'])); ?></div><?php } ?>
                    </div>
                </div>
                <?php if ($h['sql_guard_log']['lines']) { ?>
                    <div class="box box-default">
                        <div class="box-header with-border"><h3 class="box-title">SQL guard log</h3> <span class="ps-muted"><?php echo $e($h['sql_guard_log']['file']); ?></span></div>
                        <div class="box-body"><div class="ps-log"><?php echo $e(implode("\n", $h['sql_guard_log']['lines'])); ?></div></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
