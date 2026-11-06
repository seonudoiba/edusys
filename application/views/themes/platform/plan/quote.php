<section class="hero has-bg" style="padding-bottom:clamp(32px,5vw,52px)">
    <?php $this->load->view('themes/platform/_bg'); ?>
    <div class="wrap">
        <h1>Request a quote.</h1>
        <p class="lede">For schools and groups with more than 3,000 students. Implementation and training starts from &#8358;750,000. We will reply by email.</p>
    </div>
</section>
<section class="band">
    <div class="wrap" style="max-width:720px;margin-left:calc((100% - min(100%, 1160px)) / 2)">
        <?php if ($sent) { ?>
            <div class="alert alert-success">Thank you. We have your request and will reply by email.</div>
        <?php } else { ?>
            <?php if ($errors) { ?><div class="alert alert-danger" role="alert"><?php echo implode('<br>', array_map('html_escape', $errors)); ?></div><?php } ?>
            <form method="post" action="<?php echo site_url('plans/quote'); ?>">
                <div class="field"><label for="q_school">School or group name</label><input id="q_school" name="school_name" type="text" required maxlength="255" value="<?php echo html_escape($this->input->post('school_name')); ?>"></div>
                <div class="field"><label for="q_students">Number of students</label><input id="q_students" name="students" type="number" min="1" required value="<?php echo $students ?: ''; ?>"></div>
                <div class="grid2">
                    <div class="field"><label for="q_name">Your name</label><input id="q_name" name="name" type="text" required maxlength="255" value="<?php echo html_escape($this->input->post('name')); ?>"></div>
                    <div class="field"><label for="q_phone">Phone</label><input id="q_phone" name="phone" type="tel" maxlength="50" value="<?php echo html_escape($this->input->post('phone')); ?>"></div>
                </div>
                <div class="field"><label for="q_email">Email</label><input id="q_email" name="email" type="email" required maxlength="255" value="<?php echo html_escape($this->input->post('email')); ?>"></div>
                <div class="field"><label for="q_message">Anything we should know <span class="hint" style="display:inline">(optional)</span></label><textarea id="q_message" name="message" rows="4" maxlength="5000"><?php echo html_escape($this->input->post('message')); ?></textarea></div>
                <div style="position:absolute;left:-9999px" aria-hidden="true"><label>Leave this empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                <button class="btn" type="submit">Send request</button>
            </form>
        <?php } ?>
    </div>
</section>
