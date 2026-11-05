<?php
/* One ticket's conversation; used by the school (side = school) and the platform desk (side = platform). */
$e = function ($v) { return html_escape((string) $v); };
$t = $ticket;
$st = Support_tickets::STATUSES[$t['status']];
$platform = $side === 'platform';
$can_reply = isset($can_reply) ? $can_reply : true;
?>
<style>
    .tk-msg { border: 1px solid #e5e5e5; border-radius: 4px; padding: 10px 12px; margin-bottom: 10px; max-width: 88%; }
    .tk-msg.school { background: #fff; }
    .tk-msg.platform { background: #f2f8fd; border-color: #cfe3f3; margin-left: auto; }
    .tk-meta { font-size: 11px; color: #888; margin-bottom: 4px; }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-life-ring"></i> #<?php echo (int) $t['id']; ?> <?php echo $e($t['subject']); ?>
            <span class="label <?php echo $st[1]; ?>" style="font-size:12px;vertical-align:middle"><?php echo $e($st[0]); ?></span></h1>
    </section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-body">
                        <?php foreach ($messages as $m) { ?>
                            <div class="tk-msg <?php echo $m['author'] === 'platform' ? 'platform' : 'school'; ?>">
                                <div class="tk-meta"><b><?php echo $e($m['author_name']); ?></b>
                                    <?php echo $m['author'] === 'platform' ? '(platform team)' : ($platform ? '(' . $e($t['school_name']) . ')' : ''); ?>
                                    · <?php echo $e(date('d M Y H:i', strtotime($m['created_at']))); ?></div>
                                <div><?php echo nl2br($e($m['message'])); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if ($can_reply) { ?>
                        <div class="box-footer">
                            <form method="post" action="<?php echo $e($reply_url); ?>">
                                <?php echo $token; ?>
                                <textarea name="message" class="form-control" rows="4" maxlength="20000" placeholder="<?php echo $platform ? 'Reply to the school (emailed to ' . $e($t['opened_by_email']) . ')' : 'Reply'; ?>"></textarea>
                                <div style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap">
                                    <?php if ($platform) { ?>
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-paper-plane"></i> Send reply</button>
                                        <button class="btn btn-default" type="submit" name="status" value="closed"><i class="fa fa-check"></i> Send and close</button>
                                        <span style="flex:1"></span>
                                        <?php foreach (Support_tickets::STATUSES as $k => $s) { if ($k === $t['status']) { continue; } ?>
                                            <button class="btn btn-link btn-sm" type="submit" name="status" value="<?php echo $k; ?>" formnovalidate
                                                    onclick="this.form.message.value='';">Mark <?php echo $e(strtolower($s[0])); ?></button>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-paper-plane"></i> <?php echo $t['status'] === 'closed' ? 'Reply and reopen' : 'Send reply'; ?></button>
                                        <?php if ($t['status'] !== 'closed') { ?>
                                            <button class="btn btn-default" type="submit" name="close" value="1" onclick="return confirm('Close this request?');"><i class="fa fa-check"></i> Close request</button>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                    <?php } ?>
                </div>
                <a href="<?php echo $e($back_url); ?>"><i class="fa fa-arrow-left"></i> All requests</a>
            </div>
            <div class="col-md-4">
                <div class="box box-default">
                    <div class="box-body">
                        <dl>
                            <?php if ($platform) { ?>
                                <dt>School</dt><dd><a href="<?php echo site_url('admin/schools/view/' . (int) $t['school_ref']); ?>"><?php echo $e($t['school_name']); ?></a></dd>
                            <?php } ?>
                            <dt>Opened by</dt><dd><?php echo $e($t['opened_by_name']); ?><?php if ($platform) { ?><div class="text-muted"><?php echo $e($t['opened_by_email']); ?></div><?php } ?></dd>
                            <dt>Category</dt><dd><?php echo $e(isset(Support_tickets::CATEGORIES[$t['category']]) ? Support_tickets::CATEGORIES[$t['category']] : $t['category']); ?></dd>
                            <dt>Priority</dt><dd><?php echo $e(isset(Support_tickets::PRIORITIES[$t['priority']]) ? Support_tickets::PRIORITIES[$t['priority']] : $t['priority']); ?></dd>
                            <dt>Opened</dt><dd><?php echo $e(date('d M Y H:i', strtotime($t['created_at']))); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
