<?php
$e = function ($v) { return html_escape((string) $v); };
$st = Enquiries::STATUSES[$r['status']];
$action = function ($name) use ($r) { return site_url('admin/enquiries/' . $name . '/' . (int) $r['id']); };
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-inbox"></i> <?php echo $e($r['subject'] ?: '(no subject)'); ?>
            <span class="label <?php echo $st[1]; ?>" style="font-size:12px;vertical-align:middle"><?php echo $e($st[0]); ?></span></h1>
    </section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-body">
                        <div style="white-space:pre-wrap"><?php echo $e($r['message']); ?></div>
                        <?php if ($r['reply']) { ?>
                            <div style="margin-top:16px;padding:10px 12px;background:#f2f8fd;border:1px solid #cfe3f3;border-radius:4px">
                                <div class="text-muted" style="font-size:11px">Replied by <?php echo $e($r['replied_by']); ?> · <?php echo $e(date('d M Y H:i', strtotime($r['replied_at']))); ?></div>
                                <div style="white-space:pre-wrap"><?php echo $e($r['reply']); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if ($can_edit) { ?>
                        <div class="box-footer">
                            <form method="post" action="<?php echo $action('reply'); ?>">
                                <?php echo $token; ?>
                                <textarea name="message" class="form-control" rows="5" maxlength="20000" required placeholder="Reply by email to <?php echo $e($r['email']); ?>"></textarea>
                                <div style="margin-top:8px">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-paper-plane"></i> Send reply</button>
                                    <button class="btn btn-default" type="submit" name="close" value="1">Send and close</button>
                                </div>
                            </form>
                        </div>
                    <?php } ?>
                </div>
                <a href="<?php echo site_url('admin/enquiries'); ?>"><i class="fa fa-arrow-left"></i> All enquiries</a>
            </div>
            <div class="col-md-4">
                <div class="box box-default">
                    <div class="box-body">
                        <dl>
                            <dt>From</dt><dd><?php echo $e($r['name']); ?></dd>
                            <dt>Email</dt><dd><?php echo $r['email'] ? '<a href="mailto:' . $e($r['email']) . '">' . $e($r['email']) . '</a>' : '—'; ?></dd>
                            <dt>Phone</dt><dd><?php echo $e($r['phone']) ?: '—'; ?></dd>
                            <dt>Type</dt><dd><?php echo $r['kind'] === 'complaint' ? 'Complaint' : 'Contact'; ?></dd>
                            <dt>Received</dt><dd><?php echo $e(date('d M Y H:i', strtotime($r['created_at']))); ?></dd>
                        </dl>
                        <?php if ($can_edit) { ?>
                            <form method="post" action="<?php echo $action('status'); ?>" style="display:inline">
                                <?php echo $token; ?>
                                <?php foreach (Enquiries::STATUSES as $k => $s) { if ($k === $r['status']) { continue; } ?>
                                    <button class="btn btn-default btn-xs" type="submit" name="status" value="<?php echo $k; ?>">Mark <?php echo $e(strtolower($s[0])); ?></button>
                                <?php } ?>
                            </form>
                        <?php } ?>
                        <?php if ($this->rbac->hasPrivilege('platform_settings', 'can_delete')) { ?>
                            <form method="post" action="<?php echo $action('delete'); ?>" style="display:inline" onsubmit="return confirm('Delete this enquiry?');">
                                <?php echo $token; ?><button class="btn btn-link btn-xs text-danger" type="submit">delete</button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
