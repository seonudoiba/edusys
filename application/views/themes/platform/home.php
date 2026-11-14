<?php
// A layout published in the page builder replaces this page.
if (!empty($page['builder_html'])) {
    echo '<div class="wrap">' . $page['description'] . '</div>';
    return;
}
$this->load->view('themes/platform/_icons');
$CI = &get_instance();
$CI->load->library('Plan_pricing');
$bands = $CI->plan_pricing->bands();
$rates = array_filter(array_column($bands, 'rate_per_term'));
$money = function ($v) { return '&#8358;' . number_format((float) $v); };
// The first Front CMS banner image (if any) is the hero photo.
$this->load->view('themes/platform/_bg', array(), true);
$photo = !empty($CI->platform_bg_images) ? $CI->platform_bg_images[0] : null;
$areas = array(
    array('users', '', 'Students', 'Online admission, records, attendance with QR codes, behaviour, certificates, ID cards and alumni.'),
    array('wallet', 'green', 'Fees and finance', 'Fees by class with discounts and fines, online payment, reminders, income, expenses and payroll.'),
    array('book', 'yellow', 'Teaching', 'Classes, timetables, lesson plans, homework, examinations, report cards and online tests.'),
    array('badge', 'red', 'Staff', 'Staff records, leave and attendance, with roles that decide what each person can see and do.'),
    array('message', '', 'Communication', 'Notices, email, SMS and chat to a class, a group or the whole school, plus a shared calendar.'),
    array('globe', 'green', 'Your school website', 'A website at your school\'s own address, with news, events and gallery kept up to date from the system.'),
);
$faqs = array(
    'How is the price worked out?' => '<p>You pay per student, per term, at a rate set by the size of your school, plus a one-time fee for implementation and training. You can pay each term or for the year; the price per student is the same. <a href="' . site_url('plans') . '">See pricing</a>.</p>',
    'Can parents pay school fees online?' => '<p>Yes. Parents pay by card or transfer through Paystack, Flutterwave, Monnify, Interswitch or Remita, and each payment is recorded against the right student. Schools can also accept bank transfers and confirm them from the receipt.</p>',
    'Does each school get its own website?' => '<p>Yes. Each school runs on its own web address, with its own data, staff, branding and public website. Staff, parents and students sign in there.</p>',
    'Is our data kept separate from other schools?' => '<p>Yes. Every school\'s records are kept apart, and each person only sees what their role allows. Read our <a href="' . site_url('page/privacy-policy') . '">Privacy Policy</a> for how we protect data.</p>',
    'What is not included in the subscription?' => '<p>SMS and other third-party messaging charges, payment gateway fees, Zoom and Google Meet add-ons, custom development, large data migrations and hardware.</p>',
    'What happens if our school grows during the year?' => '<p>You pay for the extra students at your current rate, for the terms left in the period. Your end date does not change.</p>',
);
?>
<section class="hero hero-home has-bg">
    <div class="wrap hero-grid">
        <div>
            <span class="pill"><b>New</b> Admission forms, online fees and bank transfers</span>
            <h1>Run your whole school on one system.</h1>
            <p class="lede">EduSYS brings admissions, fees, attendance, examinations, staff and parent communication together for Nigerian schools, at your school's own web address.</p>
            <div class="actions">
                <a class="btn btn-red" href="<?php echo site_url('subscription/add'); ?>">Register your school <?php echo pf_icon('arrow'); ?></a>
                <a class="btn btn-ghost" href="<?php echo site_url('plans'); ?>">See pricing</a>
            </div>
            <?php if ($rates) { ?><p class="from">From <?php echo $money(min($rates)); ?> per student, per term. Implementation and training included in a one-time fee.</p><?php } ?>
            <ul class="ticks">
                <li><?php echo pf_icon('check'); ?>Your own web address</li>
                <li><?php echo pf_icon('check'); ?>Online fee payment</li>
                <li><?php echo pf_icon('check'); ?>Training for your staff</li>
            </ul>
        </div>
        <div class="preview" aria-hidden="true">
            <?php if ($photo) { ?>
                <div class="preview-photo"><img src="<?php echo html_escape($photo); ?>" alt=""></div>
            <?php } else { ?>
                <div class="panel-ui">
                    <div class="ui-head"><span>Fees this term</span><span class="ui-dots"><i></i><i></i><i></i></span></div>
                    <table><tr><th>Student</th><th>Class</th><th>Status</th></tr>
                        <tr><td>Student A</td><td>JSS 1</td><td><span class="tag tag-paid">Paid</span></td></tr>
                        <tr><td>Student B</td><td>JSS 2</td><td><span class="tag tag-due">Due</span></td></tr>
                        <tr><td>Student C</td><td>SS 1</td><td><span class="tag tag-paid">Paid</span></td></tr>
                    </table>
                </div>
            <?php } ?>
            <div class="preview-cards">
                <div class="preview-card preview-fees">
                    <div class="muted">Term fees collected</div>
                    <div class="big">78%</div>
                    <div class="bar"><span style="width:78%"></span></div>
                </div>
                <div class="preview-card preview-att">
                    <div class="muted">Attendance today</div>
                    <div class="row-mini"><span>Present</span><b>412</b></div>
                    <div class="row-mini"><span>Absent</span><b>9</b></div>
                    <div class="row-mini"><span>Late</span><b>14</b></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="strip" aria-label="Online payment partners">
    <div class="wrap">
        <p>Parents pay online through</p>
        <ul><li>Paystack</li><li>Flutterwave</li><li>Monnify</li><li>Interswitch</li><li>Remita</li></ul>
    </div>
