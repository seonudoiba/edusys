<?php
/*
 * Student / parent dashboard: greeting with the school's colour (as the staff dashboard), quick links,
 * and summary cards (fees owed, attendance, CBTs open now, homework to do, report cards).
 */
$h = (int) date('G');
$greet = $h < 12 ? 'Good morning' : ($h < 17 ? 'Good afternoon' : 'Good evening');
$cls = $this->db->select('c.class, s.section')->from('classes c')->join('sections s', 's.id = ' . (int) $current_class->section_id, 'inner', false)->where('c.id', (int) $current_class->class_id)->get()->row_array(); // tenancy-lint: ok int
$money = function ($v) use ($currency_symbol) { return html_escape($currency_symbol) . number_format((float) $v, 2); };
$first = trim(explode(' ', trim((string) $studentsession_username))[0]);
$att = $attendence_percentage === '-1' || $attendence_percentage === -1 ? null : (float) $attendence_percentage;
$low = $att !== null && (float) $low_attendance_limit > 0 && $att < (float) $low_attendance_limit;
if (!empty($student_data['image'])) {
    $photo = base_url() . $student_data['image'];
} else {
    $photo = base_url() . 'uploads/student_images/' . ($student_data['gender'] == 'Female' ? 'default_female.jpg' : 'default_male.jpg');
}
?>
<style>
    .sd{--b:var(--ui-brand,#1e4f8c)}
    .sd-hero{position:relative;overflow:hidden;border-radius:24px;padding:26px 28px;margin-bottom:18px;color:#fff;background:linear-gradient(125deg,color-mix(in srgb,var(--b) 92%,#000) 0%,var(--b) 45%,color-mix(in srgb,var(--b) 55%,#7c3aed) 100%);background-size:200% 200%;animation:sdRise .6s cubic-bezier(.2,.8,.2,1) forwards,sdShift 14s ease infinite;box-shadow:0 24px 50px -24px color-mix(in srgb,var(--b) 70%,transparent)}
    .sd-hero:before,.sd-hero:after{content:"";position:absolute;border-radius:50%;background:rgba(255,255,255,.08)}
    .sd-hero:before{width:260px;height:260px;right:-60px;top:-90px}
    .sd-hero:after{width:180px;height:180px;right:160px;bottom:-110px}
    @keyframes sdShift{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
    @keyframes sdRise{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
    .sd-in{position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:18px;align-items:center;justify-content:space-between}
    .sd-me{display:flex;gap:16px;align-items:center}
    .sd-me img{width:74px;height:74px;border-radius:20px;object-fit:cover;border:3px solid rgba(255,255,255,.55);background:#fff}
    .sd-me h2{margin:0;font-weight:800;font-size:26px;letter-spacing:-.02em}
    .sd-me p{margin:4px 0 0;opacity:.9;font-size:13.5px}
    .sd-qa{display:flex;flex-wrap:wrap;gap:8px}
    .sd-qa a{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;width:92px;height:78px;border-radius:16px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.22);color:#fff;font-size:12px;font-weight:700;text-align:center;transition:transform .2s,background .2s}
    .sd-qa a:hover{transform:translateY(-3px);background:rgba(255,255,255,.24);color:#fff;text-decoration:none}
    .sd-qa a i{font-size:19px}
    .sd-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:18px}
    .sd-kpi{display:block;position:relative;overflow:hidden;background:#fff;border:1px solid #e8ecf2;border-radius:18px;padding:16px 18px;color:#0f172a;box-shadow:0 1px 2px rgba(15,23,42,.04),0 8px 24px -12px rgba(15,23,42,.12);transition:transform .2s,box-shadow .2s;animation:sdRise .5s both}
    .sd-kpi:hover{transform:translateY(-3px);box-shadow:0 22px 40px -20px rgba(15,23,42,.3);color:#0f172a;text-decoration:none}
    .sd-kpi .ic{width:42px;height:42px;border-radius:13px;display:grid;place-items:center;color:#fff;font-size:18px;margin-bottom:10px}
    .sd-kpi small{display:block;text-transform:uppercase;letter-spacing:.06em;font-size:11px;font-weight:700;color:#64748b}
    .sd-kpi b{display:block;font-size:24px;font-weight:800;margin-top:2px;font-variant-numeric:tabular-nums}
    .sd-kpi span{font-size:12.5px;color:#64748b}
    .sd-kpi.warn b{color:#b91c1c}
    @media (max-width:640px){.sd-hero{padding:20px 18px}.sd-qa a{width:calc(33.33% - 6px)}.sd-me h2{font-size:21px}}
    @media (prefers-reduced-motion:reduce){.sd-hero,.sd-kpi{animation:none}}
</style>
<div class="sd">
    <div class="sd-hero">
        <div class="sd-in">
            <div class="sd-me">
                <img src="<?php echo html_escape($photo); ?>" alt="">
                <div>
                    <h2><?php echo $greet . ', ' . html_escape($first ?: $studentsession_username); ?></h2>
                    <p><i class="fa fa-graduation-cap"></i> <?php echo html_escape($cls ? $cls['class'] . ' ' . $cls['section'] : ''); ?> &nbsp;·&nbsp; <i class="fa fa-calendar"></i> <?php echo date('l, j F Y'); ?></p>
                </div>
            </div>
            <div class="sd-qa">
                <a href="<?php echo site_url('user/user/getfees'); ?>"><i class="fa fa-money"></i>Pay fees</a>
                <a href="<?php echo site_url('user/onlineexam'); ?>"><i class="fa fa-laptop"></i>CBT exams</a>
                <a href="<?php echo site_url('user/cbse/exam/reportcards'); ?>"><i class="fa fa-file-text-o"></i>Report cards</a>
                <a href="<?php echo site_url('user/timetable'); ?>"><i class="fa fa-clock-o"></i>Timetable</a>
                <a href="<?php echo site_url('user/homework'); ?>"><i class="fa fa-book"></i>Homework</a>
            </div>
        </div>
    </div>
    <div class="sd-kpis">
        <a class="sd-kpi<?php echo $kpi_fees['overdue'] > 0 ? ' warn' : ''; ?>" href="<?php echo site_url('user/user/getfees'); ?>" style="animation-delay:.05s">
            <div class="ic" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fa fa-money"></i></div>
            <small>Fees to pay</small><b><?php echo $money($kpi_fees['balance']); ?></b>
            <span><?php echo $kpi_fees['expected'] <= 0 ? 'No fees yet' : ($kpi_fees['balance'] <= 0 ? 'All paid. Thank you!' : ($kpi_fees['overdue'] > 0 ? $money($kpi_fees['overdue']) . ' past the due date' : 'of ' . $money($kpi_fees['expected']))); ?></span>
        </a>
        <a class="sd-kpi<?php echo $low ? ' warn' : ''; ?>" href="<?php echo site_url('user/attendence'); ?>" style="animation-delay:.1s">
            <div class="ic" style="background:linear-gradient(135deg,#f59e0b,#d97706)"><i class="fa fa-calendar-check-o"></i></div>
            <small>Attendance</small><b><?php echo $att === null ? '–' : rtrim(rtrim(number_format($att, 1), '0'), '.') . '%'; ?></b>
            <span><?php echo $att === null ? 'No attendance yet this session' : ($low ? 'Below the ' . (float) $low_attendance_limit . '% minimum' : 'This session'); ?></span>
        </a>
        <a class="sd-kpi" href="<?php echo site_url('user/onlineexam'); ?>" style="animation-delay:.15s">
            <div class="ic" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9)"><i class="fa fa-laptop"></i></div>
            <small>CBT exams open now</small><b><?php echo (int) $kpi_cbt; ?></b>
            <span><?php echo $kpi_cbt ? 'Take ' . ($kpi_cbt === 1 ? 'it' : 'them') . ' before they close' : 'Nothing to take right now'; ?></span>
        </a>
        <a class="sd-kpi" href="<?php echo site_url('user/homework'); ?>" style="animation-delay:.2s">
            <div class="ic" style="background:linear-gradient(135deg,#0ea5e9,#0369a1)"><i class="fa fa-book"></i></div>
            <small>Homework to do</small><b><?php echo (int) $kpi_homework; ?></b>
            <span><?php echo $kpi_homework ? 'Not submitted yet' : 'All submitted'; ?></span>
        </a>
        <a class="sd-kpi" href="<?php echo site_url('user/cbse/exam/reportcards'); ?>" style="animation-delay:.25s">
            <div class="ic" style="background:linear-gradient(135deg,var(--b),color-mix(in srgb,var(--b) 60%,#7c3aed))"><i class="fa fa-file-text-o"></i></div>
            <small>Report cards</small><b><?php echo (int) $kpi_reportcards; ?></b>
            <span><?php echo $kpi_reportcards ? 'View or download' : 'None published yet'; ?></span>
        </a>
    </div>
</div>
