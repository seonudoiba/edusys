<?php
$e = function ($v) { return html_escape((string) $v); };
$c = $coupon;
$v = function ($k, $d = '') use ($c) { return html_escape($c ? (string) $c[$k] : $d); };
$selected = $c ? json_decode((string) $c['plan_ids'], true) : null;
$all = !is_array($selected);
?>
<div class="content-wrapper">
    <section class="content-header"><h1><a href="<?php echo site_url('admin/billing/coupons'); ?>" class="text-muted"><i class="fa fa-arrow-left"></i></a> <?php echo $c ? 'Edit coupon' : 'Add coupon'; ?></h1></section>
    <section class="content">
        <?php if ($error) { ?><div class="alert alert-danger"><?php echo $e($error); ?></div><?php } ?>
        <form method="post" action="<?php echo site_url('admin/billing/save_coupon' . ($c ? '/' . (int) $c['id'] : '')); ?>" class="box box-primary" style="max-width:760px">
            <?php echo $token; ?>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 form-group"><label for="code">Code</label>
                        <input id="code" name="code" class="form-control" required maxlength="40" style="text-transform:uppercase;font-family:monospace" value="<?php echo $v('code'); ?>" placeholder="e.g. BACK2SCHOOL"></div>
                    <div class="col-sm-6 form-group"><label for="description">Description (internal)</label>
                        <input id="description" name="description" class="form-control" maxlength="255" value="<?php echo $v('description'); ?>"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4 form-group"><label for="discount_type">Discount</label>
                        <select id="discount_type" name="discount_type" class="form-control">
                            <option value="percent" <?php echo $c && $c['discount_type'] === 'fixed' ? '' : 'selected'; ?>>Percentage</option>
                            <option value="fixed" <?php echo $c && $c['discount_type'] === 'fixed' ? 'selected' : ''; ?>>Fixed amount (NGN)</option>
                        </select></div>
                    <div class="col-sm-4 form-group"><label for="value">Value</label>
                        <input id="value" name="value" type="number" step="0.01" min="0.01" class="form-control" required value="<?php echo $v('value'); ?>"></div>
                    <div class="col-sm-4 form-group"><label for="max_uses">Maximum uses</label>
                        <input id="max_uses" name="max_uses" type="number" min="0" class="form-control" value="<?php echo $v('max_uses', '0'); ?>">
                        <p class="help-block">0 = unlimited. A use counts when the school pays.</p></div>
                </div>
                <div class="row">
                    <div class="col-sm-6 form-group"><label for="starts_on">Valid from</label>
                        <input id="starts_on" name="starts_on" type="date" class="form-control" value="<?php echo $v('starts_on'); ?>"></div>
                    <div class="col-sm-6 form-group"><label for="ends_on">Valid until</label>
                        <input id="ends_on" name="ends_on" type="date" class="form-control" value="<?php echo $v('ends_on'); ?>"></div>
                </div>
                <div class="form-group"><label>Plans</label>
                    <div class="checkbox" style="margin-top:0"><label><input type="checkbox" name="all_plans" value="1" <?php echo $all ? 'checked' : ''; ?>> Every plan</label></div>
                    <?php foreach ($plans as $p) { ?>
                        <label style="display:inline-block;margin-right:14px;font-weight:normal"><input type="checkbox" name="plan_ids[]" value="<?php echo (int) $p['plan_id']; ?>" <?php echo !$all && in_array((int) $p['plan_id'], array_map('intval', $selected), true) ? 'checked' : ''; ?>> <?php echo $e($p['title']); ?></label>
                    <?php } ?>
                    <p class="help-block">Untick "Every plan" and tick the plans the coupon is for.</p>
                </div>
                <div class="checkbox"><label><input type="checkbox" name="is_active" value="1" <?php echo !$c || (int) $c['is_active'] ? 'checked' : ''; ?>> Coupon can be used</label></div>
            </div>
            <div class="box-footer"><button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save coupon</button>
                <a class="btn btn-default" href="<?php echo site_url('admin/billing/coupons'); ?>">Cancel</a></div>
        </form>
    </section>
</div>