</section>

<section class="band">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">One platform</p>
            <h2>Everything a school runs on, in one place.</h2>
            <p>Administration is the same work every term. EduSYS keeps it in one system and automates what repeats, so your school can give its attention to teaching.</p>
        </div>
        <div class="cards">
            <?php foreach ($areas as $a) { ?>
                <div class="card">
                    <span class="card-icon <?php echo $a[1]; ?>"><?php echo pf_icon($a[0]); ?></span>
                    <h3><?php echo $a[2]; ?></h3>
                    <p><?php echo $a[3]; ?></p>
                </div>
            <?php } ?>
        </div>
        <p style="margin:28px 0 0"><a class="link-arrow" href="<?php echo site_url('product'); ?>">See every feature and who uses it <?php echo pf_icon('arrow'); ?></a></p>
    </div>
</section>

<section class="band band-tint">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">How it fits your term</p>
            <h2>A school year, in one place.</h2>
        </div>
        <ol class="steps">
            <li><h3>Before resumption</h3><p>Take admissions online, place students in classes, set fees and timetables, and print ID cards.</p></li>
            <li><h3>During term</h3><p>Mark attendance, set homework, plan lessons and collect fees. Parents pay online, and each payment is recorded against the right student.</p></li>
            <li><h3>End of term</h3><p>Enter marks, publish results and report cards, follow up what is outstanding, and see the term's income and expenses.</p></li>
        </ol>
    </div>
</section>

<section class="band">
    <div class="wrap">
        <div class="feature">
            <div>
                <p class="eyebrow">Fees</p>
                <h2>Know who has paid, without the paperwork.</h2>
                <p>Set fees by class with discounts and late fines. Parents pay online or by bank transfer, and every payment lands against the right student, with a receipt.</p>
                <ul class="checks">
                    <li><?php echo pf_icon('check'); ?>Online payment through five Nigerian gateways</li>
                    <li><?php echo pf_icon('check'); ?>Bank transfers confirmed from the uploaded receipt</li>
                    <li><?php echo pf_icon('check'); ?>Reminders for what is outstanding</li>
                </ul>
            </div>
            <div class="panel-ui" aria-hidden="true">
                <div class="ui-head"><span>JSS 2 &middot; Second term fees</span><span class="ui-dots"><i></i><i></i><i></i></span></div>
                <table>
                    <tr><th>Student</th><th>Paid by</th><th>Status</th></tr>
                    <tr><td>Adaeze O.</td><td>Paystack</td><td><span class="tag tag-paid">Paid</span></td></tr>
                    <tr><td>Tunde B.</td><td>Bank transfer</td><td><span class="tag tag-new">To confirm</span></td></tr>
                    <tr><td>Ifeoma N.</td><td>Monnify</td><td><span class="tag tag-paid">Paid</span></td></tr>
                    <tr><td>Musa A.</td><td>&mdash;</td><td><span class="tag tag-due">Due</span></td></tr>
                </table>
                <div class="ui-foot">Sample screen</div>
            </div>
        </div>
        <div class="feature flip">
            <div>
                <p class="eyebrow">Admissions</p>
                <h2>Admissions that start on your website.</h2>
                <p>Parents apply online with the questions you choose, pay the form fee, and follow their application. You review, admit and place students without typing their details twice.</p>
                <ul class="checks">
                    <li><?php echo pf_icon('check'); ?>Your own form: fields, questions, classes and dates</li>
                    <li><?php echo pf_icon('check'); ?>Form fee paid online or by transfer</li>
                    <li><?php echo pf_icon('check'); ?>Admission numbers generated for you</li>
                </ul>
            </div>
            <div class="panel-ui" aria-hidden="true">
                <div class="ui-head"><span>Online applications</span><span class="ui-dots"><i></i><i></i><i></i></span></div>
                <table>
                    <tr><th>Applicant</th><th>Class</th><th>Form</th></tr>
                    <tr><td>Chinedu E.</td><td>Primary 1</td><td><span class="tag tag-paid">Fee paid</span></td></tr>
                    <tr><td>Aisha Y.</td><td>JSS 1</td><td><span class="tag tag-new">Submitted</span></td></tr>
                    <tr><td>Kemi A.</td><td>JSS 1</td><td><span class="tag tag-paid">Fee paid</span></td></tr>
                </table>
                <div class="ui-foot">Sample screen</div>
            </div>
        </div>
    </div>
