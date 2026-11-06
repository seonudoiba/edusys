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
                    <?php if ($can_edit && $r['kind'] === 'quote') { ?>
                        <div class="box-footer" style="background:#f7fbf7">
                            <h4 style="margin-top:0">Send a quote</h4>
                            <p class="text-muted">The school gets a private link, valid for 30 days, to register and pay at this price.</p>
                            <form method="post" action="<?php echo $action('send_quote'); ?>" class="row"><?php echo $token; ?>
                                <div class="col-sm-3"><label>Students</label><input name="students" type="number" min="1" required class="form-control" value="<?php echo (int) $r['students']; ?>"></div>
                                <div class="col-sm-3"><label>Per student, per term (NGN)</label><input name="rate" type="number" min="1" step="0.01" required class="form-control"></div>
                                <div class="col-sm-3"><label>Setup fee (NGN)</label><input name="setup" type="number" min="0" step="0.01" required class="form-control" value="750000"></div>
                                <div class="col-sm-3"><label>&nbsp;</label><button class="btn btn-primary btn-block" type="submit">Send quote</button></div>
                            </form>
                        </div>
                    <?php } ?>
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
                            <dt>Type</dt><dd><?php echo $r['kind'] === 'complaint' ? 'Complaint' : ($r['kind'] === 'quote' ? 'Quote request' : 'Contact'); ?></dd>
                            <?php if ($r['kind'] === 'quote') { ?><dt>School</dt><dd><?php echo $e($r['school_name']); ?></dd><dt>Students</dt><dd><?php echo number_format((int) $r['students']); ?></dd><?php } ?>
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
