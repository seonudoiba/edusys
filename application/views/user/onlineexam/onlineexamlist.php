<?php
/*
 * Student: my online exams as cards (live now, upcoming, finished) with a countdown, attempts,
 * duration and one clear action. $onlineexam: the student's exams (Onlineexam_model::getStudentexam).
 */
$brand = $this->customlib->brand();
$subject_names = array_column($this->db->select('id, name')->get('subjects')->result_array(), 'name', 'id');
$now = time();
$cards = array();
$counts = array('live' => 0, 'upcoming' => 0, 'done' => 0);
foreach ((array) $onlineexam as $e) {
    $from = strtotime($e->exam_from);
    $to = strtotime($e->exam_to);
    $used = (int) $e->counter;
    $allowed = (int) $e->attempt;
    if ($now > $to || ($allowed > 0 && $used >= $allowed)) {
        $state = 'done';
    } elseif ($now >= $from) {
        $state = 'live';
    } else {
        $state = 'upcoming';
    }
    $counts[$state]++;
    $cards[] = array('e' => $e, 'state' => $state, 'from' => $from, 'to' => $to, 'used' => $used, 'allowed' => $allowed);
}
// live first, then upcoming (soonest first), then finished (latest first)
usort($cards, function ($a, $b) {
    $order = array('live' => 0, 'upcoming' => 1, 'done' => 2);
    if ($order[$a['state']] !== $order[$b['state']]) {
        return $order[$a['state']] - $order[$b['state']];
    }
    return $a['state'] === 'done' ? $b['to'] - $a['to'] : $a['from'] - $b['from'];
});
$fmt = function ($t) { return date('D j M Y, g:i a', $t); };
?>
<style>
    .ex-wrap{--brand:<?php echo html_escape($brand['color']); ?>;--soft:#64748b;--rule:#e5e9f0}
    .ex-hero{background:linear-gradient(135deg,var(--brand),color-mix(in srgb,var(--brand) 60%,#0f172a));color:#fff;border-radius:16px;padding:22px 24px;margin-bottom:18px;display:flex;flex-wrap:wrap;gap:16px;align-items:center;justify-content:space-between;box-shadow:0 10px 30px color-mix(in srgb,var(--brand) 25%,transparent)}
    .ex-hero h1{margin:0;font-size:22px;font-weight:700;color:#fff}
    .ex-hero p{margin:4px 0 0;opacity:.85}
    .ex-stats{display:flex;gap:10px;flex-wrap:wrap}
    .ex-stat{background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:8px 14px;text-align:center;min-width:86px}
    .ex-stat b{display:block;font-size:20px;line-height:1.2}
    .ex-stat span{font-size:12px;opacity:.85}
    .ex-bar{display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;margin-bottom:16px}
    .ex-tabs{display:inline-flex;background:#fff;border:1px solid var(--rule);border-radius:12px;padding:4px;gap:2px;flex-wrap:wrap}
    .ex-tabs button{border:0;background:transparent;padding:7px 14px;border-radius:9px;font-weight:600;color:var(--soft)}
    .ex-tabs button.on{background:var(--brand);color:#fff}
    .ex-search{border:1px solid var(--rule);border-radius:12px;padding:9px 14px;min-width:240px;background:#fff}
    .ex-search:focus{outline:2px solid color-mix(in srgb,var(--brand) 35%,transparent);border-color:var(--brand)}
    .ex-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:16px}
    .ex-card{background:#fff;border:1px solid var(--rule);border-radius:16px;padding:18px;display:flex;flex-direction:column;gap:12px;box-shadow:0 1px 2px rgba(16,24,40,.04);transition:transform .15s,box-shadow .15s;position:relative;overflow:hidden}
    .ex-card:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(16,24,40,.09)}
    .ex-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--rule)}
    .ex-card.live::before{background:#16a34a}.ex-card.upcoming::before{background:var(--brand)}
    .ex-top{display:flex;justify-content:space-between;align-items:flex-start;gap:10px}
    .ex-chip{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;border-radius:999px;padding:4px 10px;white-space:nowrap}
    .ex-chip.live{background:#dcfce7;color:#15803d}.ex-chip.live::before{content:"";width:7px;height:7px;border-radius:50%;background:#16a34a;animation:expulse 1.6s infinite}
    .ex-chip.upcoming{background:color-mix(in srgb,var(--brand) 12%,#fff);color:var(--brand)}
    .ex-chip.done{background:#f1f5f9;color:#475569}
    .ex-chip.result{background:#fef3c7;color:#92400e}
    @keyframes expulse{50%{opacity:.3}}
    .ex-title{font-size:17px;font-weight:700;color:#0f172a;margin:0;line-height:1.35}
    .ex-sub{font-size:13px;color:var(--brand);font-weight:600;margin-top:2px}
    .ex-meta{display:grid;grid-template-columns:1fr 1fr;gap:8px}
    .ex-meta div{background:#f8fafc;border-radius:10px;padding:8px 10px}
    .ex-meta small{display:block;font-size:11px;color:var(--soft);text-transform:uppercase;letter-spacing:.04em}
    .ex-meta b{font-size:13px;color:#0f172a;font-weight:600}
    .ex-when{font-size:13px;color:var(--soft)}
    .ex-when i{width:16px;color:var(--brand)}
    .ex-count{font-variant-numeric:tabular-nums;font-weight:700;color:#0f172a}
    .ex-act{margin-top:auto;display:flex;gap:8px}
    .ex-btn{flex:1;display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:11px;padding:10px 14px;font-weight:700;border:1.5px solid var(--rule);color:#0f172a;background:#fff;text-decoration:none!important}
    .ex-btn:hover{border-color:var(--brand);color:var(--brand)}
    .ex-btn.go{background:var(--brand);border-color:var(--brand);color:#fff;box-shadow:0 6px 16px color-mix(in srgb,var(--brand) 30%,transparent)}
    .ex-btn.go:hover{filter:brightness(1.08);color:#fff}
    .ex-empty{background:#fff;border:1.5px dashed var(--rule);border-radius:16px;padding:46px 20px;text-align:center;color:var(--soft)}
    .ex-empty i{font-size:40px;color:color-mix(in srgb,var(--brand) 45%,#cbd5e1);display:block;margin-bottom:10px}
    .ex-desc{font-size:13px;color:var(--soft);max-height:3.2em;overflow:hidden}
    @media (max-width:600px){.ex-search{min-width:0;width:100%}.ex-hero{padding:18px}}
</style>
<div class="content-wrapper">
    <section class="content ex-wrap">
        <div class="ex-hero">
            <div>
                <h1><i class="fa fa-laptop"></i> <?php echo $this->lang->line('online_exam'); ?></h1>
                <p>Your CBT exams and quizzes. Start a live exam, or see when the next one opens.</p>
            </div>
            <div class="ex-stats">
                <div class="ex-stat"><b><?php echo $counts['live']; ?></b><span>Live now</span></div>
                <div class="ex-stat"><b><?php echo $counts['upcoming']; ?></b><span>Upcoming</span></div>
                <div class="ex-stat"><b><?php echo $counts['done']; ?></b><span>Finished</span></div>
            </div>
        </div>
        <?php if ($this->session->flashdata('msg')) { echo $this->session->flashdata('msg'); } ?>
        <div class="ex-bar">
            <div class="ex-tabs" role="tablist">
                <button type="button" class="on" data-f="all">All</button>
                <button type="button" data-f="live">Live now</button>
                <button type="button" data-f="upcoming">Upcoming</button>
                <button type="button" data-f="done">Finished</button>
            </div>
            <input type="search" class="ex-search" id="ex_search" placeholder="Search exams..." aria-label="Search exams">
        </div>

        <?php if (!$cards) { ?>
            <div class="ex-empty"><i class="fa fa-calendar-check-o"></i><b>No online exams yet.</b><br>When your teachers set a CBT exam for you, it will appear here.</div>
        <?php } else { ?>
            <div class="ex-grid" id="ex_grid">
                <?php foreach ($cards as $c) {
                    $e = $c['e'];
                    $subject = !empty($e->subject_id) && isset($subject_names[(int) $e->subject_id]) ? $subject_names[(int) $e->subject_id] : '';
                    $url = site_url('user/onlineexam/view/' . (int) $e->id);
                    ?>
                    <article class="ex-card <?php echo $c['state']; ?>" data-state="<?php echo $c['state']; ?>" data-text="<?php echo html_escape(strtolower($e->exam . ' ' . $subject)); ?>">
                        <div class="ex-top">
                            <div>
                                <h3 class="ex-title"><?php echo html_escape($e->exam); ?></h3>
                                <?php if ($subject) { ?><div class="ex-sub"><?php echo html_escape($subject); ?></div><?php } ?>
                            </div>
                            <?php if ($c['state'] === 'live') { ?><span class="ex-chip live">Live</span>
                            <?php } elseif ($c['state'] === 'upcoming') { ?><span class="ex-chip upcoming">Upcoming</span>
                            <?php } elseif ($e->publish_result) { ?><span class="ex-chip result">Result out</span>
                            <?php } else { ?><span class="ex-chip done">Finished</span><?php } ?>
                        </div>
                        <?php if (trim(strip_tags((string) $e->description)) !== '') { ?><div class="ex-desc"><?php echo html_escape(trim(strip_tags($e->description))); ?></div><?php } ?>
                        <div class="ex-meta">
                            <div><small>Duration</small><b><?php echo html_escape($e->duration); ?></b></div>
                            <div><small>Attempts</small><b><?php echo $c['used']; ?> of <?php echo $c['allowed'] ?: '&infin;'; ?></b></div>
                        </div>
                        <div class="ex-when">
                            <?php if ($c['state'] === 'upcoming') { ?>
                                <i class="fa fa-clock-o"></i> Opens in <span class="ex-count" data-at="<?php echo $c['from'] * 1000; ?>">&hellip;</span><br>
                                <i class="fa fa-calendar"></i> <?php echo $fmt($c['from']); ?>
                            <?php } elseif ($c['state'] === 'live') { ?>
                                <i class="fa fa-hourglass-half"></i> Closes in <span class="ex-count" data-at="<?php echo $c['to'] * 1000; ?>">&hellip;</span><br>
                                <i class="fa fa-calendar"></i> until <?php echo $fmt($c['to']); ?>
                            <?php } else { ?>
                                <i class="fa fa-calendar"></i> Closed <?php echo $fmt(min($c['to'], $now)); ?>
                            <?php } ?>
                        </div>
                        <div class="ex-act">
                            <?php if ($c['state'] === 'live') { ?>
                                <a class="ex-btn go" href="<?php echo $url; ?>"><i class="fa fa-play"></i> <?php echo $c['used'] ? 'Continue / retake' : 'Start exam'; ?></a>
                            <?php } elseif ($e->publish_result && $c['used']) { ?>
                                <a class="ex-btn go" href="<?php echo $url; ?>"><i class="fa fa-trophy"></i> View my result</a>
                            <?php } else { ?>
                                <a class="ex-btn" href="<?php echo $url; ?>"><i class="fa fa-eye"></i> Details</a>
                            <?php } ?>
                        </div>
                    </article>
                <?php } ?>
            </div>
            <div class="ex-empty" id="ex_none" style="display:none;margin-top:4px"><i class="fa fa-search"></i>No exams match.</div>
        <?php } ?>
    </section>
</div>
<script>
(function ($) {
    var filter = 'all';
    function apply() {
        var q = ($('#ex_search').val() || '').toLowerCase().trim(), shown = 0;
        $('#ex_grid .ex-card').each(function () {
            var ok = (filter === 'all' || this.getAttribute('data-state') === filter) && (!q || this.getAttribute('data-text').indexOf(q) > -1);
            this.style.display = ok ? '' : 'none'; shown += ok ? 1 : 0;
        });
        $('#ex_none').toggle(shown === 0);
    }
    $('.ex-tabs button').on('click', function () { $('.ex-tabs button').removeClass('on'); $(this).addClass('on'); filter = this.getAttribute('data-f'); apply(); });
    $('#ex_search').on('input', apply);
    function pad(n) { return (n < 10 ? '0' : '') + n; }
    function tick() {
        $('.ex-count').each(function () {
            var s = Math.max(0, Math.floor((+this.getAttribute('data-at') - Date.now()) / 1000));
            if (s === 0) { this.textContent = 'now'; return; }
            var d = Math.floor(s / 86400), h = Math.floor(s % 86400 / 3600), m = Math.floor(s % 3600 / 60), x = s % 60;
            this.textContent = d ? d + 'd ' + h + 'h ' + m + 'm' : (h ? h + 'h ' : '') + pad(m) + 'm ' + pad(x) + 's';
        });
    }
    tick(); setInterval(tick, 1000);
})(jQuery);
</script>
