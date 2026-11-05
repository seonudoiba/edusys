<?php
$e = function ($v) { return html_escape((string) $v); };
$labels = array('sent' => 'label-success', 'failed' => 'label-danger', 'disabled' => 'label-default');
?>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-list"></i> Email log <small>the latest 300</small></h1></section>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <form method="get" action="<?php echo site_url('admin/platform/email_log'); ?>" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px">
                    <input type="search" name="q" class="form-control" style="max-width:280px" placeholder="Recipient or subject" value="<?php echo $e($q); ?>">
                    <select name="status" class="form-control" style="max-width:160px">
                        <option value="">All results</option>
                        <?php foreach (array('sent' => 'Sent', 'failed' => 'Failed', 'disabled' => 'Switched off') as $k => $l) { ?>
                            <option value="<?php echo $k; ?>" <?php echo $status === $k ? 'selected' : ''; ?>><?php echo $l; ?></option>
                        <?php } ?>
                    </select>
                    <?php if ($school) { ?><input type="hidden" name="school" value="<?php echo (int) $school; ?>"><span class="label label-info" style="align-self:center">School #<?php echo (int) $school; ?></span><?php } ?>
                    <button class="btn btn-default" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <a class="btn btn-link" href="<?php echo site_url('admin/platform/email_log'); ?>">Clear</a>
                </form>
                <div class="table-responsive">
                    <table class="table table-condensed table-hover">
                        <thead><tr><th>When</th><th>To</th><th>Subject</th><th>Email</th><th>Result</th></tr></thead>
                        <tbody>
                            <?php if (!$rows) { ?><tr><td colspan="5" class="text-muted text-center" style="padding:24px">Nothing here.</td></tr><?php } ?>
                            <?php foreach ($rows as $r) { ?>
                                <tr>
                                    <td class="text-muted" style="white-space:nowrap"><?php echo $e(date('d M Y H:i', strtotime($r['created_at']))); ?></td>
                                    <td><?php echo $e($r['recipient']); ?>
                                        <?php if ($r['school_ref']) { ?><div><a href="<?php echo site_url('admin/schools/view/' . (int) $r['school_ref']); ?>" style="font-size:11px">school #<?php echo (int) $r['school_ref']; ?></a></div><?php } ?></td>
                                    <td><?php echo $e($r['subject']); ?>
                                        <?php if ($r['error']) { ?><div class="text-danger" style="font-size:11px;max-width:520px"><?php echo $e(mb_strimwidth($r['error'], 0, 220, '…')); ?></div><?php } ?></td>
                                    <td class="text-muted"><?php echo $e($r['template_key']); ?></td>
                                    <td><span class="label <?php echo isset($labels[$r['status']]) ? $labels[$r['status']] : 'label-default'; ?>"><?php echo $e($r['status'] === 'disabled' ? 'switched off' : $r['status']); ?></span></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
