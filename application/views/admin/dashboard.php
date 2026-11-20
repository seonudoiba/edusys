<?php
/*
 * School dashboard (Admin::school_dashboard). A welcome banner with quick actions, KPI cards that
 * count up, charts (Chart.js 2, kept apart from the Chart.js 1 the footer loads), overview cards,
 * the calendar and staff by role. Every block keeps its module and permission checks.
 */
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$db_brand = $this->customlib->brand();
$db_color = $db_brand['color'];
$db_user = (string) $this->customlib->getAdminSessionUserName();
$db_first = trim(explode(' ', trim($db_user))[0]);
$db_hour = (int) date('G');
$db_greet = $db_hour < 12 ? 'Good morning' : ($db_hour < 17 ? 'Good afternoon' : 'Good evening');
$db_can = function ($perm, $act = 'can_view') { return $this->rbac->hasPrivilege($perm, $act); };
$db_mod = function ($m) { return $this->module_lib->hasActive($m); };
$db_pct = function ($a, $b) { return $b > 0 ? max(0, min(100, round($a * 100 / $b))) : 0; };
$db_money = function ($v) use ($currency_symbol) { return $currency_symbol . amountFormat((float) $v); };
$db_std_present = 0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present'];

// quick actions the user is allowed to use
$db_actions = array();
if ($db_can('student', 'can_add')) { $db_actions[] = array('fa-user-plus', 'Add student', site_url('student/create')); }
if ($db_mod('fees_collection') && $db_can('collect_fees')) { $db_actions[] = array('fa-money', 'Collect fees', site_url('studentfee')); }
if ($db_mod('student_attendance') && $db_can('student_attendance')) { $db_actions[] = array('fa-check-square-o', 'Attendance', site_url('admin/stuattendence')); }
if ($db_can('online_examination')) { $db_actions[] = array('fa-laptop', 'Online exam', site_url('admin/onlineexam')); }
if ($db_can('notice_board')) { $db_actions[] = array('fa-bullhorn', 'Notice', site_url('admin/notification')); }
if ($db_can('email') || $db_can('sms')) { $db_actions[] = array('fa-paper-plane', 'Message', site_url('admin/mailsms/compose')); }

