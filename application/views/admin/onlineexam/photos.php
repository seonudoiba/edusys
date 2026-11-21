<?php
/* CBT > Invigilate > one student's webcam photos (Onlineexam::photos). */
$why = array('start' => 'Started', 'interval' => '', 'left_page' => 'Came back after leaving the page', 'fullscreen' => 'Left full screen');
?>
<style>
    .ph-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px}
    .ph{border:1px solid #e5e9f0;border-radius:12px;overflow:hidden;background:#fff}
    .ph img{display:block;width:100%;aspect-ratio:4/3;object-fit:cover;background:#0f172a}
    .ph div{padding:7px 10px;font-size:12.5px}
    .ph .flag{color:#b91c1c;font-weight:700}
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-camera"></i> <?php echo html_escape(trim($student['name'])); ?> <small><?php echo html_escape($student['admission_no']); ?> · <?php echo html_escape($exam['exam']); ?></small></h3>
                <div class="box-tools pull-right"><a class="btn btn-sm btn-default" href="<?php echo site_url('admin/onlineexam/invigilate/' . (int) $exam['id']); ?>"><i class="fa fa-arrow-left"></i> Invigilate</a></div>
            </div>
            <div class="box-body">
                <p class="text-muted small">Photos taken during the exam: at the start, every <?php echo (int) $exam['webcam_minutes']; ?> minutes, and when the student came back after leaving the page or full screen. Look for another person, a phone or notes, or nobody at the screen.</p>
                <?php if (!$shots) { ?><p class="text-muted">No photos.</p><?php } ?>
                <div class="ph-grid">
                    <?php foreach ($shots as $s) { ?>
                        <a class="ph" href="<?php echo site_url('admin/onlineexam/photo/' . (int) $s['id']); ?>" target="_blank" rel="noopener">
                            <img src="<?php echo site_url('admin/onlineexam/photo/' . (int) $s['id']); ?>" alt="Photo at <?php echo date('g:i a', strtotime($s['created_at'])); ?>" loading="lazy">
                            <div><?php echo date('g:i:s a', strtotime($s['created_at'])); ?><?php echo $why[$s['reason']] !== '' ? ' · <span class="' . ($s['reason'] === 'start' ? '' : 'flag') . '">' . $why[$s['reason']] . '</span>' : ''; ?></div>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>
