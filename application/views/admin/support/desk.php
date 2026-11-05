<?php
$e = function ($v) { return html_escape((string) $v); };
$statuses = Support_tickets::STATUSES;
$prio = array('urgent' => 'label-danger', 'high' => 'label-warning', 'normal' => 'label-default', 'low' => 'label-default');
$link = function ($status) use ($f) {
    $q = array('status' => $status);
    if ($f['q'] !== '') { $q['q'] = $f['q']; }
    if ($f['school_ref']) { $q['school'] = $f['school_ref']; }
    return site_url('admin/support_desk') . '?' . http_build_query($q);
};
?>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-life-ring"></i> Support desk <small>requests from schools</small></h1></section>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                <div class="btn-group btn-group-sm">
                    <?php foreach ($statuses as $k => $st) { ?>
                        <a class="btn btn-default <?php echo $f['status'] === $k ? 'active' : ''; ?>" href="<?php echo $e($link($k)); ?>"><?php echo $e($st[0]); ?> <span class="badge"><?php echo (int) $counts[$k]; ?></span></a>
                    <?php } ?>
                    <a class="btn btn-default <?php echo $f['status'] === '' ? 'active' : ''; ?>" href="<?php echo $e($link('')); ?>">All</a>
                </div>
                <form method="get" action="<?php echo site_url('admin/support_desk'); ?>" style="display:flex;gap:6px;margin-left:auto">
                    <input type="hidden" name="status" value="<?php echo $e($f['status']); ?>">
                    <?php if ($f['school_ref']) { ?><input type="hidden" name="school" value="<?php echo (int) $f['school_ref']; ?>"><span class="label label-info" style="align-self:center">School #<?php echo (int) $f['school_ref']; ?></span><?php } ?>
                    <input type="search" name="q" class="form-control input-sm" placeholder="Subject or school" value="<?php echo $e($f['q']); ?>">
                    <button class="btn btn-default btn-sm" type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Subject</th><th>School</th><th>Priority</th><th>Status</th><th>Last update</th></tr></thead>
                    <tbody>
                        <?php if (!$tickets) { ?><tr><td colspan="6" class="text-center text-muted" style="padding:30px">No requests here.</td></tr><?php } ?>
                        <?php foreach ($tickets as $t) { $st = $statuses[$t['status']]; ?>
                            <tr>
                                <td class="text-muted"><?php echo (int) $t['id']; ?></td>
                                <td><a href="<?php echo site_url('admin/support_desk/view/' . (int) $t['id']); ?>"><?php echo $e($t['subject']); ?></a>
                                    <div class="text-muted" style="font-size:11px"><?php echo $e($t['opened_by_name']); ?> · <?php echo (int) $t['message_count']; ?> message(s)</div></td>
                                <td><a href="<?php echo site_url('admin/schools/view/' . (int) $t['school_ref']); ?>"><?php echo $e($t['school_name']); ?></a></td>
                                <td><span class="label <?php echo isset($prio[$t['priority']]) ? $prio[$t['priority']] : 'label-default'; ?>"><?php echo $e($t['priority']); ?></span></td>
                                <td><span class="label <?php echo $st[1]; ?>"><?php echo $e($st[0]); ?></span></td>
                                <td class="text-muted" style="white-space:nowrap"><?php echo $e(date('d M Y H:i', strtotime($t['last_reply_at']))); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
