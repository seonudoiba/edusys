<?php
$e = function ($v) { return html_escape((string) $v); };
$s = $school;
$money = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . number_format((float) $v, 2); };
$date = function ($v, $fmt = 'd M Y') { return $v ? html_escape(date($fmt, strtotime($v))) : '—'; };
$state_labels = array(
    'waiting_approval' => array('Waiting for approval', 'label-warning'),
    'awaiting_payment' => array('Awaiting payment', 'label-default'),
    'active' => array('Active', 'label-success'),
    'disabled' => array('Disabled', 'label-danger'),
);
$l = $state_labels[$s['state']];
$can_edit = $this->rbac->hasPrivilege('schools', 'can_edit');
$can_delete = $this->rbac->hasPrivilege('schools', 'can_delete');
$action = function ($name) use ($s) { return site_url('admin/schools/' . $name . '/' . (int) $s['id']); };
$token = '<input type="hidden" name="form_token" value="' . html_escape($form_token) . '">';
$transfer_labels = array('pending' => 'label-warning', 'awaiting_receipt' => 'label-default', 'approved' => 'label-success', 'rejected' => 'label-danger');
?>
<style>
    .sv-head { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between; }
    .sv-head h1 { margin: 0; font-size: 24px; }
    .sv-head .label { font-size: 12px; vertical-align: middle; margin-left: 6px; }
    .sv-actions { display: flex; flex-wrap: wrap; gap: 6px; }
    .sv-actions form { display: inline; margin: 0; }
    .sv-stat { text-align: center; padding: 12px 6px; border: 1px solid #eee; border-radius: 4px; background: #fafafa; margin-bottom: 10px; }
    .sv-stat b { display: block; font-size: 22px; }
    .sv-stat span { color: #777; font-size: 12px; }
    .sv-dl dt { color: #777; font-weight: normal; }
    .sv-dl dd { margin-bottom: 8px; word-break: break-word; }
    .sv-muted { color: #888; }
    .sv-link { font-family: monospace; font-size: 12px; }
    .sv-setup { border-left: 4px solid #f39c12; }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <div class="sv-head">
            <h1>
                <a href="<?php echo site_url('admin/schools'); ?>" class="sv-muted" title="All schools"><i class="fa fa-arrow-left"></i></a>
                <?php echo $e($s['school_name']); ?>
                <span class="label <?php echo $l[1]; ?>"><?php echo $l[0]; ?></span>
                <?php if (!empty($s['is_platform'])) { ?><span class="label label-primary">Platform</span><?php } ?>
            </h1>
            <div class="sv-actions">
                <?php if ($site_url && $s['set_up']) { ?>
                    <a class="btn btn-default" href="<?php echo $e($site_url); ?>" target="_blank" rel="noopener"><i class="fa fa-external-link"></i> Open school site</a>
                <?php } ?>
                <?php if ($can_edit && $s['set_up'] && $s['enabled'] && empty($s['is_platform'])) { ?>
                    <form method="post" action="<?php echo $action('impersonate'); ?>" target="_blank"
                          onsubmit="return confirm('Open the admin panel of this school as its super admin? It is recorded in both audit trails.');">
                        <?php echo $token; ?>
                        <button class="btn btn-primary" type="submit"><i class="fa fa-life-ring"></i> Log in as school admin</button>
                    </form>
                <?php } ?>
                <?php if ($can_edit) { ?>
                    <a class="btn btn-default" href="<?php echo site_url('admin/schools/edit/' . (int) $s['id']); ?>"><i class="fa fa-pencil"></i> Edit details</a>
                    <?php if ($s['set_up'] && empty($s['is_platform'])) { ?>
                        <form method="post" action="<?php echo $action('toggle_status'); ?>"
                              onsubmit="return confirm('<?php echo $s['enabled'] ? 'Disable this school? Nobody will be able to log in until it is enabled again.' : 'Enable this school?'; ?>');">
                            <?php echo $token; ?>
                            <button class="btn <?php echo $s['enabled'] ? 'btn-danger' : 'btn-success'; ?>" type="submit">
                                <i class="fa <?php echo $s['enabled'] ? 'fa-ban' : 'fa-check'; ?>"></i> <?php echo $s['enabled'] ? 'Disable school' : 'Enable school'; ?>
                            </button>
                        </form>
                    <?php } ?>
                <?php } ?>
                <?php if ($can_delete && !$s['set_up'] && empty($s['is_platform'])) { ?>
                    <form method="post" action="<?php echo $action('delete'); ?>" onsubmit="return confirm('Delete this school and its registration? This can\'t be undone.');">
                        <?php echo $token; ?>
                        <button class="btn btn-default text-danger" type="submit"><i class="fa fa-trash"></i> Delete</button>
                    </form>
                <?php } ?>
            </div>
        </div>
    </section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <?php if ($set_password_url) { ?>
            <div class="alert alert-info">
                <b>One-time set-password link</b> (give it to the school's admin if the email doesn't arrive):
                <input type="text" class="form-control sv-link" readonly value="<?php echo $e($set_password_url); ?>" onclick="this.select()" style="margin-top:6px">
            </div>
        <?php } ?>

        <?php if (!$s['set_up'] && empty($s['is_platform']) && $s['status_reason'] !== 'purged') { ?>
            <div class="box box-warning sv-setup">
                <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-rocket"></i> Approve and set up this school</h3></div>
                <div class="box-body">
                    <?php if ($s['state'] === 'awaiting_payment') { ?>
                        <div class="alert alert-warning">This school hasn't paid yet. Check the payment before setting it up, or record a manual subscription below.</div>
                    <?php } ?>
                    <p>Setting up creates the school's data (sessions, menus, settings, default lists) and its super admin account
                        (<b><?php echo $e($s['email_address']); ?></b>). The owner then gets a one-time link to choose a password.</p>
                    <?php if ($can_edit) { ?>
                        <form method="post" action="<?php echo $action('setup'); ?>" class="form-inline" onsubmit="return confirm('Set up this school now?');">
                            <?php echo $token; ?>
                            <div class="form-group">
                                <label for="subdomain">School address</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo is_https() ? 'https' : 'http'; ?>://</span>
                                    <input id="subdomain" name="subdomain" type="text" class="form-control" required pattern="[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?"
                                           title="Lowercase letters, digits and inner hyphens" value="<?php echo $e($s['subdomain']); ?>" placeholder="greenfield">
                                    <span class="input-group-addon">.<?php echo $e($base_domain); ?></span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning"><i class="fa fa-check"></i> Approve &amp; set up</button>
                        </form>
                        <p class="sv-muted" style="margin-top:8px">Lowercase letters, digits and hyphens. It can't be changed once the school is set up.</p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-8">
                <?php if ($usage) { ?>
                    <div class="box box-primary">
                        <div class="box-header with-border"><h3 class="box-title">Usage</h3></div>
                        <div class="box-body">
                            <div class="row">
                                <?php $of = function ($n) { return (int) $n > 0 ? ' <small style="font-size:12px;color:#888">/ ' . number_format((int) $n) . '</small>' : ''; }; ?>
                                <div class="col-xs-6 col-sm-4 col-lg-2"><div class="sv-stat"><b><?php echo (int) $usage['students'] . $of($limits['max_students']); ?></b><span>Active students</span></div></div>
                                <div class="col-xs-6 col-sm-4 col-lg-2"><div class="sv-stat"><b><?php echo (int) $usage['staff'] . $of($limits['max_staff']); ?></b><span>Active staff</span></div></div>
                                <div class="col-xs-6 col-sm-4 col-lg-2"><div class="sv-stat"><b><?php echo (int) $usage['classes']; ?></b><span>Classes</span></div></div>
                                <div class="col-xs-6 col-sm-4 col-lg-2"><div class="sv-stat"><b><?php echo (int) $usage['sections']; ?></b><span>Sections</span></div></div>
                                <div class="col-xs-6 col-sm-4 col-lg-2"><div class="sv-stat"><b><?php echo (int) $usage['logins_30_days']; ?></b><span>Logins, 30 days</span></div></div>
                                <div class="col-xs-6 col-sm-4 col-lg-2"><div class="sv-stat"><b><?php echo round($storage_bytes / 1048576, 1) . ((int) $limits['max_storage_mb'] > 0 ? ' <small style="font-size:12px;color:#888">/ ' . (int) $limits['max_storage_mb'] . '</small>' : ''); ?></b><span>Storage (MB)</span></div></div>
                                <div class="col-xs-6 col-sm-4 col-lg-2"><div class="sv-stat"><b style="font-size:14px;line-height:31px"><?php echo $usage['last_login'] ? $date($usage['last_login']) : 'Never'; ?></b><span>Last login</span></div></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="box box-primary">
                    <div class="box-header with-border"><h3 class="box-title">Subscription</h3></div>
                    <div class="box-body">
                        <?php if ($s['paid_subscription']) { $p = $s['paid_subscription']; ?>
                            <div class="row">
                                <div class="col-sm-4"><div class="sv-muted">Plan</div><b><?php echo $e($p['plan_title'] ?: 'Plan #' . $p['plan_id']); ?></b></div>
                                <div class="col-sm-4"><div class="sv-muted">Started</div><b><?php echo $date($p['created_at']); ?></b> · <?php echo (int) $p['days']; ?> days</div>
                                <div class="col-sm-4"><div class="sv-muted">Expires</div>
                                    <b><?php echo $date($s['expires_at']); ?></b>
                                    <?php if ($s['expiry'] === 'expired') { ?><span class="label label-danger">Expired</span>
                                    <?php } elseif ($s['expiry'] === 'expiring') { ?><span class="label label-warning"><?php echo (int) $s['days_left']; ?> days left</span>
                                    <?php } else { ?><span class="text-success"><?php echo (int) $s['days_left']; ?> days left</span><?php } ?>
                                </div>
                            </div>
                        <?php } else { ?>
                            <p class="sv-muted">No paid subscription.</p>
                        <?php } ?>
                        <?php if ($can_edit && empty($s['is_platform'])) { ?>
                            <hr>
                            <form method="post" action="<?php echo $action('extend'); ?>" class="form-inline"
                                  onsubmit="return confirm('<?php echo $s['paid_subscription'] ? 'Add these days to the current subscription?' : 'Record this subscription without a payment?'; ?>');">
                                <?php echo $token; ?>
                                <?php if (!$s['paid_subscription']) { ?>
                                    <div class="form-group">
                                        <label for="plan_id">Plan</label>
                                        <select id="plan_id" name="plan_id" class="form-control" required>
                                            <option value="">Choose…</option>
                                            <?php foreach ($plans as $plan) { ?>
                                                <option value="<?php echo (int) $plan['plan_id']; ?>" data-days="<?php echo (int) $plan['days']; ?>"><?php echo $e($plan['title']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="days"><?php echo $s['paid_subscription'] ? 'Extend by' : 'Days'; ?></label>
                                    <input id="days" name="days" type="number" min="1" max="3650" class="form-control" style="width:110px" required value="<?php echo $s['paid_subscription'] ? 30 : ''; ?>"> days
                                </div>
                                <button type="submit" class="btn btn-default"><i class="fa fa-calendar-plus-o"></i> <?php echo $s['paid_subscription'] ? 'Extend subscription' : 'Record subscription'; ?></button>
                            </form>
                            <p class="sv-muted" style="margin-top:6px"><?php echo $s['paid_subscription'] ? 'For a goodwill extension or a payment received outside the system.' : 'For a school added by the platform or paid outside the system. No payment is collected.'; ?></p>
                        <?php } ?>
                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Subscription history</h3></div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-condensed">
                            <thead><tr><th>Date</th><th>Plan</th><th>Days</th><th>Method</th><th class="text-right">Amount</th><th>Payment</th></tr></thead>
                            <tbody>
                                <?php if (!$subscriptions) { ?><tr><td colspan="6" class="sv-muted">No subscriptions.</td></tr><?php } ?>
                                <?php foreach ($subscriptions as $sub) { ?>
                                    <tr>
                                        <td><?php echo $date($sub['created_at']); ?></td>
                                        <td><?php echo $e($sub['plan_title'] ?: 'Plan #' . $sub['plan_id']); ?></td>
                                        <td><?php echo (int) $sub['days']; ?></td>
                                        <td><?php echo $e(ucwords(str_replace('_', ' ', $sub['payment_method'] ?: 'free'))); ?></td>
                                        <td class="text-right"><?php echo $money($sub['total']); ?></td>
                                        <td><?php echo (int) $sub['payment_status'] === 1 ? '<span class="label label-success">Paid</span>' : '<span class="label label-default">Pending</span>'; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($invoices) { ?>
                    <div class="box box-default">
                        <div class="box-header with-border"><h3 class="box-title">Invoices</h3></div>
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-condensed">
                                <?php foreach ($invoices as $inv) { ?>
                                    <tr>
                                        <td><b><?php echo $e($inv['invoice_no']); ?></b></td>
                                        <td><?php echo $date($inv['issued_at']); ?></td>
                                        <td><?php echo $e($inv['description']); ?></td>
                                        <td class="text-right"><?php echo $money($inv['total']); ?></td>
                                        <td class="text-right"><a class="btn btn-xs btn-default" href="<?php echo site_url('admin/billing/invoice/' . (int) $inv['id']); ?>" target="_blank">PDF</a></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($bank_transfers) { ?>
                    <div class="box box-default">
                        <div class="box-header with-border"><h3 class="box-title">Bank transfers</h3></div>
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-condensed">
                                <thead><tr><th>Submitted</th><th class="text-right">Paid</th><th>Paid on</th><th>Reference</th><th>Status</th><th></th></tr></thead>
                                <tbody>
                                    <?php foreach ($bank_transfers as $t) { ?>
                                        <tr>
                                            <td><?php echo $date($t['submitted_at'], 'd M Y H:i'); ?></td>
                                            <td class="text-right"><?php echo $t['amount'] !== null ? $money($t['amount']) : '—'; ?></td>
                                            <td><?php echo $e($t['payment_date']); ?></td>
                                            <td><?php echo $e($t['reference']); ?></td>
                                            <td><span class="label <?php echo isset($transfer_labels[$t['status']]) ? $transfer_labels[$t['status']] : 'label-default'; ?>"><?php echo $e(ucwords(str_replace('_', ' ', $t['status']))); ?></span></td>
                                            <td class="text-right"><a class="btn btn-xs btn-default" href="<?php echo site_url('admin/subscription_bank_transfers/view/' . (int) $t['id']); ?>">Open</a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($modules) { ?>
                    <div class="box box-default">
                        <div class="box-header with-border"><h3 class="box-title">Modules</h3>
                            <span class="sv-muted" style="margin-left:8px">what the school can use; a switched-off module leaves its menus</span></div>
                        <div class="box-body">
                            <div class="row">
                                <?php foreach ($modules as $m) { $on = (int) $m['is_active'] === 1; ?>
                                    <div class="col-sm-6 col-lg-4" style="margin-bottom:6px">
                                        <?php if ($can_edit && (int) $m['system'] !== 1) { ?>
                                            <form method="post" action="<?php echo $action('toggle_module'); ?>" style="display:inline">
                                                <?php echo $token; ?><input type="hidden" name="module_id" value="<?php echo (int) $m['id']; ?>">
                                                <button type="submit" class="btn btn-xs <?php echo $on ? 'btn-success' : 'btn-default'; ?>" title="<?php echo $on ? 'Switch off' : 'Switch on'; ?>" style="min-width:34px"><?php echo $on ? 'On' : 'Off'; ?></button>
                                            </form>
                                        <?php } else { ?>
                                            <span class="label label-success" title="Always on">On</span>
                                        <?php } ?>
                                        <span class="<?php echo $on ? '' : 'sv-muted'; ?>"><?php echo $e($m['name']); ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Activity</h3>
                        <span class="sv-muted" style="margin-left:8px">what the platform team did with this school</span></div>
                    <div class="box-body no-padding">
                        <table class="table table-condensed">
                            <?php if (!$activity) { ?><tr><td class="sv-muted">Nothing recorded yet.</td></tr><?php } ?>
                            <?php foreach ($activity as $act) { ?>
                                <tr>
                                    <td style="width:130px" class="sv-muted"><?php echo $date($act['time'], 'd M Y H:i'); ?></td>
                                    <td><?php echo $e($act['message']); ?></td>
                                    <td class="text-right sv-muted"><?php echo $e(trim($act['name'] . ' ' . $act['surname'])); ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <?php if ($emails) { ?>
                    <div class="box box-default">
                        <div class="box-header with-border"><h3 class="box-title">Emails sent</h3>
                            <div class="box-tools"><a class="btn btn-box-tool" href="<?php echo site_url('admin/platform/email_log?school=' . (int) $s['id']); ?>">All</a></div></div>
                        <div class="box-body no-padding">
                            <table class="table table-condensed">
                                <?php foreach ($emails as $em) { ?>
                                    <tr>
                                        <td style="width:130px" class="sv-muted"><?php echo $date($em['created_at'], 'd M Y H:i'); ?></td>
                                        <td><?php echo $e($em['subject']); ?><div class="sv-muted" style="font-size:11px"><?php echo $e($em['recipient']); ?></div></td>
                                        <td class="text-right"><span class="label <?php echo $em['status'] === 'sent' ? 'label-success' : ($em['status'] === 'disabled' ? 'label-default' : 'label-danger'); ?>"><?php echo $e(ucfirst($em['status'])); ?></span></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="col-md-4">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Profile</h3></div>
                    <div class="box-body">
                        <dl class="sv-dl">
                            <dt>Site</dt>
                            <dd><?php if ($site_url) { ?>
                                    <?php if ($s['set_up']) { ?><a href="<?php echo $e($site_url); ?>" target="_blank" rel="noopener"><?php echo $e($site_url); ?></a>
                                    <?php } else { echo $e($site_url) . ' <span class="sv-muted">(not set up)</span>'; } ?>
                                <?php } else { ?><span class="sv-muted">No subdomain yet</span><?php } ?></dd>
                            <dt>Owner</dt><dd><?php echo $e($s['owner_name']) ?: '—'; ?></dd>
                            <dt>Email</dt><dd><a href="mailto:<?php echo $e($s['email_address']); ?>"><?php echo $e($s['email_address']); ?></a></dd>
                            <dt>Phone</dt><dd><?php echo $e($s['phone_number']) ?: '—'; ?></dd>
                            <dt>Address</dt><dd><?php echo nl2br($e($s['school_address'])) ?: '—'; ?></dd>
                            <dt>Principal</dt><dd><?php echo $e($s['principal_name']) ?: '—'; ?></dd>
                            <dt>Website</dt><dd><?php echo $s['website'] ? '<a href="' . $e($s['website']) . '" target="_blank" rel="noopener nofollow">' . $e($s['website']) . '</a>' : '—'; ?></dd>
                            <dt>School code</dt><dd><?php echo $e($s['school_code']) ?: '—'; ?></dd>
                            <dt>Registered</dt><dd><?php echo $date($s['created_at'], 'd M Y H:i'); ?> · #<?php echo (int) $s['id']; ?></dd>
                        </dl>
                    </div>
                </div>

                <?php if ($s['set_up']) { ?>
                    <div class="box box-default">
                        <div class="box-header with-border"><h3 class="box-title">Super admins</h3></div>
                        <div class="box-body">
                            <?php if (!$admins) { ?><p class="sv-muted">No super admin account.</p><?php } ?>
                            <?php foreach ($admins as $a) { ?>
                                <div style="margin-bottom:12px">
                                    <b><?php echo $e(trim($a['name'] . ' ' . $a['surname'])); ?></b>
                                    <?php if ((int) $a['is_active'] !== 1) { ?><span class="label label-default">Inactive</span><?php } ?>
                                    <?php if (!empty($a['verification_code'])) { ?><span class="label label-warning" title="A set-password link is outstanding">Link sent</span><?php } ?>
                                    <div class="sv-muted"><?php echo $e($a['email']); ?></div>
                                    <?php if ($can_edit) { ?>
                                        <form method="post" action="<?php echo $action('resend_access'); ?>" style="margin-top:4px"
                                              onsubmit="return confirm('Create a new set-password link for <?php echo $e(addslashes($a['email'])); ?>? Any earlier link stops working.');">
                                            <?php echo $token; ?>
                                            <input type="hidden" name="staff_id" value="<?php echo (int) $a['id']; ?>">
                                            <button class="btn btn-xs btn-default" type="submit"><i class="fa fa-key"></i> Send new set-password link</button>
                                        </form>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="box box-default">
                        <div class="box-header with-border"><h3 class="box-title">Recent logins</h3></div>
                        <div class="box-body no-padding">
                            <table class="table table-condensed">
                                <?php if (!$logins) { ?><tr><td class="sv-muted">Nobody has logged in yet.</td></tr><?php } ?>
                                <?php foreach ($logins as $lg) { ?>
                                    <tr>
                                        <td><?php echo $e($lg['user']); ?><div class="sv-muted" style="font-size:11px"><?php echo $e($lg['role']); ?></div></td>
                                        <td class="text-right sv-muted" style="font-size:12px"><?php echo $date($lg['login_datetime'], 'd M H:i'); ?></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                <?php } ?>

                <?php if (empty($s['is_platform']) && ($can_edit || $can_delete)) { ?>
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Data</h3></div>
                    <div class="box-body">
                        <?php if ($s['status_reason'] === 'purged') { ?>
                            <p class="text-danger"><i class="fa fa-trash"></i> This school's data and files were purged. Its subscriptions and invoices are kept.</p>
                        <?php } else { ?>
                            <?php if ($can_edit) { ?>
                                <form method="post" action="<?php echo $action('export'); ?>" style="margin-bottom:8px">
                                    <?php echo $token; ?>
                                    <button class="btn btn-default btn-sm" type="submit"><i class="fa fa-download"></i> Export data and files</button>
                                    <div class="sv-muted" style="font-size:11px;margin-top:4px">An SQL file of the school's rows plus its uploads (.tar.gz).</div>
                                </form>
                            <?php } ?>
                            <?php if ($can_delete && $s['state'] === 'disabled') { ?>
                                <form method="post" action="<?php echo $action('purge'); ?>" style="border-top:1px solid #f2dede;padding-top:8px"
                                      onsubmit="return confirm('Delete ALL data and files of <?php echo $e(addslashes($s['school_name'])); ?> for good? This cannot be undone.');">
                                    <?php echo $token; ?>
                                    <label style="font-weight:normal;font-size:12px">Purge for good: export first, then type <b><?php echo $e($s['subdomain']); ?></b></label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="confirm" class="form-control" autocomplete="off" required>
                                        <span class="input-group-btn"><button class="btn btn-danger" type="submit"><i class="fa fa-trash"></i> Purge</button></span>
                                    </div>
                                </form>
                            <?php } elseif ($can_delete && $s['set_up']) { ?>
                                <p class="sv-muted" style="font-size:11px">To delete all its data for good, disable the school first.</p>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
                <?php if (empty($s['is_platform']) && $s['subdomain']) {
                    $cname_target = $s['subdomain'] . '.' . $this->config->item('tenancy_base_domain'); ?>
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Custom domains</h3></div>
                    <div class="box-body">
                        <?php if (!$domains) { ?><p class="sv-muted">The school is served on its subdomain only.</p><?php } ?>
                        <?php foreach ($domains as $d) { ?>
                            <div style="border-bottom:1px solid #eee;padding:6px 0 8px">
                                <b><?php echo $e($d['domain']); ?></b>
                                <?php if ($d['verified_at']) { ?><span class="label label-success">Verified</span><?php } else { ?><span class="label label-warning">Not verified</span><?php } ?>
                                <?php if ((int) $d['is_primary']) { ?><span class="label label-primary" title="Used in links and emails">Main address</span><?php } ?>
                                <?php if (!$d['verified_at']) { ?>
                                    <div class="sv-muted" style="font-size:12px;margin-top:4px">Ask the school to add <b>one</b> DNS record, then click Verify:<br>
                                        CNAME <code><?php echo $e($d['domain']); ?></code> → <code><?php echo $e($cname_target); ?></code><br>
                                        or TXT <code>_edusys.<?php echo $e($d['domain']); ?></code> = <code><?php echo $e($d['verify_token']); ?></code></div>
                                <?php } ?>
                                <?php if ($can_edit) { ?>
                                    <div class="sv-actions" style="margin-top:4px">
                                        <?php if (!$d['verified_at']) { ?>
                                            <form method="post" action="<?php echo $action('verify_domain'); ?>"><?php echo $token; ?><input type="hidden" name="domain_id" value="<?php echo (int) $d['id']; ?>">
                                                <button class="btn btn-xs btn-success" type="submit"><i class="fa fa-check"></i> Verify</button></form>
                                        <?php } else { ?>
                                            <form method="post" action="<?php echo $action('primary_domain'); ?>"><?php echo $token; ?><input type="hidden" name="domain_id" value="<?php echo (int) $d['id']; ?>">
                                                <button class="btn btn-xs btn-default" type="submit"><?php echo (int) $d['is_primary'] ? 'Use the subdomain as main' : 'Make main address'; ?></button></form>
                                        <?php } ?>
                                        <form method="post" action="<?php echo $action('remove_domain'); ?>" onsubmit="return confirm('Remove <?php echo $e(addslashes($d['domain'])); ?>? It stops opening the school.');"><?php echo $token; ?><input type="hidden" name="domain_id" value="<?php echo (int) $d['id']; ?>">
                                            <button class="btn btn-xs btn-link text-danger" type="submit">remove</button></form>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ($can_edit) { ?>
                            <form method="post" action="<?php echo $action('add_domain'); ?>" style="margin-top:10px">
                                <?php echo $token; ?>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="domain" class="form-control" maxlength="253" required placeholder="portal.school.com">
                                    <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-plus"></i> Add domain</button></span>
                                </div>
                                <p class="sv-muted" style="font-size:11px;margin:4px 0 0">The server also needs the domain on its web server and a TLS certificate (see DEPLOYMENT.md, custom domains).</p>
                            </form>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Notes</h3>
                        <span class="sv-muted" style="margin-left:8px">internal, never shown to the school</span></div>
                    <div class="box-body">
                        <?php if ($can_edit) { ?>
                            <form method="post" action="<?php echo $action('add_note'); ?>" style="margin-bottom:10px">
                                <?php echo $token; ?>
                                <textarea name="note" class="form-control" rows="2" maxlength="5000" required placeholder="Call notes, agreements, follow-ups"></textarea>
                                <button class="btn btn-default btn-sm" type="submit" style="margin-top:6px"><i class="fa fa-plus"></i> Add note</button>
                            </form>
                        <?php } ?>
                        <?php if (!$notes) { ?><p class="sv-muted">No notes.</p><?php } ?>
                        <?php foreach ($notes as $n) { ?>
                            <div style="border-top:1px solid #eee;padding:8px 0">
                                <div><?php echo nl2br($e($n['note'])); ?></div>
                                <div class="sv-muted" style="font-size:11px"><?php echo $date($n['created_at'], 'd M Y H:i'); ?> · <?php echo $e($n['created_by']); ?>
                                    <?php if ($can_edit) { ?>
                                        <form method="post" action="<?php echo $action('delete_note'); ?>" style="display:inline" onsubmit="return confirm('Delete this note?');">
                                            <?php echo $token; ?><input type="hidden" name="note_id" value="<?php echo (int) $n['id']; ?>">
                                            <button type="submit" class="btn btn-link btn-xs text-danger" style="padding:0 4px">delete</button>
                                        </form>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    // Pre-fill the days with the chosen plan's length.
    (function () {
        var plan = document.getElementById('plan_id'), days = document.getElementById('days');
        if (plan && days) {
            plan.addEventListener('change', function () {
                var d = plan.options[plan.selectedIndex].getAttribute('data-days');
                if (d) { days.value = d; }
            });
        }
    })();
</script>
