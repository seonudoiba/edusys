<?php
/* /features: every sidebar menu a school has, what it does, and which staff roles have it by default. */
$this->load->view('themes/platform/_icons');
$data = include __DIR__ . '/_features_data.php';
$e = function ($v) { return html_escape((string) $v); };
$modules = 0;
$tools = 0;
foreach ($data['areas'] as $a) {
    $modules += count($a['modules']);
    foreach ($a['modules'] as $m) { $tools += count($m['entries']); }
}
$role_text = array(
    'Admin' => 'Runs the school day to day: students, fees, staff, exams, the website and settings.',
    'Teacher' => 'Their classes: attendance, lessons, homework, CBT, report-card scores and remarks.',
    'Accountant' => 'Money: fees and payments, income, expenses, payroll, store and transport.',
    'Librarian' => 'The library: books, members, issuing and returns.',
    'Receptionist' => 'The front office: enquiries, visitors, calls, post and complaints, and admissions.',
);
$portal = array(
    array('Dashboard', 'Notices, today\'s attendance, homework and exams due, calendar and to-do list.', 'Both'),
    array('Fees', 'What is owed and paid, receipts, a printable fee bill, and payment online or by bank transfer.', 'Both'),
    array('Report cards and results', 'Term report cards with scores, grades, position and remarks; exam results and timetables.', 'Both'),
    array('Online exams (CBT)', 'Take tests when they open; answers save as you go and you can resume.', 'Both'),
    array('Homework', 'Download homework, submit work and see the teacher\'s marks.', 'Both'),
    array('Attendance', 'The attendance calendar, and leave requests to the school.', 'Both'),
    array('Timetable and lessons', 'The class timetable, lesson plans and how far each subject has got.', 'Both'),
    array('Downloads and videos', 'Notes, assignments and videos the school shares with the class.', 'Both'),
    array('Live classes and courses', 'Join Zoom or Google Meet classes and follow online courses.', 'Both'),
    array('Library, transport and hostel', 'Books borrowed, the bus route and pickup point, the hostel room.', 'Both'),
    array('Notice board', 'Notices the school posts for students and parents.', 'Both'),
    array('Profile', 'Student and guardian details; fields the school allows can be updated.', 'Both'),
);
?>
<section class="hero hero-sm has-bg">
    <?php $this->load->view('themes/platform/_bg'); ?>
    <div class="wrap">
        <p class="crumb">Features</p>
        <h1>Every part of EduSYS, and who uses it.</h1>
        <p class="lede">Each menu a school gets, what it does, and which staff can use it. Pick a role to see one person's EduSYS.</p>
        <ul class="feat-stats">
            <li><b><?php echo $modules; ?></b> modules</li>
            <li><b><?php echo $tools; ?></b> tools</li>
            <li><b><?php echo count($data['roles']) + 1; ?></b> staff roles</li>
            <li><b>1</b> portal for parents and students</li>
        </ul>
    </div>
</section>

<div class="feat-bar" id="feat-bar">
    <div class="wrap">
        <div class="feat-roles" role="group" aria-label="Show what a role can use">
            <span class="feat-label">Show for</span>
            <button type="button" data-role="" aria-pressed="true">Everyone</button>
            <?php foreach ($data['roles'] as $r) { ?><button type="button" data-role="<?php echo $e($r); ?>" aria-pressed="false"><?php echo $e($r); ?></button><?php } ?>
        </div>
        <label class="feat-search"><span class="sr-only">Search features</span>
            <input type="search" id="feat-q" placeholder="Search, e.g. report card, fees, WhatsApp" autocomplete="off">
        </label>
    </div>
</div>