</section>

<section class="band band-tint">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">Pricing</p>
            <h2>Priced by the size of your school.</h2>
            <p>Pay each term or for the year; the price per student is the same. The implementation and training fee is paid once.</p>
        </div>
        <div class="price-card scroll">
            <table class="prices">
                <thead><tr><th>Students</th><th>Per student, per term</th><th>Implementation and training, once</th></tr></thead>
                <tbody>
                    <?php foreach ($bands as $b) { ?>
                        <tr>
                            <td><?php echo number_format((int) $b['min_students']) . ($b['max_students'] === null ? '+' : '&ndash;' . number_format((int) $b['max_students'])); ?></td>
                            <td><?php echo $b['rate_per_term'] === null ? 'Custom quote' : $money($b['rate_per_term']); ?></td>
                            <td><?php echo ($b['rate_per_term'] === null ? 'From ' : '') . $money($b['setup_fee']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="actions" style="margin-top:24px">
            <a class="btn" href="<?php echo site_url('plans'); ?>">Work out what your school would pay</a>
            <a class="link-arrow" href="<?php echo site_url('plans/quote'); ?>">More than 3,000 students? Request a quote <?php echo pf_icon('arrow'); ?></a>
        </div>
    </div>
</section>

<section class="band">
    <div class="wrap company">
        <div>
            <p class="eyebrow">Who is behind it</p>
            <h2>Built and supported by HyperCom.</h2>
            <p class="lede" style="margin-top:16px">HyperCom is an integrated information technology company founded by Dr Joshua A. Denila. We work with businesses of every size, from small enterprises to large corporations, on telecoms, internet technology and green energy.</p>
            <p style="color:var(--soft)">EduSYS is our school management platform. Each school runs on its own web address, with its own data, staff and branding.</p>
            <a class="link-arrow" href="<?php echo site_url('page/about-us'); ?>">About HyperCom <?php echo pf_icon('arrow'); ?></a>
        </div>
        <div class="offices">
            <div class="office"><?php echo pf_icon('pin'); ?><div><h3>Head office</h3><p>14 Akinremi Street, Ikeja, Lagos</p></div></div>
            <div class="office"><?php echo pf_icon('pin'); ?><div><h3>Warri office</h3><p>1 Melcut Road, Okapa, Udu, Warri</p></div></div>
            <div class="office"><?php echo pf_icon('support'); ?><div><h3>Talk to us</h3><p><a href="tel:+2348033836341">+234 803 383 6341</a> &middot; <a href="mailto:info@hypercom.net.ng">info@hypercom.net.ng</a></p></div></div>
        </div>
    </div>
</section>

<section class="band band-tint">
    <div class="wrap">
        <div class="section-head center">
            <p class="eyebrow">Questions</p>
            <h2>Frequently asked questions</h2>
        </div>
        <div class="faq">
            <?php foreach ($faqs as $q => $a) { ?>
                <details><summary><?php echo $q; ?></summary><div><?php echo $a; ?></div></details>
            <?php } ?>
        </div>
    </div>
</section>

<section class="band band-navy">
    <div class="wrap cta">
        <div>
            <h2>Register your school, or talk to us first.</h2>
            <p>We set up your school and train your staff. Call or email us with any question before you start.</p>
            <div class="contact-line">
                <a href="tel:+2348033836341"><?php echo pf_icon('phone'); ?>+234 803 383 6341</a>
                <a href="mailto:info@hypercom.net.ng"><?php echo pf_icon('mail'); ?>info@hypercom.net.ng</a>
            </div>
        </div>
        <div class="actions">
            <a class="btn" href="<?php echo site_url('subscription/add'); ?>">Register your school <?php echo pf_icon('arrow'); ?></a>
        </div>
    </div>
</section>
