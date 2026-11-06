<?php
$part = function ($title, $text) { return '<div><h3>' . $title . '</h3><p>' . $text . '</p></div>'; };
?>
<section class="hero has-bg" style="padding-bottom:clamp(40px,6vw,64px)">
    <?php $this->load->view('themes/platform/_bg'); ?>
    <div class="wrap">
        <h1>What EduSYS does.</h1>
        <p class="lede">One system for the office, the classroom and the home. Each school runs on its own web address, with its own data, staff and branding.</p>
    </div>
</section>

<section class="band">
    <div class="wrap split">
        <div><h2>Students</h2></div>
        <div class="rows">
            <?php echo $part('Admissions', 'Parents apply online. You review the applications and enrol students without typing their details twice.'); ?>
            <?php echo $part('Student records', 'Personal details, guardians, documents and history for each student, kept from year to year as they move up.'); ?>
            <?php echo $part('Attendance', 'Daily or by subject, marked by the teacher or by scanning a QR code.'); ?>
            <?php echo $part('Behaviour', 'Record incidents and see patterns by student, class or house.'); ?>
            <?php echo $part('Certificates and ID cards', 'Design your own, then generate them for a whole class at once.'); ?>
            <?php echo $part('Alumni', 'Keep in touch with former students and invite them to events.'); ?>
        </div>
    </div>
</section>

<section class="band">
    <div class="wrap split">
        <div><h2>Money</h2></div>
        <div class="rows">
            <?php echo $part('Fees', 'Set fees by class, with discounts and late fines. See who has paid and who hasn\'t, and print receipts.'); ?>
            <?php echo $part('Online payment', 'Parents pay by card or transfer through Paystack, Flutterwave, Monnify, Interswitch or Remita. Each payment is recorded against the right student.'); ?>
            <?php echo $part('Income and expenses', 'Record what comes in and goes out, by category.'); ?>
            <?php echo $part('Payroll', 'Generate staff pay and payslips.'); ?>
        </div>
    </div>
</section>

<section class="band">
    <div class="wrap split">
        <div><h2>Teaching</h2></div>
        <div class="rows">
            <?php echo $part('Classes and timetables', 'Classes, sections, subjects and teachers, with a timetable each student and teacher can see.'); ?>
            <?php echo $part('Lessons and homework', 'Plan lessons, track the syllabus, set homework and evaluate it.'); ?>
            <?php echo $part('Examinations', 'Schedules, marks and grades, with results and report cards generated from them.'); ?>
            <?php echo $part('Online examinations and courses', 'Set tests that students take online, and publish courses they can follow at their own pace.'); ?>
            <?php echo $part('Live classes', 'Run classes through Zoom or Google Meet. These are add-ons, charged separately.'); ?>
            <?php echo $part('Download centre', 'Share notes, assignments and other files with a class.'); ?>
        </div>
    </div>
</section>

<section class="band">
    <div class="wrap split">
        <div><h2>People and communication</h2></div>
        <div class="rows">
            <?php echo $part('Staff', 'Records, leave and attendance, with roles that decide what each person can see and do.'); ?>
            <?php echo $part('Logins for everyone', 'Administrators, teachers, parents and students each have their own login and see what concerns them.'); ?>
            <?php echo $part('Messages', 'Send notices, email and SMS to a class, a group or the whole school. SMS is charged by your provider.'); ?>
            <?php echo $part('Your own website', 'Your school gets a website at its own address. Arrange its pages by dragging sections into place; news, events and gallery stay up to date from the system.'); ?>
        </div>
    </div>
</section>

<section class="band">
    <div class="wrap split">
        <div><h2>Facilities</h2></div>
        <div class="rows">
            <?php echo $part('Library', 'Books, members, issues and returns.'); ?>
            <?php echo $part('Transport', 'Routes, vehicles and which students use them.'); ?>
            <?php echo $part('Hostel', 'Rooms and the students assigned to them.'); ?>
            <?php echo $part('Inventory', 'Stock, suppliers and items issued.'); ?>
            <?php echo $part('Front office', 'Enquiries, visitors, calls, post and complaints.'); ?>
        </div>
    </div>
</section>

<section class="band band-green">
    <div class="wrap">
        <h2 style="max-width:16em;margin-bottom:28px">See what it would cost your school.</h2>
        <a class="btn" href="<?php echo site_url('plans'); ?>">See pricing</a>
    </div>
</section>
