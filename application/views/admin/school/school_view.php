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
// Finer platform permissions (System Settings > Roles, "School" group; migration 299).
$can = function ($perm, $action) { return $this->rbac->hasPrivilege($perm, $action); };
$can_impersonate = $can('school_impersonate', 'can_view');
$can_status = $can('school_status', 'can_edit');
$can_approve = $can('school_approve', 'can_edit');
$can_billing = $can('school_billing', 'can_edit');
$can_modules = $can('school_modules', 'can_edit');
$can_export = $can('school_data', 'can_view');
$can_purge = $can('school_data', 'can_delete');
$can_domain_add = $can('school_domains', 'can_add');
$can_domain_edit = $can('school_domains', 'can_edit');
$can_domain_delete = $can('school_domains', 'can_delete');
$can_note_add = $can('school_notes', 'can_add');
$can_note_delete = $can('school_notes', 'can_delete');
// A subscription that can be extended (a setup fee alone can't: a subscription is recorded instead).
$extendable = $s['paid_subscription'] && $s['expiry'] !== 'onboarding';
$action = function ($name) use ($s) { return site_url('admin/schools/' . $name . '/' . (int) $s['id']); };
$token = '<input type="hidden" name="form_token" value="' . html_escape($form_token) . '">';
$transfer_labels = array('pending' => 'label-warning', 'awaiting_receipt' => 'label-default', 'approved' => 'label-success', 'rejected' => 'label-danger');
?>
<style>
    .sv-header { padding: 15px 15px 0; }
    .sv-head { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between; }
    .sv-head h1 { margin: 0; font-size: 24px; display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
    .sv-head h1 .label { margin-left: 0; }
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
    <section class="sv-header"><!-- not content-header: the admin theme hides that (style-main.css) -->
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
                <?php if ($can_impersonate && $s['set_up'] && $s['enabled'] && empty($s['is_platform'])) { ?>
                    <form method="post" action="<?php echo $action('impersonate'); ?>" target="_blank"
                          onsubmit="return confirm('Open the admin panel of this school as its super admin? It is recorded in both audit trails.');">
                        <?php echo $token; ?>
                        <button class="btn btn-primary" type="submit"><i class="fa fa-life-ring"></i> Log in as school admin</button>
                    </form>
                <?php } ?>
                <?php if ($can_edit) { ?>
                    <a class="btn btn-default" href="<?php echo site_url('admin/schools/edit/' . (int) $s['id']); ?>"><i class="fa fa-pencil"></i> Edit details</a>
                <?php } ?>
                <?php if ($can_status) { ?>
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

        <?php $purged = $s['status_reason'] === 'purged'; ?>
        <?php if (!$s['set_up'] && empty($s['is_platform'])) { ?>
            <div class="box box-warning sv-setup">
                <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-rocket"></i> <?php echo $purged ? 'Set up this school again' : 'Approve and set up this school'; ?></h3></div>
                <div class="box-body">
                    <?php if ($purged) { ?>
                        <div class="alert alert-info">This school's data was purged. Setting it up again gives it a fresh, empty school (nothing of its old data comes back; restore the export for that). Its old subscriptions and invoices stay; record a subscription below if it should be paid up.</div>
                    <?php } elseif ($s['state'] === 'awaiting_payment') { ?>
                        <div class="alert alert-warning">This school hasn't paid yet. Check the payment before setting it up, or record a manual subscription below.</div>
                    <?php } ?>
                    <p>Setting up creates the school's data (sessions, menus, settings, default lists) and its super admin account
                        (<b><?php echo $e($s['email_address']); ?></b>). The owner then gets a one-time link to choose a password.</p>
                    <?php if ($can_approve) { ?>
                        <form method="post" action="<?php echo $action('setup'); ?>" class="form-inline" onsubmit="return confirm('<?php echo $purged ? 'Set up this school again, empty?' : 'Set up this school now?'; ?>');">
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
                            <button type="submit" class="btn btn-warning"><i class="fa fa-check"></i> <?php echo $purged ? 'Set up again' : 'Approve &amp; set up'; ?></button>
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
                        <?php if ($s['expiry'] === 'unpaid') { ?>
                            <div class="alert alert-warning" style="margin-bottom:10px">
                                <b>Not paid yet: staff only.</b> Staff can sign in and set the school up; students and parents can sign in once a subscription is paid
                                (bill the school below; paying the invoice, an approved bank transfer, Mark paid or Record subscription opens it).
                                <?php if ($this->rbac->hasPrivilege('school_billing', 'can_edit')) { ?>
                                    <form method="post" action="<?php echo $action('open_portal'); ?>" style="margin-top:8px" onsubmit="return confirm('Let students and parents of this school sign in without a paid subscription?');">
                                        <?php echo $token; ?>
                                        <button class="btn btn-default btn-sm" type="submit"><i class="fa fa-unlock"></i> Let students and parents in without payment</button>
                                    </form>
                                <?php } ?>
                            </div>
                        <?php } elseif ($s['expiry'] === 'onboarding') { $p = $s['paid_subscription']; ?>
                            <div class="alert alert-info" style="margin-bottom:10px"><b>Setup fee paid on <?php echo $date($p['created_at']); ?>; subscription not paid yet.</b> Staff can sign in and set the school up; students and parents can't sign in until the owner pays the subscription (from their account) or you record one below.</div>
                        <?php } elseif ($s['paid_subscription']) { $p = $s['paid_subscription']; ?>
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
                        <?php if ($can_billing && empty($s['is_platform'])) { ?>
                            <hr>
                            <form method="post" action="<?php echo $action('extend'); ?>" class="form-inline"
                                  onsubmit="return confirm('<?php echo $extendable ? 'Add these days to the current subscription?' : 'Record this subscription without a payment?'; ?>');">
                                <?php echo $token; ?>
                                <?php if (!$extendable) { ?>
                                    <div class="form-group">
                                        <label for="plan_id">Plan</label>
                                        <select id="plan_id" name="plan_id" class="form-control" required>
                                            <option value="">Choose…</option>
                                            <?php foreach ($plans as $plan) { ?>
                                                <option value="<?php echo (int) $plan['plan_id']; ?>" data-days="<?php echo (int) $plan['days']; ?>"><?php echo $e($plan['title']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="student_count">Students paid for <span class="sv-muted">(empty: no limit)</span></label>
                                        <input id="student_count" name="student_count" type="number" min="1" class="form-control">
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="days"><?php echo $extendable ? 'Extend by' : 'Days'; ?></label>
                                    <input id="days" name="days" type="number" min="1" max="3650" class="form-control" style="width:110px" required value="<?php echo $extendable ? 30 : ''; ?>"> days
                                </div>
                                <button type="submit" class="btn btn-default"><i class="fa fa-calendar-plus-o"></i> <?php echo $extendable ? 'Extend subscription' : 'Record subscription'; ?></button>
                            </form>
                            <p class="sv-muted" style="margin-top:6px"><?php echo $extendable ? 'For a goodwill extension or a payment received outside the system.' : 'For a school added by the platform or paid outside the system. No payment is collected.'; ?></p>
                        <?php } ?>
                    </div>
                </div>

                <?php if (empty($s['is_platform']) && ($discount || $discount_history || $this->rbac->hasPrivilege('school_billing', 'can_add'))) { ?>
                <div class="box box-default" id="discount_box">
                    <div class="box-header with-border"><h3 class="box-title">Discount</h3></div>
                    <div class="box-body">
                        <?php if ($discount) { ?>
                            <p style="margin-bottom:6px"><span class="label label-success">Active</span> <b><?php echo $e(School_discounts::describe($discount)); ?></b>
                                <?php if ($discount['note']) { ?><span class="sv-muted">&middot; <?php echo $e($discount['note']); ?></span><?php } ?></p>
                            <p class="sv-muted" style="font-size:12px">Applied automatically when the owner pays the subscription, renews or adds students from their account.</p>
                            <?php if ($this->rbac->hasPrivilege('school_billing', 'can_delete')) { ?>
                                <form method="post" action="<?php echo $action('remove_discount'); ?>" onsubmit="return confirm('Remove this discount?');" style="margin-bottom:10px">
                                    <?php echo $token; ?><button class="btn btn-default btn-sm" type="submit"><i class="fa fa-times"></i> Remove discount</button>
                                </form>
                            <?php } ?>
                        <?php } else { ?>
                            <p class="sv-muted">No discount.</p>
                        <?php } ?>
                        <?php if ($this->rbac->hasPrivilege('school_billing', 'can_add')) { ?>
                            <form method="post" action="<?php echo $action('add_discount'); ?>" class="form-inline" id="discount_form">
                                <?php echo $token; ?>
                                <div class="form-group">
                                    <label class="sr-only" for="d_value">Discount</label>
                                    <input id="d_value" name="value" type="number" min="0.01" step="0.01" class="form-control" style="width:110px" required placeholder="10">
                                </div>
                                <div class="form-group">
                                    <select name="type" class="form-control" aria-label="Percentage or amount">
                                        <option value="percent">% off</option>
                                        <option value="amount"><?php echo $e($currency_symbol); ?> off</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="applies" class="form-control" aria-label="Which payments">
                                        <option value="once">the next payment</option>
                                        <option value="always">every payment</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top:6px">
                                    <input name="note" type="text" maxlength="255" class="form-control" style="width:260px" placeholder="Reason (only the platform sees it)">
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-top:6px"><i class="fa fa-tag"></i> <?php echo $discount ? 'Replace discount' : 'Give discount'; ?></button>
                            </form>
                        <?php } ?>
                        <?php $past = array_values(array_filter($discount_history, function ($d) { return !$d['is_active']; })); if ($past) { ?>
                            <details style="margin-top:10px"><summary class="sv-muted" style="cursor:pointer">Earlier discounts (<?php echo count($past); ?>)</summary>
                                <ul class="sv-muted" style="padding-left:18px;margin:6px 0 0">
                                    <?php foreach ($past as $d) { ?><li><?php echo $e(School_discounts::describe($d)); ?>, given <?php echo $date($d['created_at']); ?><?php echo $d['used_at'] ? ', used ' . $date($d['used_at']) : ', removed'; ?></li><?php } ?>
                                </ul>
                            </details>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

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

                <?php $can_bill = $this->rbac->hasPrivilege('school_billing', 'can_add'); ?>
                <?php if (empty($s['is_platform']) && $can_bill && $s['status_reason'] !== 'purged') { ?>
                    <div class="box box-default <?php echo $invoices ? 'collapsed-box' : ''; ?>" id="bill_school">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-file-text-o"></i> Bill this school</h3>
                            <span class="sv-muted" style="margin-left:8px">an invoice to pay online, by bank transfer, or marked paid here</span>
                            <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo $invoices ? 'fa-plus' : 'fa-minus'; ?>"></i></button></div>
                        </div>
                        <div class="box-body" <?php echo $invoices ? 'style="display:none"' : ''; ?>>
                            <form method="post" action="<?php echo $action('bill'); ?>" onsubmit="return confirm('Create this invoice?');">
                                <?php echo $token; ?>
                                <?php $this->load->view('admin/school/_billing_fields', array(
                                    'bands' => $bands,
                                    'preview_url' => $action('bill_preview'),
                                    'owner_email' => $s['email_address'],
                                    'currency_symbol' => $currency_symbol,
                                )); ?>
                                <button type="submit" class="btn btn-primary" style="margin-top:10px"><i class="fa fa-file-text-o"></i> Create invoice</button>
                                <span class="sv-muted" style="margin-left:8px">The subscription starts when the invoice is paid.</span>
                            </form>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($invoices) { $inv_labels = array('unpaid' => 'label-warning', 'paid' => 'label-success', 'void' => 'label-default'); ?>
                    <div class="box box-default" id="invoices">
                        <div class="box-header with-border"><h3 class="box-title">Invoices</h3></div>
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-condensed" style="margin-bottom:0">
                                <thead><tr><th>Invoice</th><th>Issued</th><th>Due / paid</th><th class="text-right">Amount</th><th>Status</th><th></th></tr></thead>
                                <?php foreach ($invoices as $inv) {
                                    $st = $inv['status'];
                                    $overdue = $st === 'unpaid' && $inv['due_date'] && $inv['due_date'] < date('Y-m-d');
                                    $public = site_url('subscription/invoice/' . $inv['token']); ?>
                                    <tr>
                                        <td><b><?php echo $e($inv['invoice_no']); ?></b><div class="sv-muted" style="font-size:11px;max-width:260px"><?php echo $e($inv['description']); ?></div></td>
                                        <td><?php echo $date($inv['issued_at']); ?></td>
                                        <td><?php echo $st === 'paid' ? $date($inv['paid_at'] ?: $inv['issued_at']) : ($inv['due_date'] ? $date($inv['due_date']) : '—'); ?></td>
                                        <td class="text-right"><?php echo $money($inv['total']); ?></td>
                                        <td><span class="label <?php echo $overdue ? 'label-danger' : $inv_labels[$st]; ?>"><?php echo $overdue ? 'Overdue' : $e(Platform_invoices::STATUSES[$st]); ?></span></td>
                                        <td class="text-right" style="white-space:nowrap">
                                            <a class="btn btn-xs btn-default" href="<?php echo $e($public); ?>" target="_blank" title="Preview and print"><i class="fa fa-eye"></i> View / print</a>
                                            <a class="btn btn-xs btn-default" href="<?php echo $e($public . '?pdf=1&download=1'); ?>" title="Download the PDF"><i class="fa fa-download"></i> PDF</a>
                                            <?php if ($st !== 'void' && $can_billing) { ?>
                                                <button type="button" class="btn btn-xs btn-default" onclick="var r=document.getElementById('inv_send_<?php echo (int) $inv['id']; ?>');r.style.display=r.style.display==='none'?'':'none'"><i class="fa fa-envelope-o"></i> Send</button>
                                            <?php } ?>
                                            <?php if ($st === 'unpaid' && $can_billing) { ?>
                                                <button type="button" class="btn btn-xs btn-success" onclick="var r=document.getElementById('inv_paid_<?php echo (int) $inv['id']; ?>');r.style.display=r.style.display==='none'?'':'none'"><i class="fa fa-check"></i> Mark paid</button>
                                            <?php } ?>
                                            <?php if ($st === 'unpaid' && $this->rbac->hasPrivilege('school_billing', 'can_delete')) { ?>
                                                <form method="post" action="<?php echo $action('invoice_action'); ?>" style="display:inline" onsubmit="return confirm('Cancel invoice <?php echo $e($inv['invoice_no']); ?>? The school can no longer pay it.');">
                                                    <?php echo $token; ?><input type="hidden" name="invoice_id" value="<?php echo (int) $inv['id']; ?>"><input type="hidden" name="do" value="void">
                                                    <button class="btn btn-xs btn-default text-danger" type="submit" title="Cancel the invoice"><i class="fa fa-ban"></i></button>
                                                </form>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php if ($st !== 'void' && $can_billing) { ?>
                                        <tr id="inv_send_<?php echo (int) $inv['id']; ?>" style="display:none"><td colspan="6" style="background:#fafafa">
                                            <form method="post" action="<?php echo $action('invoice_action'); ?>" class="form-inline">
                                                <?php echo $token; ?><input type="hidden" name="invoice_id" value="<?php echo (int) $inv['id']; ?>"><input type="hidden" name="do" value="send">
                                                <label for="inv_email_<?php echo (int) $inv['id']; ?>"><?php echo $st === 'paid' ? 'Send the receipt to' : 'Send the invoice to'; ?></label>
                                                <input id="inv_email_<?php echo (int) $inv['id']; ?>" name="email" type="email" class="form-control input-sm" style="width:260px" value="<?php echo $e($inv['bill_to_email'] ?: $s['email_address']); ?>" required>
                                                <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-paper-plane"></i> Send</button>
                                            </form>
                                        </td></tr>
                                    <?php } ?>
                                    <?php if ($st === 'unpaid' && $can_billing) { ?>
                                        <tr id="inv_paid_<?php echo (int) $inv['id']; ?>" style="display:none"><td colspan="6" style="background:#f6fbf7">
                                            <form method="post" action="<?php echo $action('invoice_action'); ?>" class="form-inline" onsubmit="return confirm('Record the payment of <?php echo $e($inv['invoice_no']); ?> (<?php echo $e($money($inv['total'])); ?>)? The subscription starts today and the receipt is emailed.');">
                                                <?php echo $token; ?><input type="hidden" name="invoice_id" value="<?php echo (int) $inv['id']; ?>"><input type="hidden" name="do" value="paid">
                                                <label>Paid by</label>
                                                <select name="method" class="form-control input-sm" required>
                                                    <option value="bank_transfer">Bank transfer</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="cheque">Cheque</option>
                                                    <option value="paystack">Paystack (outside the system)</option>
                                                    <option value="manual">Other</option>
                                                </select>
                                                <input name="reference" type="text" maxlength="200" class="form-control input-sm" style="width:220px" placeholder="Reference (optional)">
                                                <button class="btn btn-sm btn-success" type="submit"><i class="fa fa-check"></i> Record payment</button>
                                            </form>
                                        </td></tr>
                                    <?php } ?>
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
                                        <?php if ($can_modules && (int) $m['system'] !== 1) { ?>
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

                <?php if (empty($s['is_platform']) && ($can_export || $can_purge)) { ?>
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Data</h3></div>
                    <div class="box-body">
                        <?php if ($s['status_reason'] === 'purged') { ?>
                            <p class="text-danger"><i class="fa fa-trash"></i> This school's data and files were purged. Its subscriptions and invoices are kept.</p>
                        <?php } else { ?>
                            <?php if ($can_export) { ?>
                                <form method="post" action="<?php echo $action('export'); ?>" style="margin-bottom:8px">
                                    <?php echo $token; ?>
                                    <button class="btn btn-default btn-sm" type="submit"><i class="fa fa-download"></i> Export data and files</button>
                                    <div class="sv-muted" style="font-size:11px;margin-top:4px">An SQL file of the school's rows plus its uploads (.tar.gz).</div>
                                </form>
                            <?php } ?>
                            <?php if ($can_purge && $s['state'] === 'disabled') { ?>
                                <form method="post" action="<?php echo $action('purge'); ?>" style="border-top:1px solid #f2dede;padding-top:8px"
                                      onsubmit="return window.confirm('Delete ALL data and files of <?php echo $e(addslashes($s['school_name'])); ?> for good? This cannot be undone.');">
                                    <?php echo $token; ?>
                                    <label style="font-weight:normal;font-size:12px">Purge for good: export first, then type <b><?php echo $e($s['subdomain']); ?></b></label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="confirm" class="form-control" autocomplete="off" required>
                                        <span class="input-group-btn"><button class="btn btn-danger" type="submit"><i class="fa fa-trash"></i> Purge</button></span>
                                    </div>
                                </form>
                            <?php } elseif ($can_purge && $s['set_up']) { ?>
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
                                <?php if (($can_domain_edit || $can_domain_delete)) { ?>
                                    <div class="sv-actions" style="margin-top:4px">
                                        <?php if (!$can_domain_edit) { /* remove only */ } elseif (!$d['verified_at']) { ?>
                                            <form method="post" action="<?php echo $action('verify_domain'); ?>"><?php echo $token; ?><input type="hidden" name="domain_id" value="<?php echo (int) $d['id']; ?>">
                                                <button class="btn btn-xs btn-success" type="submit"><i class="fa fa-check"></i> Verify</button></form>
                                        <?php } else { ?>
                                            <form method="post" action="<?php echo $action('primary_domain'); ?>"><?php echo $token; ?><input type="hidden" name="domain_id" value="<?php echo (int) $d['id']; ?>">
                                                <button class="btn btn-xs btn-default" type="submit"><?php echo (int) $d['is_primary'] ? 'Use the subdomain as main' : 'Make main address'; ?></button></form>
                                        <?php } ?>
                                        <?php if ($can_domain_delete) { ?><form method="post" action="<?php echo $action('remove_domain'); ?>" onsubmit="return confirm('Remove <?php echo $e(addslashes($d['domain'])); ?>? It stops opening the school.');"><?php echo $token; ?><input type="hidden" name="domain_id" value="<?php echo (int) $d['id']; ?>">
                                            <button class="btn btn-xs btn-link text-danger" type="submit">remove</button></form><?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ($can_domain_add) { ?>
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
                        <?php if ($can_note_add) { ?>
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
                                    <?php if ($can_note_delete) { ?>
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
