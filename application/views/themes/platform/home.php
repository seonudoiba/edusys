<?php
// A layout published in the page builder replaces this page.
if (!empty($page['builder_html'])) {
    echo '<div class="wrap">' . $page['description'] . '</div>';
    return;
}
$CI = &get_instance();
$CI->load->library('Plan_pricing');
$bands = $CI->plan_pricing->bands();
$rates = array_filter(array_column($bands, 'rate_per_term'));
$money = function ($v) { return '&#8358;' . number_format((float) $v); };
?>
<section class="hero has-bg">
    <?php $this->load->view('themes/platform/_bg'); ?>
    <div class="wrap">
        <h1>The whole school, on one system.</h1>
        <p class="lede">EduSYS runs admissions, fees, attendance, examinations, staff and parent communication for Nigerian schools, at your school's own web address.</p>
        <div class="actions">
            <a class="btn" href="<?php echo site_url('plans'); ?>">See pricing</a>
            <a href="<?php echo site_url('product'); ?>">What it does</a>
        </div>
        <?php if ($rates) { ?><p class="from">From <?php echo $money(min($rates)); ?> per student, per term.</p><?php } ?>
    </div>
</section>

<section class="band">
    <div class="wrap split">
        <div><h2>A school year, in one place</h2></div>
        <ol class="steps">
            <li><h3>Before resumption</h3><p>Take admissions online, place students in classes, set fees and timetables, and print ID cards.</p></li>
            <li><h3>During term</h3><p>Mark attendance, set homework, plan lessons and collect fees. Parents pay online, and each payment is recorded against the right student.</p></li>
            <li><h3>End of term</h3><p>Enter marks, publish results and report cards, chase what is outstanding, and see the term's income and expenses.</p></li>
        </ol>
    </div>
</section>

<section class="band">
    <div class="wrap">
        <h2 style="margin-bottom:40px">What is in it</h2>
        <div class="index">
            <section><h3>Students</h3><ul><li>Online admission</li><li>Student records</li><li>Attendance, including QR codes</li><li>Behaviour records</li><li>Certificates and ID cards</li><li>Alumni</li></ul></section>
            <section><h3>Money</h3><ul><li>Fees and online payment</li><li>Fee reminders</li><li>Income and expenses</li><li>Payroll</li><li>Financial reports</li></ul></section>
            <section><h3>Teaching</h3><ul><li>Classes and timetables</li><li>Lesson plans and homework</li><li>Examinations and report cards</li><li>Online examinations</li><li>Online courses</li><li>Download centre</li></ul></section>
            <section><h3>People</h3><ul><li>Staff records and leave</li><li>Roles and permissions</li><li>Separate logins for staff, parents and students</li></ul></section>
            <section><h3>Communication</h3><ul><li>Notices, email and SMS</li><li>Chat</li><li>Calendar</li><li>Your school's own website</li></ul></section>
            <section><h3>Facilities</h3><ul><li>Library</li><li>Transport</li><li>Hostel</li><li>Inventory</li><li>Front office</li></ul></section>
        </div>
        <p style="margin:12px 0 0"><a href="<?php echo site_url('product'); ?>">Read about each part</a></p>
    </div>
</section>

<section class="band">
    <div class="wrap split">
        <div>
            <h2>Priced by the size of your school</h2>
            <p style="margin-top:18px;color:var(--soft)">Pay each term or for the year. The setup fee is paid once.</p>
        </div>
        <div>
            <div class="scroll">
                <table class="prices">
                    <thead><tr><th>Students</th><th>Per student, per term</th><th>Implementation and training</th></tr></thead>
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
            <p style="margin:24px 0 0"><a href="<?php echo site_url('plans'); ?>">Work out what your school would pay</a></p>
        </div>
    </div>
</section>

<section class="band">
    <div class="wrap split">
        <div><h2>Who is behind it</h2></div>
        <div>
            <p class="lede">EduSYS is built and supported by Hypercom Network Ltd, a Nigerian information technology company established in 2000 and based in Ikeja, Lagos.</p>
            <p style="margin:0"><a href="<?php echo site_url('page/about-us'); ?>">About Hypercom</a></p>
        </div>
    </div>
</section>

<section class="band band-green">
    <div class="wrap">
        <h2 style="max-width:16em;margin-bottom:28px">Register your school, or talk to us first.</h2>
        <div class="actions">
            <a class="btn" href="<?php echo site_url('subscription/add'); ?>">Register your school</a>
            <a href="tel:+2348033836341">+234 803 383 6341</a>
            <a href="mailto:info@hypercom.net.ng">info@hypercom.net.ng</a>
        </div>
    </div>
</section>
