<?php
$e = function ($v) { return html_escape((string) $v); };
$counts = array('sent' => 0, 'failed' => 0, 'disabled' => 0);
foreach ($stats as $st) { $counts[$st['status']] = (int) $st['n']; }
?>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-envelope-o"></i> Platform emails <small>what owners and your team receive</small></h1></section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <p>
            Last 30 days: <b><?php echo $counts['sent']; ?></b> sent,
            <b class="<?php echo $counts['failed'] ? 'text-danger' : ''; ?>"><?php echo $counts['failed']; ?></b> failed,
            <?php echo $counts['disabled']; ?> skipped (switched off) ·
            <a href="<?php echo site_url('admin/platform/email_log'); ?>">Email log</a>
        </p>
        <div class="box box-primary">
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead><tr><th>Email</th><th>To</th><th>Subject</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($templates as $t) { ?>
                            <tr>
                                <td><b><?php echo $e($t['name']); ?></b></td>
                                <td><?php echo $t['recipient'] === 'platform' ? 'Your team' : 'School owner'; ?></td>
                                <td class="text-muted"><?php echo $e($t['subject']); ?></td>
                                <td><span class="label <?php echo (int) $t['is_active'] ? 'label-success' : 'label-default'; ?>"><?php echo (int) $t['is_active'] ? 'On' : 'Off'; ?></span></td>
                                <td class="text-right"><a class="btn btn-sm btn-default" href="<?php echo site_url('admin/platform/email_edit/' . rawurlencode($t['template_key'])); ?>"><i class="fa fa-pencil"></i> Edit</a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
