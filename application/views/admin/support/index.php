<?php
$e = function ($v) { return html_escape((string) $v); };
$statuses = Support_tickets::STATUSES;
?>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-life-ring"></i> Help &amp; Support <small>requests to the platform team</small></h1></section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <div class="row">
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-default <?php echo $status === '' ? 'active' : ''; ?>" href="<?php echo site_url('admin/support'); ?>">All</a>
                            <?php foreach ($statuses as $k => $st) { ?>
                                <a class="btn btn-default <?php echo $status === $k ? 'active' : ''; ?>" href="<?php echo site_url('admin/support?status=' . $k); ?>"><?php echo $e($st[0]); ?> <span class="badge"><?php echo (int) $counts[$k]; ?></span></a>
                            <?php } ?>
                        </div>
                        <div class="box-tools pull-right"><a class="btn btn-primary btn-sm" href="<?php echo site_url('admin/support/create'); ?>"><i class="fa fa-plus"></i> New request</a></div>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <thead><tr><th>#</th><th>Subject</th><th>Category</th><th>Status</th><th>Last update</th></tr></thead>
                            <tbody>
                                <?php if (!$tickets) { ?><tr><td colspan="5" class="text-center text-muted" style="padding:30px">No requests yet. Click <b>New request</b> to ask the platform team for help.</td></tr><?php } ?>
                                <?php foreach ($tickets as $t) { $st = $statuses[$t['status']]; ?>
                                    <tr>
                                        <td class="text-muted"><?php echo (int) $t['id']; ?></td>
                                        <td><a href="<?php echo site_url('admin/support/view/' . (int) $t['id']); ?>"><?php echo $e($t['subject']); ?></a>
                                            <div class="text-muted" style="font-size:11px"><?php echo $e($t['opened_by_name']); ?> · <?php echo (int) $t['message_count']; ?> message(s)</div></td>
                                        <td><?php echo $e(isset(Support_tickets::CATEGORIES[$t['category']]) ? Support_tickets::CATEGORIES[$t['category']] : $t['category']); ?></td>
                                        <td><span class="label <?php echo $st[1]; ?>"><?php echo $e($t['status'] === 'answered' ? 'Replied' : $st[0]); ?></span></td>
                                        <td class="text-muted" style="white-space:nowrap"><?php echo $e(date('d M Y H:i', strtotime($t['last_reply_at']))); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Contact</h3></div>
                    <div class="box-body">
                        <p class="text-muted">Replies arrive here and by email.</p>
                        <?php if ($support_email) { ?><p><i class="fa fa-envelope-o"></i> <a href="mailto:<?php echo $e($support_email); ?>"><?php echo $e($support_email); ?></a></p><?php } ?>
                        <?php if ($support_phone) { ?><p><i class="fa fa-phone"></i> <?php echo $e($support_phone); ?></p><?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
