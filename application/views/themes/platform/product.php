<?php
$this->load->view('themes/platform/_icons');
$part = function ($title, $text) { return '<div><h3>' . $title . '</h3><p>' . $text . '</p></div>'; };
$area = function ($icon, $tone, $title, $intro) {
    return '<div><span class="card-icon ' . $tone . '">' . pf_icon($icon) . '</span><h2 style="font-size:clamp(1.5rem,2.6vw,2rem)">' . $title . '</h2><p>' . $intro . '</p></div>';
};
?>
<section class="hero hero-sm has-bg">
    <?php $this->load->view('themes/platform/_bg'); ?>
    <div class="wrap">
        <p class="crumb">Product</p>
        <h1>What EduSYS does.</h1>
        <p class="lede">One system for the office, the classroom and the home. Each school runs on its own web address, with its own data, staff and branding.</p>
        <p style="margin:18px 0 0"><a class="link-arrow" href="<?php echo site_url('features'); ?>">See every menu and who uses it <?php echo pf_icon('arrow'); ?></a></p>
    </div>
</section>

<section class="strip" aria-label="Sections on this page">
    <div class="wrap">
        <p>Jump to</p>
        <ul style="font-size:.95rem">
            <li><a href="#students">Students</a></li>
            <li><a href="#money">Money</a></li>
            <li><a href="#teaching">Teaching</a></li>
            <li><a href="#people">People</a></li>
            <li><a href="#facilities">Facilities</a></li>
        </ul>
    </div>
</section>

<div class="wrap">
    <section class="area" id="students">
        <?php echo $area('users', '', 'Students', 'From the first application to the day they leave.'); ?>
        <div class="rows">
            <?php echo $part('Admissions', 'Parents apply online with the form and questions you choose, and pay the form fee. You review the applications and enrol students without typing their details twice.'); ?>
            <?php echo $part('Student records', 'Personal details, guardians, documents and history for each student, kept from year to year as they move up.'); ?>
            <?php echo $part('Attendance', 'Daily or by subject, marked by the teacher or by scanning a QR code.'); ?>
            <?php echo $part('Behaviour', 'Record incidents and see patterns by student, class or house.'); ?>
            <?php echo $part('Certificates and ID cards', 'Design your own, then generate them for a whole class at once.'); ?>
            <?php echo $part('Alumni', 'Keep in touch with former students and invite them to events.'); ?>
        </div>
    </section>

    <section class="area" id="money">
        <?php echo $area('wallet', 'green', 'Money', 'Fees, payments and the school\'s accounts.'); ?>
        <div class="rows">
            <?php echo $part('Fees', 'Set fees by class, with discounts and late fines. See who has paid and who hasn\'t, and print receipts.'); ?>
            <?php echo $part('Online payment', 'Parents pay by card or transfer through Paystack, Flutterwave, Monnify, Interswitch or Remita. Each payment is recorded against the right student.'); ?>
            <?php echo $part('Bank transfers', 'Parents and applicants upload the transfer receipt; the school checks it and confirms the payment.'); ?>
            <?php echo $part('Income and expenses', 'Record what comes in and goes out, by category.'); ?>
            <?php echo $part('Payroll', 'Generate staff pay and payslips.'); ?>
            <?php echo $part('Financial reports', 'See the term\'s income, expenses and what is outstanding.'); ?>
        </div>
    </section>

    <section class="area" id="teaching">
        <?php echo $area('book', 'yellow', 'Teaching', 'Timetables, lessons, homework and results.'); ?>
        <div class="rows">
            <?php echo $part('Classes and timetables', 'Classes, sections, subjects and teachers, with a timetable each student and teacher can see.'); ?>
            <?php echo $part('Lessons and homework', 'Plan lessons, track the syllabus, set homework and evaluate it.'); ?>
            <?php echo $part('Examinations', 'Schedules, marks and grades, with results and report cards generated from them.'); ?>
            <?php echo $part('Online examinations and courses', 'Set tests that students take online, and publish courses they can follow at their own pace.'); ?>
            <?php echo $part('Live classes', 'Run classes through Zoom or Google Meet. These are add-ons, charged separately.'); ?>
            <?php echo $part('Download centre', 'Share notes, assignments and other files with a class.'); ?>
        </div>
    </section>

    <section class="area" id="people">
        <?php echo $area('message', 'red', 'People and communication', 'Everyone in the school, with the right access.'); ?>
        <div class="rows">
            <?php echo $part('Staff', 'Records, leave and attendance, with roles that decide what each person can see and do.'); ?>
            <?php echo $part('Logins for everyone', 'Administrators, teachers, parents and students each have their own login and see what concerns them.'); ?>
            <?php echo $part('Messages', 'Send notices, email and SMS to a class, a group or the whole school. SMS is charged by your provider.'); ?>
            <?php echo $part('Your own website', 'Your school gets a website at its own address. Arrange its pages with the block editor; news, events and gallery stay up to date from the system.'); ?>
        </div>
    </section>

    <section class="area" id="facilities">
        <?php echo $area('building', '', 'Facilities', 'The rest of the school\'s operations.'); ?>
        <div class="rows">
            <?php echo $part('Library', 'Books, members, issues and returns.'); ?>
            <?php echo $part('Transport', 'Routes, vehicles and which students use them.'); ?>
            <?php echo $part('Hostel', 'Rooms and the students assigned to them.'); ?>
            <?php echo $part('Inventory', 'Stock, suppliers and items issued.'); ?>
            <?php echo $part('Front office', 'Enquiries, visitors, calls, post and complaints.'); ?>
        </div>
    </section>
</div>

<section class="band band-navy">
    <div class="wrap cta">
        <div>
            <h2>See what it would cost your school.</h2>
            <p>One price per student, per term, set by the size of your school.</p>
        </div>
        <div class="actions"><a class="btn" href="<?php echo site_url('plans'); ?>">See pricing <?php echo pf_icon('arrow'); ?></a></div>
    </div>
</section>
