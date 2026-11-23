<?php
/* Online Classes (user/Onlineclasses): live now, coming up, ended. */
$e = function ($v) {
    return html_escape((string) $v);
};
$when = function ($c) {
    $d = date('Y-m-d', $c['start']);
    $day = $d === date('Y-m-d') ? 'Today' : ($d === date('Y-m-d', strtotime('+1 day')) ? 'Tomorrow' : ($d === date('Y-m-d', strtotime('-1 day')) ? 'Yesterday' : date('D j M', $c['start'])));
    return $day . ', ' . date('g:i a', $c['start']) . ' – ' . date('g:i a', $c['start'] + $c['minutes'] * 60);
};
$card = function ($c, $mode) use ($e, $when, $is_parent) {
    $icon = $c['kind'] === 'gmeet' ? 'fa-google' : 'fa-video-camera';
    $app = $c['kind'] === 'gmeet' ? 'Google Meet' : 'Zoom';
    ob_start(); ?>
    <div class="oc-card oc-<?php echo $mode; ?>">
        <div class="oc-ic <?php echo $c['kind']; ?>"><i class="fa <?php echo $icon; ?>"></i></div>
        <div class="oc-body">
            <div class="oc-title"><?php echo $e($c['title']); ?></div>
            <div class="oc-meta"><i class="fa fa-clock-o"></i> <?php echo $e($when($c)); ?> · <?php echo (int) $c['minutes']; ?> min</div>
            <div class="oc-meta"><?php echo $c['subject'] !== '' ? '<i class="fa fa-book"></i> ' . $e($c['subject']) . ' · ' : ''; ?><i class="fa fa-user"></i> <?php echo $e($c['teacher']); ?> · <?php echo $app; ?></div>
            <?php if ($c['description'] !== '') { ?><div class="oc-desc"><?php echo $e(mb_strimwidth($c['description'], 0, 160, '…')); ?></div><?php } ?>
        </div>
        <div class="oc-act">
            <?php if ($mode === 'live') { ?>
                <?php if ($is_parent) { ?><span class="oc-tag live"><i class="fa fa-circle"></i> Live now</span>
                <?php } else { ?><a class="btn btn-success oc-join" href="<?php echo site_url('user/onlineclasses/join/' . $c['kind'] . '/' . (int) $c['id']); ?>" target="_blank" rel="noopener"><i class="fa fa-sign-in"></i> Join class</a><?php } ?>
            <?php } elseif ($mode === 'upcoming') { ?>
                <span class="oc-tag soon" data-start="<?php echo (int) $c['start']; ?>"><i class="fa fa-hourglass-half"></i> <span class="oc-count">Starts <?php echo $e(date('g:i a', $c['start'])); ?></span></span>
            <?php } else { ?>
                <span class="oc-tag <?php echo $c['state']; ?>"><?php echo $c['state'] === 'cancelled' ? 'Cancelled' : 'Ended'; ?></span>
            <?php } ?>
        </div>
    </div>
    <?php return ob_get_clean();
};
?>
<style>
    .oc-sec{margin:0 0 18px}
    .oc-sec h4{font-weight:800;margin:0 0 10px;display:flex;align-items:center;gap:8px}
    .oc-sec h4 .n{background:#eef2f7;color:#334155;border-radius:99px;padding:1px 9px;font-size:12px}
    .oc-card{display:flex;gap:14px;align-items:center;background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:14px 16px;margin-bottom:10px;box-shadow:0 6px 18px -14px rgba(15,23,42,.35)}
    .oc-card.oc-live{border-color:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.12),0 10px 24px -14px rgba(22,163,74,.6)}
    .oc-card.oc-past{opacity:.8}
    .oc-ic{flex:none;width:46px;height:46px;border-radius:13px;display:grid;place-items:center;color:#fff;font-size:20px;box-shadow:inset 0 1px 0 rgba(255,255,255,.45),inset 0 -3px 0 rgba(0,0,0,.18)}
    .oc-ic.gmeet{background:linear-gradient(150deg,#34d399,#059669)}
    .oc-ic.zoom{background:linear-gradient(150deg,#60a5fa,#2563eb)}
    .oc-body{flex:1;min-width:0}
    .oc-title{font-weight:800;font-size:15px;color:#0f172a}
    .oc-meta{font-size:12.5px;color:#475569;margin-top:2px}
    .oc-desc{font-size:12.5px;color:#64748b;margin-top:4px}
    .oc-act{flex:none;text-align:right}
    .oc-join{font-weight:700;border-radius:10px;padding:8px 16px}
    .oc-tag{display:inline-flex;align-items:center;gap:6px;border-radius:99px;padding:4px 12px;font-size:12px;font-weight:700;background:#f1f5f9;color:#475569}
    .oc-tag.live{background:#dcfce7;color:#166534}.oc-tag.live .fa{animation:ocPulse 1.4s infinite}
    .oc-tag.soon{background:#fef3c7;color:#92400e}.oc-tag.cancelled{background:#fee2e2;color:#991b1b}
    @keyframes ocPulse{50%{opacity:.3}}
    .oc-empty{background:#fff;border:1px dashed #cbd5e1;border-radius:14px;padding:22px;text-align:center;color:#64748b}
    @media (max-width:600px){.oc-card{flex-wrap:wrap}.oc-act{width:100%;text-align:left}.oc-join{width:100%}}
</style>
<div class="content-wrapper">
    <section class="content-header"><h1><i class="fa fa-video-camera"></i> Online Classes</h1></section>
    <section class="content">
        <?php if ($msg) { ?><div class="alert alert-info"><?php echo $e($msg); ?></div><?php } ?>
        <div class="oc-sec">
            <h4><span style="color:#16a34a">●</span> Live now <span class="n"><?php echo count($live); ?></span></h4>
            <?php if ($live) { foreach ($live as $c) { echo $card($c, 'live'); } } else { ?>
                <div class="oc-empty">No class is live right now.<?php echo $upcoming ? ' The next one is below.' : ''; ?></div>
            <?php } ?>
        </div>
        <div class="oc-sec">
            <h4><i class="fa fa-calendar"></i> Coming up <span class="n"><?php echo count($upcoming); ?></span></h4>
            <?php if ($upcoming) { foreach ($upcoming as $c) { echo $card($c, 'upcoming'); } } else { ?>
                <div class="oc-empty">Nothing scheduled. Your teachers' online classes will show here.</div>
            <?php } ?>
        </div>
        <?php if ($past) { ?>
            <div class="oc-sec">
                <h4><i class="fa fa-history"></i> Ended <span class="n"><?php echo count($past); ?></span></h4>
                <?php foreach ($past as $c) { echo $card($c, 'past'); } ?>
            </div>
        <?php } ?>
        <?php if (!$is_parent) { ?><p class="text-muted small">Join opens 10 minutes before a class starts. The page refreshes itself, so you can leave it open.</p><?php } ?>
    </section>
</div>
<script>
(function () {
    var t0 = <?php echo time(); ?>, p0 = Date.now(); // the server's clock, not the device's
    function tick() {
        var now = t0 + (Date.now() - p0) / 1000;
        document.querySelectorAll('.oc-tag.soon').forEach(function (t) {
            var s = +t.getAttribute('data-start') - now, el = t.querySelector('.oc-count');
            if (s <= 600) { location.reload(); return; }
            if (s < 86400) { var mins = Math.ceil(s / 60), h = Math.floor(mins / 60), m = mins % 60; el.textContent = 'Starts in ' + (h ? h + ' h' + (m ? ' ' : '') : '') + (m ? m + ' min' : ''); }
        });
    }
    tick();
    setInterval(tick, 30000);
    setTimeout(function () { location.reload(); }, 300000);
})();
</script>
