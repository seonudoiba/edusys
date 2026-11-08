<?php
$this->load->view('themes/platform/_icons');
$money = function ($v) { return '&#8358;' . number_format((float) $v); };
?>
<section class="hero hero-sm has-bg">
    <?php $this->load->view('themes/platform/_bg'); ?>
    <div class="wrap">
        <p class="crumb">Pricing</p>
        <h1>Simple pricing, by the size of your school.</h1>
        <p class="lede">You pay per student, per term, at a rate set by the size of your school. A one-time fee covers implementation and training.</p>
    </div>
</section>

<section class="band">
    <div class="wrap">
        <div class="price-card scroll">
            <table class="prices">
                <thead><tr><th>Students</th><th>EduSYS subscription</th><th>One-time implementation and training</th></tr></thead>
                <tbody>
                    <?php foreach ($bands as $b) { ?>
                        <tr>
                            <td><?php echo number_format((int) $b['min_students']) . ($b['max_students'] === null ? '+' : '&ndash;' . number_format((int) $b['max_students'])); ?></td>
                            <td><?php echo $b['rate_per_term'] === null ? '<a href="' . site_url('plans/quote') . '">Custom quote</a>' : $money($b['rate_per_term']) . ' per student, per term'; ?></td>
                            <td><?php echo ($b['rate_per_term'] === null ? 'From ' : '') . $money($b['setup_fee']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="band band-tint">
    <div class="wrap split">
        <div>
            <p class="eyebrow">Calculator</p>
            <h2>What your school would pay</h2>
            <p style="margin-top:14px">Enter your number of students and how you want to pay. The price per student is the same either way.</p>
        </div>
        <form class="calc calc-card" method="get" action="<?php echo site_url('subscription/add'); ?>" style="max-width:560px">
            <div class="field">
                <label for="c_students">Number of students</label>
                <input id="c_students" name="students" type="number" min="1" step="1" inputmode="numeric" value="200">
            </div>
            <div class="field">
                <span style="display:block;font-size:.92rem;font-weight:600;margin-bottom:7px">Pay</span>
                <div class="choice">
                    <label><input type="radio" name="period" value="year" checked><span>For the year</span></label>
                    <label><input type="radio" name="period" value="term"><span>Each term</span></label>
                </div>
            </div>
            <table class="sum" aria-live="polite">
                <tr><td id="c_line"></td><td id="c_sub"></td></tr>
                <tr><td>Implementation and training, once</td><td id="c_setup"></td></tr>
                <tr class="total"><td>First payment</td><td id="c_total"></td></tr>
            </table>
            <p id="c_next" style="color:var(--soft);font-size:.95rem"></p>
            <button class="btn" type="submit" id="c_go">Register your school</button>
        </form>
    </div>
</section>

<section class="band">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">Billing</p>
            <h2>How billing works</h2>
        </div>
        <div class="cards cards-4">
            <div class="card"><span class="card-icon"><?php echo pf_icon('users'); ?></span><h3>Active students</h3><p>The price is based on the number of active students on EduSYS. Your band is set at the start of each subscription period.</p></div>
            <div class="card"><span class="card-icon green"><?php echo pf_icon('calendar'); ?></span><h3>Term or year</h3><p>Pay each term or for the year; the price per student is the same. A year is three terms.</p></div>
            <div class="card"><span class="card-icon yellow"><?php echo pf_icon('chart'); ?></span><h3>Growing schools</h3><p>Pay for extra students at your current rate, for the terms left in the period. Your end date doesn't change.</p></div>
            <div class="card"><span class="card-icon red"><?php echo pf_icon('support'); ?></span><h3>Paid once</h3><p>The implementation and training fee is paid once, when you join.</p></div>
        </div>
    </div>
</section>

<section class="band">
    <div class="wrap cards cards-2">
        <div class="card">
            <h3>Included</h3>
            <ul class="checks">
                <li><?php echo pf_icon('check'); ?>Access to the EduSYS school management platform and its standard modules</li>
                <li><?php echo pf_icon('check'); ?>Your school's branding and configuration</li>
                <li><?php echo pf_icon('check'); ?>Standard system updates and configuration support</li>
                <li><?php echo pf_icon('check'); ?>Logins for administrators, teachers, students and parents</li>
                <li><?php echo pf_icon('check'); ?>Standard reports</li>
            </ul>
        </div>
        <div class="card">
            <h3>Not included</h3>
            <ul class="checks no">
                <li><?php echo pf_icon('x'); ?>SMS and other third-party messaging charges</li>
                <li><?php echo pf_icon('x'); ?>Payment gateway transaction fees</li>
                <li><?php echo pf_icon('x'); ?>Zoom and Google Meet add-ons</li>
                <li><?php echo pf_icon('x'); ?>Custom software development and bespoke integrations</li>
                <li><?php echo pf_icon('x'); ?>Major data cleaning or migration outside the agreed scope</li>
                <li><?php echo pf_icon('x'); ?>Hardware such as biometric devices, scanners, printers, routers and end-user devices</li>
            </ul>
        </div>
    </div>
</section>

<section class="band band-navy">
    <div class="wrap cta">
        <div>
            <h2>More than 3,000 students?</h2>
            <p>Large schools and groups get a custom quote. Tell us about your school and we will come back to you.</p>
        </div>
        <div class="actions"><a class="btn" href="<?php echo site_url('plans/quote'); ?>">Request a quote <?php echo pf_icon('arrow'); ?></a></div>
    </div>
</section>
<script>
(function () {
    var bands = <?php echo json_encode($bands, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
    var q = function (id) { return document.getElementById(id); };
    var naira = function (n) { return '₦' + Math.round(n).toLocaleString('en-NG'); };
    var form = document.querySelector('.calc');
    function band(n) {
        for (var i = 0; i < bands.length; i++) {
            var b = bands[i];
            if (n >= +b.min_students && (b.max_students === null || n <= +b.max_students)) { return b; }
        }
        return null;
    }
    function update() {
        var n = Math.max(0, parseInt(q('c_students').value, 10) || 0), b = band(n);
        var year = form.querySelector('[name=period]:checked').value === 'year';
        var custom = b && b.rate_per_term === null;
        form.querySelector('.sum').style.display = (!b || custom) ? 'none' : '';
        if (!b) { q('c_next').textContent = 'Enter the number of students.'; q('c_go').textContent = 'Register your school'; return; }
        if (custom) { q('c_next').textContent = 'At this size your school gets a custom quote.'; q('c_go').textContent = 'Request a quote'; return; }
        var rate = +b.rate_per_term, sub = n * rate * (year ? 3 : 1), setup = +b.setup_fee;
        q('c_line').textContent = n.toLocaleString('en-NG') + ' students at ' + naira(rate) + (year ? ' for 3 terms' : ' for one term');
        q('c_sub').textContent = naira(sub);
        q('c_setup').textContent = naira(setup);
        q('c_total').textContent = naira(sub + setup);
        q('c_next').textContent = 'After that, ' + naira(sub) + (year ? ' a year.' : ' a term.');
        q('c_go').textContent = 'Register your school';
    }
    form.addEventListener('input', update);
    form.addEventListener('change', update);
    update();
})();
</script>
