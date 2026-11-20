<?php
/* Report Cards > Classes & Results (Results::index): every class with this term's progress. */
$cr_msg = $this->session->flashdata('cr_msg');
?>
<style>
    .cr-head{display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;margin-bottom:16px}
    .cr-terms{display:inline-flex;background:#fff;border:1px solid #e5e9f0;border-radius:12px;padding:4px;gap:4px}
    .cr-terms a{padding:7px 14px;border-radius:9px;font-weight:600;color:#475569}
    .cr-terms a.on{background:var(--ui-brand,#1f4e79);color:#fff}
    .cr-steps{display:flex;flex-wrap:wrap;gap:6px;margin:0 0 18px;padding:0;list-style:none;font-size:12px;color:#475569}
    .cr-steps li{background:#f1f5f9;border-radius:99px;padding:5px 12px;font-weight:600}
    .cr-steps li i{color:var(--ui-brand,#1f4e79);margin-right:4px}
    .cr-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px}
    .cr-card{display:block;background:#fff;border:1px solid #e5e9f0;border-radius:16px;padding:16px 18px;color:#0f172a;transition:transform .15s,box-shadow .15s}
    .cr-card:hover{transform:translateY(-2px);box-shadow:0 14px 30px -18px rgba(15,23,42,.35);color:#0f172a;text-decoration:none}
    .cr-card h4{margin:0 0 4px;font-weight:800}
    .cr-card small{color:#64748b}
    .cr-bar{height:8px;background:#eef2f6;border-radius:99px;overflow:hidden;margin:12px 0 6px}
    .cr-bar i{display:block;height:100%;background:var(--ui-brand,#1f4e79);border-radius:99px}
    .cr-tag{display:inline-block;border-radius:99px;padding:2px 9px;font-size:11px;font-weight:700}
    .cr-tag.new{background:#f1f5f9;color:#64748b}.cr-tag.on{background:#dbeafe;color:#1d4ed8}.cr-tag.pub{background:#dcfce7;color:#15803d}
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="cr-head">
            <div>
                <h3 style="margin:0;font-weight:800"><i class="fa fa-th-list"></i> Classes &amp; Results</h3>
                <small class="text-muted"><?php echo html_escape($session_name); ?> session</small>
            </div>
            <div class="cr-terms">
                <?php foreach ($terms as $t) { ?><a class="<?php echo (int) $t['id'] === $term ? 'on' : ''; ?>" href="<?php echo site_url('cbseexam/results?term=' . (int) $t['id']); ?>"><?php echo html_escape($t['name']); ?></a><?php } ?>
            </div>
        </div>
        <?php if ($cr_msg) { ?><div class="alert alert-<?php echo $cr_msg[0]; ?>"><?php echo html_escape($cr_msg[1]); ?></div><?php } ?>
        <ul class="cr-steps">
            <li><i class="fa fa-users"></i>1 Class</li><li><i class="fa fa-book"></i>2 Subjects</li><li><i class="fa fa-list-ol"></i>3 Assessments (CA, test, exam; written or CBT)</li>
            <li><i class="fa fa-pencil"></i>4 Scores</li><li><i class="fa fa-commenting-o"></i>5 Attendance &amp; comments</li><li><i class="fa fa-file-text-o"></i>6 Report cards</li>
        </ul>
        <?php if (!$terms) { ?>
            <div class="alert alert-warning">Add your terms first (Report Cards &gt; Terms: First Term, Second Term, Third Term).</div>
        <?php } elseif (!$classes) { ?>
            <div class="alert alert-info">No classes yet. Add them in Classes &amp; Timetable &gt; Classes.</div>
        <?php } else { ?>
            <div class="cr-grid">
                <?php foreach ($classes as $c) { $p = $c['progress']; ?>
                    <a class="cr-card" href="<?php echo site_url('cbseexam/results/klass/' . (int) $c['id'] . '?term=' . (int) $term); ?>">
                        <h4><?php echo html_escape($c['name']); ?></h4>
                        <small><?php echo (int) $c['students']; ?> student<?php echo (int) $c['students'] === 1 ? '' : 's'; ?> · <?php echo html_escape(implode(', ', $c['sections'])); ?></small>
                        <?php if (!$c['exam']) { ?>
                            <div style="margin-top:12px"><span class="cr-tag new">Not started</span></div>
                        <?php } else { ?>
                            <div class="cr-bar"><i style="width:<?php echo (int) $p['pct']; ?>%"></i></div>
                            <small><?php echo (int) $p['subjects']; ?> subjects · <?php echo (int) $p['pct']; ?>% of scores in · <?php echo (int) $p['approved']; ?> approved</small>
                            <div style="margin-top:8px"><?php echo (int) $c['exam']['is_publish'] ? '<span class="cr-tag pub"><i class="fa fa-check"></i> Report cards published</span>' : '<span class="cr-tag on">In progress</span>'; ?></div>
                        <?php } ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    </section>
</div>
