<?php
$e = function ($v) { return html_escape((string) $v); };
$t = $template;
$key = rawurlencode($t['template_key']);
?>
<div class="content-wrapper">
    <section class="content-header"><h1><a href="<?php echo site_url('admin/platform/emails'); ?>" class="text-muted"><i class="fa fa-arrow-left"></i></a> <?php echo $e($t['name']); ?></h1></section>
    <section class="content">
        <?php if ($this->session->flashdata('platform_msg')) { ?><div class="alert alert-success"><?php echo $e($this->session->flashdata('platform_msg')); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <div class="row">
            <div class="col-md-7">
                <form method="post" action="<?php echo site_url('admin/platform/email_edit/' . $key); ?>" class="box box-primary">
                    <?php echo $token; ?>
                    <div class="box-body">
                        <p class="text-muted">Sent to: <?php echo $t['recipient'] === 'platform' ? 'your team (Platform settings: notifications address)' : 'the school owner'; ?>.</p>
                        <div class="form-group"><label for="subject">Subject</label>
                            <input id="subject" name="subject" class="form-control" maxlength="255" required value="<?php echo $e($t['subject']); ?>"></div>
                        <div class="form-group"><label for="body">Message (HTML)</label>
                            <textarea id="body" name="body" class="form-control" rows="14" required style="font-family:Consolas,monospace;font-size:12px"><?php echo $e($t['body']); ?></textarea>
                            <p class="help-block">Placeholders: <code><?php echo $e($t['placeholders']); ?></code>. Scripts and event attributes are removed when saved.</p></div>
                        <div class="checkbox"><label><input type="checkbox" name="is_active" value="1" <?php echo (int) $t['is_active'] ? 'checked' : ''; ?>> Send this email</label></div>
                    </div>
                    <div class="box-footer"><button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                        <a href="<?php echo site_url('admin/platform/emails'); ?>" class="btn btn-default">Cancel</a></div>
                </form>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <form method="post" action="<?php echo site_url('admin/platform/email_test/' . $key); ?>" style="display:flex;gap:6px">
                        <?php echo $token; ?>
                        <input name="to" type="email" class="form-control" required placeholder="Send a test to" style="min-width:220px">
                        <button class="btn btn-default" type="submit"><i class="fa fa-paper-plane"></i> Send test</button>
                    </form>
                    <form method="post" action="<?php echo site_url('admin/platform/email_reset/' . $key); ?>" onsubmit="return confirm('Restore the original subject and message?');">
                        <?php echo $token; ?><button class="btn btn-link" type="submit">Restore original text</button>
                    </form>
                </div>
            </div>
            <div class="col-md-5">
                <div class="box box-default">
                    <div class="box-header with-border"><h3 class="box-title">Preview</h3> <span class="text-muted">(sample values; saved version)</span></div>
                    <div class="box-body">
                        <p><b><?php echo $e($preview[0]); ?></b></p>
                        <iframe sandbox="" style="width:100%;height:420px;border:1px solid #eee;background:#fff" srcdoc="<?php echo $e('<div style="font-family:Arial,sans-serif;font-size:14px;color:#333">' . $preview[1] . '</div>'); ?>"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