// KPI cards: [icon, label, number, display text, sub text, percent or null, link, colour]
$db_kpis = array();
if ($db_can('student_count_widget')) {
    $db_kpis[] = array('fa-graduation-cap', $this->lang->line('student') ?: 'Students', (float) $total_students, number_format((float) $total_students), 'Enrolled this session', null, site_url('student/search'), '#6366f1');
}
if ($db_mod('fees_collection') && $db_can('Monthly fees_collection_widget')) {
    $db_kpis[] = array('fa-money', $this->lang->line('monthly_fees_collection'), (float) $month_collection, $db_money($month_collection), $this->lang->line(strtolower(date('F'))) . ' ' . date('Y'), null, site_url('studentfee'), '#10b981');
}
if ($db_mod('expense') && $db_can('monthly_expense_widget')) {
    $db_kpis[] = array('fa-credit-card', $this->lang->line('monthly_expenses'), (float) $month_expense, $db_money($month_expense), $this->lang->line(strtolower(date('F'))) . ' ' . date('Y'), null, site_url('admin/expense'), '#f43f5e');
}
if ($db_mod('student_attendance') && $sch_setting->attendence_type == 0 && $db_can('student_present_today_widegts')) {
    $db_kpis[] = array('fa-child', $this->lang->line('student_present_today'), $db_std_present, number_format($db_std_present), 'of ' . number_format((float) $total_students) . ' students', $db_pct($db_std_present, $total_students), site_url('admin/stuattendence'), '#f59e0b');
}
if ($db_can('staff_present_today_widegts')) {
    $db_kpis[] = array('fa-id-badge', $this->lang->line('staff_present_today'), (float) $Staffattendence_data, number_format((float) $Staffattendence_data), 'of ' . number_format((float) $getTotalStaff_data) . ' staff', $db_pct($Staffattendence_data, $getTotalStaff_data), site_url('admin/staffattendance'), '#0ea5e9');
}
if ($db_mod('fees_collection') && $db_can('fees_awaiting_payment_widegts')) {
    $db_kpis[] = array('fa-hourglass-half', $this->lang->line('fees_awaiting_payment'), (float) $total_paid, number_format((float) $total_paid), 'paid of ' . number_format((float) $total_fees) . ' due this month', $db_pct($total_paid, $total_fees), site_url('studentfee'), '#8b5cf6');
}
if ($db_mod('front_office') && $db_can('conveted_leads_widegts')) {
    $db_kpis[] = array('fa-handshake-o', $this->lang->line('converted_leads'), (float) $total_complete, number_format((float) $total_complete), 'of ' . number_format((float) $total_enquiry) . ' enquiries', $db_pct($total_complete, $total_enquiry), site_url('admin/enquiry'), '#ec4899');
}
$db_i = 0; // animation order
?>
<style>
    .db{--b:<?php echo html_escape($db_color); ?>;--ink:#0f172a;--mut:#64748b;--line:#e8ecf2;--card:#fff;--r:18px;--sh:0 1px 2px rgba(15,23,42,.04),0 8px 24px -12px rgba(15,23,42,.12);--sh2:0 22px 48px -18px rgba(15,23,42,.28)}
    .db *{box-sizing:border-box}
    .db-rise{opacity:0;transform:translateY(14px);animation:dbRise .6s cubic-bezier(.2,.8,.2,1) forwards;animation-delay:calc(var(--i,0) * 60ms)}
    @keyframes dbRise{to{opacity:1;transform:none}}
    /* hero */
    .db-hero{position:relative;overflow:hidden;border-radius:24px;padding:28px 30px;margin-bottom:22px;color:#fff;background:linear-gradient(125deg,color-mix(in srgb,var(--b) 92%,#000) 0%,var(--b) 45%,color-mix(in srgb,var(--b) 55%,#7c3aed) 100%);background-size:200% 200%;animation:dbRise .6s cubic-bezier(.2,.8,.2,1) forwards,dbShift 14s ease infinite;box-shadow:0 24px 50px -24px color-mix(in srgb,var(--b) 70%,transparent)}
    @keyframes dbShift{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
    .db-hero .orb{position:absolute;border-radius:50%;filter:blur(2px);opacity:.22;background:radial-gradient(circle at 30% 30%,#fff,transparent 70%);animation:dbFloat 9s ease-in-out infinite}
    .db-hero .o1{width:260px;height:260px;right:-60px;top:-90px}
    .db-hero .o2{width:160px;height:160px;right:220px;bottom:-80px;animation-delay:-3s}
    .db-hero .o3{width:90px;height:90px;left:45%;top:-30px;animation-delay:-6s;opacity:.15}
    @keyframes dbFloat{50%{transform:translate(-14px,12px) scale(1.06)}}
    .db-hero-in{position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:20px 30px;align-items:flex-end;justify-content:space-between}
    .db-hero small{display:inline-flex;align-items:center;gap:8px;font-size:12px;letter-spacing:.12em;text-transform:uppercase;font-weight:700;opacity:.85}
    .db-hero small .dot{width:8px;height:8px;border-radius:50%;background:#4ade80;box-shadow:0 0 0 0 rgba(74,222,128,.7);animation:dbPing 2s infinite}
    @keyframes dbPing{70%{box-shadow:0 0 0 10px rgba(74,222,128,0)}100%{box-shadow:0 0 0 0 rgba(74,222,128,0)}}
    .db-hero h1{margin:8px 0 6px;font-size:clamp(24px,3vw,32px);font-weight:800;letter-spacing:-.02em;color:#fff}
    .db-hero p{margin:0;opacity:.88;font-size:14px}
    .db-hero p i{margin:0 4px 0 12px;opacity:.8}.db-hero p i:first-child{margin-left:0}
    .db-qa{display:flex;flex-wrap:wrap;gap:10px}
    .db-qa a{display:inline-flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;width:92px;height:84px;border-radius:16px;background:rgba(255,255,255,.13);border:1px solid rgba(255,255,255,.22);color:#fff!important;font-size:12px;font-weight:600;text-decoration:none!important;backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);transition:transform .2s,background .2s,box-shadow .2s}
    .db-qa a i{font-size:20px;transition:transform .25s}
    .db-qa a:hover{transform:translateY(-4px);background:rgba(255,255,255,.24);box-shadow:0 12px 24px -10px rgba(0,0,0,.35)}
    .db-qa a:hover i{transform:scale(1.15) rotate(-6deg)}
    /* KPI cards */
    .db-kpis{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:18px;margin-bottom:22px}
    .db-kpi{position:relative;display:block;overflow:hidden;background:var(--card);border:1px solid var(--line);border-radius:var(--r);padding:18px 18px 16px;box-shadow:var(--sh);background:radial-gradient(circle at calc(100% + 30px) -30px,color-mix(in srgb,var(--c) 13%,transparent) 0 95px,transparent 96px),#fff;background-size:100% 100%;color:var(--ink)!important;text-decoration:none!important;transition:transform .25s cubic-bezier(.2,.8,.2,1),box-shadow .25s,border-color .25s}
    .db-kpi:hover{transform:translateY(-5px);box-shadow:var(--sh2);border-color:color-mix(in srgb,var(--c) 35%,var(--line))}
    .db-kpi .ic{width:46px;height:46px;border-radius:14px;display:grid;place-items:center;font-size:19px;color:#fff;background:linear-gradient(135deg,var(--c),color-mix(in srgb,var(--c) 60%,#000));box-shadow:0 10px 20px -8px var(--c);margin-bottom:14px}
    .db-kpi .lb{font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--mut)}
    .db-kpi .vl{font-size:26px;font-weight:800;letter-spacing:-.02em;margin:2px 0 2px;font-variant-numeric:tabular-nums;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .db-kpi .sb{font-size:12.5px;color:var(--mut)}
    .db-kpi .bar{height:6px;border-radius:99px;background:#eef1f6;margin-top:12px;overflow:hidden}
    .db-kpi .bar span{display:block;height:100%;width:0;border-radius:99px;background:linear-gradient(90deg,var(--c),color-mix(in srgb,var(--c) 55%,#fff));transition:width 1.4s cubic-bezier(.2,.8,.2,1) .3s}
    .db-kpi .pc{position:absolute;top:18px;right:18px;font-size:12px;font-weight:800;color:var(--c);background:color-mix(in srgb,var(--c) 12%,#fff);border-radius:99px;padding:3px 9px}
    /* panels */
    .db-grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:18px;margin-bottom:22px}
    .db-c7{grid-column:span 7}.db-c5{grid-column:span 5}.db-c8{grid-column:span 8}.db-c4{grid-column:span 4}.db-c3{grid-column:span 3}.db-c12{grid-column:span 12}
    @media(max-width:1199px){.db-c3{grid-column:span 6}}
    @media(max-width:991px){.db-c7,.db-c5,.db-c8,.db-c4{grid-column:span 12}}
    @media(max-width:600px){.db-c3{grid-column:span 12}.db-hero{padding:22px 18px}.db-qa a{width:calc(33.33% - 7px)}}
    .db-panel{background:var(--card);border:1px solid var(--line);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden;transition:box-shadow .25s}
    .db-panel:hover{box-shadow:var(--sh2)}
    .db-ph{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:16px 20px;border-bottom:1px solid var(--line)}
    .db-ph h3{margin:0;font-size:15px;font-weight:700;color:var(--ink);display:flex;align-items:center;gap:10px}
    .db-ph h3 .hi{width:32px;height:32px;border-radius:10px;display:grid;place-items:center;font-size:14px;color:var(--c,var(--b));background:color-mix(in srgb,var(--c,var(--b)) 12%,#fff)}
    .db-ph .lg{display:flex;gap:14px;font-size:12px;color:var(--mut);font-weight:600}
    .db-ph .lg i{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:6px;vertical-align:-1px}
    .db-pb{padding:16px 20px 20px}
    .db-chart{position:relative;height:280px}
    .db-donut{position:relative;height:280px}
    .db-empty{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--mut);font-size:13px}
    .db-empty i{font-size:34px;color:color-mix(in srgb,var(--b) 35%,#cbd5e1)}
    /* overview rows */
    .db-row{display:flex;align-items:center;gap:12px;padding:10px 0}
    .db-row + .db-row{border-top:1px dashed var(--line)}
    .db-row .ri{width:34px;height:34px;border-radius:10px;display:grid;place-items:center;flex:none;font-size:14px;color:var(--c);background:color-mix(in srgb,var(--c) 12%,#fff)}
    .db-row .rt{flex:1;min-width:0}
    .db-row .rt div{display:flex;justify-content:space-between;gap:8px;font-size:13px;font-weight:600;color:var(--ink)}
    .db-row .rt div span{color:var(--mut);font-weight:700;font-variant-numeric:tabular-nums}
    .db-row .rb{height:6px;border-radius:99px;background:#eef1f6;margin-top:7px;overflow:hidden}
    .db-row .rb span{display:block;height:100%;width:0;border-radius:99px;background:var(--c);transition:width 1.2s cubic-bezier(.2,.8,.2,1) .4s}
    /* roles */
    .db-role{display:flex;align-items:center;gap:12px;padding:11px 0}
    .db-role + .db-role{border-top:1px dashed var(--line)}
    .db-role .av{width:38px;height:38px;border-radius:12px;display:grid;place-items:center;color:#fff;font-weight:800;font-size:14px;flex:none}
    .db-role .nm{flex:1;font-weight:600;color:var(--ink);font-size:13.5px}
    .db-role b{font-size:18px;color:var(--ink);font-variant-numeric:tabular-nums}
    /* onboarding + notices */
    .db-ob{display:flex;flex-wrap:wrap;align-items:center;gap:14px 24px;background:var(--card);border:1px solid var(--line);border-radius:var(--r);padding:18px 22px;margin-bottom:22px;box-shadow:var(--sh)}
    .db-ob .ring{position:relative;width:58px;height:58px;flex:none}
    .db-ob .ring svg{transform:rotate(-90deg)}
    .db-ob .ring b{position:absolute;inset:0;display:grid;place-items:center;font-size:13px;color:var(--ink)}
    .db-ob h4{margin:0 0 3px;font-weight:700;font-size:15px}
    .db-ob p{margin:0;color:var(--mut);font-size:13px}
    .db-note{display:flex;align-items:center;gap:12px;background:linear-gradient(90deg,color-mix(in srgb,var(--b) 8%,#fff),#fff);border:1px solid color-mix(in srgb,var(--b) 18%,var(--line));border-radius:14px;padding:12px 16px;margin-bottom:12px}
    .db-note i.fa-bell{color:var(--b);animation:dbBell 2.4s ease-in-out infinite;transform-origin:top center}
    @keyframes dbBell{0%,60%,100%{transform:rotate(0)}10%,30%{transform:rotate(14deg)}20%,40%{transform:rotate(-14deg)}}
    .db-note a{flex:1;font-weight:600;color:var(--ink)}
    .db-note .close{float:none;opacity:.5}
    .db #calendar{margin-top:4px}
    .db .btn-primary{background:linear-gradient(135deg,var(--b),color-mix(in srgb,var(--b) 65%,#7c3aed))!important;border:0!important;color:#fff!important;border-radius:12px;padding:9px 16px;box-shadow:0 10px 20px -10px var(--b)}
    .db .btn-primary:hover{filter:brightness(1.08);transform:translateY(-1px)}
    .db .fc-toolbar h2{font-size:18px;font-weight:700}
    .db .fc button{border-radius:8px!important}
    @media (prefers-reduced-motion:reduce){.db-rise,.db-hero,.db-hero .orb,.db-note i.fa-bell,.db-hero small .dot{animation:none!important;opacity:1;transform:none}.db-kpi .bar span,.db-row .rb span{transition:none}}
</style>

<div class="content-wrapper">
    <section class="content db">
        <?php if (ENVIRONMENT != 'production') { ?>
            <div class="alert alert-danger">Environment set to <?php echo ENVIRONMENT; ?>. Set it back to production in index.php after testing; in <?php echo ENVIRONMENT; ?> mode you may see errors and deprecation warnings.</div>
        <?php } ?>
        <?php if ($mysqlVersion && $sqlMode && strpos($sqlMode->mode, 'ONLY_FULL_GROUP_BY') !== false) { ?>
            <div class="alert alert-danger">EduSYS may not work properly because ONLY_FULL_GROUP_BY is enabled. Ask your hosting provider to remove ONLY_FULL_GROUP_BY from sql_mode.</div>
        <?php } ?>

        <!-- welcome -->
        <div class="db-hero db-rise" style="--i:0">
            <span class="orb o1"></span><span class="orb o2"></span><span class="orb o3"></span>
            <div class="db-hero-in">
                <div>
                    <small><span class="dot"></span><?php echo html_escape($db_brand['name']); ?></small>
                    <h1><?php echo $db_greet . ($db_first !== '' ? ', ' . html_escape($db_first) : ''); ?> <span aria-hidden="true">👋</span></h1>
                    <p><i class="fa fa-calendar-o"></i> <?php echo date('l, j F Y'); ?> <i class="fa fa-bookmark-o"></i> Session <?php echo html_escape($this->setting_model->getCurrentSessionName()); ?></p>
                </div>
                <?php if ($db_actions) { ?>
                    <nav class="db-qa" aria-label="Quick actions">
                        <?php foreach ($db_actions as $a) { ?><a href="<?php echo $a[2]; ?>"><i class="fa <?php echo $a[0]; ?>"></i><?php echo $a[1]; ?></a><?php } ?>
                    </nav>
                <?php } ?>
            </div>
        </div>

        <?php if (!empty($onboarding)) { list($ob_done, $ob_total) = $onboarding['progress']; $ob_p = round(100 * $ob_done / max(1, $ob_total)); ?>
            <div class="db-ob db-rise" style="--i:1">
                <div class="ring"><svg width="58" height="58" viewBox="0 0 36 36"><circle cx="18" cy="18" r="15" fill="none" stroke="#eef1f6" stroke-width="4"/><circle cx="18" cy="18" r="15" fill="none" stroke="var(--b)" stroke-width="4" stroke-linecap="round" pathLength="100" stroke-dasharray="<?php echo $ob_p; ?> 100"/></svg><b><?php echo $ob_done; ?>/<?php echo $ob_total; ?></b></div>
                <div style="flex:1;min-width:220px">
                    <h4><i class="fa fa-flag-checkered"></i> Set up your school</h4>
                    <?php if ($onboarding['next']) { ?><p>Next: <?php echo html_escape($onboarding['next']['title']); ?>. <?php echo html_escape($onboarding['next']['text']); ?></p><?php } ?>
                </div>
                <a href="<?php echo site_url('admin/onboarding'); ?>" class="btn btn-primary">Continue setup <i class="fa fa-arrow-right"></i></a>
            </div>
        <?php } ?>

        <?php
        $role = $this->customlib->getStaffRole();
        $role_id = json_decode($role)->id;
        foreach ($notifications as $notice_value) {
            if ($role_id == 7 || date($this->customlib->getSchoolDateFormat()) >= $this->customlib->showDate($notice_value->publish_date)) { ?>
                <div class="db-note dashalert db-rise" role="alert" style="--i:1">
                    <i class="fa fa-bell"></i>
                    <a href="<?php echo site_url('admin/notification'); ?>"><?php echo html_escape($notice_value->title); ?></a>
                    <button type="button" class="close close_notice" data-dismiss="alert" aria-label="Close" data-noticeid="<?php echo $notice_value->id; ?>"><span aria-hidden="true">&times;</span></button>
                </div>
        <?php }
        } ?>

        <?php if ($db_kpis) { ?>
            <div class="db-kpis">
                <?php foreach ($db_kpis as $k) { $db_i++; ?>
                    <a class="db-kpi db-rise" href="<?php echo $k[6]; ?>" style="--c:<?php echo $k[7]; ?>;--i:<?php echo $db_i + 1; ?>">
                        <?php if ($k[5] !== null) { ?><span class="pc"><?php echo $k[5]; ?>%</span><?php } ?>
                        <div class="ic"><i class="fa <?php echo $k[0]; ?>"></i></div>
                        <div class="lb"><?php echo html_escape($k[1]); ?></div>
                        <div class="vl" data-count="<?php echo (float) $k[2]; ?>" data-final="<?php echo html_escape($k[3]); ?>"><?php echo html_escape($k[3]); ?></div>
                        <div class="sb"><?php echo html_escape($k[4]); ?></div>
                        <?php if ($k[5] !== null) { ?><div class="bar"><span data-w="<?php echo $k[5]; ?>"></span></div><?php } ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>

        <?php
        $db_show_bar = ($db_mod('fees_collection') || $db_mod('expense')) && $db_can('fees_collection_and_expense_monthly_chart');
        $db_show_line = ($db_mod('fees_collection') || $db_mod('expense')) && $db_can('fees_collection_and_expense_yearly_chart');
        $db_show_inc = $db_mod('income') && $db_can('income_donut_graph');
        $db_show_exp = $db_mod('expense') && $db_can('expense_donut_graph');
        $db_month = $this->lang->line(strtolower(date('F'))) . ' ' . date('Y');
        $db_legend = '<span class="lg">' . ($db_mod('income') ? '<span><i style="background:#10b981"></i>Collection</span>' : '') . ($db_mod('expense') ? '<span><i style="background:#f43f5e"></i>Expense</span>' : '') . '</span>';
        ?>
        <?php if ($db_show_bar || $db_show_inc) { ?>
            <div class="db-grid">
                <?php if ($db_show_bar) { ?>
                    <div class="db-panel db-rise <?php echo $db_show_inc ? 'db-c8' : 'db-c12'; ?>" style="--i:8">
                        <div class="db-ph"><h3><span class="hi"><i class="fa fa-bar-chart"></i></span><?php echo $this->lang->line('fees_collection_expenses_for') . ' ' . $db_month; ?></h3><?php echo $db_legend; ?></div>
                        <div class="db-pb"><div class="db-chart"><canvas id="barChart"></canvas></div></div>
                    </div>
                <?php } ?>
                <?php if ($db_show_inc) { ?>
                    <div class="db-panel db-rise <?php echo $db_show_bar ? 'db-c4' : 'db-c12'; ?>" style="--i:9;--c:#10b981">
                        <div class="db-ph"><h3><span class="hi"><i class="fa fa-pie-chart"></i></span><?php echo $this->lang->line('income') . ' · ' . $db_month; ?></h3></div>
                        <div class="db-pb"><div class="db-donut"><?php if ($incomegraph) { ?><canvas id="doughnut-chart"></canvas><?php } else { ?><div class="db-empty"><i class="fa fa-pie-chart"></i>No income recorded this month</div><?php } ?></div></div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($db_show_line || $db_show_exp) { ?>
            <div class="db-grid">
                <?php if ($db_show_line) { ?>
                    <div class="db-panel db-rise <?php echo $db_show_exp ? 'db-c8' : 'db-c12'; ?>" style="--i:10">
                        <div class="db-ph"><h3><span class="hi"><i class="fa fa-line-chart"></i></span><?php echo $this->lang->line('fees_collection_expenses_for_session') . ' ' . html_escape($this->setting_model->getCurrentSessionName()); ?></h3><?php echo $db_legend; ?></div>
                        <div class="db-pb"><div class="db-chart"><canvas id="lineChart"></canvas></div></div>
                    </div>
                <?php } ?>
                <?php if ($db_show_exp) { ?>
                    <div class="db-panel db-rise <?php echo $db_show_line ? 'db-c4' : 'db-c12'; ?>" style="--i:11;--c:#f43f5e">
                        <div class="db-ph"><h3><span class="hi"><i class="fa fa-pie-chart"></i></span><?php echo $this->lang->line('expense') . ' · ' . $db_month; ?></h3></div>
                        <div class="db-pb"><div class="db-donut"><?php if ($expensegraph) { ?><canvas id="doughnut-chart1"></canvas><?php } else { ?><div class="db-empty"><i class="fa fa-pie-chart"></i>No expenses recorded this month</div><?php } ?></div></div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php
        // overview cards: [title, icon, colour, rows of [icon, label, count text, percent, colour]]
        $db_over = array();
        if ($db_mod('fees_collection') && $db_can('fees_overview_widegts')) {
            $db_over[] = array($this->lang->line('fees_overview'), 'fa-money', '#10b981', array(
                array('fa-check-circle', $this->lang->line('paid'), $fees_overview['total_paid'], $fees_overview['paid_progress'], '#10b981'),
                array('fa-adjust', $this->lang->line('partial'), $fees_overview['total_partial'], $fees_overview['partial_progress'], '#f59e0b'),
                array('fa-times-circle', $this->lang->line('unpaid'), $fees_overview['total_unpaid'], $fees_overview['unpaid_progress'], '#f43f5e'),
            ));
        }
        if ($db_mod('front_office') && $db_can('enquiry_overview_widegts')) {
            $db_over[] = array($this->lang->line('enquiry_overview'), 'fa-comments-o', '#ec4899', array(
                array('fa-bolt', $this->lang->line('active'), $enquiry_overview['active'], $enquiry_overview['active_progress'], '#0ea5e9'),
                array('fa-trophy', $this->lang->line('won'), $enquiry_overview['won'], $enquiry_overview['won_progress'], '#10b981'),
                array('fa-pause-circle', $this->lang->line('passive'), $enquiry_overview['passive'], $enquiry_overview['passive_progress'], '#f59e0b'),
                array('fa-thumbs-down', $this->lang->line('lost'), $enquiry_overview['lost'], $enquiry_overview['lost_progress'], '#f43f5e'),
                array('fa-ban', $this->lang->line('dead'), $enquiry_overview['dead'], $enquiry_overview['dead_progress'], '#94a3b8'),
            ));
        }
        if ($db_mod('library') && $db_can('book_overview_widegts')) {
            $bt = (float) $book_overview['total'];
            $db_over[] = array($this->lang->line('library_overview'), 'fa-book', '#8b5cf6', array(
                array('fa-clock-o', $this->lang->line('due_for_return'), $book_overview['dueforreturn'], $db_pct($book_overview['dueforreturn'], max($bt, 1)), '#f59e0b'),
                array('fa-undo', $this->lang->line('returned'), $book_overview['forreturn'], $db_pct($book_overview['forreturn'], max($bt, 1)), '#10b981'),
                array('fa-share-square-o', $this->lang->line('issued_out_of') . ' ' . $book_overview['total'], $book_overview['total_issued'], $book_overview['issued_progress'], '#8b5cf6'),
                array('fa-check-square-o', $this->lang->line('available_out_of') . ' ' . $book_overview['total'], $book_overview['availble'], $book_overview['availble_progress'], '#0ea5e9'),
            ));
        }
        if ($db_mod('student_attendance') && $db_can('today_attendance_widegts')) {
            $ap = function ($v) { return (float) str_replace('%', '', (string) $v); };
            $db_over[] = array($this->lang->line('student_today_attendance'), 'fa-calendar-check-o', '#f59e0b', array(
                array('fa-check', $this->lang->line('present'), $attendence_data['total_present'], $ap($attendence_data['present']), '#10b981'),
                array('fa-clock-o', $this->lang->line('late'), $attendence_data['total_late'], $ap($attendence_data['late']), '#f59e0b'),
                array('fa-times', $this->lang->line('absent'), $attendence_data['total_absent'], $ap($attendence_data['absent']), '#f43f5e'),
                array('fa-star-half-o', $this->lang->line('half_day'), $attendence_data['total_half_day'], $ap($attendence_data['half_day']), '#0ea5e9'),
            ));
        }
        ?>
        <?php if ($db_over) { ?>
            <div class="db-grid">
                <?php foreach ($db_over as $n => $o) { ?>
                    <div class="db-panel db-rise db-c3" style="--i:<?php echo 12 + $n; ?>;--c:<?php echo $o[2]; ?>">
                        <div class="db-ph"><h3><span class="hi"><i class="fa <?php echo $o[1]; ?>"></i></span><?php echo html_escape($o[0]); ?></h3></div>
                        <div class="db-pb">
                            <?php foreach ($o[3] as $r) { $rp = max(0, min(100, round((float) $r[3], 1))); ?>
                                <div class="db-row" style="--c:<?php echo $r[4]; ?>">
                                    <span class="ri"><i class="fa <?php echo $r[0]; ?>"></i></span>
                                    <div class="rt"><div><?php echo html_escape($r[2] . ' ' . $r[1]); ?><span><?php echo $rp; ?>%</span></div><div class="rb"><span data-w="<?php echo $rp; ?>"></span></div></div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php
        $db_cal = $db_mod('calendar_to_do_list') && $db_can('calendar_to_do_list');
        $db_roles = $db_can('staff_role_count_widget') && !empty($roles);
        $db_role_colors = array('#6366f1', '#10b981', '#f59e0b', '#ec4899', '#0ea5e9', '#8b5cf6', '#f43f5e', '#14b8a6');
        ?>
        <?php if ($db_cal || $db_roles) { ?>
            <div class="db-grid">
                <?php if ($db_cal) { ?>
                    <div class="db-panel db-rise <?php echo $db_roles ? 'db-c8' : 'db-c12'; ?>" style="--i:16">
                        <div class="db-ph"><h3><span class="hi"><i class="fa fa-calendar"></i></span>Calendar &amp; to-do</h3></div>
                        <div class="db-pb"><div id="calendar"></div></div>
                    </div>
                <?php } ?>
                <?php if ($db_roles) { ?>
                    <div class="db-panel db-rise <?php echo $db_cal ? 'db-c4' : 'db-c12'; ?>" style="--i:17;--c:#6366f1;align-self:start">
                        <div class="db-ph"><h3><span class="hi"><i class="fa fa-users"></i></span>Staff by role</h3><span class="lg"><?php echo array_sum(array_map('intval', $roles)); ?> total</span></div>
                        <div class="db-pb">
                            <?php $ri = 0; foreach ($roles as $rname => $rcount) { $rc = $db_role_colors[$ri++ % count($db_role_colors)]; ?>
                                <div class="db-role">
                                    <span class="av" style="background:linear-gradient(135deg,<?php echo $rc; ?>,color-mix(in srgb,<?php echo $rc; ?> 60%,#000))"><?php echo html_escape(strtoupper(mb_substr((string) $rname, 0, 1))); ?></span>
                                    <span class="nm"><?php echo html_escape($rname); ?></span>
                                    <b data-count="<?php echo (int) $rcount; ?>" data-final="<?php echo (int) $rcount; ?>"><?php echo (int) $rcount; ?></b>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </section>
</div>
<div id="newEventModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line("add_new_event"); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form" id="addevent_form" method="post" enctype="multipart/form-data" action="">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('event_title'); ?></label><small class="req"> *</small>
                                <input class="form-control" name="title" id="input-field">
                                <span class="text-danger"><?php echo form_error('title'); ?></span>
                            </div>    
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('description'); ?></label>
                                <textarea name="description" class="form-control" id="desc-field"></textarea>
                            </div>    
                        </div>
                    <div class="col-md-12 col-lg-12 col-sm-12">        
                         <div class="row">
                            <div class="col-md-6 col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('event_from'); ?><small class="req"> *</small></label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" autocomplete="off" name="event_from" class="form-control pull-right event_from">
                                    </div>
                                </div>    
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('event_to'); ?><small class="req"> *</small></label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" autocomplete="off" name="event_to" class="form-control pull-right event_to">
                                    </div>
                                </div>    
                            </div>
                        </div>
                    </div>    
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('event_color'); ?></label>
                                <input type="hidden" name="eventcolor" autocomplete="off" id="eventcolor" class="form-control">
                            </div>    
                        </div>
                        <div class="col-md-12">
                           <div class="form-group"> 
                            <?php
$i      = 0;
$colors = '';
foreach ($event_colors as $color) {
    $color_selected_class = 'cpicker-small';
    if ($i == 0) {
        $color_selected_class = 'cpicker-big';
    }
    $colors .= "<div class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "; border-radius:100px'></div>";
    $i++;
}
echo '<div class="cpicker-wrapper">';
echo $colors;
echo '</div>';
?>
                           </div> 
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pt15 displayblock overflow-hidden w-100"><?php echo $this->lang->line('event_type'); ?></label>
                                <label class="radio-inline w-xs-45">
                                    <input type="radio" name="event_type" value="public" id="public"><?php echo $this->lang->line('public'); ?>
                                </label>
                                <label class="radio-inline w-xs-45">
                                    <input type="radio" name="event_type" value="private" checked id="private"><?php echo $this->lang->line('private'); ?>
                                </label>
                                <label class="radio-inline w-xs-45 ml-xs-0">
                                    <input type="radio" name="event_type" value="sameforall" id="public"><?php echo $this->lang->line('all'); ?> <?php echo json_decode($role)->name; ?>
                                </label>
                                <label class="radio-inline w-xs-45">
                                    <input type="radio" name="event_type" value="protected" id="public"><?php echo $this->lang->line('protected'); ?>
                                </label>
                            </div>    
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <input type="submit" class="btn btn-primary submit_addevent pull-right" value="<?php echo $this->lang->line('save'); ?>"></div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="viewEventModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_event'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form"   method="post" id="updateevent_form"  enctype="multipart/form-data" action="" >
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_title') ?></label>
                            <input class="form-control" name="title" placeholder="" id="event_title">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('description') ?></label>
                            <textarea name="description" class="form-control" placeholder="" id="event_desc"></textarea></div>
                      <div class="row">
                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_from'); ?></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" autocomplete="off" name="event_from" class="form-control pull-right event_from">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_to'); ?></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" autocomplete="off" name="event_to" class="form-control pull-right event_to">
                            </div>
                        </div>
                            </div>
                        <input type="hidden" name="eventid" id="eventid">
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_color') ?></label>
                            <input type="hidden" name="eventcolor" autocomplete="off" placeholder="Event Color" id="event_color" class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <?php
$i      = 0;
$colors = '';
foreach ($event_colors as $color) {
    $colorid              = trim($color, "#");
    $color_selected_class = 'cpicker-small';
    if ($i == 0) {
        $color_selected_class = 'cpicker-big';
    }
    $colors .= "<div id=" . $colorid . " class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "; border-radius:100px'></div>";
    $i++;
}
echo '<div class="cpicker-wrapper selectevent">';
echo $colors;
echo '</div>';
?>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_type') ?></label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="public" id="public"><?php echo $this->lang->line('public') ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="private" id="private"><?php echo $this->lang->line('private') ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="sameforall" id="public"><?php echo $this->lang->line('all') ?> <?php echo json_decode($role)->name; ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="protected" id="public"><?php echo $this->lang->line('protected') ?>
                            </label>
                        </div>
                        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                            <input type="submit" class="btn btn-primary submit_update pull-right" value="<?php echo $this->lang->line('save'); ?>">
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
<?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_delete')) {?>
                                <input type="button" id="delete_event" class="btn btn-primary submit_delete pull-right" value="<?php echo $this->lang->line('delete'); ?>">
<?php }?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () { 
    $('#viewEventModal,#newEventModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });
});
</script> 

<!-- Chart.js 2 for this page only (the footer loads Chart.js 1 for older pages and takes over window.Chart). -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script>window.DbChart = window.Chart;</script>
<script>
(function () {
    var C = window.DbChart;
    var money = <?php echo json_encode($currency_symbol); ?>;
    var hasIncome = <?php echo $this->module_lib->hasActive('income') ? 'true' : 'false'; ?>;
    var hasExpense = <?php echo $this->module_lib->hasActive('expense') ? 'true' : 'false'; ?>;
    var GREEN = '#10b981', ROSE = '#f43f5e';
    var palette = ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#0ea5e9', '#8b5cf6', '#f43f5e', '#14b8a6', '#84cc16', '#f97316'];

    function fmt(n) { return money + Number(n || 0).toLocaleString(undefined, { maximumFractionDigits: 2 }); }
    function grad(ctx, color, h) { var g = ctx.createLinearGradient(0, 0, 0, h || 280); g.addColorStop(0, color + 'cc'); g.addColorStop(1, color + '14'); return g; }
    function nums(a) { return (a || []).map(function (v) { return parseFloat(String(v).replace(/[^0-9.\-]/g, '')) || 0; }); }

    if (C) {
        C.defaults.global.defaultFontFamily = '"Inter","Segoe UI",Roboto,Arial,sans-serif';
        C.defaults.global.defaultFontColor = '#64748b';
        C.defaults.global.legend.display = false;
        C.defaults.global.tooltips.backgroundColor = '#0f172a';
        C.defaults.global.tooltips.xPadding = 12; C.defaults.global.tooltips.yPadding = 10;
        C.defaults.global.tooltips.cornerRadius = 10;
        C.defaults.global.tooltips.displayColors = true;
        C.defaults.global.animation.duration = 1400;
        C.defaults.global.animation.easing = 'easeOutQuart';
    }
    var axes = {
        xAxes: [{ gridLines: { display: false, drawBorder: false }, ticks: { maxRotation: 0, autoSkipPadding: 8 } }],
        yAxes: [{ gridLines: { color: 'rgba(15,23,42,.06)', drawBorder: false, zeroLineColor: 'rgba(15,23,42,.08)' }, ticks: { beginAtZero: true, padding: 8, maxTicksLimit: 6, callback: function (v) { return Number(v).toLocaleString(); } } }]
    };
    var moneyTip = { mode: 'index', intersect: false, callbacks: { label: function (t, d) { return ' ' + d.datasets[t.datasetIndex].label + ': ' + fmt(t.yLabel); } } };

    function draw() {
        if (!C) { return; }
        var el;
        <?php if (($this->module_lib->hasActive('fees_collection') || $this->module_lib->hasActive('expense')) && $this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) { ?>
        if ((el = document.getElementById('barChart'))) {
            var bctx = el.getContext('2d'), bsets = [];
            if (hasIncome) { bsets.push({ label: 'Collection', data: nums(<?php echo json_encode($days_collection); ?>), backgroundColor: grad(bctx, GREEN), hoverBackgroundColor: GREEN, borderWidth: 0, barPercentage: .7, categoryPercentage: .7 }); }
            if (hasExpense) { bsets.push({ label: 'Expense', data: nums(<?php echo json_encode($days_expense); ?>), backgroundColor: grad(bctx, ROSE), hoverBackgroundColor: ROSE, borderWidth: 0, barPercentage: .7, categoryPercentage: .7 }); }
            new C(bctx, { type: 'bar', data: { labels: <?php echo json_encode($current_month_days); ?>, datasets: bsets }, options: { responsive: true, maintainAspectRatio: false, scales: axes, tooltips: moneyTip } });
        }
        <?php } ?>
        <?php if (($this->module_lib->hasActive('fees_collection') || $this->module_lib->hasActive('expense')) && $this->rbac->hasPrivilege('fees_collection_and_expense_yearly_chart', 'can_view')) { ?>
        if ((el = document.getElementById('lineChart'))) {
            var lctx = el.getContext('2d'), lsets = [];
            var line = function (label, data, c) { return { label: label, data: nums(data), borderColor: c, backgroundColor: grad(lctx, c), borderWidth: 3, pointRadius: 0, pointHoverRadius: 6, pointHoverBackgroundColor: '#fff', pointHoverBorderColor: c, pointHoverBorderWidth: 3, lineTension: .4, fill: true }; };
            if (hasIncome) { lsets.push(line('Collection', <?php echo json_encode($yearly_collection); ?>, GREEN)); }
            if (hasExpense) { lsets.push(line('Expense', <?php echo json_encode($yearly_expense); ?>, ROSE)); }
            new C(lctx, { type: 'line', data: { labels: <?php echo json_encode($total_month); ?>, datasets: lsets }, options: { responsive: true, maintainAspectRatio: false, scales: axes, tooltips: moneyTip, hover: { mode: 'index', intersect: false } } });
        }
        <?php } ?>
        var donut = function (id, labels, data) {
            var d = document.getElementById(id); if (!d) { return; }
            new C(d.getContext('2d'), {
                type: 'doughnut',
                data: { labels: labels, datasets: [{ data: nums(data), backgroundColor: palette, borderColor: '#fff', borderWidth: 3, hoverBorderWidth: 0, hoverOffset: 8 }] },
                options: { responsive: true, maintainAspectRatio: false, cutoutPercentage: 70, legend: { display: true, position: 'bottom', labels: { usePointStyle: true, padding: 14, boxWidth: 8 } },
                    animation: { animateRotate: true, animateScale: true },
                    tooltips: { callbacks: { label: function (t, dd) { return ' ' + dd.labels[t.index] + ': ' + fmt(dd.datasets[0].data[t.index]); } } } }
            });
        };
        <?php if ($this->module_lib->hasActive('income') && $this->rbac->hasPrivilege('income_donut_graph', 'can_view')) { ?>
        donut('doughnut-chart', <?php echo json_encode(array_column((array) $incomegraph, 'income_category')); ?>, <?php echo json_encode(array_column((array) $incomegraph, 'total')); ?>);
        <?php } ?>
        <?php if ($this->module_lib->hasActive('expense') && $this->rbac->hasPrivilege('expense_donut_graph', 'can_view')) { ?>
        donut('doughnut-chart1', <?php echo json_encode(array_column((array) $expensegraph, 'exp_category')); ?>, <?php echo json_encode(array_column((array) $expensegraph, 'total')); ?>);
        <?php } ?>
    }

    // numbers count up, bars fill in
    function animate() {
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        [].forEach.call(document.querySelectorAll('.db [data-w]'), function (b) { b.style.width = b.getAttribute('data-w') + '%'; });
        [].forEach.call(document.querySelectorAll('.db [data-count]'), function (el) {
            var to = parseFloat(el.getAttribute('data-count')) || 0, fin = el.getAttribute('data-final');
            if (reduce || to <= 0) { el.textContent = fin; return; }
            var isMoney = fin.indexOf(money) === 0 && money !== '', start = null, dur = 1300;
            (function step(t) {
                if (!start) { start = t; }
                var p = Math.min(1, (t - start) / dur), e = 1 - Math.pow(1 - p, 3), v = to * e;
                el.textContent = p < 1 ? (isMoney ? money : '') + Math.round(v).toLocaleString() : fin;
                if (p < 1) { requestAnimationFrame(step); }
            })(performance.now());
        });
    }

    function init() { animate(); draw(); }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }

    $(document).on('click', '.close_notice', function () {
        $.ajax({ type: 'POST', url: base_url + 'admin/notification/read', data: { notice: $(this).data('noticeid') }, dataType: 'json',
            success: function (d) { if (d.status == 'fail') { errorMsg(d.msg); } else { successMsg(d.msg); } } });
    });
})();
</script>
