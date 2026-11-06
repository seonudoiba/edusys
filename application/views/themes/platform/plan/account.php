<section class="hero hero-sm has-bg">
    <?php $this->load->view('themes/platform/_bg'); ?>
    <div class="wrap"><h1>School owners.</h1></div>
</section>
<div class="wrap">
    <div class="page-body narrow" style="max-width:480px">
        <p>Sign in to see your subscription, renew it, or pay for more students.</p>
        <div id="a_msg" class="alert" role="status" hidden></div>
        <form id="a_login">
            <div class="field"><label for="a_email">Email</label><input id="a_email" name="username" type="email" required autocomplete="username"></div>
            <div class="field"><label for="a_password">Password</label><input id="a_password" name="password" type="password" required autocomplete="current-password"></div>
            <div id="a_captcha"></div>
            <div class="field" id="a_code_field" hidden><label for="a_code">Verification code</label><input id="a_code" name="gauth_code" type="text" inputmode="numeric" autocomplete="one-time-code"></div>
            <div class="actions">
                <button class="btn" type="submit">Sign in</button>
                <a href="#" id="a_forgot">Forgot your password?</a>
            </div>
        </form>
        <p style="margin-top:44px;color:var(--soft);font-size:.95rem">Staff, parents and students sign in at their school's own address. <a href="<?php echo site_url('site/login'); ?>">Platform team</a></p>
    </div>
</div>
<script>
(function () {
    var q = function (id) { return document.getElementById(id); };
    var form = q('a_login'), msg = q('a_msg'), twofactor = false;
    var base = '<?php echo site_url(); ?>'.replace(/\/?$/, '/');
    function show(text, good) {
        msg.hidden = false;
        msg.textContent = text;
        msg.className = 'alert ' + (good ? 'alert-success' : 'alert-danger');
    }
    function flat(error) {
        if (!error) { return ''; }
        if (typeof error === 'string') { return error; }
        return Object.keys(error).map(function (k) { return error[k]; }).filter(Boolean).join(' ');
    }
    function plain(html) { var d = document.createElement('div'); d.innerHTML = html; return d.textContent.trim(); }
    function post(path, body) {
        return fetch(base + path, { method: 'POST', body: body, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.text(); });
    }
    var fail = function () { show('Something went wrong. Please try again.'); };
    var captcha = new FormData();
    captcha.append('login_type', 'guest');
    post('welcome/loadCaptcha', captcha).then(function (html) { q('a_captcha').innerHTML = html; }).catch(function () {});

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        post(twofactor ? 'course/user_submit_login' : 'course/guestlogin', new FormData(form)).then(function (t) {
            var d = JSON.parse(t), to = d.redirect_url || d.redirect_to;
            if (to) { window.location.replace(to); return; }
            if (d.gauthenticate) {
                twofactor = true;
                q('a_code_field').hidden = false;
                q('a_code').required = true;
                q('a_code').focus();
                return;
            }
            show(plain(flat(d.error)) || 'Check your email and password.');
        }).catch(fail);
    });

    q('a_forgot').addEventListener('click', function (e) {
        e.preventDefault();
        if (!q('a_email').value) { show('Enter your email first, then choose this again.'); q('a_email').focus(); return; }
        var body = new FormData();
        body.append('username', q('a_email').value);
        post('course/forgotpassword', body).then(function (t) {
            var d = JSON.parse(t);
            if (String(d.status) === '0') { show(plain(flat(d.error)) || 'We could not find that email.'); } else { show(plain(d.message || 'Check your email.'), true); }
        }).catch(fail);
    });
})();
</script>
