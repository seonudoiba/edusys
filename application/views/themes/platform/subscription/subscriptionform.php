<?php
$base_domain = (string) $this->config->item('tenancy_base_domain');
$v = function ($k, $d = '') { return html_escape(set_value($k, $d)); };
$s_students = (int) set_value('student_count', $students ?: '');
$s_period = set_value('billing_period', $period);
$locked = !empty($quote);
?>
<section class="hero has-bg" style="padding-bottom:clamp(30px,4vw,44px)">
    <?php $this->load->view('themes/platform/_bg'); ?>
    <div class="wrap">
        <h1 style="font-size:clamp(2.2rem,4.6vw,3.4rem)">Register your school.</h1>
        <?php if ($locked) { ?><p class="lede" style="margin-bottom:0">Your agreed price for <?php echo html_escape($quote['school_name']); ?>.</p><?php } ?>
    </div>
</section>
<section class="band">
    <div class="wrap">
        <form id="reg" method="post" action="<?php echo site_url('subscription/save'); ?>" class="register">
            <?php echo $this->customlib->getCSRF(); ?>
            <?php if ($locked) { ?><input type="hidden" name="quote" value="<?php echo html_escape($quote['token']); ?>"><?php } ?>
            <div>
                <div class="field">
                    <label for="school_name">School name</label>
                    <input id="school_name" name="school_name" type="text" required value="<?php echo $v('school_name', $locked ? $quote['school_name'] : ''); ?>">
                    <span class="err"><?php echo form_error('school_name'); ?></span>
                </div>
                <div class="field">
                    <label for="subdomain">Your school's web address</label>
                    <div class="addr">
                        <input id="subdomain" name="subdomain" type="text" maxlength="63" required autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="greenfield" value="<?php echo $v('subdomain'); ?>">
                        <span>.<?php echo html_escape($base_domain); ?></span>
                    </div>
                    <span class="err"><?php echo form_error('subdomain'); ?></span>
                    <span id="subdomain_result" class="hint" role="status">Students, parents and staff open your school here.</span>
                </div>
                <div class="grid2">
                    <div class="field">
                        <label for="owner_name">Your name</label>
                        <input id="owner_name" name="owner_name" type="text" required value="<?php echo $v('owner_name', $locked ? $quote['contact_name'] : ''); ?>">
                        <span class="err"><?php echo form_error('owner_name'); ?></span>
                    </div>
                    <div class="field">
                        <label for="phone_number">Phone</label>
                        <input id="phone_number" name="phone_number" type="tel" required value="<?php echo $v('phone_number', $locked ? $quote['phone'] : ''); ?>">
                        <span class="err"><?php echo form_error('phone_number'); ?></span>
                    </div>
                </div>
                <div class="field">
                    <label for="email_address">Email</label>
                    <input id="email_address" name="email_address" type="email" required value="<?php echo $v('email_address', $locked ? $quote['email'] : ''); ?>">
                    <span class="err"><?php echo form_error('email_address'); ?></span>
                    <span class="hint">Your login and receipts go here.</span>
                </div>
                <div class="field">
                    <label for="student_count">Number of students</label>
                    <input id="student_count" name="student_count" type="number" min="1" required <?php echo $locked ? 'readonly' : ''; ?> value="<?php echo $s_students ?: ''; ?>">
                    <span class="err"><?php echo form_error('student_count'); ?></span>
                    <span class="hint">You can add students later and pay for the extra ones.</span>
                </div>
                <div class="field">
                    <span style="display:block;font-size:.9rem;font-weight:500;margin-bottom:6px">Pay</span>
                    <div class="choice">
                        <label><input type="radio" name="billing_period" value="year" <?php echo $s_period !== 'term' ? 'checked' : ''; ?>><span>For the year</span></label>
                        <label><input type="radio" name="billing_period" value="term" <?php echo $s_period === 'term' ? 'checked' : ''; ?>><span>Each term</span></label>
                    </div>
                </div>
                <div class="field">
                    <span style="display:block;font-size:.9rem;font-weight:500;margin-bottom:6px">What to pay now</span>
                    <div class="choice" style="flex-wrap:wrap">
                        <label><input type="radio" name="pay_now" value="all" <?php echo set_value('pay_now') !== 'setup' ? 'checked' : ''; ?>><span>Setup and subscription</span></label>
                        <label><input type="radio" name="pay_now" value="setup" <?php echo set_value('pay_now') === 'setup' ? 'checked' : ''; ?>><span>Setup fee only</span></label>
                    </div>
                    <span class="hint" id="pay_now_hint">With the setup fee only, we set up your school and train your staff now. Students and parents can sign in once you pay the subscription from your account.</span>
                </div>
                <div class="field" id="method_field">
                    <label for="payment_method">Payment method</label>
                    <select id="payment_method" name="payment_method">
                        <option value="">Choose</option>
                        <?php foreach ((isset($online_methods) ? $online_methods : array()) as $id => $name) { ?>
                            <option value="<?php echo html_escape($id); ?>" <?php echo set_select('payment_method', $id); ?>>Pay online with <?php echo html_escape($name); ?></option>
                        <?php } ?>
                        <option value="bank_transfer" <?php echo set_select('payment_method', 'bank_transfer'); ?>>Bank transfer, then upload the receipt</option>
                    </select>
                    <span class="err"><?php echo form_error('payment_method'); ?></span>
                </div>
                <div class="field">
                    <label for="coupon_code">Coupon code <span class="hint" style="display:inline">(optional)</span></label>
                    <div class="addr">
                        <input id="coupon_code" name="coupon_code" type="text" maxlength="40" style="text-transform:uppercase;border-right:1px solid #c9c6bd" value="<?php echo $v('coupon_code'); ?>">
                        <button class="btn btn-plain" type="button" id="apply_coupon" style="border-radius:0;margin-left:-1px">Apply</button>
                    </div>
                    <span class="err"><?php echo form_error('coupon_code'); ?></span>
                    <span id="coupon_result" class="hint" role="status"></span>
                </div>
                <button class="btn" type="submit">Continue</button>
            </div>
            <aside aria-live="polite">
                <h2 style="font-size:1.4rem;margin-bottom:6px">Your price</h2>
                <p id="s_none" class="hint" style="margin:0">Enter the number of students.</p>
                <table class="sum" id="s_table" style="display:none">
                    <tr id="s_sub_row"><td id="s_line"></td><td id="s_sub"></td></tr>
                    <tr><td>Implementation and training, once</td><td id="s_setup"></td></tr>
                    <tr id="s_off_row" style="display:none"><td>Coupon</td><td id="s_off"></td></tr>
                    <tr class="total"><td>Total</td><td id="s_total"></td></tr>
                </table>
                <p id="s_next" class="hint" style="margin:0"></p>
                <button class="btn btn-plain" type="submit" id="preview_invoice" style="margin-top:14px" formaction="<?php echo site_url('subscription/preview_invoice'); ?>" formtarget="_blank" formnovalidate>Preview invoice</button>
                <p class="hint" style="margin:6px 0 0">A pro-forma invoice to print or download, for your bursar or board.</p>
            </aside>
        </form>
    </div>
