<?php
/* Report Cards > Results (Results::index): every class as a card. */
$cr_msg = $this->session->flashdata('cr_msg');
$state = array('none' => array('Not started', 'n'), 'started' => array('In progress', 's'), 'published' => array('Published', 'p'));
?>
<style>
    .cr-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px}
    .cr-card{display:block;background:#fff;border:1px solid #e5e9f0;border-radius:16px;padding:18px;color:#0f172a;transition:transform .15s,box-shadow .15s;position:relative;overflow:hidden}
    .cr-card:before{content:"";position:absolute;inset:0 0 auto 0;height:4px;background:var(--ui-brand,#1f4e79)}
    .cr-card:hover{transform:translateY(-2px);box-shadow:0 16px 32px -18px rgba(15,23,42,.35);color:#0f172a;text-decoration:none}
    .cr-card h4{margin:2px 0 4px;font-weight:800;font-size:19px}
    .cr-card small{color:#64748b}
    .cr-terms{margin-top:12px;display:flex;flex-direction:column;gap:5px}
    .cr-t{display:flex;justify-content:space-between;align-items:center;font-size:12.5px;color:#475569}
    .cr-t span{border-radius:99px;padding:2px 9px;font-size:11px;font-weight:700}
    .cr-t .n{background:#f1f5f9;color:#94a3b8}.cr-t .s{background:#dbeafe;color:#1d4ed8}.cr-t .p{background:#dcfce7;color:#15803d}
</style>
<div class="content-wrapper">
    <section class="content">
        <h3 style="margin:0;font-weight:800"><i class="fa fa-th-list"></i> Results</h3>
        <p class="text-muted" style="margin:4px 0 16px"><?php echo html_escape($session_name); ?> session · open a class to see its subjects, then a subject to see each term's exams and every student's score.</p>
        <?php if ($cr_msg) { ?><div class="alert alert-<?php echo $cr_msg[0]; ?>"><?php echo html_escape($cr_msg[1]); ?></div><?php } ?>
        <?php if (!$terms) { ?>
            <div class="alert alert-warning">Add your terms first (Report Cards &gt; Terms: First Term, Second Term, Third Term).</div>
        <?php } elseif (!$classes) { ?>
            <div class="alert alert-info">No classes yet. Add them in Classes &amp; Timetable &gt; Classes.</div>
        <?php } else { ?>
            <div class="cr-grid">
                <?php foreach ($classes as $c) { ?>
                    <a class="cr-card" href="<?php echo site_url('cbseexam/results/klass/' . (int) $c['id']); ?>">
                        <h4><?php echo html_escape($c['name']); ?></h4>
                        <small><i class="fa fa-users"></i> <?php echo (int) $c['students']; ?> student<?php echo (int) $c['students'] === 1 ? '' : 's'; ?> · <?php echo count($c['sections']); ?> section<?php echo count($c['sections']) === 1 ? '' : 's'; ?> (<?php echo html_escape(implode(', ', $c['sections'])); ?>)</small>
                        <div class="cr-terms">
                            <?php foreach ($terms as $t) { $st = $state[$c['terms'][(int) $t['id']]]; ?>
                                <div class="cr-t"><?php echo html_escape($t['name']); ?> <span class="<?php echo $st[1]; ?>"><?php echo $st[0]; ?></span></div>
                            <?php } ?>
                        </div>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    </section>
</div>
