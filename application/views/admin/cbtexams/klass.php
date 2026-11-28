<?php /* CBT Exams > a class (Cbtexams::klass): its subjects with their CBTs. */ $cr_msg = $this->session->flashdata('cr_msg'); $this->load->view('admin/cbtexams/_style'); ?>
<div class="content-wrapper">
    <section class="content">
        <a href="<?php echo site_url('admin/onlineexam'); ?>" class="cbt-muted"><i class="fa fa-angle-left"></i> CBT Exams</a>
        <h3 style="margin:2px 0 16px;font-weight:800"><?php echo html_escape($class['class']); ?> <small class="cbt-muted">CBT exams</small></h3>
        <?php if ($cr_msg) { ?><div class="alert alert-<?php echo $cr_msg[0]; ?>"><?php echo html_escape($cr_msg[1]); ?></div><?php } ?>
        <?php if (!$subjects) { ?>
            <div class="alert alert-info">No subjects for this class yet. Give it a subject group in <a href="<?php echo site_url('admin/subjectgroup'); ?>" target="_blank" rel="noopener">Classes &amp; Timetable &gt; Subject Groups <i class="fa fa-external-link"></i></a>.</div>
        <?php } else { ?>
            <div class="cbt-grid">
                <?php foreach ($subjects as $sid => $s) { $c = $counts[$sid]; ?>
                    <a class="cbt-tile" href="<?php echo site_url('admin/cbtexams/subject/' . (int) $class['id'] . '/' . (int) $sid); ?>">
                        <h4><?php echo html_escape($s['name']); ?></h4>
                        <small><?php echo html_escape($s['code'] ?: ' '); ?></small><br>
                        <span class="num"><?php echo (int) $c['total']; ?> CBT<?php echo (int) $c['total'] === 1 ? '' : 's'; ?></span>
                        <?php if ($c['open']) { ?><span class="num" style="background:#dcfce7;color:#15803d"><?php echo (int) $c['open']; ?> open now</span><?php } ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    </section>
</div>
