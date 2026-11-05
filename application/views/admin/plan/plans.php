<?php
$e = function ($v) { return html_escape((string) $v); };
$money = function ($v) use ($currency_symbol) { return (int) $v === 0 ? 'Free' : html_escape($currency_symbol) . number_format((float) $v, 0); };
$can_add = $this->rbac->hasPrivilege('plan_list', 'can_add');
$can_edit = $this->rbac->hasPrivilege('plan_list', 'can_edit');
$can_delete = $this->rbac->hasPrivilege('plan_list', 'can_delete');
?>
<style>
    .pl-grid { display: flex; flex-wrap: wrap; gap: 16px; }
    .pl-card { flex: 1 1 280px; max-width: 360px; background: #fff; border: 1px solid #e3e3e3; border-radius: 6px; display: flex; flex-direction: column; }
    .pl-card.pl-hidden { opacity: .72; border-style: dashed; }
    .pl-head { padding: 14px 16px 6px; }
    .pl-title { font-size: 16px; font-weight: 600; margin: 0 0 2px; }
    .pl-text { color: #777; font-size: 12px; min-height: 16px; }
    .pl-price { padding: 4px 16px 10px; }
    .pl-price b { font-size: 24px; }
    .pl-price s { color: #999; margin-left: 6px; }
    .pl-price span { color: #777; }
    .pl-feat { padding: 0 16px 10px; margin: 0; list-style: none; font-size: 13px; flex: 1; }
    .pl-feat li { padding: 2px 0; }
    .pl-feat li i { color: #00a65a; width: 16px; }
    .pl-stats { display: flex; border-top: 1px solid #eee; }
    .pl-stats div { flex: 1; text-align: center; padding: 8px 4px; font-size: 11px; color: #777; }
    .pl-stats b { display: block; font-size: 15px; color: #333; }
    .pl-actions { border-top: 1px solid #eee; padding: 8px 10px; display: flex; flex-wrap: wrap; gap: 4px; }
    .pl-actions form { display: inline; margin: 0; }
    .pl-empty { padding: 40px; text-align: center; color: #888; background: #fff; border: 1px dashed #ddd; border-radius: 6px; }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-tags"></i> Plans <small>what schools choose from on the sign-up page</small></h1>
    </section>
    <section class="content">
        <?php if ($message) { ?><div class="alert alert-success"><?php echo $e($message); ?></div><?php } ?>
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <div style="display:flex;flex-wrap:wrap;gap:8px;justify-content:space-between;margin-bottom:14px">
            <div>
                <a class="btn btn-default" href="<?php echo site_url('plans'); ?>" target="_blank" rel="noopener"><i class="fa fa-external-link"></i> View sign-up page</a>
                <a class="btn btn-default" href="<?php echo site_url('admin/plan_type'); ?>"><i class="fa fa-list"></i> Manage features</a>
            </div>
            <?php if ($can_add) { ?><a class="btn btn-primary" href="<?php echo site_url('admin/plan/create'); ?>"><i class="fa fa-plus"></i> Add plan</a><?php } ?>
        </div>

        <?php if (!$plans) { ?>
            <div class="pl-empty">No plans yet. <?php if ($can_add) { ?><a href="<?php echo site_url('admin/plan/create'); ?>">Add the first plan</a>.<?php } ?></div>
        <?php } ?>
        <div class="pl-grid">
            <?php foreach ($plans as $p) {
                $shown = (int) $p['status'] === 1;
                $offer = (int) $p['oprice'] > 0; ?>
                <div class="pl-card <?php echo $shown ? '' : 'pl-hidden'; ?>">
                    <div class="pl-head">
                        <span class="label <?php echo $shown ? 'label-success' : 'label-default'; ?> pull-right"><?php echo $shown ? 'On sign-up page' : 'Hidden'; ?></span>
                        <div class="pl-title"><?php echo $e($p['title']); ?></div>
                        <div class="pl-text"><?php echo $e($p['text']); ?></div>
                    </div>
                    <div class="pl-price">
                        <b><?php echo $money($p['effective_price']); ?></b>
                        <?php if ($offer) { ?><s><?php echo $money($p['price']); ?></s><?php } ?>
                        <span>/ <?php echo (int) $p['days']; ?> days</span>
                    </div>
                    <?php $mods = json_decode((string) $p['modules'], true); $lim = function ($n, $what) { return (int) $n > 0 ? number_format((int) $n) . ' ' . $what : 'Unlimited ' . $what; }; ?>
                    <div style="padding:0 16px 8px;font-size:12px;color:#555">
                        <?php echo $lim($p['max_students'], 'students'); ?> · <?php echo $lim($p['max_staff'], 'staff'); ?> · <?php echo (int) $p['max_storage_mb'] > 0 ? number_format((int) $p['max_storage_mb']) . ' MB' : 'unlimited storage'; ?><br>
                        <?php echo is_array($mods) ? count($mods) . ' modules' : 'Every module'; ?>
                    </div>
                    <ul class="pl-feat">
                        <?php if (!$p['feature_names']) { ?><li class="text-muted">No features listed</li><?php } ?>
                        <?php foreach ($p['feature_names'] as $f) { ?><li><i class="fa fa-check"></i><?php echo $e($f); ?></li><?php } ?>
                    </ul>
                    <div class="pl-stats">
                        <div><b><?php echo (int) $p['stats']['active']; ?></b>active</div>
                        <div><b><?php echo (int) $p['stats']['paid']; ?></b>paid in total</div>
                        <div><b><?php echo (int) $p['stats']['pending']; ?></b>unpaid</div>
                        <div><b><?php echo $p['stats']['revenue'] > 0 ? $money($p['stats']['revenue']) : '—'; ?></b>revenue</div>
                    </div>
                    <div class="pl-actions">
                        <?php if ($can_edit) { ?>
                            <a class="btn btn-sm btn-default" href="<?php echo site_url('admin/plan/edit/' . (int) $p['plan_id']); ?>"><i class="fa fa-pencil"></i> Edit</a>
                            <form method="post" action="<?php echo site_url('admin/plan/toggle/' . (int) $p['plan_id']); ?>"><?php echo $token; ?>
                                <button class="btn btn-sm btn-default" type="submit"><i class="fa <?php echo $shown ? 'fa-eye-slash' : 'fa-eye'; ?>"></i> <?php echo $shown ? 'Hide' : 'Show'; ?></button>
                            </form>
                        <?php } ?>
                        <?php if ($can_add) { ?>
                            <form method="post" action="<?php echo site_url('admin/plan/duplicate/' . (int) $p['plan_id']); ?>"><?php echo $token; ?>
                                <button class="btn btn-sm btn-default" type="submit" title="Make a hidden copy to edit"><i class="fa fa-copy"></i> Duplicate</button>
                            </form>
                        <?php } ?>
                        <?php if ($can_delete) { ?>
                            <form method="post" action="<?php echo site_url('admin/plan/delete/' . (int) $p['plan_id']); ?>" style="margin-left:auto"
                                  onsubmit="return confirm('Delete this plan? Plans that schools subscribed to can only be hidden.');"><?php echo $token; ?>
                                <button class="btn btn-sm btn-default text-danger" type="submit" title="Delete"><i class="fa fa-trash"></i></button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <p class="text-muted" style="margin-top:14px"><i class="fa fa-info-circle"></i>
            Editing a plan changes what new schools see and pay. Schools already subscribed keep the days and price they paid for.
            To change a price without touching a plan in use, duplicate it and hide the old one.</p>
    </section>
</div>
