<?php
/*
 * One sign-in page for a school (site/login): a switch between "Student or parent" and "Staff".
 * Each side posts to the sign-in it always had:
 *   Staff               site/login (email + password)
 *   Student or parent   site/userlogin, or gauthenticate/* when two-factor sign-in is on
 * $active: student|staff, $forced: the side was chosen by the server (a failed sign-in), so the
 * browser's remembered choice doesn't override it. $error_side: the side $error_message belongs to.
 */
$active = isset($active) ? $active : 'student';
$forced = !empty($forced);
$error_side = isset($error_side) ? $error_side : $active;
$is_staff_post = $forced && $active === 'staff';
$is_student_post = $forced && $active === 'student';
$value = function ($field, $posted) {
    return $posted ? html_escape(set_value($field)) : '';
};
$bg_staff = login_background_url($school['admin_login_page_background']);
$bg_student = login_background_url($school['user_login_page_background']);
$captcha_block = function ($error) use ($captcha_image) {
    ?>
    <div class="form-group has-feedback row">
        <div class="col-lg-7 col-md-12 col-sm-6">
            <span class="js-captcha-image"><?php echo $captcha_image; ?></span>
            <span class="fa fa-refresh catpcha" title="<?php echo $this->lang->line('refresh_captcha'); ?>" onclick="refreshCaptcha()"></span>
        </div>
        <div class="col-lg-5 col-md-12 col-sm-6">
            <input type="text" name="captcha" placeholder="<?php echo $this->lang->line('captcha'); ?>" autocomplete="off" class="form-control">
            <span class="text-danger error_captcha"><?php echo $error; ?></span>
        </div>
    </div>
    <?php
};
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#424242" />
        <title>Login : <?php echo html_escape($name); ?></title>
        <link href="<?php echo base_url(); ?>uploads/school_content/admin_small_logo/<?php echo $this->setting_model->getAdminsmalllogo(); ?>" rel="shortcut icon" type="image/x-icon">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/usertemplate/assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/usertemplate/assets/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/usertemplate/assets/css/form-elements.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/usertemplate/assets/css/style.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/usertemplate/assets/css/jquery.mCustomScrollbar.min.css">
        <style>
            .login-switch { display: flex; margin: 0 0 20px; padding: 3px; background: #eef0f3; border-radius: 8px; }
            .login-switch button { flex: 1; background: transparent; color: #555; border: 0; padding: 9px 6px; font-size: 14px; line-height: 1.3; height: auto; margin: 0; border-radius: 6px; text-transform: none; box-shadow: none; }
            .login-switch button:hover { color: #222; background: transparent; }
            .login-switch button[aria-selected="true"] { background: #fff; color: #222; font-weight: 500; box-shadow: 0 1px 3px rgba(0,0,0,.15); }
            .login-switch button:focus-visible { outline: 2px solid #3c8dbc; outline-offset: 1px; }
            .login-side[hidden] { display: none; }
            .login-side .gauthenticate-form { display: none; }
            .login-hint { color: #6b7280; font-size: 13px; margin: -4px 0 14px; }
            <?php $lg_brand = $this->customlib->brand()['color']; ?>
            /* the school's colour (Classic Blue by default) on the sign-in buttons and links */
            .form-bottom button.btn[type=submit], .login-side button.btn[type=submit] { background: <?php echo html_escape($lg_brand); ?> !important; border-radius: 10px; font-weight: 600; transition: filter .15s, transform .15s; }
            .form-bottom button.btn[type=submit]:hover { filter: brightness(1.08); transform: translateY(-1px); }
            .login-switch button:focus-visible { outline-color: <?php echo html_escape($lg_brand); ?>; }
            .forgot a, .form-bottom a { color: <?php echo html_escape($lg_brand); ?>; }
        </style>
    </head>
    <body>
        <div class="top-content">
            <div class="inner-bg">
                <div class="container-fluid">
                    <div class="row">
                        <div class="bgoffsetbg">
                            <div class="col-lg-4 col-md-4 col-sm-12 nopadding">
                                <div class="loginbg">
                                    <div class="form-top">
                                        <div class="form-top-left logowidth">
                                            <img src="<?php echo base_url(); ?>uploads/school_content/admin_logo/<?php echo $this->setting_model->getAdminlogo(); ?>" alt="<?php echo html_escape($school['name']); ?>" />
                                        </div>
                                    </div>
                                    <div class="form-bottom">
                                        <?php if ($this->session->flashdata('message')) { ?>
                                            <div class="alert alert-success"><?php echo $this->session->flashdata('message'); ?></div>
                                        <?php } ?>
                                        <?php if ($this->session->flashdata('disable_message')) { ?>
                                            <div class="alert alert-danger"><?php echo $this->session->flashdata('disable_message'); ?></div>
                                        <?php } ?>

                                        <?php if ($student_on) { ?>
                                            <div class="login-switch" role="tablist" aria-label="Who is signing in">
                                                <button type="button" role="tab" id="tab-student" aria-controls="side-student" data-side="student" aria-selected="<?php echo $active === 'student' ? 'true' : 'false'; ?>">Student or parent</button>
                                                <button type="button" role="tab" id="tab-staff" aria-controls="side-staff" data-side="staff" aria-selected="<?php echo $active === 'staff' ? 'true' : 'false'; ?>">Staff</button>
                                            </div>

                                            <div class="login-side" id="side-student" role="tabpanel" aria-labelledby="tab-student" <?php echo $active === 'student' ? '' : 'hidden'; ?>>
                                                <h3 class="font-white"><?php echo $this->lang->line('user_login'); ?></h3>
                                                <?php if (!empty($exam_link)) { ?>
                                                    <div class="alert alert-info js-exam-link">Sign in with your <b>student</b> username and password to start the exam <b><?php echo html_escape($exam_link['title']); ?></b>.</div>
                                                <?php } else { ?>
                                                <p class="login-hint">Use the username and password the school sent you, e.g. std105 or parent105.</p>
                                                <?php } ?>
                                                <?php if (isset($error_message) && $error_side === 'student') { ?>
                                                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                                <?php } ?>
                                                <?php if ($student_2fa) { ?>
                                                    <form class="login-form js-2fa-login" action="javascript:void(0);">
                                                        <span class="error_message"></span>
                                                        <?php echo $this->customlib->getCSRF(); ?>
                                                        <div class="form-group has-feedback">
                                                            <input type="text" name="username" placeholder="<?php echo $this->lang->line('username'); ?>" class="form-username form-control" autocomplete="username" aria-label="<?php echo $this->lang->line('username'); ?>">
                                                            <span class="error_username text-danger"></span>
                                                        </div>
                                                        <div class="form-group has-feedback">
                                                            <input type="password" name="password" placeholder="<?php echo $this->lang->line('password'); ?>" class="form-password form-control" autocomplete="current-password" aria-label="<?php echo $this->lang->line('password'); ?>">
                                                            <span class="error_password text-danger"></span>
                                                        </div>
                                                        <?php if ($student_captcha) { $captcha_block(''); } ?>
                                                        <button type="submit" class="btn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Please wait..."><?php echo $this->lang->line('sign_in'); ?></button>
                                                    </form>
                                                    <form class="gauthenticate-form js-2fa-code" action="javascript:void(0);">
                                                        <span class="error_message_code"></span>
                                                        <div class="form-group">
                                                            <input type="text" name="code" placeholder="<?php echo $this->lang->line('verification_code'); ?>" class="form-control" autocomplete="one-time-code" inputmode="numeric" aria-label="<?php echo $this->lang->line('verification_code'); ?>">
                                                            <span class="error_code text-danger"></span>
                                                        </div>
                                                        <button type="submit" class="btn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Please wait..."><?php echo $this->lang->line('verify_login'); ?></button>
                                                    </form>
                                                <?php } else { ?>
                                                    <form class="login-form" action="<?php echo site_url('site/userlogin'); ?>" method="post">
                                                        <?php echo $this->customlib->getCSRF(); ?>
                                                        <div class="form-group has-feedback">
                                                            <input type="text" name="username" value="<?php echo $value('username', $is_student_post); ?>" placeholder="<?php echo $this->lang->line('username'); ?>" class="form-username form-control" autocomplete="username" aria-label="<?php echo $this->lang->line('username'); ?>">
                                                            <span class="text-danger"><?php echo $is_student_post ? form_error('username') : ''; ?></span>
                                                        </div>
                                                        <div class="form-group has-feedback">
                                                            <input type="password" name="password" placeholder="<?php echo $this->lang->line('password'); ?>" class="form-password form-control" autocomplete="current-password" aria-label="<?php echo $this->lang->line('password'); ?>">
                                                            <span class="text-danger"><?php echo $is_student_post ? form_error('password') : ''; ?></span>
                                                        </div>
                                                        <?php if ($student_captcha) { $captcha_block($is_student_post ? form_error('captcha') : ''); } ?>
                                                        <button type="submit" class="btn"><?php echo $this->lang->line('sign_in'); ?></button>
                                                    </form>
                                                <?php } ?>
                                                <p><a href="<?php echo site_url('site/ufpassword'); ?>" class="forgot"><i class="fa fa-key"></i> <?php echo $this->lang->line('forgot_password'); ?></a></p>
                                            </div>
                                        <?php } ?>

                                        <div class="login-side" id="side-staff" <?php echo $student_on ? 'role="tabpanel" aria-labelledby="tab-staff"' : ''; ?> <?php echo $active === 'staff' ? '' : 'hidden'; ?>>
                                            <h3 class="font-white bolds"><?php echo $this->lang->line('admin_login'); ?></h3>
                                            <p class="login-hint">Teachers, administrators and other staff: sign in with your email address.</p>
                                            <?php if (isset($error_message) && $error_side === 'staff') { ?>
                                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                            <?php } ?>
                                            <form class="login-form" action="<?php echo site_url('site/login'); ?>" method="post">
                                                <?php echo $this->customlib->getCSRF(); ?>
                                                <div class="form-group has-feedback">
                                                    <input type="text" name="username" placeholder="<?php echo $this->lang->line('email'); ?>" value="<?php echo $value('username', $is_staff_post); ?>" class="form-username form-control" autocomplete="username" aria-label="<?php echo $this->lang->line('email'); ?>">
                                                    <span class="fa fa-envelope form-control-feedback"></span>
                                                    <span class="text-danger"><?php echo $is_staff_post ? form_error('username') : ''; ?></span>
                                                </div>
                                                <div class="form-group has-feedback">
                                                    <input type="password" name="password" placeholder="<?php echo $this->lang->line('password'); ?>" class="form-password form-control" autocomplete="current-password" aria-label="<?php echo $this->lang->line('password'); ?>">
                                                    <span class="fa fa-lock form-control-feedback"></span>
                                                    <span class="text-danger"><?php echo $is_staff_post ? form_error('password') : ''; ?></span>
                                                </div>
                                                <?php if (!empty($staff_code)) { ?>
                                                    <div class="form-group has-feedback">
                                                        <input type="text" name="gauth_code" placeholder="6-digit code from your authenticator app" class="form-control" inputmode="numeric" autocomplete="one-time-code" maxlength="6" autofocus aria-label="Authenticator code">
                                                        <span class="fa fa-shield form-control-feedback"></span>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($staff_captcha) { $captcha_block($is_staff_post ? form_error('captcha') : ''); } ?>
                                                <button type="submit" class="btn"><?php echo $this->lang->line('sign_in'); ?></button>
                                            </form>
                                            <a href="<?php echo site_url('site/forgotpassword'); ?>" class="forgot"><i class="fa fa-key"></i> <?php echo $this->lang->line('forgot_password'); ?>?</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-8 col-sm-12 nopadding-2">
                                <div id="login-bg" class="d-flex align-items-center text-wrap flex-column justify-content-center bg-position-sm-left bg-position-lg-center"
                                     data-bg-student="<?php echo html_escape($bg_student); ?>" data-bg-staff="<?php echo html_escape($bg_staff); ?>"
                                     style="background: url('<?php echo $active === 'staff' ? $bg_staff : $bg_student; ?>') no-repeat; background-size:cover">
                                    <div class="<?php if ($notice) { ?> bg-shadow-remove <?php } ?>">
                                        <?php if ($notice) { ?>
                                            <h3 class="h3"><?php echo $this->lang->line('whats_new_in'); ?> <?php echo $school['name']; ?></h3>
                                            <div class="loginright mCustomScrollbar">
                                                <div class="messages">
                                                    <?php foreach ($notice as $notice_value) { ?>
                                                        <h4><?php echo $notice_value['title']; ?></h4>
                                                        <?php
                                                        $string = strip_tags($notice_value['description']);
                                                        if (strlen($string) > 100) {
                                                            $stringCut = substr($string, 0, 100);
                                                            $endPoint = strrpos($stringCut, ' ');
                                                            $string = $endPoint ? substr($stringCut, 0, $endPoint) : $stringCut;
                                                            $string .= '... <a class=more href="' . site_url('read/' . $notice_value['slug']) . '" target="_blank">' . $this->lang->line('read_more') . '</a>';
                                                        }
                                                        echo '<p>' . $string . '</p>';
                                                        ?>
                                                        <div class="logdivider"></div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo base_url(); ?>backend/usertemplate/assets/js/jquery-1.11.1.min.js"></script>
        <script src="<?php echo base_url(); ?>backend/usertemplate/assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>backend/usertemplate/assets/js/jquery.backstretch.min.js"></script>
        <script src="<?php echo base_url(); ?>backend/usertemplate/assets/js/jquery.mCustomScrollbar.min.js"></script>
        <script src="<?php echo base_url(); ?>backend/usertemplate/assets/js/jquery.mousewheel.min.js"></script>
        <script>
            function refreshCaptcha() {
                $.post("<?php echo site_url('site/refreshCaptcha'); ?>", {}, function (captcha) {
                    $('.js-captcha-image').html(captcha);
                });
            }
            (function ($) {
                var KEY = 'edusys_login_as';
                var forced = <?php echo $forced ? 'true' : 'false'; ?>;
                var bg = document.getElementById('login-bg');

                function show(side, remember) {
                    $('.login-switch button').each(function () {
                        this.setAttribute('aria-selected', this.getAttribute('data-side') === side ? 'true' : 'false');
                    });
                    $('#side-student').prop('hidden', side !== 'student');
                    $('#side-staff').prop('hidden', side !== 'staff');
                    bg.style.backgroundImage = "url('" + bg.getAttribute('data-bg-' + side) + "')";
                    if (remember) {
                        try { localStorage.setItem(KEY, side); } catch (e) {}
                    }
                }

                if ($('.login-switch').length) {
                    $('.login-switch').on('click', 'button', function () {
                        show(this.getAttribute('data-side'), true);
                        $('#side-' + this.getAttribute('data-side') + ' input:visible:first').focus();
                    });
                    if (!forced) {
                        var saved = null;
                        try { saved = localStorage.getItem(KEY); } catch (e) {}
                        if (saved === 'staff' || saved === 'student') {
                            show(saved, false);
                        }
                    }
                }

                // Empty fields are marked before posting (as the old pages did).
                $(document).on('submit', '.login-form', function (e) {
                    $(this).find('input[type="text"], input[type="password"]').each(function () {
                        if ($(this).val() === '') {
                            e.preventDefault();
                            $(this).addClass('input-error');
                        } else {
                            $(this).removeClass('input-error');
                        }
                    });
                });
                $(document).on('focus', '.login-form input', function () { $(this).removeClass('input-error'); });

                // Student or parent with two-factor sign-in: check the password, then ask for the code
                // only if that account has an authenticator set up.
                var side = $('#side-student');
                function clearErrors() { side.find("span[class^='error_']").html(''); }
                side.on('submit', '.js-2fa-login', function (e) {
                    e.preventDefault();
                    var form = $(this), btn = form.find('button[type=submit]');
                    if (form.find('input[name=username]').val() === '' || form.find('input[name=password]').val() === '') {
                        return;
                    }
                    clearErrors();
                    $.ajax({
                        url: "<?php echo site_url('gauthenticate/verfiy_userlogin'); ?>",
                        type: 'POST', dataType: 'json', data: form.serialize(),
                        beforeSend: function () { btn.button('loading'); },
                        success: function (r) {
                            if (r.status == 0) {
                                $.each(r.error, function (k, v) { form.find('.error_' + k).html(v); });
                            } else if (r.status == 1) {
                                form.find('.error_message').html(r.error.error_message);
                            } else if (r.status == 2) {
                                if (!r.authenticator) { window.location.href = r.redirect_to; return; }
                                form.fadeOut(300, function () { side.find('.js-2fa-code').fadeIn(200).find('input').focus(); });
                            }
                        },
                        error: function () { form.find('.error_message').html('Something went wrong. Please try again.'); },
                        complete: function () { btn.button('reset'); }
                    });
                });
                side.on('submit', '.js-2fa-code', function (e) {
                    e.preventDefault();
                    var form = $(this), btn = form.find('button[type=submit]');
                    clearErrors();
                    $.ajax({
                        url: "<?php echo site_url('gauthenticate/user_submit_login'); ?>",
                        type: 'POST', dataType: 'json', data: side.find('.js-2fa-login').serialize() + '&' + form.serialize(),
                        beforeSend: function () { btn.button('loading'); },
                        success: function (r) {
                            if (r.status == 0) {
                                $.each(r.error, function (k, v) { side.find('.error_' + k).html(v); });
                            } else if (r.status == 1) {
                                form.find('.error_message_code').html(r.error.error_message);
                            } else if (r.status == 2) {
                                window.location.href = r.redirect_to;
                            }
                        },
                        error: function () { form.find('.error_message_code').html('Something went wrong. Please try again.'); },
                        complete: function () { btn.button('reset'); }
                    });
                });
            })(jQuery);
        </script>
    </body>
</html>
