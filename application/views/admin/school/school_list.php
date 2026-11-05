<?php
$e = function ($v) { return html_escape((string) $v); };
$state_labels = array(
    'waiting_approval' => array('Waiting for approval', 'label-warning'),
    'awaiting_payment' => array('Awaiting payment', 'label-default'),
    'active' => array('Active', 'label-success'),
    'disabled' => array('Disabled', 'label-danger'),
);
$tabs = array(
    'all' => 'All',
    'waiting_approval' => 'Waiting for approval',
    'active' => 'Active',
    'disabled' => 'Disabled',
    'expiring' => 'Expiring in 30 days',
    'expired' => 'Expired',
    'awaiting_payment' => 'Awaiting payment',
);
$url = function ($t) use ($q) { return site_url('admin/schools') . '?tab=' . $t . ($q !== '' ? '&q=' . rawurlencode($q) : ''); };
?>
<style>
    .sl-tabs { margin-bottom: 12px; }
    .sl-tabs > li > a { padding: 8px 12px; }
    .sl-name { font-weight: 600; font-size: 14px; }
    .sl-sub { color: #777; font-size: 12px; }
    .sl-table td { vertical-align: middle !important; }
    .sl-days { font-size: 12px; }
    .sl-empty { padding: 40px 10px; text-align: center; color: #888; }
    .sl-toolbar { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; justify-content: space-between; margin-bottom: 10px; }
    .sl-toolbar form { display: flex; gap: 6px; }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-university"></i> Schools <small><?php echo (int) $counts['all']; ?> registered</small></h1>
    </section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <?php if ($counts['waiting_approval'] > 0 && $tab !== 'waiting_approval') { ?>
            <div class="alert alert-warning">
                <i class="fa fa-clock-o"></i>
                <?php echo (int) $counts['waiting_approval']; ?> school<?php echo $counts['waiting_approval'] == 1 ? ' is' : 's are'; ?> waiting for approval.
                <a href="<?php echo $url('waiting_approval'); ?>">Review them</a>
            </div>
        <?php } ?>
        <div class="box box-primary">
            <div class="box-body">
                <div class="sl-toolbar">
                    <form method="get" action="<?php echo site_url('admin/schools'); ?>">
                        <input type="hidden" name="tab" value="<?php echo $e($tab); ?>">
                        <input type="search" name="q" class="form-control" placeholder="Search name, owner, email, phone, subdomain" value="<?php echo $e($q); ?>" style="min-width:280px">
                        <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                    </form>
                    <?php if ($this->rbac->hasPrivilege('schools', 'can_add')) { ?>
                        <a class="btn btn-primary" href="<?php echo site_url('admin/schools/create'); ?>"><i class="fa fa-plus"></i> Add school</a>
                    <?php } ?>
                </div>
                <ul class="nav nav-tabs sl-tabs">
                    <?php foreach ($tabs as $key => $label) { ?>
                        <li class="<?php echo $tab === $key ? 'active' : ''; ?>">
                            <a href="<?php echo $url($key); ?>"><?php echo $label; ?>
                                <span class="badge"><?php echo (int) $counts[$key]; ?></span></a>
                        </li>
                    <?php } ?>
                </ul>
                <div class="table-responsive">
                    <table class="table table-hover sl-table">
                        <thead>
                            <tr>
                                <th>School</th>
                                <th>Site</th>
                                <th>Plan</th>
                                <th>Subscription</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($schools)) { ?>
                                <tr><td colspan="7" class="sl-empty">
                                    <?php echo $q !== '' ? 'No school matches "' . $e($q) . '".' : 'No schools here.'; ?>
                                </td></tr>
                            <?php } ?>
                            <?php foreach ($schools as $s) {
                                $l = $state_labels[$s['state']];
                                $view = site_url('admin/schools/view/' . (int) $s['id']);
                                $site = $s['subdomain'] ? (is_https() ? 'https' : 'http') . '://' . $s['subdomain'] . '.' . $base_domain . '/' : null; ?>
                                <tr>
                                    <td>
                                        <a class="sl-name" href="<?php echo $view; ?>"><?php echo $e($s['school_name']); ?></a>
                                        <div class="sl-sub"><?php echo $e($s['owner_name']); ?> · <?php echo $e($s['email_address']); ?> · <?php echo $e($s['phone_number']); ?></div>
                                    </td>
                                    <td>
                                        <?php if ($site && $s['set_up']) { ?>
                                            <a href="<?php echo $e($site); ?>" target="_blank" rel="noopener"><?php echo $e($s['subdomain']); ?> <i class="fa fa-external-link"></i></a>
                                        <?php } elseif ($site) { ?>
                                            <?php echo $e($s['subdomain']); ?> <div class="sl-sub">not set up yet</div>
                                        <?php } else { ?>
                                            <span class="sl-sub">No subdomain yet</span>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo $s['plan_title'] ? $e($s['plan_title']) : '<span class="sl-sub">None</span>'; ?></td>
                                    <td>
                                        <?php if ($s['expires_at']) { ?>
                                            <?php echo $e(date('d M Y', strtotime($s['expires_at']))); ?>
                                            <div class="sl-days">
                                                <?php if ($s['expiry'] === 'expired') { ?>
                                                    <span class="label label-danger">Expired <?php echo abs((int) $s['days_left']); ?> days ago</span>
                                                <?php } elseif ($s['expiry'] === 'expiring') { ?>
                                                    <span class="label label-warning"><?php echo (int) $s['days_left']; ?> days left</span>
                                                <?php } else { ?>
                                                    <span class="text-success"><?php echo (int) $s['days_left']; ?> days left</span>
                                                <?php } ?>
                                            </div>
                                        <?php } else { ?>
                                            <span class="sl-sub">Not paid</span>
                                        <?php } ?>
                                    </td>
                                    <td><span class="label <?php echo $l[1]; ?>"><?php echo $l[0]; ?></span></td>
                                    <td><span class="sl-sub"><?php echo $e(date('d M Y', strtotime($s['created_at']))); ?></span></td>
                                    <td class="text-right" style="white-space:nowrap">
                                        <a class="btn btn-sm <?php echo $s['state'] === 'waiting_approval' ? 'btn-warning' : 'btn-default'; ?>" href="<?php echo $view; ?>">
                                            <?php echo $s['state'] === 'waiting_approval' ? '<i class="fa fa-check"></i> Review &amp; approve' : '<i class="fa fa-eye"></i> View'; ?>
                                        </a>
                                        <?php if ($this->rbac->hasPrivilege('schools', 'can_edit')) { ?>
                                            <a class="btn btn-sm btn-default" title="Edit details" href="<?php echo site_url('admin/schools/edit/' . (int) $s['id']); ?>"><i class="fa fa-pencil"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
