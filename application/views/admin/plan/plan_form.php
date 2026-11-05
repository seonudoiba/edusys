<?php
$e = function ($v) { return html_escape((string) $v); };
$p = $plan;
$val = function ($field, $default = '') use ($p) { return set_value($field, $p ? (string) $p[$field] : $default); };
$posted = $this->input->method() === 'post';
$checked = $posted ? array_map('intval', (array) $this->input->post('features')) : $selected;
$shown = $posted ? (bool) $this->input->post('status') : (!$p || (int) $p['status'] === 1);
?>
<style>
    .pf-features { columns: 2; column-gap: 24px; }
    .pf-features label { display: block; font-weight: normal; break-inside: avoid; margin-bottom: 6px; }
    .pf-preview { border: 1px solid #e3e3e3; border-radius: 6px; padding: 16px; background: #fff; }
    .pf-preview h4 { margin: 0 0 2px; font-weight: 600; }
    .pf-preview .pf-price { font-size: 24px; font-weight: 600; margin: 10px 0; }
    .pf-preview s { color: #999; font-size: 14px; font-weight: normal; margin-left: 6px; }
    .pf-preview ul { list-style: none; padding: 0; margin: 0; }
    .pf-preview li i { color: #00a65a; width: 16px; }
    .pf-days a { margin-right: 6px; }
    @media (max-width: 767px) { .pf-features { columns: 1; } }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><a href="<?php echo site_url('admin/plan'); ?>" class="text-muted"><i class="fa fa-arrow-left"></i></a>
            <?php echo $p ? 'Edit plan' : 'Add plan'; ?></h1>
    </section>
    <section class="content">
        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
        <form method="post" action="<?php echo site_url('admin/plan/save' . ($p ? '/' . (int) $p['plan_id'] : '')); ?>" id="plan-form">
            <?php echo $token; ?>
            <div class="row">
                <div class="col-md-7">
                    <div class="box box-primary">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="title">Plan name</label><small class="req"> *</small>
                                <input id="title" name="title" class="form-control" maxlength="255" required value="<?php echo $val('title'); ?>" placeholder="e.g. Standard (for growing schools)">
                            </div>
                            <div class="form-group">
                                <label for="text">Short description</label><small class="req"> *</small>
                                <input id="text" name="text" class="form-control" maxlength="255" required value="<?php echo $val('text'); ?>" placeholder="One line shown under the name">
                            </div>
                            <div class="row">
                                <div class="col-sm-4 form-group">
                                    <label for="price">Price (<?php echo $e($currency_symbol); ?>)</label><small class="req"> *</small>
                                    <input id="price" name="price" type="number" min="0" step="1" class="form-control" required value="<?php echo $val('price', '0'); ?>">
                                    <p class="help-block">0 makes it a free plan (no payment).</p>
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label for="oprice">Offer price (<?php echo $e($currency_symbol); ?>)</label>
                                    <input id="oprice" name="oprice" type="number" min="0" step="1" class="form-control" value="<?php echo (int) $val('oprice', '0') > 0 ? $val('oprice') : ''; ?>">
                                    <p class="help-block">Optional. Lower than the price; schools pay this.</p>
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label for="days">Duration (days)</label><small class="req"> *</small>
                                    <input id="days" name="days" type="number" min="1" max="3650" class="form-control" required value="<?php echo $val('days', '365'); ?>">
                                    <p class="help-block pf-days"><a href="#" data-days="30">30</a><a href="#" data-days="90">90</a><a href="#" data-days="180">180</a><a href="#" data-days="365">365</a></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Features listed on the sign-up page</label>
                                <a href="<?php echo site_url('admin/plan_type'); ?>" class="pull-right">Manage features</a>
                                <?php if (!$features) { ?><p class="text-muted">No features yet: add them under Manage features.</p><?php } ?>
                                <div class="pf-features">
                                    <?php foreach ($features as $f) { ?>
                                        <label class="<?php echo (int) $f['status'] === 1 ? '' : 'text-muted'; ?>">
                                            <input type="checkbox" name="features[]" value="<?php echo (int) $f['id']; ?>" data-name="<?php echo $e($f['name']); ?>" <?php echo in_array((int) $f['id'], $checked, true) ? 'checked' : ''; ?>>
                                            <?php echo $e($f['name']); ?><?php echo (int) $f['status'] === 1 ? '' : ' (hidden)'; ?>
                                        </label>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="status" value="1" <?php echo $shown ? 'checked' : ''; ?>> Show on the sign-up page</label>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save plan</button>
                            <a href="<?php echo site_url('admin/plan'); ?>" class="btn btn-default">Cancel</a>
                            <?php if ($p) { ?><span class="text-muted" style="margin-left:10px">Schools already subscribed keep what they paid for.</span><?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="box box-default">
                        <div class="box-header with-border"><h3 class="box-title">Preview</h3></div>
                        <div class="box-body">
                            <div class="pf-preview">
                                <h4 id="pv-title"></h4>
                                <div class="text-muted" id="pv-text"></div>
                                <div class="pf-price"><span id="pv-price"></span><s id="pv-old"></s> <small class="text-muted" id="pv-days"></small></div>
                                <ul id="pv-features"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
<script>
    (function () {
        var cur = <?php echo json_encode((string) $currency_symbol); ?>;
        var f = document.getElementById('plan-form');
        function fmt(n) { n = parseInt(n, 10) || 0; return n === 0 ? 'Free' : cur + n.toLocaleString(); }
        function text(id, v) { document.getElementById(id).textContent = v; }
        function update() {
            var price = parseInt(f.elements['price'].value, 10) || 0, offer = parseInt(f.elements['oprice'].value, 10) || 0;
            text('pv-title', f.elements['title'].value || 'Plan name');
            text('pv-text', f.elements['text'].value);
            text('pv-price', fmt(offer > 0 ? offer : price));
            text('pv-old', offer > 0 ? fmt(price) : '');
            text('pv-days', '/ ' + (parseInt(f.elements['days'].value, 10) || 0) + ' days');
            var ul = document.getElementById('pv-features');
            ul.innerHTML = '';
            f.querySelectorAll('input[name="features[]"]:checked').forEach(function (c) {
                var li = document.createElement('li'), i = document.createElement('i');
                i.className = 'fa fa-check';
                li.appendChild(i);
                li.appendChild(document.createTextNode(c.getAttribute('data-name')));
                ul.appendChild(li);
            });
        }
        f.addEventListener('input', update);
        f.addEventListener('change', update);
        document.querySelectorAll('.pf-days a').forEach(function (a) {
            a.addEventListener('click', function (ev) { ev.preventDefault(); f.elements['days'].value = a.getAttribute('data-days'); update(); });
        });
        update();
    })();
</script>
