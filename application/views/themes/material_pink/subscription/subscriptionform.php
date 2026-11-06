<link href="<?php echo base_url(); ?>backend/multiselect/css/jquery.multiselect.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>backend/multiselect/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>backend/multiselect/js/jquery.multiselect.js"></script>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <form id="form1"
                        action="<?php echo site_url('subscription/save/' . (isset($school['id']) ? $school['id'] : '')) ?>"
                        id="employeeform" name="employeeform" method="post" accept-charset="utf-8"
                        enctype="multipart/form-data">
                        <div class="">
                            <div class="bozero">
                                <h4 class="pagetitleh-whitebg">
                                    <?php echo $this->lang->line('school_register'); ?>
                                </h4>
                                <div class="around10">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <input type="hidden" name="plan_id"
                                        value="<?php echo set_value('plan_id', isset($plan_id) ? $plan_id : ''); ?>"
                                        id="plan_id">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('school_name'); ?>
                                                </label><small class="req"> *</small>
                                                <input id="school_name" name="school_name" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('school_name', isset($school['school_name']) ? $school['school_name'] : ''); ?>" />
                                                <span class="text-danger">
                                                    <?php echo form_error('school_name'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $base_domain = (string) $this->config->item('tenancy_base_domain'); ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="subdomain">Your school's web address</label><small class="req"> *</small>
                                                <div class="input-group">
                                                    <span class="input-group-addon">https://</span>
                                                    <input id="subdomain" name="subdomain" type="text" class="form-control" maxlength="63"
                                                           autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="greenfield"
                                                           value="<?php echo html_escape(set_value('subdomain')); ?>" />
                                                    <span class="input-group-addon">.<?php echo html_escape($base_domain); ?></span>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('subdomain'); ?></span>
                                                <span id="subdomain_result" class="help-block">Students, parents and staff open your school here. Lowercase letters, digits and hyphens.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        (function () {
                                            var input = document.getElementById('subdomain'), out = document.getElementById('subdomain_result');
                                            var name = document.getElementById('school_name'), touched = input.value !== '', timer = null, last = null;
                                            var url = '<?php echo site_url('subscription/check_subdomain'); ?>';
                                            function clean(v) {
                                                return v.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+/, '').slice(0, 63);
                                            }
                                            function show(text, color) { out.textContent = text; out.style.color = color || ''; }
                                            function check() {
                                                var v = input.value.replace(/-+$/, '');
                                                if (v === '') { show('Choose an address for your school.', ''); return; }
                                                if (v === last) { return; }
                                                last = v;
                                                show('Checking ' + v + '.<?php echo html_escape($base_domain); ?> ...', '#777');
                                                fetch(url + '?name=' + encodeURIComponent(v), {credentials: 'same-origin'}).then(function (r) { return r.json(); }).then(function (d) {
                                                    if (d.subdomain !== input.value.replace(/-+$/, '')) { return; } // the user kept typing
                                                    if (d.ok) { show('✔ ' + d.address + ' is available.', '#2e7d32'); return; }
                                                    show('✖ ' + d.message, '#c62828');
                                                    if (d.suggestions && d.suggestions.length) {
                                                        out.appendChild(document.createTextNode(' Available: '));
                                                        d.suggestions.forEach(function (s, i) {
                                                            var a = document.createElement('a');
                                                            a.href = '#'; a.textContent = s;
                                                            a.onclick = function (e) { e.preventDefault(); input.value = s; touched = true; last = null; check(); };
                                                            if (i) { out.appendChild(document.createTextNode(', ')); }
                                                            out.appendChild(a);
                                                        });
                                                    }
                                                }).catch(function () { show('Could not check the address now; it will be checked when you submit.', '#777'); });
                                            }
                                            function later() { clearTimeout(timer); timer = setTimeout(check, 400); }
                                            input.addEventListener('input', function () {
                                                touched = input.value !== '';
                                                var c = clean(input.value);
                                                if (c !== input.value) { input.value = c; }
                                                later();
                                            });
                                            input.addEventListener('blur', function () { input.value = input.value.replace(/-+$/, ''); check(); });
                                            // Suggest an address from the school name until the user types their own.
                                            if (name) {
                                                name.addEventListener('input', function () {
                                                    if (touched) { return; }
                                                    input.value = clean(name.value).replace(/-+$/, '');
                                                    later();
                                                });
                                            }
                                            if (input.value !== '') { check(); }
                                        })();
                                    </script>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('email_address'); ?>
                                                </label><small class="req"> *</small>

                                                <input id="email_address" name="email_address" placeholder=""
                                                    type="text" class="form-control"
                                                    value="<?php echo set_value('email_address', isset($school['email_address']) ? $school['email_address'] : ''); ?>" />

                                                <span class="text-danger">
                                                    <?php echo form_error('email_address'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('phone_number'); ?>
                                                </label><small class="req"> *</small>

                                                <input id="phone_number" name="phone_number" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('phone_number', isset($school['phone_number']) ? $school['phone_number'] : ''); ?>" />

                                                <span class="text-danger">
                                                    <?php echo form_error('phone_number'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('owner_name'); ?>
                                                </label><small class="req"> *</small>
                                                <input id="owner_name" name="owner_name" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('owner_name', isset($school['owner_name']) ? $school['owner_name'] : ''); ?>" />
                                                <span class="text-danger">
                                                    <?php echo form_error('owner_name'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php
                                        if(isset($amount) && $amount > 0){ ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">
                                                            <?php echo $this->lang->line('payment_method'); ?>
                                                        </label><small class="req"> *</small>
                                                        <select id="payment_method" name="payment_method" class="form-control">
                                                            <option value="">Select Payment Method</option>
                                                            <?php // The online gateways the platform has keys for (Payment_gateways::PLATFORM_ENV).
                                                            foreach ((isset($online_methods) ? $online_methods : array()) as $method_id => $method_name) { ?>
                                                                <option value="<?php echo html_escape($method_id); ?>" <?php echo set_select('payment_method', $method_id); ?>>
                                                                    Pay online with <?php echo html_escape($method_name); ?> (card, transfer, USSD)
                                                                </option>
                                                            <?php } ?>
                                                            <option value="bank_transfer" <?php echo set_select('payment_method', 'bank_transfer'); ?>>
                                                                Bank transfer (upload receipt)
                                                            </option>
                                                        </select>
                                                        <span class="text-danger">
                                                            <?php echo form_error('payment_method'); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                    ?>

                                    <?php if (isset($amount) && $amount > 0) { ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="coupon_code">Coupon code</label>
                                                    <div class="input-group">
                                                        <input id="coupon_code" name="coupon_code" type="text" class="form-control" maxlength="40" style="text-transform:uppercase"
                                                               value="<?php echo set_value('coupon_code'); ?>" placeholder="Optional">
                                                        <span class="input-group-btn"><button class="btn btn-default" type="button" id="apply_coupon">Apply</button></span>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('coupon_code'); ?></span>
                                                    <span id="coupon_result" class="help-block"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            (function () {
                                                var btn = document.getElementById('apply_coupon');
                                                btn.addEventListener('click', function () {
                                                    var code = document.getElementById('coupon_code').value.trim(), out = document.getElementById('coupon_result');
                                                    if (!code) { out.textContent = ''; return; }
                                                    var url = '<?php echo site_url('subscription/check_coupon'); ?>?plan_id=' + encodeURIComponent(document.getElementById('plan_id').value) + '&code=' + encodeURIComponent(code);
                                                    fetch(url).then(function (r) { return r.json(); }).then(function (d) {
                                                        out.textContent = d.message || 'This coupon code is not valid.';
                                                        out.style.color = d.ok ? '#00a65a' : '#dd4b39';
                                                        if (d.ok) { document.getElementById('amount').value = d.total; }
                                                    });
                                                });
                                            })();
                                        </script>
                                    <?php } ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('amount'); ?>
                                                </label><small class="req"> *</small>
                                                <input id="amount" name="amount" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('amount', isset($amount) ? $amount : '') ?>"
                                                    readonly />
                                                <span class="text-danger">
                                                    <?php echo form_error('amount'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                    <?php echo $this->lang->line('days'); ?>
                                                </label><small class="req"> *</small>
                                                <input id="days" name="days" placeholder="" type="text"
                                                    class="form-control"
                                                    value="<?php echo set_value('days', isset($days) ? $days : '') ?>"
                                                    readonly />
                                                <span class="text-danger">
                                                    <?php echo form_error('days'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-left" id="addloader"
                                style="margin-bottom:15px">
                                <?php echo $this->lang->line('saveandpayment'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>
</section>
</div>