</section>
<script>
(function () {
    var bands = <?php echo json_encode($bands, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
    var agreed = <?php echo $locked ? json_encode(array('rate' => (float) $quote['rate_per_term'], 'setup' => (float) $quote['setup_fee'])) : 'null'; ?>;
    var token = <?php echo $locked ? json_encode($quote['token'], JSON_HEX_TAG) : '""'; ?>;
    var q = function (id) { return document.getElementById(id); };
    var form = q('reg'), off = 0, plans = '<?php echo site_url('plans/quote'); ?>';
    var naira = function (n) { return '₦' + Math.round(n).toLocaleString('en-NG'); };
    var year = function () { return form.querySelector('[name=billing_period]:checked').value === 'year'; };
    var setupOnly = function () { var r = form.querySelector('[name=pay_now]:checked'); return !!r && r.value === 'setup'; };
    function band(n) {
        for (var i = 0; i < bands.length; i++) {
            var b = bands[i];
            if (n >= +b.min_students && (b.max_students === null || n <= +b.max_students)) { return b; }
        }
        return null;
    }
    function price() {
        var n = parseInt(q('student_count').value, 10) || 0, b = band(n);
        if (n < 1 || (!agreed && !b)) { q('s_table').style.display = 'none'; q('s_none').style.display = ''; q('s_none').textContent = 'Enter the number of students.'; q('s_next').textContent = ''; return; }
        if (!agreed && b.rate_per_term === null) {
            q('s_table').style.display = 'none'; q('s_next').textContent = ''; q('s_none').style.display = '';
            q('s_none').innerHTML = 'Schools this size get a custom quote. <a href="' + plans + '?students=' + n + '">Request one</a>.';
            return;
        }
        var rate = agreed ? agreed.rate : +b.rate_per_term, setup = agreed ? agreed.setup : +b.setup_fee, sub = n * rate * (year() ? 3 : 1), total = Math.max(0, (setupOnly() ? 0 : sub) + setup - off);
        q('s_table').style.display = ''; q('s_none').style.display = 'none';
        q('s_line').textContent = n.toLocaleString('en-NG') + ' students at ' + naira(rate) + (year() ? ' for 3 terms' : ' for one term');
        q('s_sub').textContent = naira(sub);
        q('s_setup').textContent = naira(setup);
        q('s_off_row').style.display = off > 0 ? '' : 'none';
        q('s_off').textContent = '-' + naira(off);
        q('s_total').textContent = naira(total);
        q('s_sub_row').style.display = setupOnly() ? 'none' : '';
        q('s_next').textContent = setupOnly() ? 'Then ' + naira(sub) + (year() ? ' a year' : ' a term') + ', paid when you are ready to start.' : 'After that, ' + naira(sub) + (year() ? ' a year.' : ' a term.');
        q('method_field').style.display = total > 0 ? '' : 'none';
    }
    form.addEventListener('input', function (e) { if (e.target.name === 'student_count') { off = 0; q('coupon_result').textContent = ''; } price(); });
    form.addEventListener('change', function (e) { if (e.target.name === 'billing_period' || e.target.name === 'pay_now') { off = 0; q('coupon_result').textContent = ''; } price(); });
    price();

    q('apply_coupon').addEventListener('click', function () {
        var code = q('coupon_code').value.trim(), out = q('coupon_result');
        if (!code) { off = 0; out.textContent = ''; price(); return; }
        var url = '<?php echo site_url('subscription/check_coupon'); ?>?code=' + encodeURIComponent(code) + '&students=' + encodeURIComponent(q('student_count').value)
            + '&period=' + (year() ? 'year' : 'term') + (setupOnly() ? '&pay_now=setup' : '') + (token ? '&quote=' + token : '');
        fetch(url, { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (d) {
            off = d.ok ? +d.discount : 0;
            out.textContent = d.message || 'This coupon code is not valid.';
            price();
        });
    });

    // The web address: suggested from the school name, checked as it is typed.
    var input = q('subdomain'), result = q('subdomain_result'), name = q('school_name'), touched = input.value !== '', timer, last = null;
    var url = '<?php echo site_url('subscription/check_subdomain'); ?>', domain = '.<?php echo html_escape($base_domain); ?>';
    var clean = function (v) { return v.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+/, '').slice(0, 63); };
    var show = function (t, c) { result.textContent = t; result.style.color = c || ''; };
    function check() {
        var server = input.parentNode.parentNode.querySelector('.err'); if (server) { server.textContent = ''; }
        var v = input.value.replace(/-+$/, '');
        if (v === '' || v === last) { return; }
        last = v;
        fetch(url + '?name=' + encodeURIComponent(v), { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (d) {
            if (d.subdomain !== input.value.replace(/-+$/, '')) { return; }
            if (d.ok) { show(d.address + ' is available.', '#1d4a3a'); return; }
            show(d.message, '#9a2a1f');
            (d.suggestions || []).forEach(function (s, i) {
                result.appendChild(document.createTextNode(i ? ', ' : ' Free: '));
                var a = document.createElement('a');
                a.href = '#'; a.textContent = s;
                a.onclick = function (e) { e.preventDefault(); input.value = s; touched = true; last = null; check(); };
                result.appendChild(a);
            });
        }).catch(function () { show('It will be checked when you continue.', ''); });
    }
    var later = function () { clearTimeout(timer); timer = setTimeout(check, 400); };
    input.addEventListener('input', function () { touched = input.value !== ''; var c = clean(input.value); if (c !== input.value) { input.value = c; } later(); });
    input.addEventListener('blur', function () { input.value = input.value.replace(/-+$/, ''); check(); });
    name.addEventListener('input', function () { if (!touched) { input.value = clean(name.value).replace(/-+$/, ''); later(); } });
    if (input.value === '' && name.value !== '') { input.value = clean(name.value).replace(/-+$/, ''); }
    if (input.value !== '') { check(); }
})();
</script>