<div class="wrap feat-layout">
    <nav class="feat-toc" aria-label="Areas">
        <p>Areas</p>
        <ol>
            <?php foreach ($data['areas'] as $a) { ?>
                <li><a href="#<?php echo $e($a['key']); ?>"><?php echo $e($a['title']); ?></a></li>
            <?php } ?>
            <li><a href="#portal">Parent and student portal</a></li>
            <li><a href="#roles">Roles</a></li>
        </ol>
    </nav>

    <div class="feat-main">
        <p class="feat-result" id="feat-result" aria-live="polite"></p>
        <?php foreach ($data['areas'] as $a) { ?>
            <section class="feat-area" id="<?php echo $e($a['key']); ?>">
                <header class="feat-area-head">
                    <span class="card-icon"><?php echo pf_icon($a['icon']); ?></span>
                    <div>
                        <h2><?php echo $e($a['title']); ?></h2>
                        <p><?php echo $e($a['intro']); ?></p>
                    </div>
                </header>
                <?php foreach ($a['modules'] as $m) { ?>
                    <article class="feat-module">
                        <div class="feat-module-head">
                            <h3><?php echo $e($m['name']); ?></h3>
                            <p><?php echo $e($m['what']); ?></p>
                            <p class="feat-who"><span>Used by</span> <?php echo $e(implode(', ', array_merge(array('Super Admin'), $m['who']))); ?></p>
                        </div>
                        <ul class="feat-list">
                            <?php foreach ($m['entries'] as $x) { ?>
                                <li data-who="<?php echo $e(implode('|', $x['who'])); ?>">
                                    <div>
                                        <h4><?php echo $e($x['name']); ?></h4>
                                        <p><?php echo $e($x['what']); ?></p>
                                    </div>
                                    <div class="feat-chips" aria-label="Roles">
                                        <?php foreach ($data['roles'] as $r) { $on = in_array($r, $x['who'], true); ?>
                                            <span class="<?php echo $on ? 'on' : ''; ?>" title="<?php echo $e($r . ($on ? '' : ': not by default')); ?>"><?php echo $e($r); ?></span>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </article>
                <?php } ?>
            </section>
        <?php } ?>
        <p class="feat-none" id="feat-none" hidden>Nothing matches. Try another word, or pick Everyone.</p>

        <section class="feat-area" id="portal">
            <header class="feat-area-head">
                <span class="card-icon green"><?php echo pf_icon('badge'); ?></span>
                <div>
                    <h2>Parent and student portal</h2>
                    <p>Parents and students sign in at the school's own address. A parent with several children picks the child; the school decides which parts parents and students each see.</p>
                </div>
            </header>
            <div class="feat-portal">
                <?php foreach ($portal as $p) { ?>
                    <div><h4><?php echo $e($p[0]); ?></h4><p><?php echo $e($p[1]); ?></p></div>
                <?php } ?>
            </div>
        </section>

        <section class="feat-area" id="roles">
            <header class="feat-area-head">
                <span class="card-icon yellow"><?php echo pf_icon('shield'); ?></span>
                <div>
                    <h2>Roles</h2>
                    <p>Every staff member has a role, and a role decides what they see and what they can change. The defaults are below; a school can change any of them, or add its own roles, in System Setting &gt; Roles Permissions.</p>
                </div>
            </header>
            <div class="feat-portal">
                <div><h4>Super Admin</h4><p>The school owner: everything, including roles, modules and settings.</p></div>
                <?php foreach ($role_text as $r => $t) { ?>
                    <div><h4><?php echo $e($r); ?></h4><p><?php echo $e($t); ?></p></div>
                <?php } ?>
            </div>
        </section>
    </div>
</div>

<section class="band band-navy">
    <div class="wrap cta">
        <div>
            <h2>See it with your own school's data.</h2>
            <p>Register your school, or talk to us and we will walk you through it.</p>
        </div>
        <div class="actions">
            <a class="btn" href="<?php echo site_url('subscription/add'); ?>">Register your school <?php echo pf_icon('arrow'); ?></a>
            <a class="btn btn-ghost" href="<?php echo site_url('page/contact-us'); ?>">Talk to us</a>
        </div>
    </div>
</section>

<script>
(function () {
    var role = '', q = '';
    var items = [].slice.call(document.querySelectorAll('.feat-list li'));
    var modules = [].slice.call(document.querySelectorAll('.feat-module'));
    var areas = [].slice.call(document.querySelectorAll('.feat-area[id]:not(#portal):not(#roles)'));
    var result = document.getElementById('feat-result');
    var none = document.getElementById('feat-none');
    function apply() {
        var shown = 0;
        items.forEach(function (li) {
            var who = li.getAttribute('data-who').split('|');
            var text = (li.closest('.feat-module').querySelector('h3').textContent + ' ' + li.textContent).toLowerCase();
            var ok = (!role || who.indexOf(role) !== -1) && (!q || text.indexOf(q) !== -1);
            li.hidden = !ok;
            li.classList.toggle('mine', !!role && ok);
            if (ok) { shown++; }
        });
        modules.forEach(function (m) { m.hidden = !m.querySelector('.feat-list li:not([hidden])'); });
        areas.forEach(function (a) { a.hidden = !a.querySelector('.feat-module:not([hidden])'); });
        none.hidden = shown > 0;
        result.textContent = (role || q) ? shown + ' of ' + items.length + ' tools' + (role ? ' for a ' + role : '') + (q ? ' matching "' + q + '"' : '') : '';
    }
    [].slice.call(document.querySelectorAll('[data-role]')).forEach(function (b) {
        b.addEventListener('click', function () {
            role = b.getAttribute('data-role');
            document.querySelectorAll('[data-role]').forEach(function (x) { x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); });
            apply();
        });
    });
    document.getElementById('feat-q').addEventListener('input', function () { q = this.value.trim().toLowerCase(); apply(); });
})();
</script>
